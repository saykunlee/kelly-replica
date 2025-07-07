const fs = require('fs').promises;
const path = require('path');
const { glob } = require('glob');

/**
 * .gitignore와 .deployignore 파일을 읽어서 파싱합니다.
 * @returns {Promise<Object>} 파싱된 ignore 규칙들
 */
async function loadIgnoreFiles() {
  const ignoreRules = {
    gitignore: [],
    deployignore: []
  };
  
  try {
    // .gitignore 파일 읽기
    const gitignorePath = path.join(process.cwd(), '.gitignore');
    const gitignoreContent = await fs.readFile(gitignorePath, 'utf8');
    ignoreRules.gitignore = parseIgnoreFile(gitignoreContent);
  } catch (error) {
    // .gitignore 파일이 없는 경우 빈 배열
    ignoreRules.gitignore = [];
  }
  
  try {
    // .deployignore 파일 읽기
    const deployignorePath = path.join(process.cwd(), '.deployignore');
    const deployignoreContent = await fs.readFile(deployignorePath, 'utf8');
    ignoreRules.deployignore = parseIgnoreFile(deployignoreContent);
  } catch (error) {
    // .deployignore 파일이 없는 경우 빈 배열
    ignoreRules.deployignore = [];
  }
  
  return ignoreRules;
}

/**
 * ignore 파일 내용을 파싱합니다.
 * @param {string} content - ignore 파일 내용
 * @returns {string[]} 패턴 배열
 */
function parseIgnoreFile(content) {
  return content
    .split('\n')
    .map(line => line.trim())
    .filter(line => line.length > 0 && !line.startsWith('#'))
    .map(line => {
      // glob 패턴으로 변환
      if (line.startsWith('/')) {
        return line.substring(1);
      }
      if (line.endsWith('/')) {
        return line + '**';
      }
      return line;
    });
}

/**
 * 파일이 ignore 규칙에 의해 제외되어야 하는지 확인합니다.
 * @param {string} filePath - 파일 경로
 * @param {Object} ignoreRules - ignore 규칙들
 * @returns {boolean} 제외되어야 하면 true
 */
function shouldIgnoreFile(filePath, ignoreRules) {
  // .gitignore 규칙 확인
  for (const pattern of ignoreRules.gitignore) {
    if (matchesPattern(filePath, pattern)) {
      return true;
    }
  }
  
  // .deployignore 규칙 확인
  for (const pattern of ignoreRules.deployignore) {
    if (matchesPattern(filePath, pattern)) {
      return true;
    }
  }
  
  return false;
}

/**
 * 파일 경로가 패턴과 일치하는지 확인합니다.
 * @param {string} filePath - 파일 경로
 * @param {string} pattern - glob 패턴
 * @returns {boolean} 일치하면 true
 */
function matchesPattern(filePath, pattern) {
  // 간단한 패턴 매칭 구현
  if (pattern.includes('*')) {
    const regex = new RegExp(pattern.replace(/\*/g, '.*'));
    return regex.test(filePath);
  }
  
  // 정확한 경로 매칭
  return filePath === pattern || filePath.startsWith(pattern + '/');
}

/**
 * 파일 목록을 ignore 규칙에 따라 필터링합니다.
 * @param {string[]} files - 파일 목록
 * @returns {Promise<string[]>} 필터링된 파일 목록
 */
async function filterIgnoredFiles(files) {
  const ignoreRules = await loadIgnoreFiles();
  
  return files.filter(file => !shouldIgnoreFile(file, ignoreRules));
}

/**
 * 특정 패턴에 맞는 파일들을 찾습니다.
 * @param {string|string[]} patterns - glob 패턴들
 * @param {Object} options - glob 옵션
 * @returns {Promise<string[]>} 매칭되는 파일 목록
 */
async function findFilesByPattern(patterns, options = {}) {
  const defaultOptions = {
    cwd: process.cwd(),
    ignore: ['node_modules/**', '.git/**'],
    ...options
  };
  
  const files = await glob(patterns, defaultOptions);
  return files;
}

/**
 * 파일이 존재하는지 확인합니다.
 * @param {string} filePath - 파일 경로
 * @returns {Promise<boolean>}
 */
async function fileExists(filePath) {
  try {
    await fs.access(filePath);
    return true;
  } catch (error) {
    return false;
  }
}

/**
 * 디렉토리가 존재하는지 확인합니다.
 * @param {string} dirPath - 디렉토리 경로
 * @returns {Promise<boolean>}
 */
async function directoryExists(dirPath) {
  try {
    const stat = await fs.stat(dirPath);
    return stat.isDirectory();
  } catch (error) {
    return false;
  }
}

module.exports = {
  loadIgnoreFiles,
  shouldIgnoreFile,
  filterIgnoredFiles,
  findFilesByPattern,
  fileExists,
  directoryExists
}; 