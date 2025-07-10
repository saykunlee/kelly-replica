const chalk = require('chalk');
const { loadConfig } = require('../utils/config');
const { testFtpConnection, listFiles } = require('../utils/ftp');
const { getEnvironment, validateEnvironment, printEnvironmentInfo } = require('../utils/environments');
const { createSpinner, succeedSpinner, failSpinner } = require('../utils/progress');
const { handleError, showWarning, showSuccess } = require('../utils/errorHandler');
const { createRetroHeader, createRetroSection, createRetroBox, showRetroSuccess, showRetroError, showRetroWarning, showRetroInfo, showRetroFileList, showRetroConfig, showRetroDivider, createRetroSpinner } = require('../utils/retroUI');

async function testCommand(options = {}) {
  const environment = options.environment || 'production';
  const spinner = createSpinner('FTP 연결 테스트 준비 중...');
  
  try {
    spinner.start();
    
    // 레트로 헤더
    createRetroHeader('FTP 연결 테스트', `${environment} 환경`);
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
    const ftpConfig = envConfig.ftp;
    
    succeedSpinner(spinner, '설정 로드 완료');
    
    // 설정 정보 표시
    showRetroConfig(ftpConfig);
    
    // 연결 테스트
    const connectSpinner = createRetroSpinner('서버에 연결 중...');
    
    const isConnected = await testFtpConnection(ftpConfig);
    
    if (isConnected) {
      connectSpinner.stop(true);
      
      // 디렉토리 목록 가져오기 시도
      const listSpinner = createRetroSpinner('원격 디렉토리 목록을 가져오는 중...');
      
      try {
        const directoryList = await listFiles(ftpConfig, ftpConfig.remotePath);
        
        if (directoryList && directoryList.length > 0) {
          listSpinner.stop(true);
          
          // 파일 목록 표시 (처음 10개만)
          const displayList = directoryList.slice(0, 10).map(item => ({
            name: item.name,
            type: item.type === 'd' ? 'd' : 'f',
            size: item.size || 0
          }));
          
          showRetroFileList(displayList, `원격 디렉토리 내용 (${directoryList.length}개 항목)`);
          
          if (directoryList.length > 10) {
            showRetroInfo(`... 외 ${directoryList.length - 10}개 항목`);
          }
        } else {
          listSpinner.stop(true);
          showRetroWarning('원격 디렉토리가 비어있습니다.');
        }
      } catch (listError) {
        listSpinner.stop(false);
        showRetroWarning(`디렉토리 목록 조회 실패: ${listError.message}`);
      }
      
      // 성공 메시지
      createRetroSection('테스트 결과');
      const successInfo = [
        '✅ FTP 연결 성공',
        '✅ 원격 디렉토리 접근 성공',
        '✅ 모든 테스트가 성공적으로 완료되었습니다!',
        '',
        '이제 kelly-deploy upload <이슈번호> 명령으로 파일을 업로드할 수 있습니다.'
      ].join('\n');
      createRetroBox(successInfo, 'green');
      
      showSuccess(`${environment} 환경 연결 테스트 성공`);
      
    } else {
      connectSpinner.stop(false);
      
      // 실패 메시지
      createRetroSection('테스트 결과');
      const errorInfo = [
        '❌ FTP 연결 실패',
        '',
        '문제 해결 방법:',
        '1. FTP 서버 주소와 포트를 확인하세요',
        '2. 사용자명과 비밀번호를 확인하세요',
        '3. 방화벽 설정을 확인하세요',
        '4. SFTP를 사용하는 경우 secure: true로 설정하세요',
        '5. .env 파일의 환경변수를 확인하세요'
      ].join('\n');
      createRetroBox(errorInfo, 'red');
      
      process.exit(1);
    }
    
  } catch (error) {
    failSpinner(spinner, '연결 테스트 준비 실패');
    handleError(error, 'FTP 연결 테스트');
  }
}

module.exports = { testCommand }; 