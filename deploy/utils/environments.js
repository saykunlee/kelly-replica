const chalk = require('chalk');
const { loadVscodeSftpConfig, convertSftpToDeployConfig } = require('./config');

/**
 * 환경별 배포 설정
 */
const environments = {
  development: {
    name: '개발 환경',
    ftp: {
      host: process.env.DEV_FTP_HOST || process.env.FTP_HOST || 'dev.example.com',
      user: process.env.DEV_FTP_USER || process.env.FTP_USER || 'dev_user',
      password: process.env.DEV_FTP_PASS || process.env.FTP_PASS || process.env.FTP_PASSWORD || '',
      port: parseInt(process.env.DEV_FTP_PORT) || parseInt(process.env.FTP_PORT) || 21,
      secure: process.env.DEV_FTP_SECURE === 'true' || process.env.FTP_SECURE === 'true',
      remotePath: process.env.DEV_FTP_REMOTE_PATH || process.env.FTP_REMOTE_PATH || '/dev/'
    },
    description: '개발 서버 배포'
  },
  staging: {
    name: '스테이징 환경',
    ftp: {
      host: process.env.STAGING_FTP_HOST || process.env.FTP_HOST || 'staging.example.com',
      user: process.env.STAGING_FTP_USER || process.env.FTP_USER || 'staging_user',
      password: process.env.STAGING_FTP_PASS || process.env.FTP_PASS || process.env.FTP_PASSWORD || '',
      port: parseInt(process.env.STAGING_FTP_PORT) || parseInt(process.env.FTP_PORT) || 21,
      secure: process.env.STAGING_FTP_SECURE === 'true' || process.env.FTP_SECURE === 'true',
      remotePath: process.env.STAGING_FTP_REMOTE_PATH || process.env.FTP_REMOTE_PATH || '/staging/'
    },
    description: '스테이징 서버 배포'
  },
  production: {
    name: '운영 환경',
    ftp: {
      host: process.env.PROD_FTP_HOST || process.env.FTP_HOST || 'prod.example.com',
      user: process.env.PROD_FTP_USER || process.env.FTP_USER || 'prod_user',
      password: process.env.PROD_FTP_PASS || process.env.FTP_PASS || process.env.FTP_PASSWORD || '',
      port: parseInt(process.env.PROD_FTP_PORT) || parseInt(process.env.FTP_PORT) || 21,
      secure: process.env.PROD_FTP_SECURE === 'true' || process.env.FTP_SECURE === 'true',
      remotePath: process.env.PROD_FTP_REMOTE_PATH || process.env.FTP_REMOTE_PATH || '/public_html/'
    },
    description: '운영 서버 배포'
  }
};

/**
 * 환경 설정 가져오기
 * @param {string} environment - 환경명
 * @returns {Object} 환경 설정
 */
async function getEnvironment(environment) {
  const sftpConfig = await loadVscodeSftpConfig();
  if (!sftpConfig) {
    const { findProjectRoot } = require('./projectRoot');
    const projectRoot = findProjectRoot();
    throw new Error(`프로젝트 루트(${projectRoot})에서 .vscode/sftp.json 파일을 찾을 수 없습니다.`);
  }
  const deployConfig = convertSftpToDeployConfig(sftpConfig);
  return { name: environment, ftp: deployConfig.ftp, description: `${environment} 환경` };
}

/**
 * 사용 가능한 환경 목록 가져오기
 * @returns {Array} 환경 목록
 */
function getAvailableEnvironments() {
  return ['production']; // sftp.json만 사용하므로 단일 환경
}

/**
 * 환경 정보 출력
 * @param {string} environment - 환경명
 */
function printEnvironmentInfo(environment) {
  console.log(chalk.blue(`\n🌍 ${environment} 환경`));
}

/**
 * 환경 설정 검증
 * @param {string} environment - 환경명
 * @returns {boolean} 검증 결과
 */
function validateEnvironment(environment) {
  // sftp.json만 있으면 항상 true
  return true;
}

/**
 * 모든 환경 정보 출력
 */
function printAllEnvironments() {
  console.log(chalk.blue('\n📋 사용 가능한 환경:'));
  console.log(chalk.white('  production: 운영 환경 (.vscode/sftp.json 기반)'));
}

module.exports = {
  getEnvironment,
  getAvailableEnvironments,
  printEnvironmentInfo,
  validateEnvironment,
  printAllEnvironments
}; 