#!/usr/bin/env node

const { Command } = require('commander');
const { showRetroLogo, createRetroHeader, showRetroDivider, showRetroError } = require('./utils/retroUI');
const { validateProjectRoot, showProjectInfo } = require('./utils/projectValidator');
const { initCommand } = require('./commands/init');
const { testCommand } = require('./commands/test');
const { listCommand } = require('./commands/list');
const { uploadCommand } = require('./commands/upload');
const { helpCommand } = require('./commands/help');
const { handleError } = require('./utils/errorHandler');

// 프로그램 설정
const program = new Command();

program
  .name('kelly-deploy')
  .description('GitHub 이슈 기반 자동 배포 시스템')
  .version('1.0.0');

// init 명령어
program
  .command('init')
  .description('초기 설정 파일 생성')
  .action(async (options) => {
    try {
      validateProjectRoot();
      showProjectInfo();
      await initCommand(options);
    } catch (error) {
      handleError(error, '초기 설정');
    }
  });

// test 명령어
program
  .command('test')
  .description('FTP 연결 테스트')
  .option('-e, --environment <env>', '배포 환경 지정', 'production')
  .action(async (options) => {
    try {
      validateProjectRoot();
      showProjectInfo();
      await testCommand(options);
    } catch (error) {
      handleError(error, 'FTP 연결 테스트');
    }
  });

// list 명령어
program
  .command('list')
  .description('변경된 파일 목록 조회')
  .argument('<issue>', 'GitHub 이슈 번호')
  .option('-v, --verbose', '상세 정보 출력')
  .action(async (issue, options) => {
    try {
      validateProjectRoot();
      showProjectInfo();
      await listCommand(issue, options);
    } catch (error) {
      handleError(error, '파일 목록 조회');
    }
  });

// upload 명령어
program
  .command('upload')
  .description('파일 업로드 및 배포')
  .argument('<issue>', 'GitHub 이슈 번호')
  .option('-e, --environment <env>', '배포 환경 지정', 'production')
  .option('-d, --dry-run', '실제 업로드하지 않고 시뮬레이션만 수행')
  .action(async (issue, options) => {
    try {
      validateProjectRoot();
      showProjectInfo();
      await uploadCommand(issue, options);
    } catch (error) {
      handleError(error, '파일 업로드');
    }
  });

// help 명령어
program
  .command('help')
  .description('도움말 표시')
  .action(() => {
    try {
      helpCommand();
    } catch (error) {
      handleError(error, '도움말');
    }
  });

// 기본 동작 (도움말 표시)
program.action(() => {
  try {
    // 레트로 로고 표시
    showRetroLogo();
    showRetroDivider();
    
    // 레트로 헤더
    createRetroHeader('Kelly Deploy CLI', 'GitHub 이슈 기반 자동 배포 시스템');
    showRetroDivider();
    
    // 도움말 표시
    helpCommand();
  } catch (error) {
    showRetroError('프로그램 실행 중 오류가 발생했습니다.');
    handleError(error, '메인 프로그램');
  }
});

// 에러 처리
program.exitOverride();

try {
  program.parse();
} catch (err) {
  if (err.code === 'commander.help') {
    // 도움말 요청 시
    showRetroLogo();
    showRetroDivider();
    helpCommand();
  } else {
    // 기타 에러
    showRetroError('명령어 실행 중 오류가 발생했습니다.');
    handleError(err, '명령어 파싱');
  }
} 