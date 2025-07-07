const chalk = require('chalk');

function helpCommand() {
  console.log(chalk.blue('🚀 Kelly Deploy CLI - GitHub 이슈 기반 자동 배포 도구'));
  console.log(chalk.gray('='.repeat(60)));
  
  console.log(chalk.yellow('\n📋 사용 가능한 명령어:'));
  
  console.log(chalk.white('\n🔧 설정 관련:'));
  console.log(chalk.cyan('  kelly-deploy init'));
  console.log(chalk.gray('     초기 설정을 진행합니다. VSCode SFTP 설정을 자동으로 읽어옵니다.'));
  
  console.log(chalk.white('\n🔍 테스트 및 확인:'));
  console.log(chalk.cyan('  kelly-deploy test'));
  console.log(chalk.gray('     FTP 서버 연결을 테스트하고 원격 디렉토리 접근을 확인합니다.'));
  
  console.log(chalk.cyan('  kelly-deploy list <이슈번호>'));
  console.log(chalk.gray('     특정 이슈와 관련된 변경된 파일 목록을 표시합니다.'));
  
  console.log(chalk.white('\n📤 배포:'));
  console.log(chalk.cyan('  kelly-deploy upload <이슈번호>'));
  console.log(chalk.gray('     특정 이슈와 관련된 변경된 파일들을 FTP 서버에 업로드합니다.'));
  
  console.log(chalk.cyan('  kelly-deploy upload <이슈번호> --dry-run'));
  console.log(chalk.gray('     실제 업로드하지 않고 업로드될 파일 목록만 표시합니다.'));
  
  console.log(chalk.white('\n❓ 도움말:'));
  console.log(chalk.cyan('  kelly-deploy help'));
  console.log(chalk.gray('     이 도움말을 표시합니다.'));
  
  console.log(chalk.yellow('\n📝 사용 예시:'));
  console.log(chalk.white('  # 초기 설정'));
  console.log(chalk.gray('  kelly-deploy init'));
  
  console.log(chalk.white('  # 연결 테스트'));
  console.log(chalk.gray('  kelly-deploy test'));
  
  console.log(chalk.white('  # 이슈 #123의 변경된 파일 확인'));
  console.log(chalk.gray('  kelly-deploy list 123'));
  
  console.log(chalk.white('  # 이슈 #123의 파일들을 업로드'));
  console.log(chalk.gray('  kelly-deploy upload 123'));
  
  console.log(chalk.white('  # 업로드 시뮬레이션'));
  console.log(chalk.gray('  kelly-deploy upload 123 --dry-run'));
  
  console.log(chalk.yellow('\n⚙️ 설정 파일:'));
  console.log(chalk.gray('  • deploy/kelly-deploy.config.js - 메인 설정 파일'));
  console.log(chalk.gray('  • .env - 환경변수 (FTP 비밀번호 등)'));
  console.log(chalk.gray('  • .deployignore - 배포에서 제외할 파일 패턴'));
  
  console.log(chalk.yellow('\n🔒 보안:'));
  console.log(chalk.gray('  • FTP 비밀번호는 .env 파일에 저장됩니다'));
  console.log(chalk.gray('  • .env 파일은 .gitignore에 포함되어 있어 Git에 커밋되지 않습니다'));
  console.log(chalk.gray('  • 입력값 검증과 SQL Injection 방지가 적용됩니다'));
  
  console.log(chalk.gray('\n' + '='.repeat(60)));
  console.log(chalk.blue('💡 문제가 있으면 kelly-deploy test 명령으로 연결 상태를 확인하세요!'));
}

module.exports = { helpCommand }; 