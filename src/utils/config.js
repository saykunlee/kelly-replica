const fs = require('fs').promises;
const path = require('path');
const dotenv = require('dotenv');
const chalk = require('chalk');

/**
 * 환경 변수와 설정 파일에서 설정을 로드합니다.
 * @returns {Promise<Object>} 설정 객체
 */
async function loadConfig() {
  // .env 파일 로드
  dotenv.config();
  
  const config = {
    ftp: {
      host: process.env.FTP_HOST,
      user: process.env.FTP_USER,
      password: process.env.FTP_PASS,
      port: parseInt(process.env.FTP_PORT) || 21,
      secure: process.env.FTP_SECURE === 'true',
      remotePath: process.env.FTP_REMOTE_PATH || '/'
    }
  };
  
  // 설정 파일 로드 (kelly-deploy.config.js)
  try {
    const configPath = path.join(process.cwd(), 'kelly-deploy.config.js');
    const configFile = await fs.access(configPath).then(() => require(configPath)).catch(() => null);
    
    if (configFile) {
      // 설정 파일의 값으로 환경 변수 값 덮어쓰기
      if (configFile.ftp) {
        config.ftp = { ...config.ftp, ...configFile.ftp };
      }
    }
  } catch (error) {
    // 설정 파일 로드 실패 시 무시
  }
  
  // 필수 설정 검증
  validateConfig(config);
  
  return config;
}

/**
 * 설정이 유효한지 검증합니다.
 * @param {Object} config - 설정 객체
 */
function validateConfig(config) {
  const requiredFields = ['host', 'user', 'password'];
  const missingFields = requiredFields.filter(field => !config.ftp[field]);
  
  if (missingFields.length > 0) {
    throw new Error(`필수 FTP 설정이 누락되었습니다: ${missingFields.join(', ')}`);
  }
}

/**
 * 기본 설정 파일을 생성합니다.
 * @param {string} configPath - 설정 파일 경로
 */
async function createDefaultConfig(configPath = 'kelly-deploy.config.js') {
  const defaultConfig = `module.exports = {
  ftp: {
    host: process.env.FTP_HOST || 'your-ftp-server.com',
    user: process.env.FTP_USER || 'your-username',
    password: process.env.FTP_PASS || 'your-password',
    port: parseInt(process.env.FTP_PORT) || 21,
    secure: process.env.FTP_SECURE === 'true' || false,
    remotePath: process.env.FTP_REMOTE_PATH || '/'
  }
};
`;
  
  try {
    await fs.writeFile(configPath, defaultConfig, 'utf8');
    console.log(chalk.green(`✅ 설정 파일이 생성되었습니다: ${configPath}`));
  } catch (error) {
    throw new Error(`설정 파일 생성 실패: ${error.message}`);
  }
}

/**
 * .env 파일을 생성합니다.
 * @param {string} envPath - .env 파일 경로
 */
async function createDefaultEnv(envPath = '.env') {
  const defaultEnv = `# FTP 서버 설정
FTP_HOST=your-ftp-server.com
FTP_USER=your-username
FTP_PASS=your-password
FTP_PORT=21
FTP_SECURE=false
FTP_REMOTE_PATH=/
`;
  
  try {
    await fs.writeFile(envPath, defaultEnv, 'utf8');
    console.log(chalk.green(`✅ .env 파일이 생성되었습니다: ${envPath}`));
  } catch (error) {
    throw new Error(`.env 파일 생성 실패: ${error.message}`);
  }
}

/**
 * 설정을 초기화합니다.
 */
async function initConfig() {
  console.log(chalk.blue('🔧 Kelly Deploy CLI 설정 초기화'));
  
  try {
    // .env 파일 생성
    await createDefaultEnv();
    
    // 설정 파일 생성
    await createDefaultConfig();
    
    console.log(chalk.green('\n✅ 설정 초기화가 완료되었습니다!'));
    console.log(chalk.yellow('\n📝 다음 단계:'));
    console.log(chalk.white('1. .env 파일에서 FTP 서버 정보를 수정하세요'));
    console.log(chalk.white('2. kelly-deploy.config.js 파일에서 추가 설정을 조정하세요'));
    console.log(chalk.white('3. kelly-deploy list <이슈번호> 명령으로 테스트해보세요'));
    
  } catch (error) {
    console.error(chalk.red('❌ 설정 초기화 실패:'), error.message);
  }
}

module.exports = {
  loadConfig,
  validateConfig,
  createDefaultConfig,
  createDefaultEnv,
  initConfig
}; 