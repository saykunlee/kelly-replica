const chalk = require('chalk');
const inquirer = require('inquirer');
const fs = require('fs').promises;
const path = require('path');
const { initConfig } = require('../utils/config');

async function initCommand() {
  try {
    console.log(chalk.blue('🚀 Kelly Deploy CLI 초기 설정을 시작합니다...'));
    console.log(chalk.gray('─'.repeat(50)));
    
    // 설정 파일 생성
    await initConfig();
    
    console.log(chalk.green('✅ 초기 설정이 완료되었습니다!'));
    console.log(chalk.white('📁 생성된 파일:'));
    console.log(chalk.gray('  • deploy/kelly-deploy.config.js - FTP 서버 설정'));
    console.log(chalk.gray('  • .env - 환경 변수 (선택사항)'));
    
    console.log();
    console.log(chalk.white('🔧 다음 단계:'));
    console.log(chalk.gray('  1. deploy/kelly-deploy.config.js 파일에서 FTP 서버 정보를 설정하세요'));
    console.log(chalk.gray('  2. .env 파일에서 FTP_PASSWORD를 설정하세요 (선택사항)'));
    console.log(chalk.gray('  3. .deployignore 파일을 생성하여 배포 제외 파일을 설정하세요'));
    
    console.log();
    console.log(chalk.white('💡 사용 예시:'));
    console.log(chalk.gray('  kelly-deploy list 123     # 이슈 #123의 변경된 파일 목록'));
    console.log(chalk.gray('  kelly-deploy upload 123   # 이슈 #123의 파일들을 업로드'));
    
    console.log(chalk.gray('─'.repeat(50)));
    console.log(chalk.white('🎉 설정이 완료되었습니다! 이제 자동 배포를 사용할 수 있습니다.'));
    
  } catch (error) {
    console.error(chalk.red('❌ 초기 설정 중 오류가 발생했습니다:'), error.message);
    process.exit(1);
  }
}

module.exports = { initCommand }; 