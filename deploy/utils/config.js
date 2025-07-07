const fs = require('fs').promises;
const path = require('path');
const dotenv = require('dotenv');
const chalk = require('chalk');
const { testFtpConnection } = require('./ftp');

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
    const configPath = path.join(process.cwd(), 'deploy', 'kelly-deploy.config.js');
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
 * .vscode/sftp.json 파일에서 SFTP 설정을 읽어옵니다.
 * @returns {Promise<Object|null>} SFTP 설정 객체 또는 null
 */
async function loadVscodeSftpConfig() {
  try {
    const sftpPath = path.join(process.cwd(), '.vscode', 'sftp.json');
    await fs.access(sftpPath);
    
    const sftpContent = await fs.readFile(sftpPath, 'utf8');
    const sftpConfig = JSON.parse(sftpContent);
    
    console.log(chalk.blue('📁 .vscode/sftp.json 파일을 발견했습니다.'));
    console.log(chalk.gray('   SFTP 설정을 자동으로 가져옵니다...'));
    
    return sftpConfig;
  } catch (error) {
    console.log(chalk.yellow('⚠ .vscode/sftp.json 파일이 없습니다.'));
    return null;
  }
}

/**
 * SFTP 설정을 배포 설정 형식으로 변환합니다.
 * @param {Object} sftpConfig - SFTP 설정 객체
 * @returns {Object} 배포 설정 객체
 */
function convertSftpToDeployConfig(sftpConfig) {
  // SFTP 설정의 첫 번째 서버 정보를 사용
  const server = Array.isArray(sftpConfig) ? sftpConfig[0] : sftpConfig;
  
  return {
    ftp: {
      host: server.host || 'your-ftp-server.com',
      user: server.username || 'your-username',
      password: process.env.FTP_PASSWORD || server.password || 'your-password',
      port: server.port || 21,
      secure: server.protocol === 'sftp' || false,
      remotePath: server.remotePath || server.path || '/'
    }
  };
}

/**
 * FTP 연결 테스트를 수행하고 결과를 표시합니다.
 * @param {Object} config - FTP 설정
 */
async function testConnectionAndShowResult(config) {
  console.log(chalk.blue('\n🔍 FTP 연결 테스트를 진행합니다...'));
  
  try {
    const isConnected = await testFtpConnection(config);
    
    if (isConnected) {
      console.log(chalk.green('✅ FTP 연결 테스트 성공!'));
      console.log(chalk.gray(`   서버: ${config.ftp.host}:${config.ftp.port}`));
      console.log(chalk.gray(`   사용자: ${config.ftp.user}`));
      console.log(chalk.gray(`   프로토콜: ${config.ftp.secure ? 'SFTP' : 'FTP'}`));
      console.log(chalk.gray(`   원격 경로: ${config.ftp.remotePath}`));
    } else {
      console.log(chalk.red('❌ FTP 연결 테스트 실패'));
    }
  } catch (error) {
    console.log(chalk.red('❌ FTP 연결 테스트 실패:'), error.message);
    console.log(chalk.yellow('\n💡 문제 해결 방법:'));
    console.log(chalk.white('1. FTP 서버 주소와 포트를 확인하세요'));
    console.log(chalk.white('2. 사용자명과 비밀번호를 확인하세요'));
    console.log(chalk.white('3. 방화벽 설정을 확인하세요'));
    console.log(chalk.white('4. SFTP를 사용하는 경우 secure: true로 설정하세요'));
  }
}

/**
 * 기본 설정 파일을 생성합니다.
 * @param {string} configPath - 설정 파일 경로
 * @param {Object} configData - 설정 데이터
 */
async function createDefaultConfig(configPath = path.join(__dirname, '..', 'kelly-deploy.config.js'), configData = null) {
  let defaultConfig;
  
  if (configData) {
    // SFTP 설정을 기반으로 한 설정
    defaultConfig = `module.exports = {
  ftp: {
    host: '${configData.ftp.host}',
    user: '${configData.ftp.user}',
    password: process.env.FTP_PASSWORD || '${configData.ftp.password}',
    port: ${configData.ftp.port},
    secure: ${configData.ftp.secure},
    remotePath: '${configData.ftp.remotePath}'
  }
};
`;
  } else {
    // 기본 설정
    defaultConfig = `module.exports = {
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
  }
  
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
 * @param {Object} sftpConfig - SFTP 설정 (선택사항)
 */
async function createDefaultEnv(envPath = path.join(__dirname, '..', '.env'), sftpConfig = null) {
  let defaultEnv;
  
  if (sftpConfig) {
    // SFTP 설정을 기반으로 한 .env
    const server = Array.isArray(sftpConfig) ? sftpConfig[0] : sftpConfig;
    defaultEnv = `# FTP 서버 설정 (VSCode SFTP에서 자동 가져옴)
FTP_HOST=${server.host || 'your-ftp-server.com'}
FTP_USER=${server.username || 'your-username'}
FTP_PASS=${server.password || 'your-password'}
FTP_PORT=${server.port || 21}
FTP_SECURE=${server.protocol === 'sftp' ? 'true' : 'false'}
FTP_REMOTE_PATH=${server.remotePath || server.path || '/'}
`;
  } else {
    // 기본 .env
    defaultEnv = `# FTP 서버 설정
FTP_HOST=your-ftp-server.com
FTP_USER=your-username
FTP_PASS=your-password
FTP_PORT=21
FTP_SECURE=false
FTP_REMOTE_PATH=/
`;
  }
  
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
    // .vscode/sftp.json 파일에서 설정 가져오기
    const sftpConfig = await loadVscodeSftpConfig();
    
    if (sftpConfig) {
      // SFTP 설정을 배포 설정으로 변환
      const deployConfig = convertSftpToDeployConfig(sftpConfig);
      
      // .env 파일 생성 (SFTP 설정 기반)
      await createDefaultEnv(path.join(__dirname, '..', '.env'), sftpConfig);
      
      // 설정 파일 생성 (SFTP 설정 기반)
      await createDefaultConfig(path.join(__dirname, '..', 'kelly-deploy.config.js'), deployConfig);
      
      console.log(chalk.green('\n✅ VSCode SFTP 설정을 기반으로 초기화가 완료되었습니다!'));
      console.log(chalk.yellow('\n📝 다음 단계:'));
      console.log(chalk.white('1. deploy/.env 파일에서 FTP_PASSWORD를 설정하세요 (보안상 권장)'));
      console.log(chalk.white('2. deploy/kelly-deploy.config.js 파일에서 추가 설정을 조정하세요'));
      console.log(chalk.white('3. kelly-deploy list <이슈번호> 명령으로 테스트해보세요'));
      
      // 연결 테스트 수행
      await testConnectionAndShowResult(deployConfig);
      
    } else {
      // SFTP 설정이 없는 경우 기본 설정 생성
      await createDefaultEnv(path.join(__dirname, '..', '.env'));
      await createDefaultConfig(path.join(__dirname, '..', 'kelly-deploy.config.js'));
      
      console.log(chalk.green('\n✅ 기본 설정으로 초기화가 완료되었습니다!'));
      console.log(chalk.yellow('\n📝 다음 단계:'));
      console.log(chalk.white('1. deploy/.env 파일에서 FTP 서버 정보를 수정하세요'));
      console.log(chalk.white('2. deploy/kelly-deploy.config.js 파일에서 추가 설정을 조정하세요'));
      console.log(chalk.white('3. kelly-deploy list <이슈번호> 명령으로 테스트해보세요'));
      
      // 기본 설정으로 연결 테스트 시도 (실패할 가능성이 높음)
      console.log(chalk.yellow('\n⚠ 기본 설정으로는 연결 테스트를 건너뜁니다.'));
      console.log(chalk.white('   실제 FTP 서버 정보를 설정한 후 다시 시도하세요.'));
    }
    
  } catch (error) {
    console.error(chalk.red('❌ 설정 초기화 실패:'), error.message);
  }
}

module.exports = {
  loadConfig,
  validateConfig,
  createDefaultConfig,
  createDefaultEnv,
  initConfig,
  loadVscodeSftpConfig,
  convertSftpToDeployConfig,
  testConnectionAndShowResult
}; 