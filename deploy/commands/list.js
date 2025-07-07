const chalk = require('chalk');
const { getChangedFilesWithStatus, calculateFileStats } = require('../utils/git');
const { filterIgnoredFiles } = require('../utils/filter');

async function listCommand(issue) {
  try {
    console.log(chalk.blue(`📋 이슈 #${issue}의 변경된 파일 목록을 가져오는 중...`));
    
    // 변경된 파일 목록과 상태 가져오기
    const filesWithStatus = await getChangedFilesWithStatus(issue);
    
    if (filesWithStatus.length === 0) {
      console.log(chalk.yellow(`⚠ 해당 이슈와 관련된 변경된 파일이 없습니다.`));
      return;
    }
    
    // 파일 필터링
    const filteredFilesWithStatus = await filterIgnoredFiles(filesWithStatus.map(item => item.file));
    
    // 필터링된 파일들의 상태 정보 재구성
    const filteredFilesWithStatusInfo = filesWithStatus.filter(item => 
      filteredFilesWithStatus.includes(item.file)
    );
    
    // 통계 계산
    const stats = calculateFileStats(filteredFilesWithStatusInfo);
    
    console.log(chalk.green(`\n✅ 이슈 #${issue}에서 변경된 파일 (${stats.total}개):`));
    console.log(chalk.gray('─'.repeat(60)));
    
    // 파일 목록 표시
    filteredFilesWithStatusInfo.forEach((item, index) => {
      const statusIcon = getStatusIcon(item.status);
      const statusColor = getStatusColor(item.status);
      const statusText = getStatusText(item.status);
      
      console.log(
        chalk.white(`${String(index + 1).padStart(4)}. `) +
        statusIcon + ' ' +
        chalk[statusColor](`[${statusText}]`) + ' ' +
        chalk.white(item.file)
      );
    });
    
    console.log(chalk.gray('─'.repeat(60)));
    
    // 통계 표시
    console.log(chalk.cyan('\n📊 파일 변경 통계:'));
    console.log(chalk.gray('─'.repeat(30)));
    
    if (stats.created > 0) {
      console.log(chalk.green(`  ➕ 생성된 파일: ${stats.created}개`));
    }
    if (stats.modified > 0) {
      console.log(chalk.yellow(`  ✏️  수정된 파일: ${stats.modified}개`));
    }
    if (stats.deleted > 0) {
      console.log(chalk.red(`  🗑️  삭제된 파일: ${stats.deleted}개`));
    }
    if (stats.renamed > 0) {
      console.log(chalk.blue(`  🔄 이름 변경된 파일: ${stats.renamed}개`));
    }
    if (stats.copied > 0) {
      console.log(chalk.magenta(`  📋 복사된 파일: ${stats.copied}개`));
    }
    
    console.log(chalk.gray('─'.repeat(30)));
    console.log(chalk.white(`  📁 총 파일 수: ${stats.total}개`));
    
  } catch (error) {
    console.error(chalk.red('❌ 오류가 발생했습니다:'), error.message);
    process.exit(1);
  }
}

/**
 * 상태에 따른 아이콘을 반환합니다.
 * @param {string} status - 파일 상태
 * @returns {string} 아이콘
 */
function getStatusIcon(status) {
  switch (status) {
    case 'created':
      return '➕';
    case 'modified':
      return '✏️';
    case 'deleted':
      return '🗑️';
    case 'renamed':
      return '🔄';
    case 'copied':
      return '📋';
    default:
      return '📄';
  }
}

/**
 * 상태에 따른 색상을 반환합니다.
 * @param {string} status - 파일 상태
 * @returns {string} 색상명
 */
function getStatusColor(status) {
  switch (status) {
    case 'created':
      return 'green';
    case 'modified':
      return 'yellow';
    case 'deleted':
      return 'red';
    case 'renamed':
      return 'blue';
    case 'copied':
      return 'magenta';
    default:
      return 'white';
  }
}

/**
 * 상태에 따른 텍스트를 반환합니다.
 * @param {string} status - 파일 상태
 * @returns {string} 상태 텍스트
 */
function getStatusText(status) {
  switch (status) {
    case 'created':
      return '생성';
    case 'modified':
      return '수정';
    case 'deleted':
      return '삭제';
    case 'renamed':
      return '이름변경';
    case 'copied':
      return '복사';
    default:
      return '수정';
  }
}

module.exports = { listCommand };