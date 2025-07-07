const { Client } = require('basic-ftp');
const fs = require('fs').promises;
const path = require('path');
const chalk = require('chalk');

/**
 * FTP 연결을 테스트합니다.
 * @param {Object} config - FTP 설정
 * @returns {Promise<boolean>} 연결 성공 여부
 */
async function testFtpConnection(config) {
  const client = new Client();
  
  try {
    await client.access({
      host: config.host,
      user: config.user,
      password: config.password,
      port: config.port,
      secure: config.secure
    });
    
    return true;
  } catch (error) {
    console.error('FTP 연결 실패:', error.message);
    return false;
  } finally {
    client.close();
  }
}

/**
 * FTP 서버에 파일을 업로드합니다.
 * @param {Object} config - FTP 설정
 * @param {Array} files - 업로드할 파일 목록
 * @param {boolean} dryRun - 실제 업로드하지 않고 시뮬레이션만 수행
 * @returns {Promise<Object>} 업로드 결과
 */
async function uploadFiles(config, files, dryRun = false) {
  const client = new Client();
  const results = {
    success: [],
    failed: [],
    total: files.length
  };
  
  try {
    if (!dryRun) {
      await client.access({
        host: config.host,
        user: config.user,
        password: config.password,
        port: config.port,
        secure: config.secure
      });
    }
    
    for (const file of files) {
      try {
        const localPath = path.resolve(file);
        const remotePath = path.join(config.remotePath, file).replace(/\\/g, '/');
        
        if (dryRun) {
          console.log(chalk.blue(`📤 [시뮬레이션] ${file} → ${remotePath}`));
          results.success.push(file);
        } else {
          console.log(chalk.blue(`📤 업로드 중: ${file}`));
          
          // 원격 디렉토리 생성
          const remoteDir = path.dirname(remotePath);
          await client.ensureDir(remoteDir);
          
          // 파일 업로드
          await client.uploadFrom(localPath, remotePath);
          
          console.log(chalk.green(`✅ 업로드 완료: ${file}`));
          results.success.push(file);
        }
      } catch (error) {
        console.log(chalk.red(`❌ 업로드 실패: ${file} - ${error.message}`));
        results.failed.push({ file, error: error.message });
      }
    }
  } catch (error) {
    console.error(chalk.red('FTP 연결 실패:', error.message));
    throw error;
  } finally {
    if (!dryRun) {
      client.close();
    }
  }
  
  return results;
}

/**
 * FTP 서버에서 파일을 다운로드합니다.
 * @param {Object} config - FTP 설정
 * @param {string} remoteFile - 원격 파일 경로
 * @param {string} localPath - 로컬 저장 경로
 * @returns {Promise<boolean>} 다운로드 성공 여부
 */
async function downloadFile(config, remoteFile, localPath) {
  const client = new Client();
  
  try {
    await client.access({
      host: config.host,
      user: config.user,
      password: config.password,
      port: config.port,
      secure: config.secure
    });
    
    await client.downloadTo(localPath, remoteFile);
    return true;
  } catch (error) {
    console.error('파일 다운로드 실패:', error.message);
    return false;
  } finally {
    client.close();
  }
}

/**
 * FTP 서버의 파일 목록을 가져옵니다.
 * @param {Object} config - FTP 설정
 * @param {string} remotePath - 원격 경로
 * @returns {Promise<Array>} 파일 목록
 */
async function listFiles(config, remotePath = '/') {
  const client = new Client();
  
  try {
    await client.access({
      host: config.host,
      user: config.user,
      password: config.password,
      port: config.port,
      secure: config.secure
    });
    
    const files = await client.list(remotePath);
    return files;
  } catch (error) {
    console.error('파일 목록 조회 실패:', error.message);
    return [];
  } finally {
    client.close();
  }
}

module.exports = {
  testFtpConnection,
  uploadFiles,
  downloadFile,
  listFiles
}; 