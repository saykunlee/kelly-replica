const chalk = require('chalk');
const stripAnsi = require('strip-ansi');
const { createSpinner } = require('./progress');

const FIXED_WIDTH = 80; // 고정 가로 길이

/**
 * 레트로 스타일의 헤더를 생성합니다.
 * @param {string} title - 제목
 * @param {string} subtitle - 부제목
 */
function createRetroHeader(title, subtitle = '') {
  const width = FIXED_WIDTH;
  // 내부 텍스트만 padEnd로 고정 폭 출력
  console.log(chalk.cyan(title.padEnd(width)));
  if (subtitle) {
    console.log(chalk.gray(subtitle.padEnd(width)));
  }
}

/**
 * 레트로 스타일의 섹션을 생성합니다.
 * @param {string} title - 섹션 제목
 */
function createRetroSection(title) {
  const width = FIXED_WIDTH;
  const border = '─'.repeat(width);
  console.log(chalk.magenta(border));
  // 제목도 고정 폭으로 padEnd
  console.log(chalk.magenta(title.padEnd(width)));
  console.log(chalk.magenta(border));
}

/**
 * 문자열의 실제 길이(ANSI 컬러코드 제외)를 반환
 */
function getDisplayLength(str) {
  return stripAnsi.default ? stripAnsi.default(str) : stripAnsi(str);
}

function padAndCutPlain(line, width) {
  // 컬러 없는 plain 기준으로 자르고 패딩
  let plain = stripAnsi.default ? stripAnsi.default(line) : stripAnsi(line);
  if (plain.length > width) plain = plain.slice(0, width);
  const padLen = width - plain.length;
  return plain + ' '.repeat(padLen > 0 ? padLen : 0);
}

function colorizeLine(prefix, content, prefixColor, contentColor, width) {
  // 1. plain 기준으로 자르기
  let plain = prefix + content;
  if (plain.length > width) {
    // prefix가 width보다 길면 prefix만 자름
    if (prefix.length >= width) {
      prefix = prefix.slice(0, width);
      content = '';
    } else {
      content = content.slice(0, width - prefix.length);
    }
    plain = prefix + content;
  }
  // 2. 컬러 적용
  let colored = chalk[prefixColor](prefix) + chalk[contentColor](content);
  // 3. 컬러 적용 후 길이 체크, 부족하면 오른쪽에 공백 추가
  const padLen = width - getDisplayLength(plain);
  if (padLen > 0) colored += ' '.repeat(padLen);
  return colored;
}

/**
 * 레트로 스타일의 박스를 생성합니다.
 * @param {string} content - 내용
 * @param {string} color - 색상 (기본값: 'blue')
 */
function createRetroBox(content, color = 'blue') {
  const lines = content.split('\n');
  const maxWidth = FIXED_WIDTH;
  lines.forEach(line => {
    const display = padAndCutPlain(line, maxWidth);
    console.log(chalk[color](display));
  });
}

/**
 * 레트로 스타일의 로딩 애니메이션을 생성합니다.
 * @param {string} text - 로딩 텍스트
 * @param {number} duration - 지속 시간 (ms)
 */
function createRetroLoading(text, duration = 2000) {
  const frames = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
  let frameIndex = 0;
  
  const interval = setInterval(() => {
    process.stdout.write(`\r${chalk.cyan(frames[frameIndex])} ${chalk.yellow(text)}`);
    frameIndex = (frameIndex + 1) % frames.length;
  }, 100);
  
  setTimeout(() => {
    clearInterval(interval);
    process.stdout.write('\r');
  }, duration);
  
  return interval;
}

/**
 * 레트로 스타일의 성공 메시지를 표시합니다.
 * @param {string} message - 메시지
 */
function showRetroSuccess(message) {
  console.log(chalk.green('✅ ') + chalk.white(message));
}

/**
 * 레트로 스타일의 에러 메시지를 표시합니다.
 * @param {string} message - 메시지
 */
function showRetroError(message) {
  console.log(chalk.red('❌ ') + chalk.white(message));
}

/**
 * 레트로 스타일의 경고 메시지를 표시합니다.
 * @param {string} message - 메시지
 */
function showRetroWarning(message) {
  console.log(chalk.yellow('⚠️ ') + chalk.white(message));
}

/**
 * 레트로 스타일의 정보 메시지를 표시합니다.
 * @param {string} message - 메시지
 */
function showRetroInfo(message) {
  console.log(chalk.blue('ℹ️ ') + chalk.white(message));
}

/**
 * 레트로 스타일의 파일 목록을 표시합니다.
 * @param {Array} files - 파일 목록
 * @param {string} title - 제목
 */
function showRetroFileList(files, title = '파일 목록') {
  createRetroSection(title);
  if (!files || files.length === 0) return;
  const maxWidth = FIXED_WIDTH;
  const lines = files.map((file, index) => {
    const icon = file.type === 'd' ? '📁' : '📄';
    const size = file.type === 'd' ? '' : ` (${file.size} bytes)`;
    const prefix = `  ${String(index + 1).padStart(2)}. `;
    const content = `${icon} ${file.name}${size}`;
    const colored = colorizeLine(prefix, content, 'cyan', file.type === 'd' ? 'blue' : 'white', maxWidth);
    return colored;
  });
  lines.forEach(line => {
    console.log(line);
  });
}

/**
 * 레트로 스타일의 진행률 바를 표시합니다.
 * @param {number} current - 현재 값
 * @param {number} total - 전체 값
 * @param {string} label - 라벨
 */
function showRetroProgress(current, total, label = '진행률') {
  const percentage = Math.round((current / total) * 100);
  const barWidth = 30;
  const filledWidth = Math.round((percentage / 100) * barWidth);
  const bar = '█'.repeat(filledWidth) + '░'.repeat(barWidth - filledWidth);
  
  console.log(chalk.cyan(`  ${label}: `) + 
              chalk.green(`[${bar}]`) + 
              chalk.white(` ${percentage}% (${current}/${total})`));
}

/**
 * 레트로 스타일의 설정 정보를 표시합니다.
 * @param {Object} config - 설정 객체
 */
function showRetroConfig(config) {
  createRetroSection('설정 정보');
  
  const configInfo = [
    `서버: ${config.host}:${config.port}`,
    `사용자: ${config.user}`,
    `프로토콜: ${config.secure ? 'SFTP' : 'FTP'}`,
    `원격 경로: ${config.remotePath}`
  ].join('\n');
  
  createRetroBox(configInfo, 'green');
}

/**
 * 레트로 스타일의 결과 요약을 표시합니다.
 * @param {Object} results - 결과 객체
 */
function showRetroResults(results) {
  createRetroSection('배포 결과');
  
  const resultInfo = [
    `총 파일 수: ${results.total}`,
    `성공: ${results.success.length}`,
    `실패: ${results.failed.length}`
  ].join('\n');
  
  createRetroBox(resultInfo, results.failed.length > 0 ? 'red' : 'green');
  
  if (results.failed.length > 0) {
    console.log(chalk.red('\n❌ 실패한 파일:'));
    results.failed.forEach((item, index) => {
      console.log(chalk.red(`  ${index + 1}. ${item.file} - ${item.error}`));
    });
  }
}

/**
 * 레트로 스타일의 도움말을 표시합니다.
 * @param {Array} commands - 명령어 목록
 */
function showRetroHelp(commands) {
  createRetroSection('사용 가능한 명령어');
  
  commands.forEach(cmd => {
    console.log(chalk.cyan(`  ${cmd.command.padEnd(20)} `) + 
                chalk.white(cmd.description));
  });
}

/**
 * 레트로 스타일의 ASCII 아트 로고를 표시합니다.
 */
function showRetroLogo() {
  const logo = `
  ██╗  ██╗███████╗██╗     ██╗     ██╗   ██╗    
  ██║ ██╔╝██╔════╝██║     ██║     ╚██╗ ██╔╝    
  █████╔╝ █████╗  ██║     ██║      ╚████╔╝     
  ██╔═██╗ ██╔══╝  ██║     ██║       ╚██╔╝      
  ██║  ██╗███████╗███████╗███████╗   ██║       
  ╚═╝  ╚═╝╚══════╝╚══════╝╚══════╝   ╚═╝       
  `;
  const deploy = '\n' + ' '.repeat(18) + chalk.yellow.bold('KELLY-DEPLOY');
  const by = ' '+ chalk.white.bold('BY UNYICT');
  
  console.log(chalk.cyan(logo));
  process.stdout.write(deploy);
  console.log(chalk.gray(by));
}

/**
 * 레트로 스타일의 구분선을 표시합니다.
 */
function showRetroDivider() {
  console.log(chalk.magenta('─'.repeat(60)));
}

/**
 * 레트로 스타일의 스피너를 생성합니다.
 * @param {string} text - 텍스트
 */
function createRetroSpinner(text) {
  const frames = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
  let frameIndex = 0;
  
  const interval = setInterval(() => {
    process.stdout.write(`\r${chalk.cyan(frames[frameIndex])} ${chalk.yellow(text)}`);
    frameIndex = (frameIndex + 1) % frames.length;
  }, 100);
  
  return {
    stop: (success = true) => {
      clearInterval(interval);
      process.stdout.write('\r');
      if (success) {
        showRetroSuccess(text + ' 완료');
      } else {
        showRetroError(text + ' 실패');
      }
    }
  };
}

module.exports = {
  createRetroHeader,
  createRetroSection,
  createRetroBox,
  createRetroLoading,
  showRetroSuccess,
  showRetroError,
  showRetroWarning,
  showRetroInfo,
  showRetroFileList,
  showRetroProgress,
  showRetroConfig,
  showRetroResults,
  showRetroHelp,
  showRetroLogo,
  showRetroDivider,
  createRetroSpinner
}; 