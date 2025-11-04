# API 설계 문서

Kelly API 설계 원칙 및 베스트 프랙티스 문서입니다.

---

## 📚 문서 목록

### 1. [api-design](./api-design) 📐
**Azure API Design Best Practices 기반 로드맵**
- API 설계 원칙 및 개념
- 리소스 및 URI 설계
- HTTP 메서드 및 응답 처리
- 데이터 최적화 및 동시성 관리
- API 운영, 보안, 테스트

**Kelly API 설계의 철학적 기반입니다.**

### 2. [azure/design-best-practices.pdf](./azure/design-best-practices.pdf)
**Web API Design Best Practices - Azure**
- Microsoft Azure의 웹 API 설계 모범 사례 가이드 (영문)
- 원본 문서

### 3. [azure/implementation.pdf](./azure/implementation.pdf)
**Web API Implementation - Azure**
- Microsoft Azure의 웹 API 구현 가이드 (영문)
- 원본 문서

---

## 🎯 설계 원칙

Kelly API는 다음 원칙을 따릅니다:

### 1. RESTful 원칙
- 리소스 중심 설계
- 표준 HTTP 메서드 사용
- Stateless 아키텍처

### 2. 일관성
- 일관된 네이밍 규칙
- 표준화된 응답 형식
- 에러 처리 일관성

### 3. 확장성
- 버전 관리 (`/api/v1`)
- 페이지네이션
- 비동기 작업 지원

### 4. 보안
- JWT 기반 인증
- HTTPS 필수 (프로덕션)
- Rate Limiting

### 5. 성능
- ETag 캐싱
- 조건부 요청
- 효율적인 쿼리

---

## 📖 설계 가이드라인

### URI 설계

```
✅ 좋은 예:
GET /api/v1/members          # 회원 목록
GET /api/v1/members/123      # 특정 회원
POST /api/v1/members         # 회원 생성
PATCH /api/v1/members/123    # 회원 부분 수정

❌ 나쁜 예:
GET /api/v1/getMembers
GET /api/v1/member_list
POST /api/v1/createMember
```

### HTTP 메서드

| 메서드 | 용도 | 멱등성 |
|--------|------|--------|
| GET | 조회 | ✅ |
| POST | 생성 | ❌ |
| PUT | 전체 수정 | ✅ |
| PATCH | 부분 수정 | ❌ |
| DELETE | 삭제 | ✅ |

### 응답 형식

```json
{
  "status": 200,
  "success": true,
  "data": { ... },
  "meta": { ... },
  "links": [ ... ]
}
```

### 에러 응답

```json
{
  "status": 400,
  "success": false,
  "message": "에러 메시지",
  "errorCode": "ERROR_CODE",
  "errors": [ ... ]
}
```

---

## 🔗 참고 자료

### 공식 문서
- [Azure API Design Best Practices](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design)
- [Azure API Implementation](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-implementation)
- [Richardson Maturity Model](https://martinfowler.com/articles/richardsonMaturityModel.html)
- [REST API Tutorial](https://restfulapi.net/)

### 내부 문서
- [백엔드 가이드](../backend/) - 구현 방법
- [API 레퍼런스](../api/) - 실제 API 스펙
- [Legacy 비교](../legacy/) - 기존 시스템과 비교

---

## 💡 설계 체크리스트

새로운 API를 설계할 때:

- [ ] 리소스를 명사로 표현했는가?
- [ ] 적절한 HTTP 메서드를 사용했는가?
- [ ] URI가 계층 구조를 반영하는가?
- [ ] 버전 관리가 포함되어 있는가?
- [ ] 에러 처리가 일관적인가?
- [ ] 페이지네이션이 필요한가?
- [ ] 인증/권한이 필요한가?
- [ ] 성능 최적화가 고려되었는가?
- [ ] 문서화가 충분한가?

---

**좋은 API를 설계하세요! 🎨**

