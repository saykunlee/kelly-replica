#!/usr/bin/env node

const { Command } = require('commander');
const chalk = require('chalk');
const { listCommand } = require('./commands/list');
const { uploadCommand } = require('./commands/upload');
const { helpCommand } = require('./commands/help');
const { initCommand } = require('./commands/init');
const { testCommand } = require('./commands/test');
const { isGitRepository } = require('./utils/git');

const program = new Command();

program
  .name('kelly-deploy')
  .description('GitHub 이슈 기반 자동 배포 CLI 툴')
  .version('1.0.0');

// Git 저장소 확인 (help 명령어 제외)
if (process.argv.length > 2 && !process.argv.includes('help') && !process.argv.includes('init')) {
  if (!isGitRepository()) {
    console.error(chalk.red('❌ 현재 디렉토리가 Git 저장소가 아닙니다.'));
    console.error(chalk.yellow('Git 저장소 루트에서 실행해주세요.'));
    process.exit(1);
  }
}

// 명령어 정의
program
  .command('list <issue>')
  .description('특정 이슈와 관련된 변경된 파일 목록을 표시합니다.')
  .option('-v, --verbose', '상세한 정보 출력')
  .action(listCommand);

program
  .command('upload <issue>')
  .description('특정 이슈와 관련된 변경된 파일들을 FTP 서버에 업로드합니다.')
  .option('--dry-run', '실제 업로드하지 않고 업로드될 파일 목록만 표시')
  .option('-e, --environment <env>', '배포 환경 지정 (development, staging, production)', 'production')
  .action(uploadCommand);

program
  .command('init')
  .description('초기 설정을 진행합니다.')
  .option('-f, --force', '기존 설정을 덮어씁니다.')
  .action(initCommand);

program
  .command('test')
  .description('FTP 서버 연결을 테스트합니다.')
  .option('-e, --environment <env>', '테스트할 환경 지정', 'production')
  .action(testCommand);

program
  .command('help')
  .description('도움말을 표시합니다.')
  .action(helpCommand);

// 기본 도움말
program.on('--help', () => {
  console.log(chalk.blue('\n📖 사용 예시:'));
  console.log(chalk.white('  kelly-deploy init         # 초기 설정'));
  console.log(chalk.white('  kelly-deploy test         # FTP 연결 테스트'));
  console.log(chalk.white('  kelly-deploy list 123     # 이슈 #123의 변경된 파일 목록'));
  console.log(chalk.white('  kelly-deploy upload 123   # 이슈 #123의 파일들을 업로드'));
  console.log(chalk.white('  kelly-deploy upload 123 --dry-run  # 업로드 시뮬레이션'));
  console.log(chalk.white('  kelly-deploy help         # 도움말'));
  console.log(chalk.blue('\n🔧 환경별 배포:'));
  console.log(chalk.white('  kelly-deploy upload 123 -e development  # 개발 환경 배포'));
  console.log(chalk.white('  kelly-deploy upload 123 -e staging      # 스테이징 환경 배포'));
  console.log(chalk.white('  kelly-deploy upload 123 -e production   # 운영 환경 배포'));
});

// 에러 처리
process.on('uncaughtException', (error) => {
  console.error(chalk.red('❌ 예상치 못한 오류가 발생했습니다:'), error.message);
  if (process.env.NODE_ENV === 'development') {
    console.error(chalk.gray('스택 트레이스:'), error.stack);
  }
  process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error(chalk.red('❌ 처리되지 않은 Promise 거부:'), reason);
  process.exit(1);
});

program.parse(); 