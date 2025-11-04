# RESTful API 프레임워크 사용 가이드

## 개요

이 프로젝트는 **Azure API Design Best Practices**를 기반으로 구축된 RESTful API 프레임워크를 제공합니다.

### 주요 특징

- ✅ **RESTful 설계 원칙** - 리소스 중심, HTTP 메서드 표준화
- ✅ **HATEOAS 지원** - 하이퍼링크를 통한 API 탐색
- ✅ **버전 관리** - URI, Query, Header 기반 버전 관리
- ✅ **ETag 캐싱** - 조건부 요청 및 낙관적 동시성 제어
- ✅ **Rate Limiting** - 요청 빈도 제한
- ✅ **CORS 지원** - Cross-Origin Resource Sharing
- ✅ **비동기 작업 처리** - 202 Accepted 패턴
- ✅ **페이지네이션** - 대량 데이터 효율적 조회
- ✅ **표준화된 에러 응답** - 일관된 에러 처리

---

## 빠른 시작

### 1. 리소스 컨트롤러 생성

```php
<?php

namespace App\Controllers\Api\V1;

use App\Libraries\RestApi\RestApiController;

class ProductsController extends RestApiController
{
    protected $modelName = 'App\Models\ProductModel';
    protected $resourceName = 'products';
    protected $apiVersion = 'v1';
    protected $enableETag = true;
    protected $cacheMaxAge = 300; // 5분

    protected function getValidationRules(string $action): array
    {
        $rules = [];

        switch ($action) {
            case 'create':
                $rules = [
                    'name' => 'required|max_length[255]',
                    'price' => 'required|numeric',
                ];
                break;

            case 'update':
                $rules = [
                    'name' => 'required|max_length[255]',
                    'price' => 'required|numeric',
                ];
                break;

            case 'patch':
                $rules = [
                    'name' => 'permit_empty|max_length[255]',
                    'price' => 'permit_empty|numeric',
                ];
                break;
        }

        return $rules;
    }
}
```

### 2. 라우팅 설정

`app/Config/Routes.php`에 추가:

```php
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function ($routes) {
    // RESTful 리소스 라우팅
    $routes->resource('products', ['controller' => 'ProductsController']);
});
```

### 3. API 호출

자동으로 생성되는 엔드포인트:

| HTTP 메서드 | URI | 액션 | 설명 |
|------------|-----|------|------|
| GET | `/api/v1/products` | index | 제품 목록 조회 |
| GET | `/api/v1/products/{id}` | show | 제품 상세 조회 |
| POST | `/api/v1/products` | create | 제품 생성 |
| PUT | `/api/v1/products/{id}` | update | 제품 전체 업데이트 |
| PATCH | `/api/v1/products/{id}` | patch | 제품 부분 업데이트 |
| DELETE | `/api/v1/products/{id}` | delete | 제품 삭제 |

---

## API 응답 형식

### 성공 응답

```json
{
  "status": 200,
  "success": true,
  "data": {
    "id": 1,
    "name": "상품명",
    "price": 10000,
    "links": [
      {
        "rel": "self",
        "href": "http://example.com/api/v1/products/1",
        "action": "GET",
        "types": ["application/json"]
      },
      {
        "rel": "update",
        "href": "http://example.com/api/v1/products/1",
        "action": "PUT",
        "types": ["application/json"]
      }
    ]
  }
}
```

### 페이지네이션 응답

```json
{
  "status": 200,
  "success": true,
  "data": [
    { "id": 1, "name": "상품1" },
    { "id": 2, "name": "상품2" }
  ],
  "meta": {
    "total": 100,
    "page": 1,
    "limit": 20,
    "totalPages": 5
  },
  "links": [
    {
      "rel": "self",
      "href": "http://example.com/api/v1/products?page=1&limit=20",
      "action": "GET"
    },
    {
      "rel": "next",
      "href": "http://example.com/api/v1/products?page=2&limit=20",
      "action": "GET"
    }
  ]
}
```

### 에러 응답

```json
{
  "status": 400,
  "success": false,
  "message": "유효성 검증 실패",
  "errorCode": "VALIDATION_ERROR",
  "errors": [
    {
      "field": "name",
      "message": "이름은 필수입니다"
    }
  ]
}
```

---

## HTTP 메서드 사용 가이드

### GET - 리소스 조회

```bash
# 목록 조회 (페이지네이션, 필터링)
GET /api/v1/products?page=1&limit=20&category=electronics

# 단일 리소스 조회
GET /api/v1/products/123
```

### POST - 리소스 생성

```bash
POST /api/v1/products
Content-Type: application/json

{
  "name": "새 상품",
  "price": 15000,
  "category": "electronics"
}

# 응답: 201 Created
# Location: /api/v1/products/124
```

### PUT - 리소스 전체 교체

```bash
PUT /api/v1/products/123
Content-Type: application/json
If-Match: "abc123"

{
  "name": "수정된 상품",
  "price": 20000,
  "category": "electronics"
}
```

### PATCH - 리소스 부분 수정

```bash
PATCH /api/v1/products/123
Content-Type: application/json
If-Match: "abc123"

{
  "price": 18000
}
```

### DELETE - 리소스 삭제

```bash
DELETE /api/v1/products/123

# 응답: 204 No Content
```

---

## 고급 기능

### 1. ETag 기반 캐싱

#### 조건부 GET 요청

```bash
GET /api/v1/products/123
If-None-Match: "abc123"

# 리소스가 변경되지 않았으면 304 Not Modified 반환
```

#### 낙관적 동시성 제어

```bash
PUT /api/v1/products/123
If-Match: "abc123"
Content-Type: application/json

{
  "name": "수정된 상품"
}

# ETag가 일치하지 않으면 412 Precondition Failed 반환
```

### 2. 비동기 작업 처리

장기 실행 작업은 비동기로 처리:

```php
public function bulkImport()
{
    $data = $this->getRequestData();
    
    $asyncHandler = new AsyncJobHandler();
    
    $jobId = $asyncHandler->createJob(
        function ($jobId) use ($data) {
            // 장기 실행 작업
            // ...
            
            return ['result' => 'success'];
        },
        [$data]
    );
    
    $response = $asyncHandler->createAcceptedResponse($jobId, 'v1');
    return $this->respond($response, $response['status']);
}
```

#### 클라이언트 워크플로우

```bash
# 1. 비동기 작업 요청
POST /api/v1/products/bulk-import
# 응답: 202 Accepted
# Location: /api/v1/jobs/job_123

# 2. 작업 상태 조회
GET /api/v1/jobs/job_123
# 응답: 200 OK (진행 중)
{
  "status": 200,
  "data": {
    "status": "processing",
    "progress": 50,
    "message": "작업 진행 중..."
  }
}

# 3. 작업 완료 후
GET /api/v1/jobs/job_123
# 응답: 303 See Other
# Location: /api/v1/products/bulk-import-result/456
```

### 3. Rate Limiting

요청 빈도 제한 헤더:

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1699999999
```

제한 초과 시:

```json
{
  "status": 429,
  "success": false,
  "message": "요청 제한을 초과했습니다",
  "errorCode": "RATE_LIMIT_EXCEEDED",
  "retryAfter": 30
}
```

### 4. API 버전 관리

세 가지 방식 지원:

```bash
# 1. URI 버전
GET /api/v1/products

# 2. Query 파라미터
GET /api/products?version=1

# 3. Header
GET /api/products
api-version: 1.0
```

---

## 헬퍼 함수

```php
// API 응답 생성
$response = api_response($data, 200);

// 에러 응답
$response = api_error('리소스를 찾을 수 없습니다', 404);

// HATEOAS 링크 생성
$link = api_link('self', '/api/v1/products/1', 'GET');

// 페이지네이션 응답
$response = api_paginated($items, $total, $page, $limit, $baseUrl);

// 리소스 생성 응답
$response = api_created($data, '/api/v1/products/1');

// 비동기 작업 수락 응답
$response = api_accepted('/api/v1/jobs/job_123', '작업이 접수되었습니다');

// ETag 생성
$etag = generate_etag($data);

// 리소스 URL 생성
$url = api_resource_url('products', 1, 'v1');
```

---

## 설정

`app/Config/RestApi.php`에서 전역 설정 변경 가능:

```php
public array $rateLimit = [
    'enabled' => true,
    'windowSize' => 60,
    'maxRequests' => 100,
];

public bool $enableETag = true;

public array $pagination = [
    'defaultLimit' => 20,
    'maxLimit' => 100,
];
```

---

## 상태 코드 가이드

| 코드 | 의미 | 사용 시나리오 |
|-----|------|------------|
| 200 | OK | 요청 성공 |
| 201 | Created | 리소스 생성 성공 |
| 202 | Accepted | 비동기 작업 수락 |
| 204 | No Content | 삭제 성공 (본문 없음) |
| 304 | Not Modified | 리소스 변경 없음 (캐시 사용) |
| 400 | Bad Request | 잘못된 요청 |
| 401 | Unauthorized | 인증 실패 |
| 403 | Forbidden | 권한 없음 |
| 404 | Not Found | 리소스 없음 |
| 409 | Conflict | 리소스 충돌 |
| 412 | Precondition Failed | 조건부 요청 실패 (ETag 불일치) |
| 422 | Unprocessable Entity | 유효성 검증 실패 |
| 429 | Too Many Requests | Rate Limit 초과 |
| 500 | Internal Server Error | 서버 오류 |
| 503 | Service Unavailable | 서비스 사용 불가 |

---

## 보안 고려사항

1. **HTTPS 필수** - 모든 API는 TLS 1.2 이상 사용
2. **입력 검증** - 모든 입력 데이터 유효성 검사
3. **SQL Injection 방지** - Prepared Statement 사용
4. **Rate Limiting** - 과도한 요청 방지
5. **인증/인가** - OAuth 2.0 / JWT 권장
6. **민감 데이터 캐싱 금지** - Cache-Control: no-store

---

## 테스트 예제

```bash
# 목록 조회
curl -X GET "http://localhost/api/v1/members?page=1&limit=10" \
  -H "Accept: application/json"

# 단일 조회
curl -X GET "http://localhost/api/v1/members/1" \
  -H "Accept: application/json"

# 생성
curl -X POST "http://localhost/api/v1/members" \
  -H "Content-Type: application/json" \
  -d '{
    "mem_userid": "testuser",
    "mem_password": "password123",
    "mem_email": "test@example.com",
    "mem_username": "테스트 사용자"
  }'

# 수정
curl -X PATCH "http://localhost/api/v1/members/1" \
  -H "Content-Type: application/json" \
  -H "If-Match: \"abc123\"" \
  -d '{
    "mem_username": "수정된 이름"
  }'

# 삭제
curl -X DELETE "http://localhost/api/v1/members/1"
```

---

## 문제 해결

### Rate Limit 초과

```json
{
  "status": 429,
  "message": "요청 제한을 초과했습니다",
  "retryAfter": 30
}
```

**해결**: `retryAfter` 초만큼 대기 후 재시도

### ETag 불일치

```json
{
  "status": 412,
  "message": "리소스가 이미 수정되었습니다"
}
```

**해결**: 최신 리소스 조회 후 재시도

---

## 추가 자료

- [Azure API Design Best Practices](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design)
- [Azure API Implementation Guide](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-implementation)
- [Richardson Maturity Model](https://martinfowler.com/articles/richardsonMaturityModel.html)
- [REST API Tutorial](https://restfulapi.net/)

---

## 변경 이력

### v1.0.0 (2025-11-01)
- RESTful API 프레임워크 초기 구현
- HATEOAS 지원
- 버전 관리, Rate Limiting, ETag 캐싱, CORS 필터
- 비동기 작업 처리
- 예제 컨트롤러 및 문서화

