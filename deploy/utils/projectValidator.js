const fs = require('fs');
const path = require('path');
const { showRetroError } = require('./retroUI');

/**
 * 현재 디렉토리가 프로젝트 루트인지 확인
 * @returns {boolean} 프로젝트 루트 여부
 */
function isProjectRoot() {
  const currentDir = process.cwd();
  const packageJsonPath = path.join(currentDir, 'package.json');
  
  // package.json이 없으면 false
  if (!fs.existsSync(packageJsonPath)) {
    return false;
  }
  
  // deploy 디렉토리가 있는지 확인
  const deployDirPath = path.join(currentDir, 'deploy');
  if (!fs.existsSync(deployDirPath)) {
    return false;
  }
  
  // deploy 디렉토리 안에 index.js가 있는지 확인
  const deployIndexPath = path.join(deployDirPath, 'index.js');
  if (!fs.existsSync(deployIndexPath)) {
    return false;
  }
  
  return true;
}

/**
 * 프로젝트 루트 검증 및 에러 처리
 * @throws {Error} 프로젝트 루트가 아닌 경우
 */
function validateProjectRoot() {
  if (!isProjectRoot()) {
    showRetroError('❌ 프로젝트 루트에서만 실행 가능합니다.');
    console.log('\n📁 현재 디렉토리:', process.cwd());
    console.log('💡 프로젝트 루트 디렉토리로 이동 후 다시 시도해주세요.');
    console.log('   예: cd /path/to/your/project');
    console.log('\n🔍 프로젝트 루트에는 다음이 있어야 합니다:');
    console.log('   - package.json 파일');
    console.log('   - deploy/ 디렉토리');
    console.log('   - deploy/index.js 파일');
    
    process.exit(1);
  }
}

/**
 * 프로젝트 정보 표시
 */
function showProjectInfo() {
  const currentDir = process.cwd();
  const packageJsonPath = path.join(currentDir, 'package.json');
  
  try {
    const packageJson = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
    console.log('📦 프로젝트:', packageJson.name || 'Unknown Project');
    console.log('📍 경로:', currentDir);
    console.log('');
  } catch (error) {
    console.log('📦 프로젝트: Unknown Project');
    console.log('📍 경로:', currentDir);
    console.log('');
  }
}

module.exports = {
  isProjectRoot,
  validateProjectRoot,
  showProjectInfo
}; 