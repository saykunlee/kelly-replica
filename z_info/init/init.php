<?php
declare(strict_types=1);

// 초기 DB 생성 & 스키마 복제 & 관리자 계정 생성 스크립트 (MariaDB)

// 보안: 에러는 로그에만 기록, 화면에는 최소 정보만 노출
ini_set('display_errors', '0');
error_reporting(E_ALL);

$rootDir = dirname(__DIR__, 2);
$initDir = $rootDir . DIRECTORY_SEPARATOR . 'z_info' . DIRECTORY_SEPARATOR . 'init';
$logFile = $initDir . DIRECTORY_SEPARATOR . 'init.log';
if (!is_dir($initDir)) {
    mkdir($initDir, 0775, true);
}
if (!file_exists($logFile)) {
    touch($logFile);
}

function logMessage(string $message): void
{
    global $logFile;
    $ts = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$ts] $message\n", FILE_APPEND);
}

function println(string $message): void
{
    fwrite(STDOUT, $message . PHP_EOL);
}

function progress(int $percent, string $label): void
{
    $percent = max(0, min(100, $percent));
    $barLength = 40;
    $filled = (int) floor($percent / (100 / $barLength));
    $bar = str_repeat('#', $filled) . str_repeat('-', $barLength - $filled);
    printf("\r[%s] %3d%% %s", $bar, $percent, $label);
    if ($percent >= 100) {
        echo PHP_EOL;
    }
}

function commandExists(string $name): bool
{
    $which = stripos(PHP_OS, 'WIN') === 0 ? 'where' : 'command -v';
    [$code] = runShell($which . ' ' . escapeshellarg($name));
    return $code === 0;
}

function readHidden(string $prompt): string
{
    if (stripos(PHP_OS, 'WIN') === 0) {
        // 윈도우 환경 최소 대응: 에코 없이 입력 어려워 평문 입력
        echo $prompt;
        return trim((string) fgets(STDIN));
    }
    echo $prompt;
    system('stty -echo');
    $value = trim((string) fgets(STDIN));
    system('stty echo');
    echo PHP_EOL;
    return $value;
}

function validateDbName(string $dbName): void
{
    if ($dbName === '' || !preg_match('/^[A-Za-z0-9_]+$/', $dbName)) {
        throw new RuntimeException('DB명은 영문/숫자/밑줄(_)만 허용됩니다.');
    }
}

function pdoConnect(string $host, int $port, string $user, string $pass, ?string $db = null): PDO
{
    $dsn = $db ? "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4" : "mysql:host={$host};port={$port};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    return new PDO($dsn, $user, $pass, $options);
}

function dbExists(PDO $pdo, string $dbName): bool
{
    $stmt = $pdo->prepare('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?');
    $stmt->execute([$dbName]);
    return (bool) $stmt->fetchColumn();
}

function runShell(string $cmd): array
{
    $descriptor = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $proc = proc_open($cmd, $descriptor, $pipes);
    if (!is_resource($proc)) {
        throw new RuntimeException('명령 실행 실패: ' . $cmd);
    }
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    foreach ($pipes as $p) { fclose($p); }
    $code = proc_close($proc);
    return [$code, $out, $err];
}

function exportSchemaWithMysqldump(string $host, int $port, string $user, string $pass, string $database, string $targetFile): void
{
    // 스키마 파일이 이미 존재하는지 확인
    if (!is_file($targetFile)) {
        throw new RuntimeException('스키마 파일이 존재하지 않습니다: ' . $targetFile);
    }
    // 파일 크기 확인 (최소 1KB 이상)
    if (filesize($targetFile) < 1024) {
        throw new RuntimeException('스키마 파일이 비어있거나 너무 작습니다: ' . $targetFile);
    }
}

function importSchemaFromFile(PDO $pdo, string $sqlFile): void
{
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new RuntimeException('스키마 파일 읽기 실패');
    }
    
    $fileName = basename($sqlFile);
    
    if (strpos($fileName, 'routines') !== false) {
        $statements = parseSqlFile($sql); // Handles DELIMITER
    } else {
        $statements = explode(';', $sql); // Simple split for tables/views
    }
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement === '' || strpos($statement, '--') === 0) {
            continue;
        }
        
        // 뷰 파일의 경우 특별 처리
        if (strpos($fileName, 'views') !== false) {
            // 뷰는 백틱 문제가 있을 수 있으므로 직접 실행
            try {
                // 백틱을 모두 제거하고 실행
                $cleanStatement = str_replace('`', '', $statement);
                $pdo->exec($cleanStatement);
                logMessage("뷰 생성 성공 (백틱 제거): " . substr($statement, 0, 50) . "...");
                continue; // 이미 실행했으므로 아래 실행 건너뛰기
            } catch (Exception $e) {
                logMessage("뷰 생성 실패 (백틱 제거 후): " . $e->getMessage() . " - " . substr($statement, 0, 100));
                // 백틱 제거 실패 시 원본으로 시도
            }
        }
        
        try {
            $pdo->exec($statement);
            logMessage("SQL 실행 성공: " . substr($statement, 0, 50) . "...");
        } catch (Exception $e) {
            logMessage("SQL 실행 실패 (무시됨): " . $e->getMessage() . " - " . substr($statement, 0, 100));
        }
    }
}

function fixViewStatement(string $statement): string
{
    // 모든 이중 백틱을 단일 백틱으로 변환 (전역)
    $statement = preg_replace('/``/', '`', $statement);
    
    // DEFINER 제거 (새 DB에서는 불필요)
    $statement = preg_replace('/DEFINER=`[^`]+`@`[^`]+`\s+/', '', $statement);
    
    // SQL SECURITY DEFINER 제거 (새 DB에서는 불필요)
    $statement = str_replace('SQL SECURITY DEFINER', '', $statement);
    
    // 불필요한 공백 정리
    $statement = preg_replace('/\s+/', ' ', $statement);
    $statement = trim($statement);
    
    return $statement;
}

function createViewsFromScratch(PDO $pdo): void
{
    // 뷰를 처음부터 다시 생성하는 함수
    $viewDefinitions = [
        'v_board' => "CREATE VIEW v_board AS SELECT bd.brd_id, bd.bgr_id, bd.brd_key, bd.brd_type, bd.brd_form, bd.brd_name, bd.brd_mobile_name, bd.brd_order, bd.brd_search, bd.update_date, bd.insert_date, bd.insert_id, bd.update_id, bd.useing, bd.is_deleted, bd.brd_id AS no, bd.brd_id AS pkey, FN_GET_ALL_TAGS_FOR_BOARD(bd.brd_id,'1') AS tags, FN_GET_ALL_TAGS_FOR_BOARD(bd.brd_id,'all') AS all_tags, FN_GET_POST_CNT(bd.brd_id,'brd_id') AS post_cnt, FN_get_bordgroup_name(bd.bgr_id) AS bgr_name, FN_get_memname(bd.insert_id) AS insert_member, FN_get_memname(bd.update_id) AS update_member, DATE_FORMAT(bd.insert_date,'%Y-%m-%d') AS insert_date_ymd, DATE_FORMAT(bd.update_date,'%Y-%m-%d') AS update_date_ymd, IF(bd.useing = 1,'사용','미사용') AS useing_text FROM board bd",
        
        'v_board_category' => "CREATE VIEW v_board_category AS SELECT bc.bca_id AS no, bc.bca_id AS pkey, FN_get_bord_name(bc.brd_id) AS brd_name, bc.bca_id, bc.brd_id, bc.bca_key, bc.bca_value, bc.bca_parent, bc.bca_order, bc.insert_date, bc.insert_id, bc.update_date, bc.update_id, bc.useing, bc.is_deleted, FN_get_memname(bc.insert_id) AS insert_member, FN_get_memname(bc.update_id) AS update_member, DATE_FORMAT(bc.insert_date,'%Y-%m-%d') AS insert_date_ymd, DATE_FORMAT(bc.update_date,'%Y-%m-%d') AS update_date_ymd, IF(bc.useing = 1,'사용','미사용') AS useing_text FROM board_category bc",
        
        'v_board_group' => "CREATE VIEW v_board_group AS SELECT bg.bgr_id, bg.bgr_key, bg.bgr_name, FN_GET_BOARD_COUNT_BY_GROUP(bg.bgr_id) AS bg_count, bg.bgr_order, bg.update_date, bg.insert_date, bg.insert_id, bg.update_id, bg.useing, bg.is_deleted, bg.bgr_id AS no, bg.bgr_id AS pkey, FN_get_memname(bg.insert_id) AS insert_member, FN_get_memname(bg.update_id) AS update_member, DATE_FORMAT(bg.insert_date,'%Y-%m-%d') AS insert_date_ymd, DATE_FORMAT(bg.update_date,'%Y-%m-%d') AS update_date_ymd, IF(bg.useing = 1,'사용','미사용') AS useing_text FROM board_group bg",
        
        'v_post' => "CREATE VIEW v_post AS SELECT p.post_id AS no, p.post_id AS pkey, p.post_id, p.post_num, p.post_reply, p.brd_id, FN_get_bordgroup_id(p.brd_id) AS bgr_id, FN_get_bord_key(p.brd_id) AS brd_key, FN_get_bord_type(p.brd_id) AS brd_type, FN_get_bord_name(p.brd_id) AS brd_name, p.post_title, LEFT(p.post_content,133333) AS post_content, p.post_content AS post_content_full, p.post_category, p.mem_id, p.post_userid, p.post_username, p.post_nickname, CASE WHEN CHAR_LENGTH(p.post_nickname) > 2 THEN CONCAT(SUBSTR(p.post_nickname,1,1),REPEAT('*',CHAR_LENGTH(p.post_nickname) - 2),SUBSTR(p.post_nickname,CHAR_LENGTH(p.post_nickname),1)) WHEN CHAR_LENGTH(p.post_nickname) = 2 THEN CONCAT(SUBSTR(p.post_nickname,1,1),'*') ELSE p.post_nickname END AS masked_post_nickname, p.post_hash, CASE WHEN p.post_sex = 0 THEN '남' WHEN p.post_sex = 1 THEN '여' ELSE '' END AS post_sex_text, p.post_sex, p.post_travel_team, IF(p.post_travel_chasu <> '' AND p.post_travel_team <> '' AND p.post_travel_month <> '',CONCAT(p.post_travel_team,' ',p.post_travel_month,'월 ',p.post_travel_chasu,'차'),'') AS post_travel_team_name, p.post_travel_year, p.post_travel_month, p.post_travel_chasu, p.post_age, FN_GET_TAGS_FOR_POST(p.post_id) AS post_tags, p.post_email, p.post_homepage, p.post_datetime, p.post_password, p.post_updated_datetime, p.post_update_mem_id, p.post_comment_count, p.post_comment_updated_datetime, p.post_link_count, p.post_secret, p.post_html, p.post_hide_comment, p.post_notice, p.post_receive_email, FN_GET_COMMENT_CNT(p.post_id) AS post_comment_cnt, p.post_hit, p.post_like, p.post_dislike, p.post_ip, p.post_blame, p.post_device, p.post_file, p.post_image, p.post_del, p.post_recommand, p.is_temp, FN_IS_RECOMMEND_DISPLAY_MANUAL() AS is_recommend_display_manual, p.useing, p.mem_id AS insert_id, FN_get_memname(p.mem_id) AS insert_member, p.post_update_mem_id AS update_id, FN_get_memname(p.post_update_mem_id) AS update_member, p.post_datetime AS insert_date, DATE_FORMAT(p.post_datetime,'%Y-%m-%d') AS insert_date_ymd, p.post_updated_datetime AS update_date, DATE_FORMAT(p.post_updated_datetime,'%Y-%m-%d') AS update_date_ymd, IF(p.useing = 1,'사용','미사용') AS useing_text, p.post_del AS is_deleted FROM post p",
        
        'v_post_short' => "CREATE VIEW v_post_short AS SELECT p.post_id AS no, p.post_id AS pkey, p.post_id, p.post_num, p.post_reply, p.brd_id, FN_get_bordgroup_id(p.brd_id) AS bgr_id, FN_get_bord_key(p.brd_id) AS brd_key, FN_get_bord_type(p.brd_id) AS brd_type, FN_get_bord_name(p.brd_id) AS brd_name, p.post_title, CASE WHEN CHAR_LENGTH(p.post_content) > 100 THEN CONCAT(SUBSTR(p.post_content,1,1000),'...') ELSE p.post_content END AS post_content_short, p.post_category, p.mem_id, p.post_userid, p.post_username, p.post_nickname, CASE WHEN CHAR_LENGTH(p.post_nickname) > 2 THEN CONCAT(SUBSTR(p.post_nickname,1,1),REPEAT('*',CHAR_LENGTH(p.post_nickname) - 2),SUBSTR(p.post_nickname,CHAR_LENGTH(p.post_nickname),1)) WHEN CHAR_LENGTH(p.post_nickname) = 2 THEN CONCAT(SUBSTR(p.post_nickname,1,1),'*') ELSE p.post_nickname END AS masked_post_nickname, p.post_hash, IF(p.post_sex = 0,'남성','여성') AS post_sex_text, p.post_sex, p.post_travel_team, p.post_travel_year, p.post_travel_month, p.post_travel_chasu, p.post_age, FN_GET_TAGS_FOR_POST(p.post_id) AS post_tags, p.post_email, p.post_homepage, p.post_datetime, DATE_FORMAT(p.post_datetime,'%Y-%m-%d') AS post_date, p.post_password, p.post_updated_datetime, p.post_update_mem_id, p.post_comment_count, p.post_comment_updated_datetime, p.post_link_count, p.post_secret, p.post_html, p.post_hide_comment, p.post_notice, p.post_receive_email, FN_GET_COMMENT_CNT(p.post_id) AS post_comment_cnt, p.post_hit, p.post_like, p.post_dislike, p.post_ip, p.post_blame, p.post_device, p.post_file, p.post_image, p.post_del, p.post_recommand, p.is_temp, FN_IS_RECOMMEND_DISPLAY_MANUAL() AS is_recommend_display_manual, p.useing, p.mem_id AS insert_id, FN_get_memname(p.mem_id) AS insert_member, p.post_update_mem_id AS update_id, FN_get_memname(p.post_update_mem_id) AS update_member, p.post_datetime AS insert_date, DATE_FORMAT(p.post_datetime,'%Y-%m-%d') AS insert_date_ymd, p.post_updated_datetime AS update_date, DATE_FORMAT(p.post_updated_datetime,'%Y-%m-%d') AS update_date_ymd, IF(p.useing = 1,'사용','미사용') AS useing_text, p.post_del AS is_deleted FROM post p",
        
        'v_post_tag' => "CREATE VIEW v_post_tag AS SELECT pt.pta_id, pt.post_id, pt.brd_id, pt.pta_tag, FN_GET_POST_TITLE(pt.post_id) AS post_title, pt.is_show_to_search, pt.pta_id AS no, pt.pta_id AS pkey FROM post_tag pt"
    ];
    
    foreach ($viewDefinitions as $viewName => $sql) {
        try {
            // 기존 뷰가 있다면 삭제
            $pdo->exec("DROP VIEW IF EXISTS `{$viewName}`");
            
            // 새 뷰 생성
            $pdo->exec($sql);
            logMessage("뷰 생성 성공 (처음부터): {$viewName}");
        } catch (Exception $e) {
            logMessage("뷰 생성 실패 (처음부터): {$viewName} - " . $e->getMessage());
        }
    }
}

function importBasicData(PDO $pdo, string $initDir): void
{
    $dataFiles = [
        'board_group' => 'schema_board_group_data.sql',
        'board' => 'schema_board_data.sql', 
        'board_meta' => 'schema_board_meta_data.sql',
        'post' => 'schema_post_data.sql',
        'menus' => 'schema_menus_data.sql',
        'menu_categories' => 'schema_menu_categories_data.sql',
        'datatable' => 'schema_datatable_data.sql'
    ];
    
    foreach ($dataFiles as $tableName => $fileName) {
        $filePath = $initDir . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)) {
            logMessage("기본 데이터 파일 없음 (무시됨): {$fileName}");
            continue;
        }
        
        try {
            $sql = file_get_contents($filePath);
            if ($sql === false) {
                logMessage("데이터 파일 읽기 실패 (무시됨): {$fileName}");
                continue;
            }
            
            // INSERT 문만 추출 (주석 제거)
            $lines = explode("\n", $sql);
            $insertStatements = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, 'INSERT INTO') === 0) {
                    $insertStatements[] = $line;
                }
            }
            
            if (empty($insertStatements)) {
                logMessage("INSERT 문 없음 (무시됨): {$fileName}");
                continue;
            }
            
            $successCount = 0;
            foreach ($insertStatements as $statement) {
                try {
                    $pdo->exec($statement);
                    $successCount++;
                } catch (Exception $e) {
                    logMessage("INSERT 실행 실패 (무시됨): " . $e->getMessage() . " - " . substr($statement, 0, 100));
                }
            }
            
            logMessage("기본 데이터 삽입 완료: {$tableName} ({$successCount}개)");
            
        } catch (Exception $e) {
            logMessage("기본 데이터 삽입 실패 (무시됨): {$tableName} - " . $e->getMessage());
        }
    }
}

function parseSqlFile(string $sql): array
{
    $statements = [];
    $currentStatement = '';
    $delimiter = ';';
    
    // 줄 단위로 분리
    $lines = explode("\n", $sql);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // DELIMITER 구문 처리
        if (preg_match('/^DELIMITER\s+(.+)$/i', $line, $matches)) {
            $delimiter = $matches[1];
            continue;
        }
        
        // 현재 구분자로 문장이 끝나는지 확인
        if (strpos($line, $delimiter) !== false) {
            $currentStatement .= $line;
            $statements[] = $currentStatement;
            $currentStatement = '';
        } else {
            $currentStatement .= $line . "\n";
        }
    }
    
    // 마지막 문장이 구분자로 끝나지 않는 경우 추가
    if (trim($currentStatement) !== '') {
        $statements[] = $currentStatement;
    }
    
    return array_filter($statements, function($stmt) {
        return trim($stmt) !== '';
    });
}

function createDbUser(PDO $pdo, string $username, string $password, string $database): void
{
    // DB 사용자 생성 및 권한 부여
    try {
        // 사용자 생성 (localhost와 % 모두 허용)
        $pdo->exec("CREATE USER IF NOT EXISTS '{$username}'@'localhost' IDENTIFIED BY '{$password}'");
        $pdo->exec("CREATE USER IF NOT EXISTS '{$username}'@'%' IDENTIFIED BY '{$password}'");
        
        // 데이터베이스에 대한 모든 권한 부여
        $pdo->exec("GRANT ALL PRIVILEGES ON `{$database}`.* TO '{$username}'@'localhost'");
        $pdo->exec("GRANT ALL PRIVILEGES ON `{$database}`.* TO '{$username}'@'%'");
        
        // 권한 적용
        $pdo->exec("FLUSH PRIVILEGES");
        
        logMessage("DB 사용자 생성 완료: {$username}@localhost, {$username}@%");
        logMessage("권한 부여 완료: {$username} -> {$database}.*");
    } catch (Exception $e) {
        throw new RuntimeException("DB 사용자 생성 실패: " . $e->getMessage());
    }
}

function createAdminUser(PDO $pdo, string $userId, string $password): void
{
    // 비밀번호 해시: 프로젝트 정책과 동일(BCRYPT)
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // member 테이블에 존재하는 컬럼 파악 후 동적 INSERT
    $columns = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'member'")
                  ->fetchAll(PDO::FETCH_COLUMN);
    if (!$columns) {
        throw new RuntimeException("'member' 테이블이 존재하지 않습니다");
    }

    $desired = [
        'mem_userid' => $userId,
        'mem_password' => $hash,
        'mem_is_admin' => 1,
        'mem_email_cert' => 1,
        'mem_denied' => 0,
        'mem_username' => $userId,
        'mem_nickname' => $userId,
        'mem_password_changed' => 1,
    ];

    $insertData = [];
    foreach ($desired as $col => $val) {
        if (in_array($col, $columns, true)) {
            $insertData[$col] = $val;
        }
    }
    if (!isset($insertData['mem_userid']) || !isset($insertData['mem_password'])) {
        throw new RuntimeException("'member' 테이블에 필수 컬럼(mem_userid, mem_password)이 없습니다");
    }

    $cols = array_keys($insertData);
    $params = array_map(fn($c) => ':' . $c, $cols);
    $sql = 'INSERT INTO member (' . implode(',', $cols) . ') VALUES (' . implode(',', $params) . ')';
    $stmt = $pdo->prepare($sql);
    foreach ($insertData as $c => $v) {
        $stmt->bindValue(':' . $c, $v);
    }
    $stmt->execute();
    
    // member_userid 테이블에도 히스토리 기록
    $memId = $pdo->lastInsertId();
    if ($memId) {
        try {
            // member_userid 테이블 존재 여부 확인
            $useridTableExists = $pdo->query("SHOW TABLES LIKE 'member_userid'")->rowCount() > 0;
            if ($useridTableExists) {
                $useridColumns = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'member_userid'")
                                   ->fetchAll(PDO::FETCH_COLUMN);
                
                if (in_array('mem_id', $useridColumns) && in_array('mem_userid', $useridColumns)) {
                    $useridData = [
                        'mem_id' => $memId,
                        'mem_userid' => $userId,
                        'mem_status' => 0  // 기본 상태값
                    ];
                    
                    $useridCols = array_keys($useridData);
                    $useridParams = array_map(fn($c) => ':' . $c, $useridCols);
                    $useridSql = 'INSERT INTO member_userid (' . implode(',', $useridCols) . ') VALUES (' . implode(',', $useridParams) . ')';
                    $useridStmt = $pdo->prepare($useridSql);
                    foreach ($useridData as $c => $v) {
                        $useridStmt->bindValue(':' . $c, $v);
                    }
                    $useridStmt->execute();
                    logMessage("member_userid 히스토리 기록 완료: mem_id={$memId}, userid={$userId}");
                }
            }
        } catch (Exception $e) {
            // member_userid 테이블 기록 실패는 치명적이지 않으므로 로그만 남기고 계속 진행
            logMessage("member_userid 히스토리 기록 실패 (무시됨): " . $e->getMessage());
        }
    }
}

try {
    println('Kelly DB 초기화 시작');
    
    // 입력 수집 (원본 DB 정보는 불필요, 미리 추출된 스키마 사용)
    progress(10, '사용자 입력 수집 중');
    
    // 기본값 설정
    $defaultHost = 'localhost';
    $defaultPort = 3306;
    
    echo "대상 DB host [{$defaultHost}]: "; $hostInput = trim((string) fgets(STDIN));
    $dstHost = $hostInput !== '' ? $hostInput : $defaultHost;
    
    echo "대상 포트 [{$defaultPort}]: "; $portInput = trim((string) fgets(STDIN));
    $dstPort = $portInput !== '' ? (int) $portInput : $defaultPort;
    
    echo '대상 DB명: '; $dstDb = trim((string) fgets(STDIN));
    echo '대상 user: '; $dstUser = trim((string) fgets(STDIN));
    $dstPass = readHidden('대상 password: ');
    
    echo '생성할 DB 사용자명: '; $newDbUser = trim((string) fgets(STDIN));
    $newDbPass = readHidden('생성할 DB 사용자 비밀번호: ');

    echo '관리자 아이디: '; $adminId = trim((string) fgets(STDIN));
    $adminPw = readHidden('관리자 비밀번호: ');

    // 검증
    progress(20, '입력값 검증 중');
    if ($dstHost === '' || $dstPort <= 0 || $dstDb === '' || $dstUser === '') { 
        throw new RuntimeException('대상 DB 접속 정보가 올바르지 않습니다'); 
    }
    validateDbName($dstDb);
    if ($newDbUser === '' || $newDbPass === '') {
        throw new RuntimeException('생성할 DB 사용자명/비밀번호를 입력하세요');
    }
    if ($adminId === '' || $adminPw === '') { 
        throw new RuntimeException('관리자 아이디/비밀번호를 입력하세요'); 
    }

    progress(30, '대상 서버 접속 확인');
    $dstRootPdo = pdoConnect($dstHost, $dstPort, $dstUser, $dstPass, null);

    progress(40, '대상 DB 존재 여부 확인');
    if (dbExists($dstRootPdo, $dstDb)) {
        println("대상 DB '{$dstDb}' 가 이미 존재합니다. 작업을 종료합니다.");
        logMessage("종료: DB 존재함 - {$dstDb}");
        exit(0);
    }

    progress(50, '스키마 파일 확인');
    $tablesFile = $initDir . DIRECTORY_SEPARATOR . 'schema_tables.sql';
    $routinesFile = $initDir . DIRECTORY_SEPARATOR . 'schema_routines.sql';
    $viewsFile = $initDir . DIRECTORY_SEPARATOR . 'schema_views.sql';
    
    // 각 스키마 파일 존재 여부 확인
    exportSchemaWithMysqldump($dstHost, $dstPort, $dstUser, $dstPass, $dstDb, $tablesFile);
    exportSchemaWithMysqldump($dstHost, $dstPort, $dstUser, $dstPass, $dstDb, $routinesFile);
    exportSchemaWithMysqldump($dstHost, $dstPort, $dstUser, $dstPass, $dstDb, $viewsFile);
    
    logMessage('스키마 파일 확인 완료: tables, routines, views');

    progress(60, '대상 DB 생성');
    $dstRootPdo->exec("CREATE DATABASE `{$dstDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

    progress(65, 'DB 사용자 생성');
    createDbUser($dstRootPdo, $newDbUser, $newDbPass, $dstDb);

    progress(70, '대상 DB에 연결');
    $dstPdo = pdoConnect($dstHost, $dstPort, $newDbUser, $newDbPass, $dstDb);

    progress(75, '테이블 스키마 반영');
    $tablesFile = $initDir . DIRECTORY_SEPARATOR . 'schema_tables.sql';
    importSchemaFromFile($dstPdo, $tablesFile);
    
    progress(77, '함수/프로시저 스키마 반영');
    $routinesFile = $initDir . DIRECTORY_SEPARATOR . 'schema_routines.sql';
    importSchemaFromFile($dstPdo, $routinesFile);
    
    progress(79, '뷰 스키마 반영');
    // 뷰를 처음부터 다시 생성 (백틱 문제 해결)
    createViewsFromScratch($dstPdo);

    progress(80, '기본 데이터 삽입');
    importBasicData($dstPdo, $initDir);

    progress(90, '관리자 사용자 생성');
    createAdminUser($dstPdo, $adminId, $adminPw);

    progress(100, '완료');
    println('DB 초기화 완료');
    logMessage('완료: 스키마 반영 및 관리자 생성');
} catch (Throwable $e) {
    logMessage('오류: ' . $e->getMessage());
    println('오류가 발생했습니다. 상세 내용은 z_info/init/init.log를 확인하세요.');
    exit(1);
}


