# Kelly Replica - CodeIgniter 4 관리자 대시보드
# Kelly Replica - CodeIgniter 4 Admin Dashboard

CodeIgniter 4 기반의 관리자 대시보드 웹 애플리케이션입니다. DashForge 테마를 활용한 현대적인 UI와 다양한 관리 기능을 제공합니다.

A web application based on CodeIgniter 4 admin dashboard. Provides modern UI using DashForge theme and various management features.

## 🚀 주요 기능 / Key Features

- **관리자 대시보드**: 통계, 차트, 데이터 관리
- **사용자 관리**: 회원가입, 로그인, 권한 관리
- **데이터 테이블**: DataTables를 활용한 데이터 표시
- **반응형 디자인**: Bootstrap 기반 모바일 친화적 UI
- **차트 및 시각화**: Chart.js를 활용한 데이터 시각화

- **Admin Dashboard**: Statistics, charts, data management
- **User Management**: Registration, login, permission management
- **Data Tables**: Data display using DataTables
- **Responsive Design**: Mobile-friendly UI based on Bootstrap
- **Charts & Visualization**: Data visualization using Chart.js

## 📋 요구사항 / Requirements

- **PHP**: 8.1 이상 / 8.1 or higher
- **Composer**: 최신 버전 / Latest version
- **MariaDB/MySQL**: 5.7 이상 / 5.7 or higher
- **Web Server**: Apache/Nginx

## 🛠️ 설치 및 설정 / Installation & Setup

### 1. 프로젝트 클론 / Clone Project

```bash
git clone https://github.com/saykunlee/kelly-replica.git
cd kelly-replica
```

### 2. 의존성 설치 / Install Dependencies

```bash
composer install
```

### 3. 환경 설정 / Environment Configuration

```bash
# 환경 설정 파일 복사 / Copy environment configuration files
cp app/Config/App.php.example app/Config/App.php
cp app/Config/Database.php.example app/Config/Database.php
cp app/Config/Email.php.example app/Config/Email.php
```

### 4. 데이터베이스 설정 / Database Configuration

`app/Config/Database.php` 파일에서 데이터베이스 연결 정보를 설정하세요:

Configure database connection information in `app/Config/Database.php`:

```php
public $default = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'your_database',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => (ENVIRONMENT !== 'production'),
    'charset'  => 'utf8',
    'DBCollate' => 'utf8_general_ci',
    'swapPre'  => '',
    'encrypt'  => false,
    'compress' => false,
    'strictOn' => false,
    'failover' => [],
    'port'     => 3306,
];
```

### 5. 개발 환경 설정 / Development Environment Setup

프로젝트를 처음 클론하거나 초기화할 때, 개발 환경별 설정 파일(.vscode)은 보안상 Git에 포함되지 않습니다.

When cloning or initializing the project for the first time, development environment-specific configuration files (.vscode) are not included in Git for security reasons.

**VSCode 개발환경 설정 예시 / VSCode Development Environment Setup Example:**

```bash
# .vscode 폴더 및 기본 설정파일 생성 / Create .vscode folder and basic configuration files
mkdir -p .vscode
cat > .vscode/settings.json <<EOF
{
    "php.validate.executablePath": "/usr/local/bin/php",
    "editor.formatOnSave": true,
    "files.exclude": {
        "**/vendor": true,
        "**/node_modules": true
    }
}
EOF
```

- 필요에 따라 `.vscode/launch.json`, `.vscode/extensions.json` 등도 직접 생성해 사용하세요.
- 민감 정보(접속 정보, 비밀번호 등)는 절대 Git에 올리지 마세요.

- Create `.vscode/launch.json`, `.vscode/extensions.json` etc. as needed.
- Never upload sensitive information (connection info, passwords, etc.) to Git.

## 🏗️ 프로젝트 구조 / Project Structure

```
kelly-replica/
├── app/                    # 애플리케이션 코어 / Application Core
│   ├── Config/            # 설정 파일들 / Configuration Files
│   ├── Controllers/       # 컨트롤러 / Controllers
│   ├── Models/           # 데이터 모델 / Data Models
│   ├── Views/            # 뷰 템플릿 / View Templates
│   ├── Database/         # 마이그레이션/시드 / Migrations/Seeds
│   └── Helpers/          # 헬퍼 함수들 / Helper Functions
├── public/               # 웹 루트 디렉토리 / Web Root Directory
│   ├── assets/          # CSS, JS, 이미지 / CSS, JS, Images
│   ├── lib/             # 외부 라이브러리 / External Libraries
│   └── uploads/         # 업로드 파일 / Upload Files
├── vendor/              # Composer 의존성 / Composer Dependencies
├── tests/               # 테스트 파일들 / Test Files
└── writable/            # 로그, 캐시, 세션 / Logs, Cache, Sessions
```

## 🧪 테스트 실행 / Running Tests

```bash
# 전체 테스트 실행 / Run all tests
composer test

# 특정 테스트 실행 / Run specific tests
./vendor/bin/phpunit tests/unit/
```

## 🔒 보안 주의사항 / Security Considerations

### Git에서 제외되는 파일들 / Files Excluded from Git

다음 파일들은 보안상 Git에 포함되지 않습니다:

The following files are not included in Git for security reasons:

- **환경 설정**: `.env`, `app/Config/Database.php`, `app/Config/Email.php`
- **개발자 설정**: `.vscode/`, `*.local.php`
- **인증 파일**: `*.key`, `*.pem`, `*.crt`
- **로그/캐시**: `writable/`, `logs/`, `*.log`

- **Environment Config**: `.env`, `app/Config/Database.php`, `app/Config/Email.php`
- **Developer Settings**: `.vscode/`, `*.local.php`
- **Authentication Files**: `*.key`, `*.pem`, `*.crt`
- **Logs/Cache**: `writable/`, `logs/`, `*.log`

### 보안 체크리스트 / Security Checklist

- [ ] 데이터베이스 비밀번호가 Git에 포함되지 않음
- [ ] API 키나 토큰이 코드에 하드코딩되지 않음
- [ ] 환경별 설정 파일이 적절히 분리됨
- [ ] 민감한 정보가 로그에 기록되지 않음

- [ ] Database passwords are not included in Git
- [ ] API keys or tokens are not hardcoded in code
- [ ] Environment-specific configuration files are properly separated
- [ ] Sensitive information is not logged

## 🚀 배포 / Deployment

### 프로덕션 환경 설정 / Production Environment Setup

1. **환경 변수 설정** / Set environment variables
2. **데이터베이스 마이그레이션 실행** / Run database migrations
3. **캐시 최적화** / Optimize cache
4. **파일 권한 설정** / Set file permissions

```bash
# 마이그레이션 실행 / Run migrations
php spark migrate

# 캐시 클리어 / Clear cache
php spark cache:clear
```

## 🤝 기여하기 / Contributing

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 라이선스 / License

이 프로젝트는 MIT 라이선스 하에 배포됩니다. 자세한 내용은 `LICENSE` 파일을 참조하세요.

This project is distributed under the MIT License. See the `LICENSE` file for details.

## 📞 지원 / Support

- **이슈 리포트**: [GitHub Issues](https://github.com/saykunlee/kelly-replica/issues)
- **문서**: [CodeIgniter 4 문서](https://codeigniter4.github.io/userguide/)

- **Issue Reports**: [GitHub Issues](https://github.com/saykunlee/kelly-replica/issues)
- **Documentation**: [CodeIgniter 4 Documentation](https://codeigniter4.github.io/userguide/)

---

**주의**: 이 프로젝트는 개발/학습 목적으로 제작되었습니다. 프로덕션 환경에서 사용하기 전에 충분한 보안 검토를 진행하세요.

**Warning**: This project is created for development/learning purposes. Please conduct thorough security review before using in production environment.