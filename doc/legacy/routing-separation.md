# API 라우팅 분리 가이드

## 📋 개요

이 문서는 Legacy API와 RESTful API의 라우팅 분리 방법과 동작 원리를 설명합니다.

---

## 🎯 목표

- ✅ **Legacy API**: 기존 시스템 호환성 유지 (`/api/{controller}/{method}`)
- ✅ **RESTful API**: 새로운 표준 API (`/api/v1/{resource}`)
- ✅ **명확한 분리**: 두 시스템이 서로 간섭하지 않도록 구성

---

## 🛤️ 라우팅 구조

### Routes.php 설정

```php
/*
 * RESTful API Routing (v1)
 */
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function ($routes) {
    $routes->resource('members', ['controller' => 'MembersController']);
    $routes->resource('examples', ['controller' => 'ExampleResourceController']);
    // ... 더 많은 RESTful 리소스
});

/*
 * Legacy API Routing
 */
// (?!v\d+) - negative lookahead: v1, v2, v3... 등으로 시작하지 않는 경우만 매칭
$routes->get('/api/(?!v\d+)(:segment)', 'RouteHandler::handle/$1');
$routes->post('/api/(?!v\d+)(:segment)/(:segment)', 'RouteHandler::handle/$1/$2');
$routes->get('/api/(?!v\d+)(:segment)/(:segment)', 'RouteHandler::handle/$1/$2');
```

---

## 🔍 Negative Lookahead 설명

### `(?!v\d+)` 패턴의 의미

**Negative Lookahead**: 특정 패턴이 **뒤따르지 않는** 경우만 매칭

```regex
(?!v\d+)
│  │ └─ 하나 이상의 숫자 (1, 2, 3, ...)
│  └─── 문자 'v'
└────── Negative Lookahead (뒤따르지 않는 경우)
```

### 매칭 예시

#### ✅ 매칭되는 경우 (Legacy API)

```
/api/member-api              → RouteHandler로 처리
/api/member-api/get-list     → RouteHandler로 처리
/api/board-api               → RouteHandler로 처리
/api/menu-api/get-menu-list  → RouteHandler로 처리
/api/estimate-api/create     → RouteHandler로 처리
```

**이유**: `v1`, `v2` 등으로 시작하지 않음

#### ❌ 매칭되지 않는 경우 (RESTful API)

```
/api/v1/members              → RESTful API로 처리
/api/v1/members/123          → RESTful API로 처리
/api/v2/orders               → RESTful API로 처리 (미래 버전)
/api/v10/products            → RESTful API로 처리
```

**이유**: `v1`, `v2` 등으로 시작함 (negative lookahead에 의해 제외)

---

## 📊 URL 매핑 테이블

### Legacy API (kebab-case 자동 변환)

| URL | 매칭 | 처리 |
|-----|-----|------|
| `/api/member-api` | ✅ | `RouteHandler` → `MemberApi::index()` |
| `/api/member-api/get-member-list` | ✅ | `RouteHandler` → `MemberApi::getMemberList()` |
| `/api/board-api/get-board-group-list` | ✅ | `RouteHandler` → `BoardApi::getBoardGroupList()` |
| `/api/menu-api/get-menu-list` | ✅ | `RouteHandler` → `MenuApi::getMenuList()` |

### RESTful API (표준 RESTful)

| URL | 매칭 | 처리 |
|-----|-----|------|
| `/api/v1/members` | ❌ Legacy 제외 | `MembersController::index()` |
| `/api/v1/members/123` | ❌ Legacy 제외 | `MembersController::show(123)` |
| `/api/v1/examples` | ❌ Legacy 제외 | `ExampleResourceController::index()` |
| `/api/v2/products` | ❌ Legacy 제외 | 미래 버전 (준비 가능) |

---

## 🔄 라우팅 처리 순서

CodeIgniter는 라우트를 **위에서 아래로** 순차적으로 매칭합니다.

### 1단계: RESTful API 그룹 (우선)

```php
$routes->group('api/v1', function ($routes) {
    $routes->resource('members', ...);
    // ...
});
```

- `/api/v1/*` 패턴이 먼저 검사됨
- 매칭되면 RESTful API 컨트롤러로 라우팅

### 2단계: Legacy API 패턴 (후순위)

```php
$routes->get('/api/(?!v\d+)(:segment)', ...);
```

- RESTful API에 매칭되지 않은 경우만 검사
- `v1`, `v2` 등은 negative lookahead로 제외
- 나머지는 RouteHandler로 라우팅

---

## 🧪 테스트

### 테스트 시나리오

#### 1. Legacy API 테스트

```bash
# 회원 목록 조회 (Legacy)
curl -X POST "http://localhost/api/member-api/get-member-list" \
  -H "Content-Type: application/json" \
  -d '{"draw":1,"start":0,"length":10}'

# 예상: MemberApi::getMemberList() 호출
```

#### 2. RESTful API 테스트

```bash
# 회원 목록 조회 (RESTful)
curl -X GET "http://localhost/api/v1/members?page=1&limit=10"

# 예상: MembersController::index() 호출
```

#### 3. 충돌 테스트

```bash
# Legacy 패턴이지만 v1으로 시작
curl -X GET "http://localhost/api/v1/test"

# 예상: 404 (Legacy API에서 제외됨, RESTful 라우트도 정의 안됨)
```

#### 4. 버전 확장 테스트

```bash
# v2 API (미래)
curl -X GET "http://localhost/api/v2/members"

# 예상: 404 (아직 v2 그룹 정의 안됨, Legacy에서 제외됨)
```

---

## 📝 라우팅 규칙 정리

### Legacy API 규칙

1. **패턴**: `/api/{controller}/{method}`
2. **제약**: `v1`, `v2`, ... `v9999` 등으로 시작 불가
3. **변환**: kebab-case → camelCase 자동 변환
4. **예시**:
   - ✅ `/api/member-api/get-list`
   - ✅ `/api/board-api/create-board`
   - ❌ `/api/v1/anything` (제외됨)

### RESTful API 규칙

1. **패턴**: `/api/{version}/{resource}`
2. **버전**: `v1`, `v2`, `v3`, ... (숫자로 구분)
3. **표준**: HTTP 메서드 + URI 리소스 조합
4. **예시**:
   - ✅ `GET /api/v1/members`
   - ✅ `POST /api/v1/members`
   - ✅ `DELETE /api/v1/members/123`

---

## 🎨 라우팅 플로우차트

```
클라이언트 요청
    │
    ├─ /api/v1/* ?
    │   │
    │   ├─ YES → RESTful API 그룹
    │   │           │
    │   │           ├─ /api/v1/members → MembersController
    │   │           ├─ /api/v1/examples → ExampleResourceController
    │   │           └─ /api/v1/jobs → JobsController
    │   │
    │   └─ NO → 계속 진행
    │
    ├─ /api/{v숫자}/* ?
    │   │
    │   └─ YES → 404 (아직 정의 안됨)
    │
    └─ /api/{controller}/{method} ?
        │
        └─ YES → Legacy API (RouteHandler)
                    │
                    ├─ kebab-case 변환
                    ├─ 컨트롤러 로드
                    └─ 메서드 실행
```

---

## ⚠️ 주의사항

### 1. 버전 이름 제약

RESTful API 버전은 **반드시** `v{숫자}` 형식이어야 합니다.

```
✅ 올바른 버전명:
- v1, v2, v3, v10, v100

❌ 잘못된 버전명:
- version1 (Legacy API로 처리됨)
- api-v1 (Legacy API로 처리됨)  
- v1.0 (점은 URL 세그먼트 구분자가 아님)
```

### 2. Legacy Controller 이름 제약

Legacy API 컨트롤러 이름은 `v{숫자}`로 시작할 수 없습니다.

```
❌ 사용 불가:
/api/v1api/get-list  (RESTful 패턴으로 오인)

✅ 사용 가능:
/api/version-api/get-list
/api/api-v1/get-list (세그먼트가 v1으로 시작하지 않음)
```

### 3. 라우트 정의 순서

RESTful API를 **반드시 먼저** 정의해야 합니다.

```php
// ✅ 올바른 순서
$routes->group('api/v1', ...);  // RESTful 먼저
$routes->get('/api/(?!v\d+)(:segment)', ...);  // Legacy 나중

// ❌ 잘못된 순서
$routes->get('/api/(:segment)', ...);  // Legacy 먼저 (모든 것 매칭)
$routes->group('api/v1', ...);  // RESTful 나중 (절대 도달 안함!)
```

---

## 🔧 추가 버전 추가 방법

### v2 API 추가 예시

```php
// Routes.php

// v1 API
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function ($routes) {
    $routes->resource('members', ['controller' => 'MembersController']);
});

// v2 API 추가
$routes->group('api/v2', ['namespace' => 'App\Controllers\Api\V2'], function ($routes) {
    $routes->resource('members', ['controller' => 'MembersController']);
    $routes->resource('orders', ['controller' => 'OrdersController']);
});

// Legacy API (자동으로 v1, v2 모두 제외됨)
$routes->get('/api/(?!v\d+)(:segment)', 'RouteHandler::handle/$1');
```

---

## 📚 관련 문서

- [Legacy API 구조](./legacy-api-structure.md) - 기존 API 동작 원리
- [RESTful API 가이드](./restful-api-guide.md) - 새로운 API 사용법
- [API 비교](./api-comparison.md) - Legacy vs RESTful 비교

---

## 🎯 결론

### 핵심 포인트

1. **명확한 분리**: negative lookahead를 사용한 패턴 제외
2. **순서 중요**: RESTful API 먼저, Legacy API 나중
3. **하위 호환성**: 기존 API 완전 유지
4. **확장 가능**: v2, v3 등 추가 버전 쉽게 지원

### 이점

- ✅ 기존 시스템 중단 없음
- ✅ 새로운 API는 RESTful 표준 준수
- ✅ 두 시스템 독립적 운영
- ✅ 점진적 마이그레이션 가능

---

## 버전

**v1.0.0** - 2025-11-03

초기 라우팅 분리 구성 완료

