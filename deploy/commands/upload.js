const chalk = require('chalk');
const { getChangedFilesWithStatus } = require('../utils/git');
const { filterIgnoredFiles } = require('../utils/filter');
const { uploadFiles } = require('../utils/ftp');
const { loadConfig } = require('../utils/config');
const { getEnvironment, validateEnvironment, printEnvironmentInfo } = require('../utils/environments');
const { createSpinner, succeedSpinner, failSpinner, showUploadProgress } = require('../utils/progress');
const { handleError, showWarning, showSuccess } = require('../utils/errorHandler');

async function uploadCommand(issue, options = {}) {
  const environment = options.environment || 'production';
  const spinner = createSpinner('업로드 준비 중...');
  
  try {
    spinner.start();
    
    // 환경 설정 검증
    if (!validateEnvironment(environment)) {
      failSpinner(spinner, '환경 설정이 유효하지 않습니다');
      return;
    }
    
    // 환경 정보 출력
    printEnvironmentInfo(environment);
    
    // 설정 로드
    const config = await loadConfig();
    const envConfig = await getEnvironment(environment);
    
    // 환경별 FTP 설정 병합
    const ftpConfig = {
      ...config.ftp,
      ...envConfig.ftp
    };
    
    succeedSpinner(spinner, '설정 로드 완료');
    
    // 변경된 파일 가져오기
    const filesSpinner = createSpinner('변경된 파일을 가져오는 중...');
    filesSpinner.start();
    
    const filesWithStatus = await getChangedFilesWithStatus(issue);
    
    if (filesWithStatus.length === 0) {
      succeedSpinner(filesSpinner, '변경된 파일을 가져왔습니다');
      showWarning(`이슈 #${issue}와 관련된 변경된 파일이 없습니다.`);
      return;
    }
    
    // 파일 경로만 추출
    const changedFiles = filesWithStatus.map(item => item.file);
    succeedSpinner(filesSpinner, `변경된 파일 ${changedFiles.length}개 발견`);
    
    // 파일 필터링
    const filterSpinner = createSpinner('파일 필터링 중...');
    filterSpinner.start();
    
    const filteredFiles = await filterIgnoredFiles(changedFiles);
    succeedSpinner(filterSpinner, `필터링 완료: ${filteredFiles.length}개 파일`);
    
    if (filteredFiles.length === 0) {
      showWarning('업로드할 파일이 없습니다.');
      return;
    }
    
    // 드라이 런 모드
    if (options.dryRun) {
      console.log(chalk.blue('\n🔍 드라이 런 모드 - 실제 업로드하지 않습니다'));
      console.log(chalk.gray('─'.repeat(50)));
      console.log(chalk.white('업로드될 파일 목록:'));
      
      filteredFiles.forEach((file, index) => {
        console.log(chalk.white(`${String(index + 1).padStart(3)}. ${file}`));
      });
      
      console.log(chalk.gray('─'.repeat(50)));
      console.log(chalk.green(`총 ${filteredFiles.length}개 파일이 업로드될 예정입니다.`));
      console.log(chalk.blue(`대상 환경: ${envConfig.name} (${environment})`));
      console.log(chalk.gray(`원격 경로: ${ftpConfig.remotePath}`));
      return;
    }
    
    // 실제 업로드
    const uploadSpinner = createSpinner('FTP 서버에 연결 중...');
    uploadSpinner.start();
    
    try {
      const results = await uploadFiles(ftpConfig, filteredFiles, false);
      
      succeedSpinner(uploadSpinner, '업로드 완료');
      
      // 결과 표시
      console.log(chalk.green(`\n✅ 이슈 #${issue}의 파일 업로드가 완료되었습니다!`));
      console.log(chalk.gray('─'.repeat(50)));
      console.log(chalk.white(`대상 환경: ${envConfig.name} (${environment})`));
      console.log(chalk.white(`업로드된 파일: ${results.success.length}개`));
      console.log(chalk.white(`실패한 파일: ${results.failed.length}개`));
      console.log(chalk.white(`총 파일 수: ${results.total}개`));
      
      if (results.failed.length > 0) {
        console.log(chalk.red('\n❌ 실패한 파일:'));
        results.failed.forEach((item, index) => {
          console.log(chalk.red(`  ${index + 1}. ${item.file} - ${item.error}`));
        });
      }
      
      showSuccess(`이슈 #${issue}의 배포가 완료되었습니다.`);
      
    } catch (uploadError) {
      failSpinner(uploadSpinner, '업로드 실패');
      throw uploadError;
    }
    
  } catch (error) {
    failSpinner(spinner, '업로드 준비 실패');
    handleError(error, '파일 업로드');
  }
}

module.exports = { uploadCommand }; 