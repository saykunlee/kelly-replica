const chalk = require('chalk');
const ora = require('ora');
const { getChangedFiles } = require('../utils/git');
const { filterIgnoredFiles } = require('../utils/filter');
const { uploadToFtp } = require('../utils/ftp');
const { loadConfig } = require('../utils/config');

async function uploadCommand(issue, options) {
  try {
    const spinner = ora('설정을 로드하는 중...').start();
    
    // 설정 로드
    const config = await loadConfig();
    spinner.succeed('설정 로드 완료');
    
    spinner.start('변경된 파일을 가져오는 중...');
    const changedFiles = await getChangedFiles(issue);
    
    if (changedFiles.length === 0) {
      spinner.warn('해당 이슈와 관련된 변경된 파일이 없습니다.');
      return;
    }
    
    spinner.succeed(`변경된 파일 ${changedFiles.length}개 발견`);
    
    spinner.start('파일 필터링 중...');
    const filteredFiles = await filterIgnoredFiles(changedFiles);
    spinner.succeed(`필터링 완료: ${filteredFiles.length}개 파일`);
    
    if (options.dryRun) {
      console.log(chalk.blue('\n🔍 드라이 런 모드 - 실제 업로드하지 않습니다'));
      console.log(chalk.gray('─'.repeat(50)));
      console.log(chalk.white('업로드될 파일 목록:'));
      filteredFiles.forEach((file, index) => {
        console.log(chalk.white(`${index + 1}. ${file}`));
      });
      console.log(chalk.gray('─'.repeat(50)));
      console.log(chalk.green(`총 ${filteredFiles.length}개 파일이 업로드될 예정입니다.`));
      return;
    }
    
    // FTP 업로드
    spinner.start('FTP 서버에 연결 중...');
    await uploadToFtp(filteredFiles, config, spinner);
    
    console.log(chalk.green(`\n✅ 이슈 #${issue}의 파일 업로드가 완료되었습니다!`));
    console.log(chalk.gray(`업로드된 파일: ${filteredFiles.length}개`));
    
  } catch (error) {
    console.error(chalk.red('❌ 업로드 중 오류가 발생했습니다:'), error.message);
    process.exit(1);
  }
}

module.exports = { uploadCommand }; 