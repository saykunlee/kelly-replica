# Kelly API v1 정의서 (API Reference)

**Version:** 1.0.0  
**Base URL:** `http://your-domain.com/api/v1`  
**Protocol:** HTTP/HTTPS  
**Content-Type:** `application/json`  
**Last Updated:** 2025-11-03

---

## 📋 목차

1. [개요](#개요)
2. [인증 (Authentication)](#인증-authentication)
3. [공통 사항](#공통-사항)
4. [API 엔드포인트](#api-엔드포인트)
   - [Members API - 회원 관리](#members-api---회원-관리)
   - [Jobs API - 비동기 작업](#jobs-api---비동기-작업)
5. [응답 형식](#응답-형식)
6. [에러 코드](#에러-코드)
7. [예제 코드](#예제-코드)
8. [Rate Limiting](#rate-limiting)
9. [버전 관리](#버전-관리)
10. [FAQ](#faq)

---

## 개요

Kelly API는 RESTful 원칙을 따르는 HTTP 기반 API입니다. 모든 요청과 응답은 JSON 형식을 사용합니다.

### 주요 특징

- ✅ **RESTful 설계**: 표준 HTTP 메서드 사용 (GET, POST, PUT, PATCH, DELETE)
- ✅ **HATEOAS 지원**: 응답에 관련 링크 포함
- ✅ **ETag 캐싱**: 조건부 요청 지원 (304 Not Modified)
- ✅ **페이지네이션**: 대량 데이터 효율적 조회
- ✅ **비동기 처리**: 장기 실행 작업 지원 (202 Accepted)
- ✅ **Rate Limiting**: 요청 빈도 제한
- ✅ **CORS 지원**: Cross-Origin Resource Sharing

---

## 인증 (Authentication)

현재 버전은 인증이 비활성화되어 있습니다. 향후 버전에서는 다음을 지원할 예정입니다:

- **JWT (JSON Web Token)**
- **OAuth 2.0**
- **API Key**

### 예정된 헤더 형식

```http
Authorization: Bearer <your_access_token>
```

---

## 공통 사항

### Base URL

```
http://localhost:3005/api/v1
```

### Request Headers

| 헤더 | 필수 | 설명 | 예시 |
|-----|------|------|------|
| `Content-Type` | POST, PUT, PATCH | 요청 본문 형식 | `application/json` |
| `Accept` | 권장 | 응답 형식 | `application/json` |
| `If-None-Match` | 선택 | ETag 조건부 요청 (캐싱) | `"abc123def456"` |
| `If-Match` | 선택 | ETag 동시성 제어 | `"abc123def456"` |
| `api-version` | 선택 | API 버전 지정 | `1.0` |

### Response Headers

| 헤더 | 설명 | 예시 |
|-----|------|------|
| `Content-Type` | 응답 형식 | `application/json` |
| `ETag` | 리소스 고유 해시 | `"abc123def456"` |
| `Cache-Control` | 캐시 제어 | `max-age=300, private` |
| `Location` | 생성된 리소스 위치 (201, 202) | `/api/v1/members/123` |
| `X-RateLimit-Limit` | 최대 요청 수 | `100` |
| `X-RateLimit-Remaining` | 남은 요청 수 | `95` |
| `X-RateLimit-Reset` | 제한 초기화 시간 (Unix timestamp) | `1699999999` |

### HTTP 상태 코드

| 코드 | 의미 | 설명 |
|-----|------|------|
| `200` | OK | 요청 성공 |
| `201` | Created | 리소스 생성 성공 |
| `202` | Accepted | 비동기 작업 수락 |
| `204` | No Content | 삭제 성공 (응답 본문 없음) |
| `304` | Not Modified | 캐시된 리소스 사용 (ETag 일치) |
| `400` | Bad Request | 잘못된 요청 |
| `404` | Not Found | 리소스를 찾을 수 없음 |
| `412` | Precondition Failed | ETag 불일치 (동시성 제어) |
| `422` | Unprocessable Entity | 유효성 검증 실패 |
| `429` | Too Many Requests | Rate Limit 초과 |
| `500` | Internal Server Error | 서버 오류 |

---

## API 엔드포인트

### Members API - 회원 관리

회원 리소스를 관리하는 RESTful API입니다.

---

#### 1. 회원 목록 조회

**Endpoint:** `GET /api/v1/members`

**설명:** 회원 목록을 페이지네이션, 필터링, 정렬하여 조회합니다.

**Query Parameters:**

| 파라미터 | 타입 | 필수 | 기본값 | 설명 |
|---------|-----|------|-------|------|
| `page` | integer | 선택 | `1` | 페이지 번호 (1부터 시작) |
| `limit` | integer | 선택 | `20` | 페이지당 항목 수 (최대 100) |
| `sort` | string | 선택 | `mem_id` | 정렬 필드 |
| `order` | string | 선택 | `DESC` | 정렬 순서 (`ASC` 또는 `DESC`) |
| `mem_email` | string | 선택 | - | 이메일로 필터링 |
| `mem_level` | integer | 선택 | - | 회원 레벨로 필터링 |

**Request Example:**

```http
GET /api/v1/members?page=1&limit=20&sort=mem_username&order=ASC&mem_level=1
Accept: application/json
```

**Response (200 OK):**

```json
{
  "status": 200,
  "success": true,
  "data": [
    {
      "mem_id": 1,
      "mem_userid": "user001",
      "mem_email": "user001@example.com",
      "mem_username": "홍길동",
      "mem_nickname": "길동이",
      "mem_phone": "010-1234-5678",
      "mem_level": 1,
      "mem_created_at": "2025-01-01 10:00:00",
      "links": [
        {
          "rel": "self",
          "href": "http://localhost:3005/api/v1/members/1",
          "method": "GET"
        },
        {
          "rel": "update",
          "href": "http://localhost:3005/api/v1/members/1",
          "method": "PUT"
        },
        {
          "rel": "patch",
          "href": "http://localhost:3005/api/v1/members/1",
          "method": "PATCH"
        },
        {
          "rel": "delete",
          "href": "http://localhost:3005/api/v1/members/1",
          "method": "DELETE"
        },
        {
          "rel": "collection",
          "href": "http://localhost:3005/api/v1/members",
          "method": "GET"
        }
      ]
    }
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
      "href": "http://localhost:3005/api/v1/members?page=1&limit=20",
      "method": "GET"
    },
    {
      "rel": "next",
      "href": "http://localhost:3005/api/v1/members?page=2&limit=20",
      "method": "GET"
    },
    {
      "rel": "last",
      "href": "http://localhost:3005/api/v1/members?page=5&limit=20",
      "method": "GET"
    }
  ]
}
```

---

#### 2. 회원 상세 조회

**Endpoint:** `GET /api/v1/members/{id}`

**설명:** 특정 회원의 상세 정보를 조회합니다. ETag 캐싱을 지원합니다.

**Path Parameters:**

| 파라미터 | 타입 | 필수 | 설명 |
|---------|-----|------|------|
| `id` | integer | 필수 | 회원 ID |

**Request Example:**

```http
GET /api/v1/members/1
Accept: application/json
```

**Response (200 OK):**

```json
{
  "status": 200,
  "success": true,
  "data": {
    "mem_id": 1,
    "mem_userid": "user001",
    "mem_email": "user001@example.com",
    "mem_username": "홍길동",
    "mem_nickname": "길동이",
    "mem_phone": "010-1234-5678",
    "mem_level": 1,
    "mem_created_at": "2025-01-01 10:00:00",
    "mem_updated_at": "2025-01-15 14:30:00",
    "links": [
      {
        "rel": "self",
        "href": "http://localhost:3005/api/v1/members/1",
        "method": "GET"
      },
      {
        "rel": "update",
        "href": "http://localhost:3005/api/v1/members/1",
        "method": "PUT"
      },
      {
        "rel": "patch",
        "href": "http://localhost:3005/api/v1/members/1",
        "method": "PATCH"
      },
      {
        "rel": "delete",
        "href": "http://localhost:3005/api/v1/members/1",
        "method": "DELETE"
      },
      {
        "rel": "collection",
        "href": "http://localhost:3005/api/v1/members",
        "method": "GET"
      }
    ]
  }
}
```

**Response Headers:**
```
ETag: "abc123def456"
Cache-Control: max-age=300, private
```

**조건부 요청 (304 Not Modified):**

```http
GET /api/v1/members/1
Accept: application/json
If-None-Match: "abc123def456"
```

리소스가 변경되지 않았으면 `304 Not Modified` 반환 (본문 없음)

**Error Response (404 Not Found):**

```json
{
  "status": 404,
  "success": false,
  "message": "리소스를 찾을 수 없습니다",
  "errorCode": "NOT_FOUND"
}
```

---

#### 3. 회원 생성

**Endpoint:** `POST /api/v1/members`

**설명:** 새로운 회원을 생성합니다. 비밀번호는 자동으로 해싱됩니다.

**Request Body:**

| 필드 | 타입 | 필수 | 제약 조건 | 설명 |
|-----|------|------|----------|------|
| `mem_userid` | string | 필수 | 4-50자, 고유값 | 사용자 ID |
| `mem_password` | string | 필수 | 최소 8자 | 비밀번호 (평문) |
| `mem_email` | string | 필수 | 유효한 이메일, 고유값 | 이메일 |
| `mem_username` | string | 필수 | 최대 100자 | 이름 |
| `mem_nickname` | string | 선택 | 최대 50자 | 닉네임 |
| `mem_phone` | string | 선택 | 최대 20자 | 전화번호 |
| `mem_level` | integer | 선택 | - | 회원 레벨 |

**Request Example:**

```http
POST /api/v1/members
Content-Type: application/json
Accept: application/json

{
  "mem_userid": "newuser001",
  "mem_password": "securePassword123!",
  "mem_email": "newuser@example.com",
  "mem_username": "신규 사용자",
  "mem_nickname": "뉴비",
  "mem_phone": "010-9999-8888",
  "mem_level": 1
}
```

**Response (201 Created):**

```json
{
  "status": 201,
  "success": true,
  "message": "리소스가 생성되었습니다",
  "data": {
    "mem_id": 123,
    "mem_userid": "newuser001",
    "mem_email": "newuser@example.com",
    "mem_username": "신규 사용자",
    "mem_nickname": "뉴비",
    "mem_phone": "010-9999-8888",
    "mem_level": 1,
    "mem_created_at": "2025-11-03 15:30:00",
    "links": [
      {
        "rel": "self",
        "href": "http://localhost:3005/api/v1/members/123",
        "method": "GET"
      }
    ]
  }
}
```

**Response Headers:**
```
Location: http://localhost:3005/api/v1/members/123
```

**Error Response (422 Unprocessable Entity):**

```json
{
  "status": 422,
  "success": false,
  "message": "유효성 검증 실패",
  "errorCode": "VALIDATION_ERROR",
  "errors": [
    {
      "field": "mem_userid",
      "message": "이미 사용 중인 사용자 ID입니다"
    },
    {
      "field": "mem_password",
      "message": "비밀번호는 최소 8자 이상이어야 합니다"
    }
  ]
}
```

---

#### 4. 회원 전체 수정

**Endpoint:** `PUT /api/v1/members/{id}`

**설명:** 회원 정보를 전체적으로 수정합니다. 모든 필수 필드를 포함해야 합니다.

**Path Parameters:**

| 파라미터 | 타입 | 필수 | 설명 |
|---------|-----|------|------|
| `id` | integer | 필수 | 회원 ID |

**Request Headers (선택):**

```http
If-Match: "abc123def456"
```

ETag 기반 낙관적 동시성 제어. If-Match 헤더가 현재 리소스의 ETag와 일치하지 않으면 `412 Precondition Failed` 반환.

**Request Body:**

| 필드 | 타입 | 필수 | 설명 |
|-----|------|------|------|
| `mem_email` | string | 필수 | 이메일 |
| `mem_username` | string | 필수 | 이름 |
| `mem_nickname` | string | 선택 | 닉네임 |
| `mem_phone` | string | 선택 | 전화번호 |

**Request Example:**

```http
PUT /api/v1/members/1
Content-Type: application/json
Accept: application/json
If-Match: "abc123def456"

{
  "mem_email": "updated@example.com",
  "mem_username": "수정된 이름",
  "mem_phone": "010-5555-6666",
  "mem_nickname": "수정닉네임"
}
```

**Response (200 OK):**

```json
{
  "status": 200,
  "success": true,
  "data": {
    "mem_id": 1,
    "mem_userid": "user001",
    "mem_email": "updated@example.com",
    "mem_username": "수정된 이름",
    "mem_phone": "010-5555-6666",
    "mem_nickname": "수정닉네임",
    "mem_updated_at": "2025-11-03 16:00:00",
    "links": [ ... ]
  }
}
```

**Error Response (412 Precondition Failed):**

```json
{
  "status": 412,
  "success": false,
  "message": "리소스가 이미 수정되었습니다",
  "errorCode": "PRECONDITION_FAILED"
}
```

---

#### 5. 회원 부분 수정

**Endpoint:** `PATCH /api/v1/members/{id}`

**설명:** 회원 정보를 부분적으로 수정합니다. 변경하려는 필드만 포함하면 됩니다.

**Path Parameters:**

| 파라미터 | 타입 | 필수 | 설명 |
|---------|-----|------|------|
| `id` | integer | 필수 | 회원 ID |

**Request Body (모두 선택):**

| 필드 | 타입 | 제약 조건 | 설명 |
|-----|------|----------|------|
| `mem_email` | string | 유효한 이메일 | 이메일 |
| `mem_username` | string | 최대 100자 | 이름 |
| `mem_nickname` | string | 최대 50자 | 닉네임 |
| `mem_phone` | string | 최대 20자 | 전화번호 |

**Request Example:**

```http
PATCH /api/v1/members/1
Content-Type: application/json
Accept: application/json

{
  "mem_phone": "010-7777-8888"
}
```

**Response (200 OK):**

```json
{
  "status": 200,
  "success": true,
  "data": {
    "mem_id": 1,
    "mem_userid": "user001",
    "mem_email": "user001@example.com",
    "mem_username": "홍길동",
    "mem_phone": "010-7777-8888",
    "mem_updated_at": "2025-11-03 16:30:00",
    "links": [ ... ]
  }
}
```

---

#### 6. 회원 삭제

**Endpoint:** `DELETE /api/v1/members/{id}`

**설명:** 회원을 삭제합니다.

**Path Parameters:**

| 파라미터 | 타입 | 필수 | 설명 |
|---------|-----|------|------|
| `id` | integer | 필수 | 회원 ID |

**Request Example:**

```http
DELETE /api/v1/members/1
Accept: application/json
```

**Response (204 No Content):**

응답 본문 없음. 상태 코드 `204`로 성공 확인.

**Error Response (404 Not Found):**

```json
{
  "status": 404,
  "success": false,
  "message": "리소스를 찾을 수 없습니다",
  "errorCode": "NOT_FOUND"
}
```

---

#### 7. 테스트 엔드포인트

**Endpoint:** `GET /api/v1/members/test`

**설명:** API 동작 확인용 테스트 엔드포인트입니다.

**Request Example:**

```http
GET /api/v1/members/test
Accept: application/json
```

**Response (200 OK):**

```json
{
  "status": "success",
  "data": {
    "message": "Members API 테스트 엔드포인트",
    "timestamp": "2025-11-03 17:00:00",
    "request_info": {
      "method": "GET",
      "uri": "http://localhost:3005/api/v1/members/test",
      "ip_address": "::1",
      "user_agent": "Mozilla/5.0 ..."
    },
    "headers": {
      "content_type": "application/json",
      "accept": "application/json",
      "api_version": "v1"
    },
    "controller_info": {
      "resource_name": "members",
      "api_version": "v1",
      "model_name": "App\\Models\\MemberModel",
      "etag_enabled": true,
      "cache_max_age": 300
    },
    "environment": {
      "ci_environment": "production",
      "php_version": "8.1.32"
    }
  },
  "links": [ ... ]
}
```

---

### Jobs API - 비동기 작업

비동기 작업의 상태를 조회하고 관리하는 API입니다.

---

#### 1. 작업 상태 조회

**Endpoint:** `GET /api/v1/jobs/{jobId}`

**설명:** 비동기 작업의 현재 상태를 조회합니다.

**Path Parameters:**

| 파라미터 | 타입 | 필수 | 설명 |
|---------|-----|------|------|
| `jobId` | string | 필수 | 작업 ID (예: `job_abc123`) |

**Request Example:**

```http
GET /api/v1/jobs/job_abc123
Accept: application/json
```

**Response - 작업 대기 중 (200 OK):**

```json
{
  "status": 200,
  "success": true,
  "data": {
    "jobId": "job_abc123",
    "status": "pending",
    "message": "작업이 대기 중입니다",
    "createdAt": "2025-11-03 17:00:00"
  },
  "links": [
    {
      "rel": "self",
      "href": "http://localhost:3005/api/v1/jobs/job_abc123",
      "method": "GET"
    },
    {
      "rel": "cancel",
      "href": "http://localhost:3005/api/v1/jobs/job_abc123",
      "method": "DELETE"
    }
  ]
}
```

**Response - 작업 진행 중 (200 OK):**

```json
{
  "status": 200,
  "success": true,
  "data": {
    "jobId": "job_abc123",
    "status": "processing",
    "progress": 75,
    "message": "작업 진행 중... (75%)",
    "createdAt": "2025-11-03 17:00:00",
    "startedAt": "2025-11-03 17:00:05"
  },
  "links": [
    {
      "rel": "self",
      "href": "http://localhost:3005/api/v1/jobs/job_abc123",
      "method": "GET"
    }
  ]
}
```

**Response - 작업 완료 (303 See Other):**

```json
{
  "status": 303,
  "success": true,
  "message": "작업이 완료되었습니다",
  "location": "http://localhost:3005/api/v1/members/123"
}
```

**Response Headers:**
```
Location: http://localhost:3005/api/v1/members/123
```

클라이언트는 `Location` 헤더의 URL로 리다이렉트하여 결과를 확인해야 합니다.

**Response - 작업 실패 (200 OK):**

```json
{
  "status": 200,
  "success": false,
  "data": {
    "jobId": "job_abc123",
    "status": "failed",
    "message": "작업 처리 중 오류가 발생했습니다",
    "error": "데이터베이스 연결 실패",
    "createdAt": "2025-11-03 17:00:00",
    "failedAt": "2025-11-03 17:01:30"
  }
}
```

---

#### 2. 작업 취소

**Endpoint:** `DELETE /api/v1/jobs/{jobId}`

**설명:** 진행 중인 작업을 취소합니다. 이미 완료되거나 실패한 작업은 취소할 수 없습니다.

**Path Parameters:**

| 파라미터 | 타입 | 필수 | 설명 |
|---------|-----|------|------|
| `jobId` | string | 필수 | 작업 ID |

**Request Example:**

```http
DELETE /api/v1/jobs/job_abc123
Accept: application/json
```

**Response (200 OK):**

```json
{
  "status": 200,
  "success": true,
  "data": {
    "message": "작업이 취소되었습니다"
  }
}
```

**Error Response (500 Internal Server Error):**

```json
{
  "status": 500,
  "success": false,
  "message": "작업 취소에 실패했습니다",
  "errorCode": "INTERNAL_ERROR"
}
```

---

## 응답 형식

### 성공 응답 구조

```json
{
  "status": 200,
  "success": true,
  "data": { ... },
  "links": [ ... ],
  "meta": { ... }
}
```

| 필드 | 타입 | 설명 |
|-----|------|------|
| `status` | integer | HTTP 상태 코드 |
| `success` | boolean | 성공 여부 |
| `data` | object/array | 응답 데이터 |
| `links` | array | HATEOAS 링크 (선택) |
| `meta` | object | 메타데이터 (페이지네이션 등, 선택) |

### 에러 응답 구조

```json
{
  "status": 400,
  "success": false,
  "message": "에러 메시지",
  "errorCode": "ERROR_CODE",
  "errors": [ ... ]
}
```

| 필드 | 타입 | 설명 |
|-----|------|------|
| `status` | integer | HTTP 상태 코드 |
| `success` | boolean | 항상 `false` |
| `message` | string | 에러 메시지 |
| `errorCode` | string | 에러 코드 (선택) |
| `errors` | array | 상세 에러 목록 (유효성 검증 실패 시, 선택) |

### HATEOAS 링크 구조

```json
{
  "rel": "self",
  "href": "http://localhost:3005/api/v1/members/1",
  "method": "GET"
}
```

| 필드 | 타입 | 설명 |
|-----|------|------|
| `rel` | string | 링크 관계 (`self`, `next`, `prev`, `collection`, `update`, `delete` 등) |
| `href` | string | 링크 URL |
| `method` | string | HTTP 메서드 |

---

## 에러 코드

| 에러 코드 | HTTP 상태 | 설명 |
|---------|---------|------|
| `BAD_REQUEST` | 400 | 잘못된 요청 |
| `NOT_FOUND` | 404 | 리소스를 찾을 수 없음 |
| `PRECONDITION_FAILED` | 412 | ETag 불일치 |
| `VALIDATION_ERROR` | 422 | 유효성 검증 실패 |
| `RATE_LIMIT_EXCEEDED` | 429 | Rate Limit 초과 |
| `INTERNAL_ERROR` | 500 | 서버 내부 오류 |

---

## 예제 코드

### JavaScript (Fetch API)

```javascript
// 1. 회원 목록 조회
async function getMembers(page = 1, limit = 20) {
  const response = await fetch(
    `http://localhost:3005/api/v1/members?page=${page}&limit=${limit}`,
    {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    }
  );
  
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  
  const data = await response.json();
  return data;
}

// 2. 회원 상세 조회 (ETag 캐싱)
async function getMember(id, etag = null) {
  const headers = {
    'Accept': 'application/json'
  };
  
  if (etag) {
    headers['If-None-Match'] = etag;
  }
  
  const response = await fetch(
    `http://localhost:3005/api/v1/members/${id}`,
    {
      method: 'GET',
      headers: headers
    }
  );
  
  if (response.status === 304) {
    // 캐시된 데이터 사용
    return { cached: true };
  }
  
  const data = await response.json();
  const newEtag = response.headers.get('ETag');
  
  return { data, etag: newEtag };
}

// 3. 회원 생성
async function createMember(memberData) {
  const response = await fetch(
    'http://localhost:3005/api/v1/members',
    {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(memberData)
    }
  );
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message);
  }
  
  const data = await response.json();
  const location = response.headers.get('Location');
  
  return { data, location };
}

// 4. 회원 부분 수정
async function patchMember(id, updates) {
  const response = await fetch(
    `http://localhost:3005/api/v1/members/${id}`,
    {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(updates)
    }
  );
  
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  
  return await response.json();
}

// 5. 회원 삭제
async function deleteMember(id) {
  const response = await fetch(
    `http://localhost:3005/api/v1/members/${id}`,
    {
      method: 'DELETE'
    }
  );
  
  if (response.status !== 204) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  
  return true;
}

// 6. 비동기 작업 폴링
async function pollJobStatus(jobId, interval = 2000) {
  return new Promise((resolve, reject) => {
    const checkStatus = async () => {
      try {
        const response = await fetch(
          `http://localhost:3005/api/v1/jobs/${jobId}`,
          {
            method: 'GET',
            headers: {
              'Accept': 'application/json'
            }
          }
        );
        
        if (response.status === 303) {
          // 작업 완료
          const location = response.headers.get('Location');
          resolve({ completed: true, location });
          return;
        }
        
        const data = await response.json();
        
        if (data.data.status === 'failed') {
          reject(new Error(data.data.message));
          return;
        }
        
        if (data.data.status === 'completed') {
          resolve({ completed: true, data: data.data });
          return;
        }
        
        // 진행 중 - 계속 폴링
        console.log(`Progress: ${data.data.progress}%`);
        setTimeout(checkStatus, interval);
        
      } catch (error) {
        reject(error);
      }
    };
    
    checkStatus();
  });
}

// 사용 예시
async function example() {
  try {
    // 회원 생성
    const newMember = {
      mem_userid: 'testuser123',
      mem_password: 'securePass123!',
      mem_email: 'test@example.com',
      mem_username: '테스트 사용자',
      mem_phone: '010-1234-5678'
    };
    
    const { data, location } = await createMember(newMember);
    console.log('Created member:', data);
    console.log('Location:', location);
    
    // 회원 목록 조회
    const members = await getMembers(1, 10);
    console.log('Members:', members);
    
    // 회원 수정
    const updated = await patchMember(data.data.mem_id, {
      mem_phone: '010-9999-8888'
    });
    console.log('Updated member:', updated);
    
  } catch (error) {
    console.error('Error:', error);
  }
}
```

### Python (Requests)

```python
import requests
import time

BASE_URL = 'http://localhost:3005/api/v1'

# 1. 회원 목록 조회
def get_members(page=1, limit=20):
    response = requests.get(
        f'{BASE_URL}/members',
        params={'page': page, 'limit': limit},
        headers={'Accept': 'application/json'}
    )
    response.raise_for_status()
    return response.json()

# 2. 회원 상세 조회
def get_member(member_id, etag=None):
    headers = {'Accept': 'application/json'}
    if etag:
        headers['If-None-Match'] = etag
    
    response = requests.get(
        f'{BASE_URL}/members/{member_id}',
        headers=headers
    )
    
    if response.status_code == 304:
        return {'cached': True}
    
    response.raise_for_status()
    return {
        'data': response.json(),
        'etag': response.headers.get('ETag')
    }

# 3. 회원 생성
def create_member(member_data):
    response = requests.post(
        f'{BASE_URL}/members',
        json=member_data,
        headers={
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    )
    response.raise_for_status()
    return {
        'data': response.json(),
        'location': response.headers.get('Location')
    }

# 4. 회원 수정 (PATCH)
def patch_member(member_id, updates):
    response = requests.patch(
        f'{BASE_URL}/members/{member_id}',
        json=updates,
        headers={
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    )
    response.raise_for_status()
    return response.json()

# 5. 회원 삭제
def delete_member(member_id):
    response = requests.delete(f'{BASE_URL}/members/{member_id}')
    response.raise_for_status()
    return True

# 6. 비동기 작업 상태 폴링
def poll_job_status(job_id, interval=2):
    while True:
        response = requests.get(
            f'{BASE_URL}/jobs/{job_id}',
            headers={'Accept': 'application/json'}
        )
        
        if response.status_code == 303:
            # 작업 완료
            location = response.headers.get('Location')
            return {'completed': True, 'location': location}
        
        response.raise_for_status()
        data = response.json()
        
        status = data['data']['status']
        
        if status == 'failed':
            raise Exception(data['data']['message'])
        
        if status == 'completed':
            return {'completed': True, 'data': data['data']}
        
        # 진행 중
        print(f"Progress: {data['data'].get('progress', 0)}%")
        time.sleep(interval)

# 사용 예시
if __name__ == '__main__':
    try:
        # 회원 생성
        new_member = {
            'mem_userid': 'pythonuser',
            'mem_password': 'securePass123!',
            'mem_email': 'python@example.com',
            'mem_username': '파이썬 사용자',
            'mem_phone': '010-1234-5678'
        }
        
        result = create_member(new_member)
        print(f"Created: {result['data']}")
        
        member_id = result['data']['data']['mem_id']
        
        # 회원 조회
        member = get_member(member_id)
        print(f"Member: {member}")
        
        # 회원 수정
        updated = patch_member(member_id, {
            'mem_phone': '010-9999-8888'
        })
        print(f"Updated: {updated}")
        
    except requests.exceptions.HTTPError as e:
        print(f"HTTP Error: {e}")
        print(f"Response: {e.response.text}")
```

### cURL

```bash
# 1. 회원 목록 조회
curl -X GET "http://localhost:3005/api/v1/members?page=1&limit=10" \
  -H "Accept: application/json"

# 2. 회원 상세 조회
curl -X GET "http://localhost:3005/api/v1/members/1" \
  -H "Accept: application/json"

# 3. ETag 캐싱 (조건부 요청)
curl -X GET "http://localhost:3005/api/v1/members/1" \
  -H "Accept: application/json" \
  -H "If-None-Match: \"abc123def456\"" \
  -i

# 4. 회원 생성
curl -X POST "http://localhost:3005/api/v1/members" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "mem_userid": "curluser",
    "mem_password": "securePass123!",
    "mem_email": "curl@example.com",
    "mem_username": "CURL 사용자",
    "mem_phone": "010-1234-5678"
  }'

# 5. 회원 전체 수정 (PUT)
curl -X PUT "http://localhost:3005/api/v1/members/1" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "If-Match: \"abc123def456\"" \
  -d '{
    "mem_email": "updated@example.com",
    "mem_username": "수정된 이름"
  }'

# 6. 회원 부분 수정 (PATCH)
curl -X PATCH "http://localhost:3005/api/v1/members/1" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "mem_phone": "010-9999-8888"
  }'

# 7. 회원 삭제
curl -X DELETE "http://localhost:3005/api/v1/members/1" \
  -i

# 8. 비동기 작업 상태 조회
curl -X GET "http://localhost:3005/api/v1/jobs/job_abc123" \
  -H "Accept: application/json"

# 9. Rate Limit 헤더 확인
curl -X GET "http://localhost:3005/api/v1/members" \
  -H "Accept: application/json" \
  -i | grep -i "X-RateLimit"
```

---

## Rate Limiting

### 제한 사항

- **시간 창**: 60초
- **최대 요청 수**: 100회
- **초과 시**: `429 Too Many Requests` 반환

### Rate Limit 헤더

모든 응답에 다음 헤더가 포함됩니다:

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1699999999
```

### Rate Limit 초과 응답

```json
{
  "status": 429,
  "success": false,
  "message": "요청 제한을 초과했습니다",
  "errorCode": "RATE_LIMIT_EXCEEDED",
  "retryAfter": 30
}
```

**권장 사항:**
- `retryAfter` 초만큼 대기 후 재시도
- Exponential Backoff 알고리즘 사용
- 요청을 배치(batch)로 처리

---

## 버전 관리

### 지원하는 버전 지정 방식

#### 1. URI 버전 (권장)

```http
GET /api/v1/members
```

#### 2. Query 파라미터

```http
GET /api/members?version=1
```

#### 3. 헤더

```http
GET /api/members
api-version: 1.0
```

### 현재 지원 버전

- **v1**: 현재 버전 (안정)

### 버전 업그레이드 정책

- 하위 호환성을 유지하는 변경: PATCH 버전 증가
- 새로운 기능 추가: MINOR 버전 증가
- 하위 호환성을 깨는 변경: MAJOR 버전 증가

---

## FAQ

### Q1. API 인증이 필요한가요?

**A:** 현재 버전(v1)은 인증이 비활성화되어 있습니다. 향후 버전에서 JWT 또는 OAuth 2.0을 지원할 예정입니다.

### Q2. HTTPS를 사용해야 하나요?

**A:** 프로덕션 환경에서는 반드시 HTTPS(TLS 1.2 이상)를 사용해야 합니다. 민감한 데이터(비밀번호 등)를 전송하기 때문입니다.

### Q3. ETag는 어떻게 사용하나요?

**A:** 
1. 첫 번째 요청에서 응답 헤더의 `ETag` 값을 저장합니다.
2. 다음 요청 시 `If-None-Match` 헤더에 저장한 ETag를 포함합니다.
3. 리소스가 변경되지 않았으면 `304 Not Modified` 응답을 받습니다.

### Q4. 페이지네이션 최대 limit는?

**A:** 최대 100개까지 가능합니다. 더 많은 데이터가 필요하면 여러 요청으로 나누어 조회하세요.

### Q5. 비동기 작업은 어떻게 확인하나요?

**A:** 
1. 비동기 작업 요청 시 `202 Accepted` 응답과 함께 `Location` 헤더를 받습니다.
2. `Location` URL(`/api/v1/jobs/{jobId}`)을 주기적으로 폴링하여 상태를 확인합니다.
3. `303 See Other` 응답을 받으면 작업이 완료된 것이며, `Location` 헤더의 URL로 결과를 조회합니다.

### Q6. CORS 오류가 발생합니다.

**A:** Kelly API는 CORS를 지원합니다. 서버 설정에서 허용된 오리진을 확인하거나, 서버 관리자에게 문의하세요.

### Q7. 에러 메시지가 한글로 나오는데 영어로 받을 수 있나요?

**A:** 현재는 한글만 지원합니다. 향후 `Accept-Language` 헤더를 통한 다국어 지원을 고려 중입니다.

### Q8. 대량의 데이터를 한 번에 생성할 수 있나요?

**A:** 대량 생성(bulk create)은 비동기 작업으로 처리해야 합니다. 향후 버전에서 지원할 예정입니다.

### Q9. 응답 시간이 느립니다.

**A:** 
- ETag 캐싱을 사용하여 불필요한 데이터 전송을 줄이세요.
- 페이지네이션 `limit`를 줄여서 요청하세요.
- 필요한 필드만 조회하는 기능은 향후 추가 예정입니다.

### Q10. API 변경 사항은 어떻게 알 수 있나요?

**A:** 
- 이 문서의 "Last Updated" 날짜를 확인하세요.
- 메이저 변경 사항은 사전 공지됩니다.
- 변경 이력은 문서 하단의 "변경 이력" 섹션을 참조하세요.

---

## 추가 리소스

- [Kelly RESTful API 가이드](./restful-api-guide.md)
- [API 구조 문서](./restful-api-structure.md)
- [API 설계 문서](./api-design)
- [Azure API Design Best Practices](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design)

---

## 지원

문제가 있거나 질문이 있으시면:
- GitHub Issues: [프로젝트 이슈 페이지]
- 이메일: [support@example.com]
- 문서: [doc/README.md](./README.md)

---

## 변경 이력

### v1.0.0 (2025-11-03)
- ✅ Members API 초기 릴리스
- ✅ Jobs API 초기 릴리스
- ✅ ETag 캐싱 지원
- ✅ Rate Limiting 구현
- ✅ HATEOAS 링크 지원
- ✅ 페이지네이션, 필터링, 정렬
- ✅ 비동기 작업 처리 (202 Accepted 패턴)

---

## 라이선스

이 API는 [라이선스 유형]에 따라 제공됩니다.

---

**문서 버전:** 1.0.0  
**마지막 업데이트:** 2025-11-03  
**작성자:** Kelly Development Team

