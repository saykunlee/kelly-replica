const fs = require('fs');
const path = require('path');
const { loadConfig } = require('./config');

/**
 * deploy/.deployignore 파일을 읽어서 무시 패턴을 로드합니다.
 * @returns {Promise<string[]>} 무시 패턴 목록
 */
async function loadDeployIgnore() {
  try {
    const { findProjectRoot } = require('./projectRoot');
    const projectRoot = findProjectRoot();
    const ignorePath = path.join(projectRoot, 'deploy', '.deployignore');
    const ignoreContent = await fs.promises.readFile(ignorePath, 'utf8');
    
    // 주석 제거 및 빈 줄 제거
    const patterns = ignoreContent
      .split('\n')
      .map(line => line.trim())
      .filter(line => line && !line.startsWith('#'))
      .map(pattern => {
        // 이미 **/로 시작하는 패턴은 그대로 사용
        if (pattern.startsWith('**/')) {
          return pattern;
        }
        // 상대 경로를 glob 패턴으로 변환
        if (pattern.startsWith('/')) {
          return pattern.substring(1);
        }
        return `**/${pattern}`;
      });
    
    return patterns;
  } catch (error) {
    console.warn('⚠ deploy/.deployignore 파일을 읽을 수 없어 기본 패턴을 사용합니다.');
    return getDefaultIgnorePatterns();
  }
}

/**
 * 배포에서 제외할 파일들을 필터링합니다.
 * @param {string[]} files - 필터링할 파일 목록
 * @returns {Promise<string[]>} 필터링된 파일 목록
 */
async function filterIgnoredFiles(files) {
  try {
    const ignorePatterns = await loadDeployIgnore();
    
    return files.filter(file => {
      // 각 무시 패턴에 대해 검사
      for (const pattern of ignorePatterns) {
        if (matchesPattern(file, pattern)) {
          console.log(`🚫 제외: ${file} (패턴: ${pattern})`);
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
          console.log(`🚫 제외: ${file} (패턴: ${pattern})`);
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
  // 디렉토리 패턴 보강: deploy/ → deploy/**
  if (glob.endsWith('/')) {
    glob = glob + '**';
  }
  // 디렉토리 패턴을 정확히 하위 전체에 매칭되도록 보강
  // 예: **/deploy/** → (?:^|/)deploy(?:/|$)
  if (glob.startsWith('**/')) {
    glob = glob.replace('**/', '');
    return new RegExp(`(?:^|\/)${glob.replace(/\*\*/g, '.*').replace(/\*/g, '[^/]*').replace(/\?/g, '.').replace(/\//g, '\/')}(?:\/|$)`);
  }
  let regex = glob
    .replace(/\./g, '\\.') // 점 이스케이프
    .replace(/\*\*/g, '.*') // ** → .*
    .replace(/\*/g, '[^/]*') // * → [^/]* (경로 구분자 제외)
    .replace(/\?/g, '.') // ? → .
    .replace(/\//g, '\/'); // / → \/
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
  filterIgnoredFiles,
  loadDeployIgnore,
  matchesPattern,
  globToRegex
}; 