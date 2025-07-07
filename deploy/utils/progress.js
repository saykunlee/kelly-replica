const ora = require('ora');
const chalk = require('chalk');

/**
 * 스피너 생성
 * @param {string} text - 스피너 텍스트
 * @returns {Object} ora 스피너 객체
 */
function createSpinner(text) {
  return ora({
    text,
    color: 'blue',
    spinner: 'dots'
  });
}

/**
 * 파일 업로드 진행률 표시
 * @param {number} current - 현재 파일 번호
 * @param {number} total - 전체 파일 수
 * @param {string} filename - 현재 파일명
 */
function showUploadProgress(current, total, filename) {
  const percentage = Math.round((current / total) * 100);
  const progressBar = createProgressBar(percentage);
  
  console.log(chalk.blue(`📤 업로드 중: ${filename}`));
  console.log(chalk.gray(`   진행률: ${progressBar} ${percentage}% (${current}/${total})`));
}

/**
 * 진행률 바 생성
 * @param {number} percentage - 진행률 (0-100)
 * @returns {string} 진행률 바 문자열
 */
function createProgressBar(percentage) {
  const barLength = 20;
  const filledLength = Math.round((percentage / 100) * barLength);
  const emptyLength = barLength - filledLength;
  
  const filled = '█'.repeat(filledLength);
  const empty = '░'.repeat(emptyLength);
  
  return `${filled}${empty}`;
}

/**
 * 배치 작업 진행률 표시
 * @param {string} operation - 작업명
 * @param {number} current - 현재 항목 번호
 * @param {number} total - 전체 항목 수
 * @param {string} currentItem - 현재 항목명
 */
function showBatchProgress(operation, current, total, currentItem) {
  const percentage = Math.round((current / total) * 100);
  const progressBar = createProgressBar(percentage);
  
  console.log(chalk.blue(`${operation}: ${currentItem}`));
  console.log(chalk.gray(`   진행률: ${progressBar} ${percentage}% (${current}/${total})`));
}

/**
 * 파일 처리 진행률 표시
 * @param {string} operation - 작업명 (읽기, 필터링, 업로드 등)
 * @param {number} current - 현재 파일 번호
 * @param {number} total - 전체 파일 수
 */
function showFileProgress(operation, current, total) {
  const percentage = Math.round((current / total) * 100);
  const progressBar = createProgressBar(percentage);
  
  console.log(chalk.blue(`${operation} 중...`));
  console.log(chalk.gray(`   진행률: ${progressBar} ${percentage}% (${current}/${total})`));
}

/**
 * 단계별 진행률 표시
 * @param {string} step - 현재 단계
 * @param {number} currentStep - 현재 단계 번호
 * @param {number} totalSteps - 전체 단계 수
 */
function showStepProgress(step, currentStep, totalSteps) {
  console.log(chalk.blue(`\n📋 단계 ${currentStep}/${totalSteps}: ${step}`));
}

/**
 * 성공 메시지와 함께 스피너 완료
 * @param {Object} spinner - ora 스피너 객체
 * @param {string} message - 성공 메시지
 */
function succeedSpinner(spinner, message) {
  spinner.succeed(chalk.green(message));
}

/**
 * 실패 메시지와 함께 스피너 완료
 * @param {Object} spinner - ora 스피너 객체
 * @param {string} message - 실패 메시지
 */
function failSpinner(spinner, message) {
  spinner.fail(chalk.red(message));
}

/**
 * 정보 메시지와 함께 스피너 완료
 * @param {Object} spinner - ora 스피너 객체
 * @param {string} message - 정보 메시지
 */
function infoSpinner(spinner, message) {
  spinner.info(chalk.blue(message));
}

/**
 * 경고 메시지와 함께 스피너 완료
 * @param {Object} spinner - ora 스피너 객체
 * @param {string} message - 경고 메시지
 */
function warnSpinner(spinner, message) {
  spinner.warn(chalk.yellow(message));
}

module.exports = {
  createSpinner,
  showUploadProgress,
  showBatchProgress,
  showFileProgress,
  showStepProgress,
  succeedSpinner,
  failSpinner,
  infoSpinner,
  warnSpinner
}; 