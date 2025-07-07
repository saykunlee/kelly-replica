const fs = require('fs').promises;
const path = require('path');
const { glob } = require('glob');
const gitignoreParser = require('gitignore-parser');

/**
 * .gitignore와 .deployignore 파일을 읽어서 파싱합니다.
 * @returns {Promise<Object>} 파싱된 ignore 규칙들
 */
async function loadIgnoreFiles() {
  const ignoreRules = {};
  
  try {
    // .gitignore 파일 읽기
    const gitignorePath = path.join(process.cwd(), '.gitignore');
    const gitignoreContent = await fs.readFile(gitignorePath, 'utf8');
    ignoreRules.gitignore = gitignoreParser.compile(gitignoreContent);
  } catch (error) {
    // .gitignore 파일이 없는 경우 무시
    ignoreRules.gitignore = () => false;
  }
  
  try {
    // .deployignore 파일 읽기
    const deployignorePath = path.join(process.cwd(), '.deployignore');
    const deployignoreContent = await fs.readFile(deployignorePath, 'utf8');
    ignoreRules.deployignore = gitignoreParser.compile(deployignoreContent);
  } catch (error) {
    // .deployignore 파일이 없는 경우 무시
    ignoreRules.deployignore = () => false;
  }
  
  return ignoreRules;
}

/**
 * 파일이 ignore 규칙에 의해 제외되어야 하는지 확인합니다.
 * @param {string} filePath - 파일 경로
 * @param {Object} ignoreRules - ignore 규칙들
 * @returns {boolean} 제외되어야 하면 true
 */
function shouldIgnoreFile(filePath, ignoreRules) {
  // .gitignore 규칙 확인
  if (ignoreRules.gitignore(filePath)) {
    return true;
  }
  
  // .deployignore 규칙 확인
  if (ignoreRules.deployignore(filePath)) {
    return true;
  }
  
  return false;
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