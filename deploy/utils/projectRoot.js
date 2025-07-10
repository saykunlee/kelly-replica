const path = require('path');
const fs = require('fs');

/**
 * 프로젝트 루트 디렉토리를 찾습니다.
 * @returns {string} 프로젝트 루트 경로
 */
function findProjectRoot() {
  let currentDir = process.cwd();
  
  // 최대 10단계 상위 디렉토리까지 검색
  for (let i = 0; i < 10; i++) {
    const sftpPath = path.join(currentDir, '.vscode', 'sftp.json');
    try {
      fs.accessSync(sftpPath);
      return currentDir;
    } catch (error) {
      // 파일이 없으면 상위 디렉토리로 이동
      const parentDir = path.dirname(currentDir);
      if (parentDir === currentDir) {
        // 루트 디렉토리에 도달했으면 중단
        break;
      }
      currentDir = parentDir;
    }
  }
  
  // 프로젝트 루트를 찾지 못한 경우 현재 디렉토리 반환
  return process.cwd();
}

module.exports = { findProjectRoot }; 