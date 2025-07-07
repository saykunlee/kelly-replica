const chalk = require('chalk');
const { loadConfig } = require('../utils/config');
const { testFtpConnection, listFtpDirectory } = require('../utils/ftp');

async function testCommand() {
  try {
    console.log(chalk.blue('🔍 FTP 연결 테스트를 시작합니다...'));
    
    // 설정 로드
    const config = await loadConfig();
    
    console.log(chalk.gray('📋 현재 설정:'));
    console.log(chalk.gray(`   서버: ${config.ftp.host}:${config.ftp.port}`));
    console.log(chalk.gray(`   사용자: ${config.ftp.user}`));
    console.log(chalk.gray(`   프로토콜: ${config.ftp.secure ? 'SFTP' : 'FTP'}`));
    console.log(chalk.gray(`   원격 경로: ${config.ftp.remotePath}`));
    
    console.log(chalk.blue('\n🔗 서버에 연결 중...'));
    
    // 연결 테스트
    const isConnected = await testFtpConnection(config);
    
    if (isConnected) {
      console.log(chalk.green('\n✅ FTP 연결 테스트 성공!'));
      
      // 디렉토리 목록 가져오기 시도
      try {
        console.log(chalk.blue('\n📁 원격 디렉토리 목록을 가져오는 중...'));
        const directoryList = await listFtpDirectory(config, config.ftp.remotePath);
        
        if (directoryList && directoryList.length > 0) {
          console.log(chalk.green(`✅ 원격 디렉토리 접근 성공! (${directoryList.length}개 항목)`));
          console.log(chalk.gray('\n📋 디렉토리 내용 (처음 10개):'));
          
          directoryList.slice(0, 10).forEach((item, index) => {
            const icon = item.type === 'd' ? '📁' : '📄';
            const size = item.type === 'd' ? '' : ` (${item.size} bytes)`;
            console.log(chalk.white(`   ${index + 1}. ${icon} ${item.name}${size}`));
          });
          
          if (directoryList.length > 10) {
            console.log(chalk.gray(`   ... 외 ${directoryList.length - 10}개 항목`));
          }
        } else {
          console.log(chalk.yellow('⚠ 원격 디렉토리가 비어있습니다.'));
        }
      } catch (error) {
        console.log(chalk.yellow('⚠ 디렉토리 목록을 가져올 수 없습니다:'), error.message);
      }
      
      console.log(chalk.green('\n🎉 모든 테스트가 성공적으로 완료되었습니다!'));
      console.log(chalk.white('이제 kelly-deploy upload <이슈번호> 명령으로 파일을 업로드할 수 있습니다.'));
      
    } else {
      console.log(chalk.red('\n❌ FTP 연결 테스트 실패'));
      console.log(chalk.yellow('\n💡 문제 해결 방법:'));
      console.log(chalk.white('1. FTP 서버 주소와 포트를 확인하세요'));
      console.log(chalk.white('2. 사용자명과 비밀번호를 확인하세요'));
      console.log(chalk.white('3. 방화벽 설정을 확인하세요'));
      console.log(chalk.white('4. SFTP를 사용하는 경우 secure: true로 설정하세요'));
      console.log(chalk.white('5. deploy/kelly-deploy.config.js 파일의 설정을 확인하세요'));
      
      process.exit(1);
    }
    
  } catch (error) {
    console.error(chalk.red('❌ 연결 테스트 중 오류가 발생했습니다:'), error.message);
    console.log(chalk.yellow('\n💡 설정을 확인하세요:'));
    console.log(chalk.white('1. kelly-deploy init 명령으로 설정을 초기화하세요'));
    console.log(chalk.white('2. deploy/kelly-deploy.config.js 파일의 설정을 확인하세요'));
    console.log(chalk.white('3. .env 파일의 환경변수를 확인하세요'));
    
    process.exit(1);
  }
}

module.exports = { testCommand }; 