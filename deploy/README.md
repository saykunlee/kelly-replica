# Kelly Deploy

GitHub 이슈 기반 자동 배포 시스템

## 설치

```bash
# 전역 설치
npm install -g kelly-deploy

# 또는 로컬에서 npm link 사용
cd deploy
npm link
```

## 사용법

### 1. 초기 설정

프로젝트 루트에서 초기 설정을 실행합니다:

```bash
kelly-deploy init
```

이 명령어는 다음 파일들을 생성합니다:
- `deploy/.env` - 환경 변수
- `deploy/kelly-deploy.config.js` - FTP 서버 설정
- `deploy/.deployignore` - 배포 제외 파일

### 2. FTP 연결 테스트

```bash
kelly-deploy test
# 또는 특정 환경 지정
kelly-deploy test -e staging
```

### 3. 변경된 파일 목록 조회

```bash
kelly-deploy list <이슈번호>
# 상세 정보 포함
kelly-deploy list <이슈번호> -v
```

### 4. 파일 업로드 및 배포

```bash
# 운영 환경 배포
kelly-deploy upload <이슈번호>

# 스테이징 환경 배포
kelly-deploy upload <이슈번호> -e staging

# 드라이 런 (시뮬레이션)
kelly-deploy upload <이슈번호> -d
```

### 5. 도움말

```bash
kelly-deploy help
```

## 프로젝트 구조

```
your-project/
├── package.json          # 프로젝트 루트에 필요
├── deploy/              # 배포 프로그램
│   ├── index.js         # 메인 실행 파일
│   ├── commands/        # 명령어 모듈
│   ├── utils/           # 유틸리티
│   ├── .env             # 환경 변수 (생성됨)
│   ├── kelly-deploy.config.js  # 설정 파일 (생성됨)
│   └── .deployignore    # 배포 제외 파일 (생성됨)
└── .vscode/sftp.json    # VSCode SFTP 설정 (선택사항)
```

## 환경 설정

### 지원 환경
- `production` - 운영 환경 (기본값)
- `development` - 개발 환경
- `staging` - 스테이징 환경

### 설정 파일

#### deploy/.env
```env
# FTP 비밀번호 (보안상 권장)
FTP_PASSWORD=your_password

# 환경별 설정
PRODUCTION_FTP_PASSWORD=prod_password
STAGING_FTP_PASSWORD=staging_password
```

#### deploy/kelly-deploy.config.js
```javascript
module.exports = {
  production: {
    ftp: {
      host: 'your-server.com',
      port: 21,
      user: 'username',
      secure: false, // FTP: false, SFTP: true
      remotePath: '/path/to/remote'
    }
  },
  staging: {
    ftp: {
      host: 'staging-server.com',
      port: 21,
      user: 'staging_user',
      secure: false,
      remotePath: '/path/to/staging'
    }
  }
};
```

## 특징

- 🎨 **레트로 UI**: 아름다운 CLI 인터페이스
- 🔒 **보안**: 환경변수를 통한 비밀번호 관리
- 🚀 **자동화**: GitHub 이슈 기반 자동 배포
- 📁 **스마트 필터링**: .deployignore를 통한 파일 제외
- 🔧 **다중 환경**: production, staging, development 지원
- ⚡ **빠른 실행**: 프로젝트 루트 검증으로 안전성 보장

## 제한사항

- 프로젝트 루트에서만 실행 가능
- package.json 파일이 반드시 필요
- deploy/ 디렉토리와 deploy/index.js 파일이 필요

## 라이선스

MIT License 