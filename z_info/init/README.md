# Kelly DB 초기화 스크립트

이 스크립트는 Kelly 프로젝트의 데이터베이스를 새 환경에 초기화하는 도구입니다.

## 사전 준비

1. **스키마 파일 준비**: 다음 파일들이 준비되어 있어야 합니다.
   - `schema_tables.sql`: 테이블 구조 (mysqldump --no-data --skip-routines --skip-triggers)
   - `schema_routines.sql`: 함수/프로시저 (mysqldump --no-data --routines --triggers --skip-opt --no-create-db --no-create-info)
   - `schema_views.sql`: 뷰 정의 (수동 생성 또는 스크립트 생성)
   - 새 환경에서는 이 파일들을 순서대로 사용하여 완전한 스키마를 생성합니다.

2. **기본 데이터 파일 준비**: 다음 파일들이 준비되어 있어야 합니다.
   - `schema_board_group_data.sql`: 게시판 그룹 기본 데이터
   - `schema_board_data.sql`: 게시판 기본 데이터
   - `schema_board_meta_data.sql`: 게시판 메타 데이터
   - `schema_post_data.sql`: 게시글 기본 데이터
   - `schema_menus_data.sql`: 메뉴 기본 데이터
   - `schema_menu_categories_data.sql`: 메뉴 카테고리 기본 데이터
   - `schema_datatable_data.sql`: 데이터테이블 설정 기본 데이터

## 사용법

### 1. 실행 권한 확인
```bash
chmod +x z_info/init/init.sh
```

### 2. 스크립트 실행
```bash
# 방법 1: Shell 스크립트 실행
bash z_info/init/init.sh

# 방법 2: PHP 직접 실행
php z_info/init/init.php
```

### 3. 입력 정보
스크립트 실행 시 다음 정보를 입력해야 합니다:

- **대상 DB host**: 새 DB 서버의 호스트 주소 (기본값: localhost)
- **대상 포트**: 새 DB 서버의 포트 (기본값: 3306)
- **대상 DB명**: 생성할 새 데이터베이스 이름
- **대상 user**: 새 DB 서버의 사용자명
- **대상 password**: 새 DB 서버의 비밀번호
- **생성할 DB 사용자명**: 새로 생성할 DB 사용자 계정명
- **생성할 DB 사용자 비밀번호**: 새 DB 사용자 계정의 비밀번호
- **관리자 아이디**: 생성할 관리자 계정의 아이디
- **관리자 비밀번호**: 생성할 관리자 계정의 비밀번호

## 실행 과정

1. **사용자 입력 수집** (10%): 대상 DB 정보, 새 DB 사용자, 관리자 계정 정보 입력
2. **입력값 검증** (20%): 입력된 정보의 유효성 검사
3. **대상 서버 접속 확인** (30%): DB 서버 연결 테스트
4. **대상 DB 존재 여부 확인** (40%): 중복 DB 확인
5. **스키마 파일 확인** (50%): 미리 준비된 스키마 파일 검증
6. **대상 DB 생성** (60%): 새 데이터베이스 생성
7. **DB 사용자 생성** (65%): 새 DB 사용자 계정 생성 및 권한 부여
8. **대상 DB에 연결** (70%): 새 DB 사용자로 DB 연결
9. **테이블 스키마 반영** (75%): 테이블 구조 생성
10. **함수/프로시저 스키마 반영** (77%): 함수/프로시저 생성
11. **뷰 스키마 반영** (79%): 뷰 생성
12. **기본 데이터 삽입** (80%): 게시판, 메뉴, 데이터테이블 등 기본 데이터 삽입
13. **관리자 사용자 생성** (90%): 관리자 계정 생성 및 member_userid 히스토리 기록
14. **완료** (100%): 초기화 완료

## 생성되는 파일

- `init.log`: 실행 로그 파일
- `schema_tables.sql`: 테이블 구조 파일
- `schema_routines.sql`: 함수/프로시저 파일
- `schema_views.sql`: 뷰 정의 파일
- `schema_board_group_data.sql`: 게시판 그룹 기본 데이터
- `schema_board_data.sql`: 게시판 기본 데이터
- `schema_board_meta_data.sql`: 게시판 메타 데이터
- `schema_post_data.sql`: 게시글 기본 데이터
- `schema_menu_data.sql`: 메뉴 기본 데이터
- `schema_datatable_data.sql`: 데이터테이블 설정 기본 데이터

## 주의사항

1. **스키마 파일**: `schema_export.sql` 파일이 반드시 존재해야 합니다.
2. **권한**: 대상 DB 서버에 DB 생성 권한이 있어야 합니다.
3. **중복 방지**: 이미 존재하는 DB명은 사용할 수 없습니다.
4. **로그 확인**: 오류 발생 시 `init.log` 파일을 확인하세요.
5. **히스토리 기록**: 관리자 생성 시 `member_userid` 테이블에도 자동으로 히스토리가 기록됩니다.

## 문제 해결

### 스키마 파일이 없는 경우
```bash
# 1. 테이블 구조 추출
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-data --skip-routines --skip-triggers kelly > z_info/init/schema_tables.sql

# 2. 함수/프로시저 추출
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-data --routines --triggers --skip-opt --no-create-db --no-create-info kelly > z_info/init/schema_routines.sql

# 3. 뷰 정의 추출 (수동)
mysql -h [호스트] -P [포트] -u [사용자] -p[비밀번호] -e "SHOW CREATE VIEW kelly.v_board\G" | grep "Create View:" | sed 's/Create View: //' > z_info/init/schema_views.sql

# 4. 기본 데이터 추출
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-create-info --skip-routines --skip-triggers --skip-opt --no-create-db kelly board_group > z_info/init/schema_board_group_data.sql
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-create-info --skip-routines --skip-triggers --skip-opt --no-create-db kelly board > z_info/init/schema_board_data.sql
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-create-info --skip-routines --skip-triggers --skip-opt --no-create-db kelly board_meta > z_info/init/schema_board_meta_data.sql
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-create-info --skip-routines --skip-triggers --skip-opt --no-create-db kelly post > z_info/init/schema_post_data.sql
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-create-info --skip-routines --skip-triggers --skip-opt --no-create-db kelly menus > z_info/init/schema_menus_data.sql
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-create-info --skip-routines --skip-triggers --skip-opt --no-create-db kelly menu_categories > z_info/init/schema_menu_categories_data.sql
mysqldump -h [호스트] -P [포트] -u [사용자] -p[비밀번호] --no-create-info --skip-routines --skip-triggers --skip-opt --no-create-db kelly datatable_settings datatable_columns > z_info/init/schema_datatable_data.sql
```

### 권한 오류
- 대상 DB 서버에 DB 생성 권한이 있는 사용자로 접속했는지 확인
- 사용자에게 `CREATE`, `INSERT`, `SELECT` 권한이 부여되어 있는지 확인

### 연결 오류
- 호스트, 포트, 사용자명, 비밀번호가 정확한지 확인
- 방화벽 설정 확인
- DB 서버가 실행 중인지 확인
