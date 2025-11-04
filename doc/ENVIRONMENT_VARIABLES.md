# Kelly API 환경 변수 설정 가이드

**Version:** 2.0.0  
**Last Updated:** 2025-11-04

---

## 필수 환경 변수

### JWT 설정

```env
# JWT Secret Key (필수!)
# 프로덕션에서는 반드시 강력한 랜덤 키로 변경
# 생성 방법: openssl rand -base64 32
JWT_SECRET_KEY=your-secret-key-please-change-this-in-production

# Access Token 만료 시간 (초)
# 기본: 3600 (1시간)
JWT_ACCESS_EXPIRE=3600

# Refresh Token 만료 시간 (초)
# 기본: 604800 (7일)
JWT_REFRESH_EXPIRE=604800
```

---

## 보안 기능 환경 변수 (v2.0)

### 동시 로그인 제한

```env
# 동시 로그인 제한 활성화
# true: 하나의 디바이스만 로그인 가능
# false: 여러 디바이스 동시 로그인 가능 (기본값)
LIMIT_CONCURRENT_SESSIONS=false
```

**사용 시나리오:**

| 값 | 설명 | 권장 |
|----|------|------|
| `true` | 단일 세션만 허용 | 금융, 기업용, 라이선스 서비스 |
| `false` | 멀티 세션 허용 | 일반 웹 서비스, 소셜 미디어 |

### 로그인 보안

```env
# 로그인 실패 최대 시도 횟수 (기본: 5회)
LOGIN_MAX_ATTEMPTS=5

# IP 차단 유지 시간 (분) (기본: 60분)
LOGIN_LOCKOUT_TIME=60
```

**동작 방식:**
```
동일 IP에서 60분 내 5회 로그인 실패
    ↓
자동으로 IP 차단
    ↓
60분 후 자동 해제
```

---

## 데이터베이스 설정

```env
database.default.hostname=localhost
database.default.database=kelly
database.default.username=root
database.default.password=your_password
database.default.DBDriver=MySQLi
database.default.port=3306
```

---

## CORS 설정

```env
# 허용할 Origin (쉼표로 구분)
# 개발 환경
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:3001

# 프로덕션 환경
# CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
```

---

## Rate Limiting

```env
# Rate Limit 활성화
RATE_LIMIT_ENABLED=true

# 시간 창 (초) - 기본: 60초
RATE_LIMIT_WINDOW=60

# 최대 요청 수 - 기본: 100회
RATE_LIMIT_MAX_REQUESTS=100
```

---

## 유지보수 (배치 작업)

```env
# 만료된 Refresh Token 정리 주기 (일)
# 기본: 30일
CLEANUP_REFRESH_TOKENS_DAYS=30

# 오래된 로그인 이력 아카이브 주기 (일)
# 기본: 90일
ARCHIVE_LOGIN_HISTORY_DAYS=90
```

---

## 환경별 설정 예시

### 개발 환경 (.env.development)

```env
CI_ENVIRONMENT=development

JWT_SECRET_KEY=dev-secret-key-not-for-production
JWT_ACCESS_EXPIRE=3600
JWT_REFRESH_EXPIRE=604800

LIMIT_CONCURRENT_SESSIONS=false
LOGIN_MAX_ATTEMPTS=10
LOGIN_LOCKOUT_TIME=30

CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:3001

RATE_LIMIT_ENABLED=false
```

### 스테이징 환경 (.env.staging)

```env
CI_ENVIRONMENT=staging

JWT_SECRET_KEY=staging-secret-key-change-this
JWT_ACCESS_EXPIRE=3600
JWT_REFRESH_EXPIRE=604800

LIMIT_CONCURRENT_SESSIONS=true
LOGIN_MAX_ATTEMPTS=5
LOGIN_LOCKOUT_TIME=60

CORS_ALLOWED_ORIGINS=https://staging.yourdomain.com

RATE_LIMIT_ENABLED=true
RATE_LIMIT_MAX_REQUESTS=100
```

### 프로덕션 환경 (.env.production)

```env
CI_ENVIRONMENT=production

# 강력한 랜덤 키 사용 필수!
JWT_SECRET_KEY=CHANGE_THIS_TO_RANDOM_STRING_IN_PRODUCTION
JWT_ACCESS_EXPIRE=3600
JWT_REFRESH_EXPIRE=604800

# 프로덕션에서는 보안 강화
LIMIT_CONCURRENT_SESSIONS=true
LOGIN_MAX_ATTEMPTS=5
LOGIN_LOCKOUT_TIME=60

# 실제 도메인만 허용
CORS_ALLOWED_ORIGINS=https://yourdomain.com

RATE_LIMIT_ENABLED=true
RATE_LIMIT_MAX_REQUESTS=100

# 정기적인 정리
CLEANUP_REFRESH_TOKENS_DAYS=30
ARCHIVE_LOGIN_HISTORY_DAYS=90
```

---

## 보안 체크리스트

프로덕션 배포 전 확인:

### JWT 설정
- [ ] `JWT_SECRET_KEY`를 강력한 랜덤 키로 변경
- [ ] Secret Key를 Git에 커밋하지 않음
- [ ] 적절한 토큰 만료 시간 설정

### 보안 기능
- [ ] 서비스 특성에 맞게 `LIMIT_CONCURRENT_SESSIONS` 설정
- [ ] 적절한 로그인 시도 제한 설정
- [ ] HTTPS 사용 (필수)

### CORS
- [ ] 허용할 도메인만 명시
- [ ] 와일드카드(`*`) 사용하지 않음

### 데이터베이스
- [ ] 강력한 DB 비밀번호 사용
- [ ] DB 접근 제한 (방화벽)
- [ ] 정기 백업 설정

---

## 문제 해결

### JWT_SECRET_KEY 생성

```bash
# OpenSSL 사용
openssl rand -base64 32

# PHP 사용
php -r "echo bin2hex(random_bytes(32));"

# 온라인 도구
# https://generate-random.org/api-token-generator
```

### 환경 변수 확인

```php
<?php
// 임시 테스트 파일
echo "JWT_SECRET_KEY: " . (getenv('JWT_SECRET_KEY') ?: '설정되지 않음') . "\n";
echo "JWT_ACCESS_EXPIRE: " . (getenv('JWT_ACCESS_EXPIRE') ?: '기본값: 3600') . "\n";
echo "LIMIT_CONCURRENT_SESSIONS: " . (getenv('LIMIT_CONCURRENT_SESSIONS') ?: 'false') . "\n";
```

---

## 참고 문서

- [보안 기능 가이드](./frontend/SECURITY_FEATURES.md)
- [보안 구현 완료 보고서](./SECURITY_IMPLEMENTATION_COMPLETE.md)
- [CodeIgniter 4 환경 변수](https://codeigniter4.github.io/userguide/general/configuration.html)

---

**문서 버전:** 2.0.0  
**작성자:** Kelly Development Team


