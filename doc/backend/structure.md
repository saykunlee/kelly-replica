# RESTful API 프레임워크 구조

## 📁 프로젝트 구조

```
kelly-replica/
├── app/
│   ├── Libraries/
│   │   └── RestApi/
│   │       ├── RestApiResponse.php       # 표준화된 응답 생성 클래스
│   │       ├── RestApiController.php     # RESTful API 기본 컨트롤러
│   │       └── AsyncJobHandler.php       # 비동기 작업 처리 핸들러
│   │
│   ├── Filters/
│   │   ├── ApiVersionFilter.php          # API 버전 관리 필터
│   │   ├── RateLimitFilter.php           # Rate Limiting 필터
│   │   ├── ETagFilter.php                # ETag 캐싱 필터
│   │   └── RestApiCorsFilter.php         # CORS 필터
│   │
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           ├── JobsController.php            # 비동기 작업 상태 조회
│   │           ├── MembersController.php         # 회원 리소스 API
│   │           └── ExampleResourceController.php # 예제 리소스 API
│   │
│   ├── Helpers/
│   │   └── api_helper.php                # API 헬퍼 함수
│   │
│   └── Config/
│       ├── RestApi.php                   # RESTful API 전역 설정
│       ├── Filters.php                   # 필터 등록 (수정됨)
│       └── Routes.php                    # 라우팅 설정 (수정됨)
│
└── doc/
    ├── README.md                         # 문서 디렉토리 안내
    ├── api-design                        # API 디자인 가이드
    ├── restful-api-guide.md              # API 사용 가이드
    ├── restful-api-structure.md          # 이 문서
    └── *.pdf                             # Azure API 가이드 (PDF)
```

---

## 🏗️ 아키텍처 개요

### 계층 구조

```
┌─────────────────────────────────────────┐
│          클라이언트 (Client)             │
└─────────────────┬───────────────────────┘
                  │ HTTP Request/Response
┌─────────────────▼───────────────────────┐
│          필터 체인 (Filters)             │
│  ┌───────────────────────────────────┐  │
│  │ ApiVersionFilter  │ RateLimitFilter│ │
│  │ ETagFilter       │ RestApiCorsFilter│ │
│  └───────────────────────────────────┘  │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│      컨트롤러 (RestApiController)        │
│  ┌───────────────────────────────────┐  │
│  │ index()   - 목록 조회             │  │
│  │ show()    - 단일 조회             │  │
│  │ create()  - 생성                  │  │
│  │ update()  - 전체 수정             │  │
│  │ patch()   - 부분 수정             │  │
│  │ delete()  - 삭제                  │  │
│  └───────────────────────────────────┘  │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│      응답 생성 (RestApiResponse)         │
│  ┌───────────────────────────────────┐  │
│  │ success()        - 성공 응답      │  │
│  │ error()          - 에러 응답      │  │
│  │ paginated()      - 페이지네이션   │  │
│  │ created()        - 생성 응답      │  │
│  │ accepted()       - 비동기 수락    │  │
│  │ createLink()     - HATEOAS 링크   │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

---

## 📦 주요 컴포넌트

### 1. RestApiResponse 클래스

**위치**: `app/Libraries/RestApi/RestApiResponse.php`

**역할**: 표준화된 API 응답 생성 및 HATEOAS 링크 관리

**주요 메서드**:
- `success()` - 성공 응답 생성
- `error()` - 에러 응답 생성
- `paginated()` - 페이지네이션 응답
- `created()` - 201 Created 응답
- `accepted()` - 202 Accepted 응답
- `createLink()` - HATEOAS 링크 생성

**응답 형식**:
```json
{
  "status": 200,
  "success": true,
  "data": { ... },
  "links": [ ... ],
  "meta": { ... }
}
```

### 2. RestApiController 클래스

**위치**: `app/Libraries/RestApi/RestApiController.php`

**역할**: RESTful API 컨트롤러 기본 클래스

**자동 구현 기능**:
- ✅ CRUD 메서드 (index, show, create, update, patch, delete)
- ✅ 페이지네이션 (page, limit 파라미터)
- ✅ 필터링 (쿼리 파라미터 기반)
- ✅ 정렬 (sort, order 파라미터)
- ✅ HATEOAS 링크 자동 생성
- ✅ ETag 생성 및 검증
- ✅ 유효성 검증

**사용 방법**:
```php
class ProductsController extends RestApiController
{
    protected $modelName = 'App\Models\ProductModel';
    protected $resourceName = 'products';
    protected $apiVersion = 'v1';
    
    protected function getValidationRules(string $action): array
    {
        // 유효성 검증 규칙 정의
    }
}
```

### 3. AsyncJobHandler 클래스

**위치**: `app/Libraries/RestApi/AsyncJobHandler.php`

**역할**: 비동기 작업 처리 및 상태 관리

**주요 메서드**:
- `createJob()` - 비동기 작업 생성
- `getJobStatus()` - 작업 상태 조회
- `updateProgress()` - 진행 상태 업데이트
- `completeJob()` - 작업 완료 처리
- `failJob()` - 작업 실패 처리

**워크플로우**:
```
Client                    Server
  │                         │
  ├─ POST /bulk-import ────>│
  │<── 202 Accepted ────────┤ (job_123 생성)
  │    Location: /jobs/job_123
  │                         │
  ├─ GET /jobs/job_123 ───>│
  │<── 200 OK ──────────────┤ (status: processing, progress: 50%)
  │                         │
  ├─ GET /jobs/job_123 ───>│
  │<── 303 See Other ───────┤ (status: completed)
  │    Location: /result/456
```

---

## 🔧 필터 (Filters)

### 1. ApiVersionFilter

**역할**: API 버전 관리 및 검증

**지원 방식**:
- URI: `/api/v1/products`
- Query: `/api/products?version=1`
- Header: `api-version: 1.0`

### 2. RateLimitFilter

**역할**: API 요청 빈도 제한

**기능**:
- 슬라이딩 윈도우 방식
- 클라이언트별 제한 (IP 또는 사용자 ID)
- Rate Limit 헤더 반환

**응답 헤더**:
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1699999999
```

### 3. ETagFilter

**역할**: ETag 기반 캐싱 및 조건부 요청

**기능**:
- 자동 ETag 생성
- If-None-Match 처리 (304 Not Modified)
- If-Match 처리 (낙관적 동시성 제어)

### 4. RestApiCorsFilter

**역할**: Cross-Origin Resource Sharing 지원

**기능**:
- OPTIONS Preflight 요청 처리
- CORS 헤더 자동 추가
- 허용 오리진/메서드/헤더 설정

---

## 🌐 API 엔드포인트

### RESTful 리소스 엔드포인트

| 메서드 | URI | 액션 | 설명 |
|-------|-----|------|------|
| GET | `/api/v1/members` | index | 회원 목록 조회 |
| GET | `/api/v1/members/{id}` | show | 회원 상세 조회 |
| POST | `/api/v1/members` | create | 회원 생성 |
| PUT | `/api/v1/members/{id}` | update | 회원 전체 수정 |
| PATCH | `/api/v1/members/{id}` | patch | 회원 부분 수정 |
| DELETE | `/api/v1/members/{id}` | delete | 회원 삭제 |

### 비동기 작업 엔드포인트

| 메서드 | URI | 설명 |
|-------|-----|------|
| GET | `/api/v1/jobs/{jobId}` | 작업 상태 조회 |
| DELETE | `/api/v1/jobs/{jobId}` | 작업 취소 |

---

## ⚙️ 설정

### RestApi.php 설정

**위치**: `app/Config/RestApi.php`

```php
public array $supportedVersions = ['v1'];
public string $defaultVersion = 'v1';

public array $rateLimit = [
    'enabled' => true,
    'windowSize' => 60,
    'maxRequests' => 100,
];

public bool $enableETag = true;
public int $defaultCacheMaxAge = 300;

public array $pagination = [
    'defaultLimit' => 20,
    'maxLimit' => 100,
];
```

### Filters.php 설정

**위치**: `app/Config/Filters.php`

필터 등록:
```php
public array $filters = [
    'apiVersion' => ['before' => ['api/*'], 'after' => ['api/*']],
    'rateLimit' => ['before' => ['api/*'], 'after' => ['api/*']],
    'etag' => ['after' => ['api/*']],
    'apiCors' => ['before' => ['api/*'], 'after' => ['api/*']],
];
```

---

## 🔍 HATEOAS 구현

### HATEOAS란?

**Hypermedia as the Engine of Application State**

클라이언트가 응답에 포함된 링크를 통해 API를 동적으로 탐색할 수 있도록 함.

### 링크 형식

```json
{
  "rel": "self",
  "href": "http://example.com/api/v1/products/1",
  "action": "GET",
  "types": ["application/json"]
}
```

### 주요 관계 (rel)

- `self` - 자기 자신
- `collection` - 컬렉션
- `next` / `prev` - 페이지네이션
- `update` - 업데이트
- `patch` - 부분 수정
- `delete` - 삭제
- `first` / `last` - 첫/마지막 페이지

---

## 🛡️ 보안

### 구현된 보안 기능

1. **Rate Limiting** - DDoS 공격 방지
2. **CORS** - Cross-Origin 요청 제어
3. **Input Validation** - 입력 데이터 검증
4. **ETag** - 데이터 무결성 보장

### 권장 추가 보안

1. **HTTPS** - TLS 1.2 이상 필수
2. **Authentication** - JWT 또는 OAuth 2.0
3. **Authorization** - RBAC 권한 관리
4. **SQL Injection 방지** - Prepared Statement 사용 (CodeIgniter 기본)

---

## 📊 HTTP 상태 코드 사용

### 성공 응답

- `200 OK` - 요청 성공
- `201 Created` - 리소스 생성 성공
- `202 Accepted` - 비동기 작업 수락
- `204 No Content` - 삭제 성공 (본문 없음)
- `304 Not Modified` - 캐시된 리소스 사용

### 클라이언트 에러

- `400 Bad Request` - 잘못된 요청
- `401 Unauthorized` - 인증 실패
- `403 Forbidden` - 권한 없음
- `404 Not Found` - 리소스 없음
- `409 Conflict` - 리소스 충돌
- `412 Precondition Failed` - ETag 불일치
- `422 Unprocessable Entity` - 유효성 검증 실패
- `429 Too Many Requests` - Rate Limit 초과

### 서버 에러

- `500 Internal Server Error` - 서버 오류
- `503 Service Unavailable` - 서비스 사용 불가

---

## 📝 헬퍼 함수

**위치**: `app/Helpers/api_helper.php`

### 주요 함수

```php
// 성공 응답
api_response($data, 200);

// 에러 응답
api_error('리소스를 찾을 수 없습니다', 404);

// HATEOAS 링크
api_link('self', '/api/v1/products/1', 'GET');

// 페이지네이션
api_paginated($items, $total, $page, $limit, $baseUrl);

// 리소스 생성
api_created($data, '/api/v1/products/1');

// 비동기 작업
api_accepted('/api/v1/jobs/job_123', '작업 접수됨');

// ETag 생성
generate_etag($data);

// 리소스 URL
api_resource_url('products', 1, 'v1');
```

---

## 🚀 확장 가이드

### 새로운 리소스 API 추가

1. **모델 생성** (기존 모델 사용 가능)
2. **컨트롤러 생성**:
   ```php
   class OrdersController extends RestApiController
   {
       protected $modelName = 'App\Models\OrderModel';
       protected $resourceName = 'orders';
   }
   ```
3. **라우팅 등록**:
   ```php
   $routes->resource('orders', ['controller' => 'OrdersController']);
   ```

### 커스텀 엔드포인트 추가

```php
class OrdersController extends RestApiController
{
    // ... 기본 메서드
    
    public function cancel($id = null)
    {
        // 주문 취소 로직
        $response = RestApiResponse::success(['cancelled' => true]);
        return $this->respond($response, 200);
    }
}
```

라우팅:
```php
$routes->post('orders/(:num)/cancel', 'OrdersController::cancel/$1');
```

---

## 🧪 테스트

### 기본 테스트

```bash
# 목록 조회
curl -X GET "http://localhost/api/v1/members?page=1&limit=10"

# 단일 조회
curl -X GET "http://localhost/api/v1/members/1"

# 생성
curl -X POST "http://localhost/api/v1/members" \
  -H "Content-Type: application/json" \
  -d '{"mem_userid":"test","mem_email":"test@test.com"}'

# 수정
curl -X PATCH "http://localhost/api/v1/members/1" \
  -H "Content-Type: application/json" \
  -H "If-Match: \"abc123\"" \
  -d '{"mem_username":"수정된이름"}'

# 삭제
curl -X DELETE "http://localhost/api/v1/members/1"
```

---

## 📚 참고 문서

- [API 사용 가이드](./restful-api-guide.md)
- [API 디자인 문서](./api-design)
- [Azure API Design Best Practices PDF](./Web%20API%20Design%20Best%20Practices%20-%20Azure%20Architecture%20Center%20_%20Microsoft%20Learn.pdf)
- [Azure API Implementation PDF](./Web%20API%20Implementation%20-%20Azure%20Architecture%20Center%20_%20Microsoft%20Learn.pdf)
- [Azure API Design Best Practices (온라인)](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design)

---

## 🔄 마이그레이션 (기존 API → RESTful API)

### 기존 API

```
POST /api/member/getMemberList
{
  "search": { ... }
}
```

### RESTful API

```
GET /api/v1/members?page=1&limit=20&status=active
```

### 호환성 유지

기존 API(`/api/{controller}/{method}`)와 새로운 RESTful API(`/api/v1/{resource}`)가 공존 가능하므로 점진적 마이그레이션 가능.

---

## ✅ 체크리스트

### 구현 완료 항목

- ✅ RESTful 설계 원칙 (리소스 중심, HTTP 메서드 표준화)
- ✅ HATEOAS 링크 생성
- ✅ 표준화된 응답 형식
- ✅ 페이지네이션, 필터링, 정렬
- ✅ ETag 캐싱 및 조건부 요청
- ✅ Rate Limiting
- ✅ CORS 지원
- ✅ API 버전 관리
- ✅ 비동기 작업 처리 (202 Accepted 패턴)
- ✅ 유효성 검증
- ✅ 에러 핸들링
- ✅ 헬퍼 함수
- ✅ 설정 파일
- ✅ 예제 컨트롤러
- ✅ 문서화

### 권장 추가 구현

- ⬜ JWT 인증
- ⬜ OAuth 2.0
- ⬜ API 키 관리
- ⬜ OpenAPI/Swagger 자동 문서 생성
- ⬜ 웹훅 (Webhook)
- ⬜ GraphQL 지원
- ⬜ 실시간 알림 (WebSocket)
- ⬜ API 분석 및 모니터링

---

## 버전

**v1.0.0** - 2025-11-01

초기 RESTful API 프레임워크 구축 완료

