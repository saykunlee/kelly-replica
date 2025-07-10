const chalk = require('chalk');
const { showRetroLogo, createRetroHeader, createRetroSection, createRetroBox, showRetroHelp, showRetroDivider } = require('../utils/retroUI');

function helpCommand() {
  // 레트로 로고 표시
  showRetroLogo();
  showRetroDivider();
  
  // 레트로 헤더
  createRetroHeader('Kelly Deploy CLI 도움말', 'GitHub 이슈 기반 자동 배포 시스템');
  showRetroDivider();
  
  // 명령어 목록
  const commands = [
    { command: 'init', description: '초기 설정 파일 생성' },
    { command: 'test', description: 'FTP 연결 테스트' },
    { command: 'list <이슈번호>', description: '변경된 파일 목록 조회' },
    { command: 'upload <이슈번호>', description: '파일 업로드 및 배포' },
    { command: 'help', description: '도움말 표시' }
  ];
  
  showRetroHelp(commands);
  
  // 사용 예시
  createRetroSection('사용 예시');
  const examples = [
    'kelly-deploy init                    # 초기 설정',
    'kelly-deploy test                   # FTP 연결 테스트',
    'kelly-deploy test -e staging        # 스테이징 환경 테스트',
    'kelly-deploy list 123               # 이슈 #123 파일 목록',
    'kelly-deploy list 123 -v            # 상세 정보 포함',
    'kelly-deploy upload 123             # 운영 환경 배포',
    'kelly-deploy upload 123 -e dev      # 개발 환경 배포',
    'kelly-deploy upload 123 -d          # 드라이 런 (시뮬레이션)',
    'kelly-deploy upload 123 -d -e dev   # 개발 환경 드라이 런'
  ].join('\n');
  createRetroBox(examples, 'blue');
  
  // 옵션 설명
  createRetroSection('옵션');
  const options = [
    '-e, --environment <env>  # 배포 환경 지정 (production, staging, development)',
    '-d, --dry-run           # 실제 업로드하지 않고 시뮬레이션만 수행',
    '-v, --verbose           # 상세 정보 출력',
    '--help                  # 도움말 표시'
  ].join('\n');
  createRetroBox(options, 'cyan');
  
  // 환경 설명
  createRetroSection('지원 환경');
  const environments = [
    'production:  운영 환경 (.vscode/sftp.json 기반)',
    'staging:     스테이징 환경 (환경변수 기반)',
    'development: 개발 환경 (환경변수 기반)'
  ].join('\n');
  createRetroBox(environments, 'green');
  
  // 설정 파일
  createRetroSection('설정 파일');
  const configFiles = [
    'deploy/kelly-deploy.config.js - FTP 서버 설정',
    'deploy/.env                    - 환경별 비밀번호',
    'deploy/.deployignore           - 배포 제외 파일 패턴',
    '.vscode/sftp.json              - VS Code SFTP 설정 (production 환경)'
  ].join('\n');
  createRetroBox(configFiles, 'yellow');
  
  // 워크플로우
  createRetroSection('일반적인 워크플로우');
  const workflow = [
    '1. kelly-deploy init           # 초기 설정',
    '2. 설정 파일 편집              # FTP 정보 입력',
    '3. kelly-deploy test          # 연결 테스트',
    '4. kelly-deploy list 123      # 변경 파일 확인',
    '5. kelly-deploy upload 123    # 실제 배포'
  ].join('\n');
  createRetroBox(workflow, 'magenta');
  
  showRetroDivider();
  console.log(chalk.cyan('💡 더 자세한 정보는 각 명령어에 --help 옵션을 사용하세요.'));
  showRetroDivider();
}

module.exports = { helpCommand }; 