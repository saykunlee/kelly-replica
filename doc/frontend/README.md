# 프론트엔드 개발자 가이드

Kelly API를 프론트엔드에서 사용하기 위한 통합 문서입니다.

---

## 📚 문서 목록 (3개)

### 1. [integration-guide.md](./integration-guide.md) ⭐ **핵심 문서**
**프론트엔드 통합 가이드 - 모든 것이 여기 있습니다**

**포함 내용:**
- ✅ 빠른 시작 체크리스트
- ✅ 인증 시스템 전체 흐름
- ✅ JWT 로그인/로그아웃 구현
- ✅ Next.js/React 완전한 코드 예제
  - API Client (토큰 자동 갱신)
  - Auth Service
  - useAuth Hook
  - 로그인 페이지
  - Protected Route 미들웨어
- ✅ 주요 API 엔드포인트 요약
- ✅ 에러 처리 및 보안 가이드
- ✅ FAQ

**🎯 이 문서 하나만 읽어도 모든 개발이 가능합니다!**

---

### 2. [SECURITY_FEATURES.md](./SECURITY_FEATURES.md) 🔒 **v2.0 보안 업데이트**
**신규 보안 기능 가이드**

**포함 내용:**
- 🔒 Refresh Token DB 저장
- 🔒 토큰 블랙리스트 (즉시 무효화)
- 🔒 동시 로그인 제한 (옵션)
- 🔒 로그인 이력 추적 & 무차별 대입 방지
- 🔒 활성 세션 관리 API
- 🔒 프론트엔드 구현 가이드
- 🔒 새로운 에러 코드 처리

**💡 보안 기능 추가사항을 확인하세요!**  
**기존 코드는 변경 없이 그대로 작동합니다.**

---

### 3. [../QUICKSTART.md](../QUICKSTART.md) ⚡ **5분 빠른 시작**
**즉시 시작 가이드 (상위 폴더)**

**포함 내용:**
- 30초 로그인 테스트
- 3분 API Client 설정
- 바로 복사 가능한 코드
- 자주 묻는 질문

**🚀 가장 빠르게 시작하고 싶다면!**

---

## 🎯 학습 순서

### 완전 초보자
```
QUICKSTART.md (5분)
    ↓
integration-guide.md (30분)
    ↓
SECURITY_FEATURES.md (15분)
    ↓
테스트 파일 실습 (tests/api/auth.http)
```

### 경험자
```
integration-guide.md (20분)
    ↓
SECURITY_FEATURES.md (10분)
    ↓
필요 시 API 레퍼런스 참고
```

### v2.0 업데이트 확인
```
SECURITY_FEATURES.md (10분)
    ↓
기존 코드에 개선사항 선택적 적용
```

---

## 🆕 v2.0 주요 변경사항 (2025-11-04)

### 새로운 보안 기능 추가

- ✅ **Refresh Token DB 저장** - 로그아웃 시 즉시 무효화
- ✅ **토큰 블랙리스트** - 강제 로그아웃 가능
- ✅ **동시 로그인 제한** - 계정 도용 방지 (옵션)
- ✅ **로그인 이력 추적** - 보안 감사 로그
- ✅ **무차별 대입 방지** - IP 자동 차단

### 신규 API 엔드포인트

- 🆕 `POST /auth/force-logout` - 강제 로그아웃 (관리자)
- 🆕 `GET /auth/sessions` - 활성 세션 조회

### 호환성

**✅ 기존 프론트엔드 코드는 변경 없이 그대로 작동합니다!**

자세한 내용은 [SECURITY_FEATURES.md](./SECURITY_FEATURES.md)를 참고하세요.

---

## 🔗 관련 문서

- [../api/reference.md](../api/reference.md) - API 전체 스펙
- [../../tests/api/auth.http](../../tests/api/auth.http) - API 테스트
- [../backend/](../backend/) - 백엔드 개발자용

---

## 💡 빠른 참조

### 주요 API

| 엔드포인트 | 메서드 | 인증 | 설명 |
|----------|--------|------|------|
| `/auth/login` | POST | ❌ | 로그인 |
| `/auth/me` | GET | ✅ | 사용자 정보 |
| `/auth/refresh` | POST | ❌ | 토큰 갱신 |
| `/auth/logout` | POST | ✅ | 로그아웃 |
| `/auth/sessions` | GET | ✅ | 활성 세션 (v2.0) |
| `/members` | GET | ✅ | 회원 목록 |

### 주요 에러 코드

| 코드 | 메시지 | 처리 |
|-----|--------|------|
| 401 | TOKEN_EXPIRED | 자동 갱신 |
| 401 | TOKEN_REVOKED | 로그인 필요 |
| 429 | TOO_MANY_REQUESTS | 대기 후 재시도 |

---

**프론트엔드 개발을 시작하세요! 🎉**

