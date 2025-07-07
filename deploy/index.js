#!/usr/bin/env node

const { Command } = require('commander');
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

// Git 저장소 확인
if (!isGitRepository()) {
  console.error('❌ 현재 디렉토리가 Git 저장소가 아닙니다.');
  console.error('Git 저장소 루트에서 실행해주세요.');
  process.exit(1);
}

// 명령어 정의
program
  .command('list <issue>')
  .description('특정 이슈와 관련된 변경된 파일 목록을 표시합니다.')
  .action(listCommand);

program
  .command('upload <issue>')
  .description('특정 이슈와 관련된 변경된 파일들을 FTP 서버에 업로드합니다.')
  .option('--dry-run', '실제 업로드하지 않고 업로드될 파일 목록만 표시')
  .action(uploadCommand);

program
  .command('init')
  .description('초기 설정을 진행합니다.')
  .action(initCommand);

program
  .command('test')
  .description('FTP 서버 연결을 테스트합니다.')
  .action(testCommand);

program
  .command('help')
  .description('도움말을 표시합니다.')
  .action(helpCommand);

// 기본 도움말
program.on('--help', () => {
  console.log('\n사용 예시:');
  console.log('  kelly-deploy init         # 초기 설정');
  console.log('  kelly-deploy test         # FTP 연결 테스트');
  console.log('  kelly-deploy list 123     # 이슈 #123의 변경된 파일 목록');
  console.log('  kelly-deploy upload 123   # 이슈 #123의 파일들을 업로드');
  console.log('  kelly-deploy upload 123 --dry-run  # 업로드 시뮬레이션');
  console.log('  kelly-deploy help         # 도움말');
});

program.parse(); 