const chalk = require('chalk');
const inquirer = require('inquirer');
const fs = require('fs').promises;
const path = require('path');
const { initConfig } = require('../utils/config');
const { getAvailableEnvironments, printAllEnvironments } = require('../utils/environments');
const { createSpinner, succeedSpinner, failSpinner } = require('../utils/progress');
const { handleError, showSuccess, showInfo } = require('../utils/errorHandler');

async function initCommand(options = {}) {
  const spinner = createSpinner('초기 설정 준비 중...');
  
  try {
    spinner.start();
    
    console.log(chalk.blue('🚀 Kelly Deploy CLI 초기 설정을 시작합니다...'));
    console.log(chalk.gray('─'.repeat(50)));
    
    // 사용 가능한 환경 정보 출력
    printAllEnvironments();
    
    // 설정 파일 생성
    const configSpinner = createSpinner('설정 파일을 생성하는 중...');
    configSpinner.start();
    
    await initConfig();
    
    succeedSpinner(configSpinner, '설정 파일 생성 완료');
    succeedSpinner(spinner, '초기 설정 준비 완료');
    
    console.log(chalk.green('\n✅ 초기 설정이 완료되었습니다!'));
    console.log(chalk.white('📁 생성된 파일:'));
    console.log(chalk.gray('  • deploy/kelly-deploy.config.js - FTP 서버 설정'));
    console.log(chalk.gray('  • .env - 환경 변수'));
    console.log(chalk.gray('  • .deployignore - 배포 제외 파일'));
    
    console.log();
    console.log(chalk.white('🔧 다음 단계:'));
    console.log(chalk.gray('  1. deploy/kelly-deploy.config.js 파일에서 FTP 서버 정보를 설정하세요'));
    console.log(chalk.gray('  2. .env 파일에서 환경별 FTP 비밀번호를 설정하세요'));
    console.log(chalk.gray('  3. kelly-deploy test 명령으로 연결을 테스트하세요'));
    
    console.log();
    console.log(chalk.white('💡 사용 예시:'));
    console.log(chalk.gray('  kelly-deploy test                    # FTP 연결 테스트'));
    console.log(chalk.gray('  kelly-deploy list 123               # 이슈 #123의 변경된 파일 목록'));
    console.log(chalk.gray('  kelly-deploy upload 123             # 운영 환경 배포'));
    console.log(chalk.gray('  kelly-deploy upload 123 -e staging  # 스테이징 환경 배포'));
    
    console.log(chalk.gray('─'.repeat(50)));
    showSuccess('설정이 완료되었습니다! 이제 자동 배포를 사용할 수 있습니다.');
    
  } catch (error) {
    failSpinner(spinner, '초기 설정 실패');
    handleError(error, '초기 설정');
  }
}

/**
 * .env 파일 생성
 * @param {string} defaultEnvironment - 기본 환경
 */
async function createEnvFile(defaultEnvironment) {
  const envContent = `# Kelly Deploy CLI 환경 변수
# 기본 환경: ${defaultEnvironment}

# 개발 환경 설정
DEV_FTP_HOST=dev.example.com
DEV_FTP_USER=dev_user
DEV_FTP_PASS=your_dev_password
DEV_FTP_PORT=21
DEV_FTP_SECURE=false
DEV_FTP_REMOTE_PATH=/dev/

# 스테이징 환경 설정
STAGING_FTP_HOST=staging.example.com
STAGING_FTP_USER=staging_user
STAGING_FTP_PASS=your_staging_password
STAGING_FTP_PORT=21
STAGING_FTP_SECURE=false
STAGING_FTP_REMOTE_PATH=/staging/

# 운영 환경 설정
PROD_FTP_HOST=prod.example.com
PROD_FTP_USER=prod_user
PROD_FTP_PASS=your_prod_password
PROD_FTP_PORT=21
PROD_FTP_SECURE=false
PROD_FTP_REMOTE_PATH=/public_html/

# 기본 환경 설정 (하위 호환성)
FTP_HOST=${defaultEnvironment === 'production' ? 'prod.example.com' : defaultEnvironment === 'staging' ? 'staging.example.com' : 'dev.example.com'}
FTP_USER=${defaultEnvironment === 'production' ? 'prod_user' : defaultEnvironment === 'staging' ? 'staging_user' : 'dev_user'}
FTP_PASS=your_${defaultEnvironment}_password
FTP_PORT=21
FTP_SECURE=false
FTP_REMOTE_PATH=${defaultEnvironment === 'production' ? '/public_html/' : defaultEnvironment === 'staging' ? '/staging/' : '/dev/'}
`;

  await fs.writeFile('.env', envContent, 'utf8');
}

/**
 * .deployignore 파일 생성
 */
async function createDeployIgnoreFile() {
  const ignoreContent = `# Kelly Deploy CLI 배포 제외 파일
# 이 파일에 명시된 파일/폴더는 배포에서 제외됩니다

# 개발 파일
node_modules/
.git/
.env
.env.*
*.log
.DS_Store
Thumbs.db

# 빌드 파일
dist/
build/
coverage/
.nyc_output/

# IDE 파일
.vscode/
.idea/
*.swp
*.swo
*~

# 임시 파일
tmp/
temp/
*.tmp
*.temp

# 백업 파일
*.bak
*.backup
*~

# 문서 파일
README.md
CHANGELOG.md
*.md
docs/

# 테스트 파일
test/
tests/
__tests__/
*.test.js
*.spec.js

# 설정 파일
.eslintrc*
.prettierrc*
.editorconfig
.gitignore
`;
  
  await fs.writeFile('.deployignore', ignoreContent, 'utf8');
}

module.exports = { initCommand }; 