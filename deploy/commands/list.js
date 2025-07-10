const chalk = require('chalk');
const { getChangedFilesWithStatus } = require('../utils/git');
const { filterIgnoredFiles } = require('../utils/filter');
const { createSpinner, succeedSpinner, failSpinner } = require('../utils/progress');
const { handleError, showWarning, showSuccess } = require('../utils/errorHandler');
const { createRetroHeader, createRetroSection, createRetroBox, showRetroSuccess, showRetroError, showRetroWarning, showRetroInfo, showRetroFileList, showRetroProgress, showRetroDivider, createRetroSpinner } = require('../utils/retroUI');

async function listCommand(issue, options = {}) {
  const spinner = createSpinner('변경된 파일을 가져오는 중...');
  
  try {
    spinner.start();
    
    // 레트로 헤더
    createRetroHeader('변경된 파일 목록', `이슈 #${issue}`);
    showRetroDivider();
    
    // 변경된 파일 가져오기
    const filesSpinner = createRetroSpinner('Git에서 변경된 파일을 가져오는 중...');
    
    const filesWithStatus = await getChangedFilesWithStatus(issue);
    
    if (filesWithStatus.length === 0) {
      filesSpinner.stop(false);
      showRetroWarning(`이슈 #${issue}와 관련된 변경된 파일이 없습니다.`);
      return;
    }
    
    filesSpinner.stop(true);
    
    // 파일 상태별 분류
    const addedFiles = filesWithStatus.filter(item => item.status === 'added');
    const modifiedFiles = filesWithStatus.filter(item => item.status === 'modified');
    const deletedFiles = filesWithStatus.filter(item => item.status === 'deleted');
    
    // 전체 파일 목록 표시
    createRetroSection('전체 변경된 파일');
    const allFiles = filesWithStatus.map(item => ({
      name: item.file,
      type: 'f',
      size: 0,
      status: item.status
    }));
    
    showRetroFileList(allFiles, `총 ${filesWithStatus.length}개 파일`);
    
    // 상태별 통계
    createRetroSection('파일 상태 통계');
    const statsInfo = [
      `추가된 파일: ${addedFiles.length}개`,
      `수정된 파일: ${modifiedFiles.length}개`,
      `삭제된 파일: ${deletedFiles.length}개`
    ].join('\n');
    createRetroBox(statsInfo, 'cyan');
    
    // 업로드 대상 파일 필터링
    const uploadSpinner = createRetroSpinner('업로드 대상 파일을 필터링하는 중...');
    
    // 삭제된 파일 제외
    const uploadCandidates = filesWithStatus.filter(item => item.status !== 'deleted');
    const changedFiles = uploadCandidates.map(item => item.file);
    
    const filteredFiles = await filterIgnoredFiles(changedFiles);
    
    uploadSpinner.stop(true);
    
    // 필터링 결과 표시
    createRetroSection('필터링 결과');
    const filterInfo = [
      `원본 파일: ${changedFiles.length}개`,
      `필터링 후: ${filteredFiles.length}개`,
      `제외된 파일: ${changedFiles.length - filteredFiles.length}개`
    ].join('\n');
    createRetroBox(filterInfo, 'yellow');
    
    if (filteredFiles.length === 0) {
      showRetroWarning('업로드할 파일이 없습니다.');
      return;
    }
    
    // 업로드 대상 파일 목록
    createRetroSection('업로드 대상 파일');
    const uploadFiles = filteredFiles.map(file => ({
      name: file,
      type: 'f',
      size: 0
    }));
    
    showRetroFileList(uploadFiles, `${filteredFiles.length}개 파일`);
    
    // 진행률 표시
    showRetroProgress(filteredFiles.length, filesWithStatus.length, '업로드 준비율');
    
    // 상세 정보 (verbose 모드)
    if (options.verbose) {
      createRetroSection('상세 정보');
      const detailInfo = [
        `이슈 번호: #${issue}`,
        `Git 커밋 범위: HEAD~1..HEAD`,
        `필터링 규칙: .deployignore 파일 기반`,
        `제외 패턴: node_modules, .git, .vscode 등`
      ].join('\n');
      createRetroBox(detailInfo, 'blue');
    }
    
    // 다음 단계 안내
    createRetroSection('다음 단계');
    const nextSteps = [
      `kelly-deploy upload ${issue}          # 실제 업로드`,
      `kelly-deploy upload ${issue} -d      # 드라이 런 (시뮬레이션)`,
      `kelly-deploy upload ${issue} -e dev  # 개발 환경 배포`
    ].join('\n');
    createRetroBox(nextSteps, 'green');
    
    showRetroDivider();
    showRetroSuccess(`이슈 #${issue}의 변경된 파일 목록을 성공적으로 가져왔습니다.`);
    showRetroDivider();
    
    succeedSpinner(spinner, '파일 목록 가져오기 완료');
    
  } catch (error) {
    failSpinner(spinner, '파일 목록 가져오기 실패');
    handleError(error, '파일 목록 조회');
  }
}

module.exports = { listCommand };