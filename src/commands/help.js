const chalk = require('chalk');

function helpCommand() {
  console.log(chalk.blue('🚀 Kelly Deploy CLI - 사용법 안내'));
  console.log(chalk.gray('─'.repeat(60)));
  
  console.log(chalk.yellow('\n📋 명령어:'));
  console.log(chalk.white('  init                - 설정 파일 초기화'));
  console.log(chalk.white('  list <이슈번호>     - 이슈에 해당하는 변경된 파일 목록 출력'));
  console.log(chalk.white('  upload <이슈번호>   - 변경된 파일을 FTP 서버에 업로드'));
  console.log(chalk.white('  help                - 이 도움말 출력'));
  
  console.log(chalk.yellow('\n🔧 옵션:'));
  console.log(chalk.white('  -v, --verbose       - 상세 정보 출력'));
  console.log(chalk.white('  -d, --dry-run       - 실제 업로드하지 않고 시뮬레이션만 실행'));
  
  console.log(chalk.yellow('\n📝 예시:'));
  console.log(chalk.gray('  # 설정 파일 초기화'));
  console.log(chalk.white('  kelly-deploy init'));
  console.log(chalk.gray('  # 이슈 #123의 변경된 파일 목록 확인'));
  console.log(chalk.white('  kelly-deploy list 123'));
  console.log(chalk.gray('  # 이슈 #123의 파일을 FTP에 업로드'));
  console.log(chalk.white('  kelly-deploy upload 123'));
  console.log(chalk.gray('  # 드라이 런으로 업로드할 파일 미리 확인'));
  console.log(chalk.white('  kelly-deploy upload 123 --dry-run'));
  
  console.log(chalk.yellow('\n⚙️  설정:'));
  console.log(chalk.white('  .env 파일 또는 kelly-deploy.config.js 파일에서 FTP 설정을 관리합니다.'));
  console.log(chalk.gray('  - FTP_HOST: FTP 서버 주소'));
  console.log(chalk.gray('  - FTP_USER: FTP 사용자명'));
  console.log(chalk.gray('  - FTP_PASS: FTP 비밀번호'));
  console.log(chalk.gray('  - FTP_PORT: FTP 포트 (기본값: 21)'));
  console.log(chalk.gray('  - FTP_REMOTE_PATH: 원격 서버 경로'));
  
  console.log(chalk.yellow('\n📁 파일 필터링:'));
  console.log(chalk.white('  .gitignore와 .deployignore 파일을 기반으로 업로드에서 제외할 파일을 설정합니다.'));
  
  console.log(chalk.gray('\n─'.repeat(60)));
  console.log(chalk.blue('GitHub 이슈 기반 자동 배포 CLI 툴 v1.0.0'));
}

module.exports = { helpCommand }; 