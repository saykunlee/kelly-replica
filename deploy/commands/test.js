const chalk = require('chalk');
const { loadConfig } = require('../utils/config');
const { testFtpConnection, listFiles } = require('../utils/ftp');
const { getEnvironment, validateEnvironment, printEnvironmentInfo } = require('../utils/environments');
const { createSpinner, succeedSpinner, failSpinner } = require('../utils/progress');
const { handleError, showWarning, showSuccess } = require('../utils/errorHandler');

async function testCommand(options = {}) {
  const environment = options.environment || 'production';
  const spinner = createSpinner('FTP 연결 테스트 준비 중...');
  
  try {
    spinner.start();
    
    // 환경 설정 검증
    if (!validateEnvironment(environment)) {
      failSpinner(spinner, '환경 설정이 유효하지 않습니다');
      return;
    }
    
    // 환경 정보 출력
    printEnvironmentInfo(environment);
    
    // 환경별 설정 로드
    const envConfig = await getEnvironment(environment);
    
    // 환경별 FTP 설정 사용
    const ftpConfig = envConfig.ftp;
    
    succeedSpinner(spinner, '설정 로드 완료');
    
    console.log(chalk.gray('📋 테스트할 설정:'));
    console.log(chalk.gray(`   서버: ${ftpConfig.host}:${ftpConfig.port}`));
    console.log(chalk.gray(`   사용자: ${ftpConfig.user}`));
    console.log(chalk.gray(`   프로토콜: ${ftpConfig.secure ? 'SFTP' : 'FTP'}`));
    console.log(chalk.gray(`   원격 경로: ${ftpConfig.remotePath}`));
    
    // 연결 테스트
    const connectSpinner = createSpinner('서버에 연결 중...');
    connectSpinner.start();
    
    const isConnected = await testFtpConnection(ftpConfig);
    
    if (isConnected) {
      succeedSpinner(connectSpinner, 'FTP 연결 성공');
      
      // 디렉토리 목록 가져오기 시도
      const listSpinner = createSpinner('원격 디렉토리 목록을 가져오는 중...');
      listSpinner.start();
      
      try {
        const directoryList = await listFiles(ftpConfig, ftpConfig.remotePath);
        
        if (directoryList && directoryList.length > 0) {
          succeedSpinner(listSpinner, `원격 디렉토리 접근 성공 (${directoryList.length}개 항목)`);
          
          console.log(chalk.gray('\n📋 디렉토리 내용 (처음 10개):'));
          
          directoryList.slice(0, 10).forEach((item, index) => {
            const icon = item.type === 'd' ? '📁' : '📄';
            const size = item.type === 'd' ? '' : ` (${item.size} bytes)`;
            const color = item.type === 'd' ? 'blue' : 'white';
            console.log(chalk[color](`   ${String(index + 1).padStart(2)}. ${icon} ${item.name}${size}`));
          });
          
          if (directoryList.length > 10) {
            console.log(chalk.gray(`   ... 외 ${directoryList.length - 10}개 항목`));
          }
        } else {
          succeedSpinner(listSpinner, '원격 디렉토리 접근 성공');
          showWarning('원격 디렉토리가 비어있습니다.');
        }
      } catch (listError) {
        failSpinner(listSpinner, '디렉토리 목록을 가져올 수 없습니다');
        showWarning(`디렉토리 목록 조회 실패: ${listError.message}`);
      }
      
      console.log(chalk.green('\n🎉 모든 테스트가 성공적으로 완료되었습니다!'));
      console.log(chalk.white('이제 kelly-deploy upload <이슈번호> 명령으로 파일을 업로드할 수 있습니다.'));
      showSuccess(`${environment} 환경 연결 테스트 성공`);
      
    } else {
      failSpinner(connectSpinner, 'FTP 연결 실패');
      
      console.log(chalk.red('\n❌ FTP 연결 테스트 실패'));
      console.log(chalk.yellow('\n💡 문제 해결 방법:'));
      console.log(chalk.white('1. FTP 서버 주소와 포트를 확인하세요'));
      console.log(chalk.white('2. 사용자명과 비밀번호를 확인하세요'));
      console.log(chalk.white('3. 방화벽 설정을 확인하세요'));
      console.log(chalk.white('4. SFTP를 사용하는 경우 secure: true로 설정하세요'));
      console.log(chalk.white('5. .env 파일의 환경변수를 확인하세요'));
      
      process.exit(1);
    }
    
  } catch (error) {
    failSpinner(spinner, '연결 테스트 준비 실패');
    handleError(error, 'FTP 연결 테스트');
  }
}

module.exports = { testCommand }; 