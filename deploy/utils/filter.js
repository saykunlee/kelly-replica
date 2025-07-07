const fs = require('fs');
const path = require('path');
const { loadConfig } = require('./config');

/**
 * 배포에서 제외할 파일들을 필터링합니다.
 * @param {string[]} files - 필터링할 파일 목록
 * @returns {Promise<string[]>} 필터링된 파일 목록
 */
async function filterIgnoredFiles(files) {
  try {
    const config = await loadConfig();
    const ignorePatterns = config.ignorePatterns || getDefaultIgnorePatterns();
    
    return files.filter(file => {
      // 각 무시 패턴에 대해 검사
      for (const pattern of ignorePatterns) {
        if (matchesPattern(file, pattern)) {
          return false; // 제외
        }
      }
      return true; // 포함
    });
  } catch (error) {
    console.warn('설정 파일을 로드할 수 없어 기본 무시 패턴을 사용합니다.');
    const defaultPatterns = getDefaultIgnorePatterns();
    
    return files.filter(file => {
      for (const pattern of defaultPatterns) {
        if (matchesPattern(file, pattern)) {
          return false;
        }
      }
      return true;
    });
  }
}

/**
 * 파일이 패턴과 일치하는지 확인합니다.
 * @param {string} file - 파일 경로
 * @param {string} pattern - 패턴 (glob 형식)
 * @returns {boolean} 일치 여부
 */
function matchesPattern(file, pattern) {
  // 간단한 glob 패턴 매칭 구현
  const regex = globToRegex(pattern);
  return regex.test(file);
}

/**
 * glob 패턴을 정규식으로 변환합니다.
 * @param {string} glob - glob 패턴
 * @returns {RegExp} 정규식
 */
function globToRegex(glob) {
  // glob 패턴을 정규식으로 변환
  let regex = glob
    .replace(/\./g, '\\.') // 점을 이스케이프
    .replace(/\*/g, '.*') // *를 .*로 변환
    .replace(/\?/g, '.') // ?를 .로 변환
    .replace(/\//g, '\\/'); // 슬래시를 이스케이프
  
  return new RegExp(`^${regex}$`);
}

/**
 * 기본 무시 패턴을 반환합니다.
 * @returns {string[]} 기본 무시 패턴 목록
 */
function getDefaultIgnorePatterns() {
  return [
    // 개발 관련 파일들
    '**/node_modules/**',
    '**/vendor/**',
    '**/.git/**',
    '**/.gitignore',
    '**/.env*',
    '**/composer.lock',
    '**/package-lock.json',
    '**/yarn.lock',
    
    // IDE/에디터 파일들
    '**/.vscode/**',
    '**/.idea/**',
    '**/*.swp',
    '**/*.swo',
    '**/*~',
    '**/Thumbs.db',
    '**/.DS_Store',
    
    // 로그 파일들
    '**/*.log',
    '**/logs/**',
    '**/writable/logs/**',
    
    // 임시 파일들
    '**/tmp/**',
    '**/temp/**',
    '**/cache/**',
    '**/writable/cache/**',
    
    // 테스트 파일들
    '**/tests/**',
    '**/*.test.js',
    '**/*.spec.js',
    '**/phpunit.xml',
    
    // 빌드/배포 관련
    '**/builds/**',
    '**/deploy/**',
    '**/.cursor/**',
    '**/z_*/**',
    
    // 문서 파일들
    '**/README.md',
    '**/CHANGELOG.md',
    '**/LICENSE',
    '**/*.md',
    
    // 설정 파일들 (개발용)
    '**/phpunit.xml.dist',
    '**/.phpunit.result.cache',
    '**/phpcs.xml',
    '**/.eslintrc*',
    '**/.prettierrc*',
    
    // 데이터베이스 관련
    '**/database.sql',
    '**/backup/**',
    '**/db_backup/**'
  ];
}

module.exports = {
  filterIgnoredFiles
}; 