#!/usr/bin/env node

const { Command } = require('commander');
const chalk = require('chalk');
const { listCommand } = require('./commands/list');
const { uploadCommand } = require('./commands/upload');
const { helpCommand } = require('./commands/help');
const { initConfig } = require('./utils/config');

const program = new Command();

program
  .name('kelly-deploy')
  .description('GitHub 이슈 기반 자동 배포 CLI 툴')
  .version('1.0.0');

// init 명령어
program
  .command('init')
  .description('설정 파일 초기화')
  .action(initConfig);

// list 명령어
program
  .command('list <issue>')
  .description('이슈 번호에 해당하는 변경된 파일 목록을 출력')
  .option('-v, --verbose', '상세 정보 출력')
  .action(listCommand);

// upload 명령어
program
  .command('upload <issue>')
  .description('이슈 번호에 해당하는 변경된 파일을 FTP 서버에 업로드')
  .option('-d, --dry-run', '실제 업로드하지 않고 시뮬레이션만 실행')
  .option('-v, --verbose', '상세 정보 출력')
  .action(uploadCommand);

// help 명령어
program
  .command('help')
  .description('사용법 안내')
  .action(helpCommand);

// 기본 help 출력
program
  .addHelpText('after', `
예시:
  $ kelly-deploy init
  $ kelly-deploy list 123
  $ kelly-deploy upload 123
  $ kelly-deploy help
  `);

program.parse(); 