const { execSync } = require('child_process');
const path = require('path');

/**
 * 특정 이슈 번호와 관련된 커밋에서 변경된 파일 목록을 가져옵니다.
 * @param {string} issue - 이슈 번호
 * @returns {Promise<string[]>} 변경된 파일 목록
 */
async function getChangedFiles(issue) {
  try {
    // git log --grep="#123" --oneline --name-only --pretty=format:"" | sort | uniq
    const command = `git log --grep="#${issue}" --oneline --name-only --pretty=format:"" | sort | uniq`;
    
    const output = execSync(command, { 
      encoding: 'utf8',
      cwd: process.cwd()
    });
    
    // 빈 줄 제거하고 파일 목록 반환
    const files = output
      .split('\n')
      .map(line => line.trim())
      .filter(line => line.length > 0);
    
    return files;
  } catch (error) {
    if (error.status === 1) {
      // git log에서 결과가 없는 경우 (일반적인 상황)
      return [];
    }
    throw new Error(`Git 명령어 실행 중 오류: ${error.message}`);
  }
}

/**
 * Git 저장소가 초기화되어 있는지 확인합니다.
 * @returns {boolean}
 */
function isGitRepository() {
  try {
    execSync('git rev-parse --git-dir', { stdio: 'ignore' });
    return true;
  } catch (error) {
    return false;
  }
}

/**
 * 현재 브랜치를 가져옵니다.
 * @returns {string} 현재 브랜치명
 */
function getCurrentBranch() {
  try {
    return execSync('git branch --show-current', { 
      encoding: 'utf8',
      cwd: process.cwd()
    }).trim();
  } catch (error) {
    throw new Error(`현재 브랜치를 가져올 수 없습니다: ${error.message}`);
  }
}

/**
 * 파일이 Git에 추적되고 있는지 확인합니다.
 * @param {string} filePath - 파일 경로
 * @returns {boolean}
 */
function isTrackedFile(filePath) {
  try {
    execSync(`git ls-files --error-unmatch "${filePath}"`, { 
      stdio: 'ignore',
      cwd: process.cwd()
    });
    return true;
  } catch (error) {
    return false;
  }
}

module.exports = {
  getChangedFiles,
  isGitRepository,
  getCurrentBranch,
  isTrackedFile
}; 