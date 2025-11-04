# 백엔드 개발자 가이드

Kelly RESTful API 프레임워크 개발 가이드입니다.

---

## 📚 문서 목록

### 1. [restful-guide.md](./restful-guide.md) ⭐ **시작 문서**
**RESTful API 프레임워크 사용 가이드**
- 빠른 시작 가이드
- API 엔드포인트 개발 방법
- HTTP 메서드 가이드 (GET, POST, PUT, PATCH, DELETE)
- 고급 기능 (ETag, 비동기 작업, Rate Limiting)
- 예제 코드 및 테스트

**새로운 API를 개발할 때 참고하세요.**

### 2. [structure.md](./structure.md) 🏗️
**RESTful API 프레임워크 구조**
- 프로젝트 구조 및 아키텍처
- 주요 컴포넌트 설명
  - RestApiController
  - RestApiResponse
  - RestfulRouteHandler
- 확장 가이드
- 성능 최적화

**프레임워크 내부를 이해하고 싶다면 읽으세요.**

### 3. [dynamic-routing.md](./dynamic-routing.md) 🚀
**동적 라우팅 가이드**
- 자동 라우팅 기능
- 라우팅 우선순위
- 네이밍 규칙
- 커스텀 라우트 설정
- 성능 고려사항

**컨트롤러만 만들면 자동으로 라우팅됩니다!**

---

## 🚀 빠른 시작

### 새로운 API 개발
1. [restful-guide.md](./restful-guide.md) 읽기
2. 컨트롤러 생성 예제 따라하기
3. [dynamic-routing.md](./dynamic-routing.md)로 라우팅 이해

### 프레임워크 커스터마이징
1. [structure.md](./structure.md)로 구조 파악
2. 확장 포인트 확인
3. 새로운 기능 구현

---

## 💡 개발 팁

### RESTful API 개발 체크리스트

- [ ] RestApiController 상속
- [ ] 모델 설정 (`$modelName`, `$resourceName`)
- [ ] 유효성 검증 규칙 정의
- [ ] 비즈니스 로직 구현
- [ ] 테스트 작성
- [ ] 문서화

### 주요 메서드

```php
// 기본 CRUD는 자동 제공
index()    // GET /resources
show($id)  // GET /resources/{id}
create()   // POST /resources
update($id)// PUT /resources/{id}
patch($id) // PATCH /resources/{id}
delete($id)// DELETE /resources/{id}

// 커스텀 액션은 직접 구현
test()     // GET /resources/test
```

---

## 🔗 관련 링크

- [API 레퍼런스](../api/) - API 스펙 확인
- [Legacy 문서](../legacy/) - 기존 시스템과 비교
- [설계 문서](../design/) - API 설계 원칙

---

**RESTful API를 개발하세요! 🛠️**

