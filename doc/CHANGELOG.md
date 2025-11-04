# Kelly API 변경 이력

전체 API 시스템의 주요 변경사항을 기록합니다.

---

## v2.0.0 (2025-11-04) 🔒

### 🎯 주요 변경사항: JWT 보안 기능 대폭 강화

#### 신규 기능

1. **Refresh Token DB 저장**
   - `refresh_tokens` 테이블 추가
   - RefreshTokenModel 구현
   - 로그아웃 시 즉시 무효화 가능
   - 동시 로그인 제한 지원

2. **토큰 블랙리스트**
   - `token_blacklist` 테이블 추가
   - TokenBlacklistModel 구현
   - 로그아웃 후 토큰 즉시 차단
   - 강제 로그아웃 기능

3. **로그인 이력 추적**
   - `login_history` 테이블 추가
   - LoginHistoryModel 구현
   - 모든 로그인 시도 기록
   - 무차별 대입 공격 자동 차단

4. **신규 API 엔드포인트**
   - `POST /auth/force-logout` - 강제 로그아웃 (관리자)
   - `GET /auth/sessions` - 활성 세션 조회

#### 파일 변경

**신규 파일 (6개):**
- `app/Database/Migrations/2025-11-04-000001_CreateAuthTokenTables.sql`
- `app/Models/RefreshTokenModel.php`
- `app/Models/TokenBlacklistModel.php`
- `app/Models/LoginHistoryModel.php`
- `doc/frontend/SECURITY_FEATURES.md`
- `doc/SECURITY_IMPLEMENTATION_COMPLETE.md`

**수정 파일 (4개):**
- `app/Controllers/Api/V1/AuthController.php`
- `app/Filters/JwtAuthFilter.php`
- `app/Config/Routes.php`
- `doc/frontend/README.md`

#### 호환성

- ✅ **기존 프론트엔드 호환**: 코드 변경 없이 작동
- ✅ **기존 API 호환**: 모든 기존 API 정상 작동
- ✅ **선택적 활성화**: 환경변수로 제어 가능

---

## v1.1.0 (2025-11-04) 📁

### 문서 구조 대규모 개편

#### 변경사항

1. **폴더 구조 재구성**
   - 평면적 18개 파일 → 6개 카테고리 폴더
   - `frontend/`, `api/`, `backend/`, `legacy/`, `design/`, `scripts/`

2. **신규 문서**
   - `QUICKSTART.md` - 5분 빠른 시작
   - 각 폴더별 README.md (6개)
   - `REORGANIZATION_SUMMARY.md`

3. **파일명 단순화**
   - `frontend-integration-guide.md` → `frontend/integration-guide.md`
   - `restful-api-guide.md` → `backend/restful-guide.md`

#### 효과

- ✅ 찾기 쉬움: 역할별 폴더 구조
- ✅ 배우기 쉬움: 학습 경로 안내
- ✅ 시작하기 쉬움: QUICKSTART.md

---

## v1.0.0 (2025-11-03) 🎉

### 초기 릴리스: JWT 인증 시스템

#### 주요 기능

1. **JWT 인증 API**
   - `POST /auth/login` - 로그인
   - `GET /auth/me` - 사용자 정보
   - `POST /auth/refresh` - 토큰 갱신
   - `POST /auth/logout` - 로그아웃
   - `POST /auth/change-password` - 비밀번호 변경
   - `POST /auth/verify` - 토큰 검증

2. **핵심 컴포넌트**
   - JwtHandler Library
   - JwtAuthFilter
   - AuthController

3. **문서**
   - frontend-auth-guide.md
   - api-reference.md
   - openapi.json
   - tests/api/auth.http (40+ 테스트)

---

## 이전 버전

### 2025-11-01
- RESTful API 프레임워크 초기 구현
- Members API 구현
- 동적 라우팅 시스템
- Legacy API 분석 문서

---

## 다음 버전 계획

### v2.1.0 (예정)
- [ ] Redis 캐싱 지원
- [ ] 세션별 로그아웃 기능
- [ ] 이메일 알림 (새로운 위치 로그인)
- [ ] 2FA (Two-Factor Authentication)

### v3.0.0 (예정)
- [ ] GraphQL API 지원
- [ ] WebSocket 인증
- [ ] API Key 인증 방식 추가
- [ ] OAuth 2.0 지원

---

**문서 버전:** 2.0.0  
**마지막 업데이트:** 2025-11-04


