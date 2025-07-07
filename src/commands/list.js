const { execSync } = require('child_process');
const chalk = require('chalk');
const { getChangedFiles } = require('../utils/git');
const { filterIgnoredFiles } = require('../utils/filter');

async function listCommand(issue, options) {
  try {
    console.log(chalk.blue(`📋 이슈 #${issue}의 변경된 파일 목록을 가져오는 중...`));
    
    // Git에서 변경된 파일 가져오기
    const changedFiles = await getChangedFiles(issue);
    
    if (changedFiles.length === 0) {
      console.log(chalk.yellow('⚠️  해당 이슈와 관련된 변경된 파일이 없습니다.'));
      return;
    }
    
    // .gitignore와 .deployignore 기반으로 필터링
    const filteredFiles = await filterIgnoredFiles(changedFiles);
    
    console.log(chalk.green(`\n✅ 이슈 #${issue}에서 변경된 파일 (${filteredFiles.length}개):`));
    console.log(chalk.gray('─'.repeat(50)));
    
    filteredFiles.forEach((file, index) => {
      console.log(chalk.white(`${index + 1}. ${file}`));
    });
    
    if (options.verbose) {
      console.log(chalk.gray('\n📊 상세 정보:'));
      console.log(chalk.gray(`- 전체 변경 파일: ${changedFiles.length}개`));
      console.log(chalk.gray(`- 필터링 후 파일: ${filteredFiles.length}개`));
      console.log(chalk.gray(`- 제외된 파일: ${changedFiles.length - filteredFiles.length}개`));
    }
    
  } catch (error) {
    console.error(chalk.red('❌ 오류가 발생했습니다:'), error.message);
    process.exit(1);
  }
}

module.exports = { listCommand }; 