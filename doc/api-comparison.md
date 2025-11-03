# API 비교: Legacy vs RESTful

## 📊 한눈에 보는 비교표

| 항목 | Legacy API | RESTful API (v1) |
|-----|-----------|------------------|
| **URL 스타일** | `/api/member-api/getMemberList` | `/api/v1/members` |
| **HTTP 메서드** | 주로 POST만 사용 | GET, POST, PUT, PATCH, DELETE |
| **설계 철학** | RPC (Remote Procedure Call) | Resource-Oriented |
| **버전 관리** | ❌ 없음 | ✅ URI/Query/Header 기반 |
| **응답 표준화** | 부분적 | ✅ 완전 표준화 |
| **HATEOAS** | ❌ 미지원 | ✅ 지원 |
| **ETag 캐싱** | ❌ 미지원 | ✅ 지원 |
| **Rate Limiting** | ❌ 미지원 | ✅ 지원 |
| **CORS** | 부분적 | ✅ 완전 지원 |
| **페이지네이션** | draw/start/length | page/limit (표준화) |
| **비동기 작업** | ❌ 미지원 | ✅ 202 Accepted 패턴 |

---

## 🔄 실제 사용 예시 비교

### 1️⃣ 회원 검색 목록 조회

#### Legacy API
```javascript
// 요청 (kebab-case 메서드명 사용)
POST /api/member-api/get-member-search-list
Content-Type: application/json

{
  "draw": 1,
  "start": 0,
  "length": 10,
  "search": {
    "mem_userid": "test"
  }
}

// URL의 get-member-search-list가 getMemberSearchList() 메서드로 자동 변환됨!

// 응답
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 10,
  "data": [...]
}
```

**참고**: Legacy API는 메서드명을 URL에 그대로 사용할 수도 있지만 (`/api/member-api/getMemberSearchList`), kebab-case를 사용하는 것이 URL 표준에 더 적합합니다.

#### RESTful API
```javascript
// 요청
GET /api/v1/members?page=1&limit=10&mem_userid=test

// 응답
{
  "status": 200,
  "success": true,
  "data": [...],
  "meta": {
    "total": 100,
    "page": 1,
    "limit": 10,
    "totalPages": 10
  },
  "links": [
    {"rel": "self", "href": "/api/v1/members?page=1&limit=10"},
    {"rel": "next", "href": "/api/v1/members?page=2&limit=10"}
  ]
}
```

---

### 2️⃣ 회원 상세 조회

#### Legacy API
```javascript
// 요청
POST /api/member-api/getMemberDetails
Content-Type: application/json

{
  "mem_id": 1
}

// 응답
{
  "status": "200",
  "data": {
    "mem_id": 1,
    "mem_userid": "admin"
  }
}
```

#### RESTful API
```javascript
// 요청
GET /api/v1/members/1
If-None-Match: "abc123"  // ETag 캐싱

// 응답 (변경 있음)
200 OK
ETag: "def456"
{
  "status": 200,
  "success": true,
  "data": {
    "id": 1,
    "mem_userid": "admin",
    "links": [
      {"rel": "self", "href": "/api/v1/members/1", "action": "GET"},
      {"rel": "update", "href": "/api/v1/members/1", "action": "PUT"},
      {"rel": "delete", "href": "/api/v1/members/1", "action": "DELETE"}
    ]
  }
}

// 응답 (변경 없음 - 캐시 사용)
304 Not Modified
ETag: "abc123"
```

---

### 3️⃣ 회원 생성

#### Legacy API
```javascript
// 요청
POST /api/member-api/createMember
{
  "mem_userid": "newuser",
  "mem_password": "password123",
  "mem_email": "new@example.com"
}

// 응답
{
  "status": "200",
  "message": "Member created successfully"
}
```

#### RESTful API
```javascript
// 요청
POST /api/v1/members
Content-Type: application/json

{
  "mem_userid": "newuser",
  "mem_password": "password123",
  "mem_email": "new@example.com"
}

// 응답
201 Created
Location: /api/v1/members/123

{
  "status": 201,
  "success": true,
  "location": "/api/v1/members/123",
  "data": {
    "id": 123,
    "mem_userid": "newuser",
    "mem_email": "new@example.com",
    "links": [
      {"rel": "self", "href": "/api/v1/members/123", "action": "GET"}
    ]
  }
}
```

---

### 4️⃣ 회원 수정

#### Legacy API
```javascript
// 요청
POST /api/member-api/updateMember
{
  "mem_id": 1,
  "mem_username": "수정된이름",
  "mem_email": "updated@example.com"
}

// 응답
{
  "status": "200",
  "message": "회원 정보가 성공적으로 업데이트되었습니다."
}
```

#### RESTful API (전체 수정)
```javascript
// 요청
PUT /api/v1/members/1
If-Match: "abc123"  // 낙관적 동시성 제어
Content-Type: application/json

{
  "mem_userid": "admin",
  "mem_username": "수정된이름",
  "mem_email": "updated@example.com"
}

// 응답 (성공)
200 OK
{
  "status": 200,
  "success": true,
  "data": {...}
}

// 응답 (ETag 불일치)
412 Precondition Failed
{
  "status": 412,
  "success": false,
  "message": "리소스가 이미 수정되었습니다"
}
```

#### RESTful API (부분 수정)
```javascript
// 요청
PATCH /api/v1/members/1
Content-Type: application/json

{
  "mem_username": "수정된이름"  // 변경할 필드만 전송
}

// 응답
200 OK
{
  "status": 200,
  "success": true,
  "data": {...}
}
```

---

### 5️⃣ 회원 삭제

#### Legacy API
```javascript
// 요청
POST /api/member-api/deleteMember
{
  "mem_id": 1
}

// 응답
{
  "status": "200",
  "message": "회원과 관련된 기록이 성공적으로 삭제되었습니다."
}
```

#### RESTful API
```javascript
// 요청
DELETE /api/v1/members/1

// 응답
204 No Content
(응답 본문 없음)
```

---

## 🎯 시나리오별 권장 API

### ✅ Legacy API 사용이 적합한 경우

1. **기존 시스템과의 호환성**
   - 이미 구축된 프론트엔드와 연동
   - 레거시 클라이언트 지원 필요

2. **단순한 RPC 스타일 작업**
   - `checkUserId`, `checkEmail` 같은 유틸리티 함수
   - 복잡한 비즈니스 로직을 수행하는 단일 작업

3. **빠른 프로토타입**
   - 간단한 내부 도구
   - 임시 API

### ✅ RESTful API 사용이 적합한 경우

1. **새로운 프로젝트**
   - 표준 준수가 중요한 프로젝트
   - 외부 API 공개

2. **리소스 중심 작업**
   - CRUD 작업이 명확한 경우
   - 회원, 게시글, 주문 등 엔티티 관리

3. **확장성과 유지보수**
   - 장기 운영 예정
   - API 버전 관리 필요
   - 캐싱, Rate Limiting 필요

4. **모바일 앱/SPA**
   - 네트워크 효율성 중요
   - ETag 캐싱으로 트래픽 절감
   - HATEOAS로 동적 UI 구성

---

## 🔀 마이그레이션 전략

### 1단계: 공존 (현재 상태)

```
Legacy API: /api/member-api/*
RESTful API: /api/v1/*
```

**장점**:
- 기존 시스템 중단 없음
- 점진적 전환 가능

**단점**:
- 두 시스템 유지보수 부담
- 일관성 부족

### 2단계: 점진적 마이그레이션

**우선순위**:
1. 새로운 기능 → RESTful API로 개발
2. 트래픽이 높은 API → RESTful로 전환
3. CRUD가 명확한 API → RESTful로 전환
4. 유틸리티 함수 → Legacy 유지 가능

### 3단계: Adapter 패턴

Legacy API를 RESTful API로 래핑:

```php
// Legacy 클라이언트 호환 레이어
class LegacyApiAdapter extends BaseController
{
    public function getMemberList()
    {
        // Legacy 요청을 RESTful 형식으로 변환
        $page = ($this->request->getPost('start') / 
                 $this->request->getPost('length')) + 1;
        $limit = $this->request->getPost('length');
        
        // RESTful API 호출
        $restfulApi = new MembersController();
        $response = $restfulApi->index();
        
        // RESTful 응답을 Legacy 형식으로 변환
        return $this->convertToLegacyFormat($response);
    }
}
```

---

## 📈 성능 비교

### 네트워크 효율성

#### Legacy API
```
요청 1: POST /api/member-api/getMemberDetails
크기: 1.2 KB

요청 2: POST /api/member-api/getMemberDetails (동일)
크기: 1.2 KB

총: 2.4 KB
```

#### RESTful API (ETag 캐싱)
```
요청 1: GET /api/v1/members/1
크기: 1.2 KB
응답: ETag: "abc123"

요청 2: GET /api/v1/members/1
If-None-Match: "abc123"
크기: 0.2 KB (304 Not Modified, 본문 없음)

총: 1.4 KB (약 42% 절감)
```

### Rate Limiting

#### Legacy API
- 제한 없음 (서버 부하 위험)

#### RESTful API
- 100 requests / 60 seconds (설정 가능)
- 초과 시 429 Too Many Requests
- DDoS 공격 방지

---

## 🧪 테스트 편의성

### Legacy API
```bash
# 항상 POST, 본문에 데이터
curl -X POST http://localhost/api/member-api/getMemberDetails \
  -H "Content-Type: application/json" \
  -d '{"mem_id": 1}'
```

### RESTful API
```bash
# 표준 HTTP 메서드 사용
curl -X GET http://localhost/api/v1/members/1

# 브라우저에서 직접 테스트 가능
# http://localhost/api/v1/members?page=1&limit=10
```

---

## 📊 개발 생산성

### Legacy API

**장점**:
- 빠른 개발 (간단한 구조)
- 직관적인 메서드명

**단점**:
- 메서드마다 라우팅 추가 불필요 (자동)
- 표준화 부족으로 문서화 어려움

### RESTful API

**장점**:
- 자동 CRUD 엔드포인트 생성
- 표준 준수로 학습 비용 낮음
- HATEOAS로 자가 문서화

**단점**:
- 초기 학습 곡선
- 설정이 다소 복잡

---

## 🎓 학습 리소스

### Legacy API 이해하기
- [Legacy API 구조 문서](./legacy-api-structure.md)
- [BaseApiController 소스 코드](../app/Controllers/Base/BaseApiController.php)
- [RouteHandler 소스 코드](../app/Controllers/RouteHandler.php)

### RESTful API 학습하기
- [RESTful API 가이드](./restful-api-guide.md)
- [RESTful API 구조](./restful-api-structure.md)
- [API 설계 원칙](./api-design)

---

## 💡 결론 및 권장사항

### 권장 접근법

1. **신규 프로젝트/기능**
   - ✅ RESTful API 사용
   - 표준 준수로 장기적 이점

2. **기존 시스템**
   - ⚖️ 비용/효과 분석 후 결정
   - 트래픽이 높은 API부터 전환

3. **내부 도구**
   - ⚖️ Legacy API 유지 가능
   - 간단하고 빠른 개발

4. **외부 API**
   - ✅ 반드시 RESTful API
   - 표준 준수 필수

### 최종 권장사항

**현재 프로젝트**는 두 API 시스템이 공존하는 것이 최선입니다:
- Legacy API: 기존 기능 유지
- RESTful API: 신규 기능 개발

점진적으로 중요한 API부터 RESTful로 전환하되, 기존 시스템의 안정성을 최우선으로 고려하세요.

---

## 📚 추가 참고자료

- [Azure API Design Best Practices](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design)
- [REST API Tutorial](https://restfulapi.net/)
- [HTTP Status Codes](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status)
- [Richardson Maturity Model](https://martinfowler.com/articles/richardsonMaturityModel.html)

