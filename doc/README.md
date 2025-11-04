# 📚 Kelly API 문서 센터

Kelly RESTful API 프레임워크의 모든 문서를 제공합니다.

---

## ⚡ 빠른 시작

| 역할 | 시작 문서 | 소요 시간 |
|-----|----------|----------|
| **프론트엔드** | [QUICKSTART.md](./QUICKSTART.md) | 5분 |
| **백엔드** | [backend/restful-guide.md](./backend/restful-guide.md) | 15분 |
| **외부 개발자** | [api/reference.md](./api/reference.md) | 10분 |

---

## 📁 문서 구조

```
doc/
├── README.md                          # 이 문서
├── QUICKSTART.md                      # 5분 빠른 시작
├── CHANGELOG.md                       # 변경 이력
├── ENVIRONMENT_VARIABLES.md           # 환경 변수 가이드
├── SECURITY_IMPLEMENTATION_COMPLETE.md # 보안 구현 상세
├── DOCUMENTATION_RULES.md             # 📘 문서 작성 규칙
│
├── frontend/                          # 프론트엔드 (3개)
│   ├── README.md
│   ├── integration-guide.md           # ⭐ 핵심 통합 가이드
│   └── SECURITY_FEATURES.md           # 🔒 보안 기능 (v2.0)
│
├── api/                               # API 레퍼런스 (4개)
│   ├── README.md
│   ├── reference.md                   # API 정의서
│   ├── openapi.json                   # OpenAPI 스펙
│   └── tools-guide.md                 # 도구 활용
│
├── backend/                           # 백엔드 (4개)
│   ├── README.md
│   ├── restful-guide.md               # RESTful API 개발
│   ├── structure.md                   # 프레임워크 구조
│   └── dynamic-routing.md             # 동적 라우팅
│
├── legacy/                            # Legacy (5개)
│   ├── README.md
│   ├── api-structure.md               # 구조 분석
│   ├── naming-conventions.md          # 네이밍 규칙
│   ├── routing-separation.md          # 라우팅 분리
│   └── comparison.md                  # Legacy vs RESTful
│
├── design/                            # 설계 (4개)
│   ├── README.md
│   ├── api-design                     # 설계 로드맵
│   └── azure/                         # Azure PDF 문서
│
└── scripts/                           # 스크립트 (2개)
    ├── README.md
    └── test-routing.sh
```

**총 문서: 27개** (폴더별 README 포함)

---

## 🎯 역할별 가이드

### 🎨 프론트엔드 개발자

**필수 문서 (2개만!):**

1. **[frontend/integration-guide.md](./frontend/integration-guide.md)** ⭐
   - 빠른 시작부터 완전한 구현까지 모든 것
   - Next.js API Client, Auth Service, Hook 예제
   - 에러 처리, 보안, FAQ
   - **이 문서 하나로 모든 개발 가능**

2. **[frontend/SECURITY_FEATURES.md](./frontend/SECURITY_FEATURES.md)** 🔒
   - v2.0 신규 보안 기능
   - Refresh Token DB 저장, 블랙리스트, 동시 로그인 제한
   - 프론트엔드 구현 가이드
   - **기존 코드는 변경 불필요!**

**선택 문서:**
- [QUICKSTART.md](./QUICKSTART.md) - 5분 빠른 테스트
- [api/reference.md](./api/reference.md) - API 상세 스펙

---

### 🔧 백엔드 개발자

**필수 문서 (2개만!):**

1. **[backend/restful-guide.md](./backend/restful-guide.md)** ⭐
   - RESTful API 개발 가이드
   - 컨트롤러 생성, CRUD 구현
   - ETag, 비동기 작업 등 고급 기능

2. **[SECURITY_IMPLEMENTATION_COMPLETE.md](./SECURITY_IMPLEMENTATION_COMPLETE.md)** 🔒
   - v2.0 보안 기능 구현 상세
   - DB 스키마, 모델, SQL 샘플
   - 배치 작업 설정

**선택 문서:**
- [backend/structure.md](./backend/structure.md) - 프레임워크 내부 구조
- [backend/dynamic-routing.md](./backend/dynamic-routing.md) - 자동 라우팅

---

### 📘 외부 개발자 / AI 에이전트

**필수 문서 (1개만!):**

1. **[api/reference.md](./api/reference.md)** 📘
   - 모든 API 엔드포인트 스펙
   - Request/Response 예시
   - JavaScript, Python, cURL 예제
   - **API 호출에 필요한 모든 정보**

**도구 사용 시:**
- [api/openapi.json](./api/openapi.json) - Postman/Swagger 임포트
- [api/tools-guide.md](./api/tools-guide.md) - 도구 활용 방법

---

## 📖 문서 카테고리별 목적

| 폴더 | 목적 | 문서 수 | 대상 |
|-----|------|---------|------|
| `frontend/` | 프론트엔드 개발 | 3개 | 프론트엔드 개발자 |
| `api/` | API 레퍼런스 | 4개 | 모든 개발자 |
| `backend/` | 백엔드 개발 | 4개 | 백엔드 개발자 |
| `legacy/` | Legacy 분석 | 5개 | 마이그레이션 담당자 |
| `design/` | 설계 원칙 | 4개 | 아키텍트 |
| `scripts/` | 유틸리티 | 2개 | DevOps |

---

## 🔗 주요 문서 바로가기

### 가장 많이 찾는 문서

1. **[QUICKSTART.md](./QUICKSTART.md)** - 5분 빠른 시작
2. **[frontend/integration-guide.md](./frontend/integration-guide.md)** - 프론트엔드 통합
3. **[api/reference.md](./api/reference.md)** - API 레퍼런스
4. **[backend/restful-guide.md](./backend/restful-guide.md)** - 백엔드 개발

### 설정 및 배포

5. **[ENVIRONMENT_VARIABLES.md](./ENVIRONMENT_VARIABLES.md)** - 환경 설정
6. **[SECURITY_IMPLEMENTATION_COMPLETE.md](./SECURITY_IMPLEMENTATION_COMPLETE.md)** - 보안 구현

### 참고

7. **[CHANGELOG.md](./CHANGELOG.md)** - 변경 이력
8. **[DOCUMENTATION_RULES.md](./DOCUMENTATION_RULES.md)** 📘 - 문서 작성 규칙

---

## 💡 문서 활용 팁

### 시간이 없다면 (각 역할별 1개 문서만)

- **프론트엔드**: [frontend/integration-guide.md](./frontend/integration-guide.md)
- **백엔드**: [backend/restful-guide.md](./backend/restful-guide.md)
- **외부 개발자**: [api/reference.md](./api/reference.md)

### 체계적으로 학습하려면

1. 해당 역할별 폴더의 README.md 읽기
2. 추천 순서대로 문서 읽기
3. 예제 코드 실습
4. 테스트 파일로 확인 ([../tests/api/auth.http](../tests/api/auth.http))

### 문제 해결

1. FAQ 섹션 확인 (각 문서 하단)
2. [SECURITY_IMPLEMENTATION_COMPLETE.md](./SECURITY_IMPLEMENTATION_COMPLETE.md) 참고
3. SQL 샘플 쿼리 확인

---

## 🔒 v2.0 보안 업데이트 (2025-11-04)

### 신규 보안 기능

- ✅ Refresh Token DB 저장
- ✅ 토큰 블랙리스트
- ✅ 동시 로그인 제한
- ✅ 로그인 이력 추적
- ✅ 무차별 대입 방지

### 관련 문서

- [frontend/SECURITY_FEATURES.md](./frontend/SECURITY_FEATURES.md) - 프론트엔드 보안 가이드
- [SECURITY_IMPLEMENTATION_COMPLETE.md](./SECURITY_IMPLEMENTATION_COMPLETE.md) - 구현 상세
- [ENVIRONMENT_VARIABLES.md](./ENVIRONMENT_VARIABLES.md) - 환경 설정

---

## 📝 문서 업데이트 이력

- **2025-11-04**: 문서 통합 및 간소화 📘
  - auth-guide.md → integration-guide.md로 통합
  - implementation-summary.md → SECURITY_FEATURES.md로 통합
  - FINAL_SUMMARY.md 삭제 (중복)
  - DOCUMENTATION_RULES.md 추가 (문서 작성 규칙)
  - 총 문서 수: 30개 → 27개
  
- **2025-11-04**: JWT 보안 기능 대폭 강화 🔒 (v2.0)
  - Refresh Token DB 저장, 토큰 블랙리스트
  - 동시 로그인 제한, 로그인 이력 추적
  - DB 스키마 및 3개 모델 추가
  
- **2025-11-04**: 문서 구조 대규모 개편 🎯
  - 6개 카테고리 폴더로 재구성
  - 각 폴더에 README.md 추가

---

## 📞 지원

### 문서 관련
- **작성 규칙**: [DOCUMENTATION_RULES.md](./DOCUMENTATION_RULES.md)
- **문서 구조**: 각 폴더의 README.md 참고

### API 질문
- **API 스펙**: [api/reference.md](./api/reference.md)
- **테스트**: [../tests/api/auth.http](../tests/api/auth.http)

### 보안/배포
- **보안 기능**: [SECURITY_IMPLEMENTATION_COMPLETE.md](./SECURITY_IMPLEMENTATION_COMPLETE.md)
- **환경 설정**: [ENVIRONMENT_VARIABLES.md](./ENVIRONMENT_VARIABLES.md)

---

**Kelly API로 멋진 애플리케이션을 만드세요! 🚀**

