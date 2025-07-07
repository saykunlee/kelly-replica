const ftp = require('basic-ftp');
const path = require('path');
const fs = require('fs').promises;
const chalk = require('chalk');

/**
 * FTP 서버에 파일들을 업로드합니다.
 * @param {string[]} files - 업로드할 파일 목록
 * @param {Object} config - FTP 설정
 * @param {Object} spinner - ora 스피너 객체
 */
async function uploadToFtp(files, config, spinner) {
  const client = new ftp.Client();
  client.ftp.verbose = false; // 상세 로그 비활성화
  
  try {
    // FTP 서버에 연결
    spinner.text = 'FTP 서버에 연결 중...';
    await client.access({
      host: config.ftp.host,
      user: config.ftp.user,
      password: config.ftp.password,
      port: config.ftp.port || 21,
      secure: config.ftp.secure || false
    });
    
    spinner.succeed('FTP 서버 연결 성공');
    
    // 원격 디렉토리로 이동
    if (config.ftp.remotePath) {
      spinner.start('원격 디렉토리로 이동 중...');
      await client.ensureDir(config.ftp.remotePath);
      spinner.succeed(`원격 디렉토리 이동: ${config.ftp.remotePath}`);
    }
    
    // 파일 업로드
    let uploadedCount = 0;
    const totalFiles = files.length;
    
    for (const file of files) {
      try {
        spinner.start(`업로드 중: ${file} (${uploadedCount + 1}/${totalFiles})`);
        
        // 로컬 파일 존재 확인
        const localPath = path.join(process.cwd(), file);
        await fs.access(localPath);
        
        // 원격 경로 생성
        const remotePath = path.dirname(file);
        if (remotePath !== '.') {
          await client.ensureDir(remotePath);
        }
        
        // 파일 업로드
        await client.uploadFrom(localPath, file);
        uploadedCount++;
        
        spinner.succeed(`업로드 완료: ${file}`);
        
      } catch (error) {
        spinner.fail(`업로드 실패: ${file} - ${error.message}`);
        console.error(chalk.red(`  오류: ${error.message}`));
      }
    }
    
    console.log(chalk.green(`\n📤 업로드 완료: ${uploadedCount}/${totalFiles} 파일`));
    
  } catch (error) {
    throw new Error(`FTP 업로드 중 오류: ${error.message}`);
  } finally {
    client.close();
  }
}

/**
 * FTP 연결을 테스트합니다.
 * @param {Object} config - FTP 설정
 * @returns {Promise<boolean>} 연결 성공 여부
 */
async function testFtpConnection(config) {
  const client = new ftp.Client();
  client.ftp.verbose = false;
  
  try {
    await client.access({
      host: config.ftp.host,
      user: config.ftp.user,
      password: config.ftp.password,
      port: config.ftp.port || 21,
      secure: config.ftp.secure || false
    });
    
    return true;
  } catch (error) {
    throw new Error(`FTP 연결 테스트 실패: ${error.message}`);
  } finally {
    client.close();
  }
}

/**
 * FTP 서버의 디렉토리 목록을 가져옵니다.
 * @param {Object} config - FTP 설정
 * @param {string} remotePath - 원격 경로
 * @returns {Promise<Array>} 디렉토리 목록
 */
async function listFtpDirectory(config, remotePath = '/') {
  const client = new ftp.Client();
  client.ftp.verbose = false;
  
  try {
    await client.access({
      host: config.ftp.host,
      user: config.ftp.user,
      password: config.ftp.password,
      port: config.ftp.port || 21,
      secure: config.ftp.secure || false
    });
    
    const list = await client.list(remotePath);
    return list;
  } catch (error) {
    throw new Error(`FTP 디렉토리 목록 가져오기 실패: ${error.message}`);
  } finally {
    client.close();
  }
}

module.exports = {
  uploadToFtp,
  testFtpConnection,
  listFtpDirectory
}; 