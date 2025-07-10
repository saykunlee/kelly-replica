const chalk = require('chalk');
const { getChangedFilesWithStatus } = require('../utils/git');
const { filterIgnoredFiles } = require('../utils/filter');
const { uploadFiles } = require('../utils/ftp');
const { loadConfig } = require('../utils/config');
const { getEnvironment, validateEnvironment, printEnvironmentInfo } = require('../utils/environments');
const { createSpinner, succeedSpinner, failSpinner, showUploadProgress } = require('../utils/progress');
const { handleError, showWarning, showSuccess } = require('../utils/errorHandler');
const { createRetroHeader, createRetroSection, createRetroBox, showRetroSuccess, showRetroError, showRetroWarning, showRetroInfo, showRetroFileList, showRetroProgress, showRetroResults, showRetroConfig, showRetroDivider, createRetroSpinner } = require('../utils/retroUI');

async function uploadCommand(issue, options = {}) {
  const environment = options.environment || 'production';
  const spinner = createSpinner('업로드 준비 중...');
  
  try {
    spinner.start();
    
    // 레트로 헤더
    createRetroHeader('파일 업로드', `이슈 #${issue} → ${environment} 환경`);
    showRetroDivider();
    
    // 환경 설정 검증
    if (!validateEnvironment(environment)) {
      failSpinner(spinner, '환경 설정이 유효하지 않습니다');
      return;
    }
    
    // 환경 정보 출력
    createRetroSection('환경 정보');
    console.log(chalk.cyan(`  환경: ${environment}`));
    
    // 설정 로드
    const config = await loadConfig();
    const envConfig = await getEnvironment(environment);
    
    // 환경별 FTP 설정 병합
    const ftpConfig = {
      ...config.ftp,
      ...envConfig.ftp
    };
    
    succeedSpinner(spinner, '설정 로드 완료');
    
    // 설정 정보 표시
    showRetroConfig(ftpConfig);
    
    // 변경된 파일 가져오기
    const filesSpinner = createRetroSpinner('변경된 파일을 가져오는 중...');
    
    const filesWithStatus = await getChangedFilesWithStatus(issue);
    
    if (filesWithStatus.length === 0) {
      filesSpinner.stop(false);
      showRetroWarning(`이슈 #${issue}와 관련된 변경된 파일이 없습니다.`);
      return;
    }
    
    // 삭제된 파일 제외
    const uploadCandidates = filesWithStatus.filter(item => item.status !== 'deleted');
    const changedFiles = uploadCandidates.map(item => item.file);
    
    filesSpinner.stop(true);
    
    // 파일 필터링
    const filterSpinner = createRetroSpinner('파일 필터링 중...');
    
    const filteredFiles = await filterIgnoredFiles(changedFiles);
    
    filterSpinner.stop(true);
    
    if (filteredFiles.length === 0) {
      showRetroWarning('업로드할 파일이 없습니다.');
      return;
    }
    
    // 필터링 결과 표시
    createRetroSection('업로드 대상 파일');
    const uploadFileList = filteredFiles.map(file => ({
      name: file,
      type: 'f',
      size: 0
    }));
    
    showRetroFileList(uploadFileList, `${filteredFiles.length}개 파일`);
    
    // 드라이 런 모드
    if (options.dryRun) {
      createRetroSection('드라이 런 모드');
      const dryRunInfo = [
        '실제 업로드하지 않고 시뮬레이션만 수행합니다.',
        '',
        '업로드될 파일 목록:',
        ...filteredFiles.map((file, index) => `${index + 1}. ${file}`)
      ].join('\n');
      createRetroBox(dryRunInfo, 'yellow');
      
      showRetroDivider();
      showRetroSuccess(`총 ${filteredFiles.length}개 파일이 업로드될 예정입니다.`);
      showRetroDivider();
      return;
    }
    
    // 실제 업로드
    const uploadSpinner = createRetroSpinner('FTP 서버에 연결 중...');
    
    try {
      const results = await uploadFiles(ftpConfig, filteredFiles, false);
      
      uploadSpinner.stop(true);
      
      // 결과 표시
      showRetroResults(results);
      
      // 성공 메시지
      createRetroSection('업로드 완료');
      const successInfo = [
        `✅ 이슈 #${issue}의 파일 업로드가 완료되었습니다!`,
        `대상 환경: ${envConfig.name} (${environment})`,
        `업로드된 파일: ${results.success.length}개`,
        `실패한 파일: ${results.failed.length}개`,
        `총 파일 수: ${results.total}개`
      ].join('\n');
      createRetroBox(successInfo, results.failed.length > 0 ? 'red' : 'green');
      
      showRetroDivider();
      showRetroSuccess(`이슈 #${issue}의 배포가 완료되었습니다.`);
      showRetroDivider();
      
    } catch (uploadError) {
      uploadSpinner.stop(false);
      throw uploadError;
    }
    
  } catch (error) {
    failSpinner(spinner, '업로드 준비 실패');
    handleError(error, '파일 업로드');
  }
}

module.exports = { uploadCommand }; 