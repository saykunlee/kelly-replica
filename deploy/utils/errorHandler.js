const chalk = require('chalk');

/**
 * 사용자 친화적 에러 처리
 * @param {Error} error - 에러 객체
 * @param {string} context - 에러 컨텍스트
 */
function handleError(error, context = '') {
  const message = context ? `${context}: ${error.message}` : error.message;
  console.error(chalk.red('❌ 오류:'), message);
  
  if (process.env.NODE_ENV === 'development') {
    console.error(chalk.gray('스택 트레이스:'), error.stack);
  }
  
  process.exit(1);
}

/**
 * 경고 메시지 출력
 * @param {string} message - 경고 메시지
 */
function showWarning(message) {
  console.log(chalk.yellow('⚠️  경고:'), message);
}

/**
 * 정보 메시지 출력
 * @param {string} message - 정보 메시지
 */
function showInfo(message) {
  console.log(chalk.blue('ℹ️  정보:'), message);
}

/**
 * 성공 메시지 출력
 * @param {string} message - 성공 메시지
 */
function showSuccess(message) {
  console.log(chalk.green('✅ 성공:'), message);
}

/**
 * FTP 관련 에러 처리
 * @param {Error} error - FTP 에러
 */
function handleFtpError(error) {
  const errorMessage = error.message.toLowerCase();
  
  if (errorMessage.includes('connection')) {
    console.error(chalk.red('❌ FTP 연결 실패:'));
    console.error(chalk.yellow('   - 서버 주소와 포트를 확인하세요'));
    console.error(chalk.yellow('   - 방화벽 설정을 확인하세요'));
    console.error(chalk.yellow('   - 네트워크 연결을 확인하세요'));
  } else if (errorMessage.includes('authentication') || errorMessage.includes('login')) {
    console.error(chalk.red('❌ FTP 인증 실패:'));
    console.error(chalk.yellow('   - 사용자명과 비밀번호를 확인하세요'));
    console.error(chalk.yellow('   - 계정 권한을 확인하세요'));
  } else if (errorMessage.includes('permission')) {
    console.error(chalk.red('❌ FTP 권한 오류:'));
    console.error(chalk.yellow('   - 파일 업로드 권한을 확인하세요'));
    console.error(chalk.yellow('   - 디렉토리 쓰기 권한을 확인하세요'));
  } else {
    console.error(chalk.red('❌ FTP 오류:'), error.message);
  }
  
  if (process.env.NODE_ENV === 'development') {
    console.error(chalk.gray('상세 오류:'), error.stack);
  }
}

/**
 * Git 관련 에러 처리
 * @param {Error} error - Git 에러
 */
function handleGitError(error) {
  const errorMessage = error.message.toLowerCase();
  
  if (errorMessage.includes('not a git repository')) {
    console.error(chalk.red('❌ Git 저장소가 아닙니다:'));
    console.error(chalk.yellow('   - Git 저장소 루트에서 실행하세요'));
    console.error(chalk.yellow('   - git init으로 저장소를 초기화하세요'));
  } else if (errorMessage.includes('no commits')) {
    console.error(chalk.red('❌ Git 커밋이 없습니다:'));
    console.error(chalk.yellow('   - 최소 하나의 커밋을 생성하세요'));
  } else {
    console.error(chalk.red('❌ Git 오류:'), error.message);
  }
}

/**
 * 설정 관련 에러 처리
 * @param {Error} error - 설정 에러
 */
function handleConfigError(error) {
  console.error(chalk.red('❌ 설정 오류:'), error.message);
  console.error(chalk.yellow('   - kelly-deploy init으로 설정을 초기화하세요'));
  console.error(chalk.yellow('   - .env 파일을 확인하세요'));
}

/**
 * 파일 시스템 에러 처리
 * @param {Error} error - 파일 시스템 에러
 */
function handleFileSystemError(error) {
  const errorMessage = error.message.toLowerCase();
  
  if (errorMessage.includes('no such file') || errorMessage.includes('not found')) {
    console.error(chalk.red('❌ 파일을 찾을 수 없습니다:'));
    console.error(chalk.yellow('   - 파일 경로를 확인하세요'));
    console.error(chalk.yellow('   - 파일이 존재하는지 확인하세요'));
  } else if (errorMessage.includes('permission denied')) {
    console.error(chalk.red('❌ 파일 권한 오류:'));
    console.error(chalk.yellow('   - 파일 읽기/쓰기 권한을 확인하세요'));
  } else {
    console.error(chalk.red('❌ 파일 시스템 오류:'), error.message);
  }
}

module.exports = {
  handleError,
  showWarning,
  showInfo,
  showSuccess,
  handleFtpError,
  handleGitError,
  handleConfigError,
  handleFileSystemError
}; 