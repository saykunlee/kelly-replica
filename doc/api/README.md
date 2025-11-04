# API 레퍼런스

Kelly API의 전체 스펙과 도구 활용 가이드입니다.

---

## 📚 문서 목록

### 1. [reference.md](./reference.md) 📘 **핵심 문서**
**Kelly API v1 정의서**
- 모든 API 엔드포인트 상세 스펙
- Request/Response 예시
- HTTP 상태 코드 및 에러 처리
- JavaScript, Python, cURL 예제 코드
- Rate Limiting, ETag, 비동기 작업 가이드

**모든 API를 사용하기 전에 참고하세요.**

### 2. [openapi.json](./openapi.json) 🔧
**OpenAPI 3.0 Specification**
- 머신 판독 가능한 API 스키마
- Postman, Insomnia, Swagger 등에 임포트 가능
- 자동화된 클라이언트 코드 생성 지원
- AI 에이전트 및 자동화 도구용

### 3. [tools-guide.md](./tools-guide.md) 🛠️
**API 도구 활용 가이드**
- Postman으로 OpenAPI 임포트
- Insomnia 사용법
- Swagger UI로 문서 보기
- 클라이언트 코드 자동 생성 (TypeScript, Python, Java)
- AI 에이전트 통합 (GPT, Claude, LangChain)
- CI/CD 통합 및 모니터링

---

## 🚀 빠른 시작

### 개발자라면
1. [reference.md](./reference.md) 읽기
2. 필요한 언어의 예제 코드 복사
3. API 호출 테스트

### 도구를 사용한다면
1. [openapi.json](./openapi.json)을 Postman/Insomnia에 임포트
2. [tools-guide.md](./tools-guide.md)의 설정 방법 따라하기
3. 도구에서 API 테스트

### AI 에이전트 개발자라면
1. [openapi.json](./openapi.json) 다운로드
2. [tools-guide.md](./tools-guide.md)의 AI 통합 섹션 참고
3. Function Calling 또는 LangChain으로 통합

---

## 📋 주요 API 엔드포인트

| 카테고리 | 엔드포인트 | 설명 |
|---------|-----------|------|
| **인증** | `POST /auth/login` | 로그인 |
| | `GET /auth/me` | 사용자 정보 |
| | `POST /auth/refresh` | 토큰 갱신 |
| **회원** | `GET /members` | 회원 목록 |
| | `POST /members` | 회원 생성 |
| | `PATCH /members/{id}` | 회원 수정 |

자세한 내용은 [reference.md](./reference.md)를 참고하세요.

---

## 🔗 관련 링크

- [프론트엔드 가이드](../frontend/) - 프론트엔드 개발자용
- [백엔드 가이드](../backend/) - 백엔드 개발자용
- [테스트 파일](../../tests/api/auth.http) - REST Client 테스트

---

**API를 활용하세요! 🚀**

