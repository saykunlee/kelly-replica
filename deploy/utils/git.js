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
 * 특정 이슈 번호와 관련된 커밋에서 변경된 파일 목록과 상태를 가져옵니다.
 * @param {string} issue - 이슈 번호
 * @returns {Promise<Array>} 변경된 파일 목록과 상태
 */
async function getChangedFilesWithStatus(issue) {
  try {
    // git log --grep="#123" --name-status --pretty=format:"" | sort | uniq
    const command = `git log --grep="#${issue}" --name-status --pretty=format:""`;
    
    const output = execSync(command, { 
      encoding: 'utf8',
      cwd: process.cwd()
    });
    
    // 빈 줄 제거하고 파일 목록과 상태 파싱
    const lines = output
      .split('\n')
      .map(line => line.trim())
      .filter(line => line.length > 0);
    
    const fileStatusMap = new Map();
    
    lines.forEach(line => {
      const parts = line.split('\t');
      if (parts.length === 2) {
        const status = parts[0];
        const file = parts[1];
        
        // 파일이 이미 있으면 가장 최근 상태를 유지
        if (!fileStatusMap.has(file)) {
          fileStatusMap.set(file, status);
        }
      }
    });
    
    // Map을 배열로 변환
    const filesWithStatus = Array.from(fileStatusMap.entries()).map(([file, status]) => ({
      file,
      status: normalizeStatus(status)
    }));
    
    return filesWithStatus;
  } catch (error) {
    if (error.status === 1) {
      // git log에서 결과가 없는 경우 (일반적인 상황)
      return [];
    }
    throw new Error(`Git 명령어 실행 중 오류: ${error.message}`);
  }
}

/**
 * Git 상태 코드를 정규화합니다.
 * @param {string} status - Git 상태 코드
 * @returns {string} 정규화된 상태
 */
function normalizeStatus(status) {
  switch (status) {
    case 'A':
      return 'created';
    case 'M':
      return 'modified';
    case 'D':
      return 'deleted';
    case 'R':
      return 'renamed';
    case 'C':
      return 'copied';
    default:
      return 'modified';
  }
}

/**
 * 파일 상태별 통계를 계산합니다.
 * @param {Array} filesWithStatus - 파일과 상태 정보
 * @returns {Object} 통계 정보
 */
function calculateFileStats(filesWithStatus) {
  const stats = {
    total: filesWithStatus.length,
    created: 0,
    modified: 0,
    deleted: 0,
    renamed: 0,
    copied: 0
  };
  
  filesWithStatus.forEach(({ status }) => {
    if (stats.hasOwnProperty(status)) {
      stats[status]++;
    }
  });
  
  return stats;
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
  getChangedFilesWithStatus,
  calculateFileStats,
  isGitRepository,
  getCurrentBranch,
  isTrackedFile
}; 