# RESTful API 동적 라우팅 가이드

## 📋 개요

RESTful API 동적 라우팅은 새로운 리소스 컨트롤러를 생성할 때 **라우팅 파일을 수정할 필요 없이** 자동으로 라우팅되도록 하는 기능입니다.

---

## 🎯 주요 장점

- ✅ **자동 라우팅**: 컨트롤러만 만들면 즉시 사용 가능
- ✅ **일관성**: 모든 리소스가 동일한 RESTful 패턴 준수
- ✅ **유지보수성**: Routes.php 파일이 간결해짐
- ✅ **확장성**: 새로운 버전(v2, v3 등) 추가 용이

---

## 🚀 사용 방법

### 1. 컨트롤러 생성

`app/Controllers/Api/V1/ProductsController.php`:

```php
<?php

namespace App\Controllers\Api\V1;

use App\Libraries\RestApi\RestApiController;

class ProductsController extends RestApiController
{
    protected $modelName = 'App\Models\ProductModel';
    protected $resourceName = 'products';
    protected $apiVersion = 'v1';
    
    protected function getValidationRules(string $action): array
    {
        // 유효성 검증 규칙
        return [];
    }
}
```

### 2. 즉시 사용 가능

**라우팅 파일 수정 없이** 바로 사용 가능:

```bash
# 목록 조회
GET /api/v1/products

# 상세 조회
GET /api/v1/products/123

# 생성
POST /api/v1/products

# 전체 수정
PUT /api/v1/products/123

# 부분 수정
PATCH /api/v1/products/123

# 삭제
DELETE /api/v1/products/123
```

---

## 🔄 라우팅 우선순위

Routes.php에서 라우팅은 **위에서 아래로** 순차적으로 매칭됩니다.

### 우선순위 순서

```
1순위: 명시적 커스텀 라우트
   ↓
2순위: resource() 명시적 리소스 라우트
   ↓
3순위: 동적 라우팅 (RestfulRouteHandler)
   ↓
4순위: 404 Not Found
```

### 예시

```php
// Routes.php

// 1순위: 커스텀 엔드포인트 (가장 먼저 매칭)
$routes->get('api/v1/members/test', 'MembersController::test');

// 2순위: 명시적 리소스 라우트
$routes->resource('api/v1/members', ['controller' => 'MembersController']);

// 3순위: 동적 라우팅 (위에서 매칭 안된 것들)
// products, orders 등 명시적으로 정의 안된 리소스들
$routes->match(['get', 'post'], 'api/(v\d+)/(:segment)', 'RestfulRouteHandler::handle/$1/$2');
```

### 실제 매칭 예시

| 요청 | 매칭 방식 | 처리 |
|------|----------|------|
| `GET /api/v1/members/test` | 1순위 (명시적) | `MembersController::test()` |
| `GET /api/v1/members` | 2순위 (resource) | `MembersController::index()` |
| `GET /api/v1/members/123` | 2순위 (resource) | `MembersController::show(123)` |
| `GET /api/v1/products` | 3순위 (동적) | `ProductsController::index()` |
| `GET /api/v1/products/456` | 3순위 (동적) | `ProductsController::show(456)` |
| `GET /api/v1/nonexistent` | 404 | 컨트롤러 없음 |

---

## 🛠️ RestfulRouteHandler 동작 원리

### 1. 요청 분석

```
GET /api/v1/products/123
  ↓
version: v1
resource: products
id: 123
method: GET
```

### 2. 컨트롤러 경로 생성

```
resource: products
  ↓ (kebab-case → PascalCase)
controllerName: ProductsController
  ↓
namespace: App\Controllers\Api\V1
  ↓
fullPath: App\Controllers\Api\V1\ProductsController
```

### 3. HTTP 메서드 → 액션 매핑

| HTTP 메서드 | ID 존재 | 액션 | 예시 |
|------------|--------|------|------|
| GET | X | `index()` | 목록 조회 |
| GET | O | `show($id)` | 상세 조회 |
| POST | X | `create()` | 생성 |
| PUT | O | `update($id)` | 전체 수정 |
| PATCH | O | `patch($id)` | 부분 수정 |
| DELETE | O | `delete($id)` | 삭제 |

### 4. 컨트롤러 호출

```php
// 컨트롤러 존재 확인
if (!class_exists($controllerClass)) {
    return 404;
}

// 메서드 존재 확인
if (!method_exists($controller, $action)) {
    return 405;
}

// 호출
return $controller->{$action}($id);
```

---

## 📁 네이밍 규칙

### 리소스명 → 컨트롤러명 변환

| URL 리소스 | 컨트롤러명 | 설명 |
|-----------|----------|------|
| `members` | `MembersController` | 일반 복수형 |
| `products` | `ProductsController` | 일반 복수형 |
| `product-categories` | `ProductCategoriesController` | kebab-case → PascalCase |
| `user_profiles` | `UserProfilesController` | snake_case → PascalCase |

### 버전 네임스페이스

| URL 버전 | 네임스페이스 |
|---------|------------|
| `/api/v1/products` | `App\Controllers\Api\V1\ProductsController` |
| `/api/v2/products` | `App\Controllers\Api\V2\ProductsController` |
| `/api/v10/products` | `App\Controllers\Api\V10\ProductsController` |

---

## 💡 커스텀 엔드포인트 추가

동적 라우팅을 사용하더라도 커스텀 엔드포인트는 명시적으로 정의해야 합니다.

### 방법 1: 명시적 라우팅 (권장)

```php
// Routes.php
$routes->get('api/v1/members/test', 'MembersController::test');
$routes->post('api/v1/products/(:num)/activate', 'ProductsController::activate/$1');
```

### 방법 2: 컨트롤러 내부 분기

```php
// MembersController.php
public function show($id = null)
{
    // 특수 ID 처리
    if ($id === 'test') {
        return $this->test();
    }
    
    // 일반 조회
    return parent::show($id);
}
```

---

## 🧪 테스트

### 1. 새로운 리소스 추가 테스트

```bash
# 1. ProductsController 생성
# app/Controllers/Api/V1/ProductsController.php

# 2. 즉시 테스트 가능
curl -X GET "http://localhost/api/v1/products"
```

### 2. 존재하지 않는 리소스 테스트

```bash
curl -X GET "http://localhost/api/v1/nonexistent"

# 응답: 404
{
  "status": 404,
  "success": false,
  "message": "API 리소스를 찾을 수 없습니다: nonexistent",
  "errorCode": "RESOURCE_NOT_FOUND"
}
```

### 3. 지원하지 않는 HTTP 메서드 테스트

```bash
# ProductsController에 patch() 메서드가 없는 경우
curl -X PATCH "http://localhost/api/v1/products/123"

# 응답: 405
{
  "status": 405,
  "success": false,
  "message": "PATCH 메서드는 이 리소스에서 지원되지 않습니다",
  "errorCode": "METHOD_NOT_ALLOWED"
}
```

---

## 🔧 명시적 라우팅과의 비교

### 기존 방식 (명시적 라우팅)

```php
// Routes.php
$routes->resource('members', ['controller' => 'MembersController']);
$routes->resource('products', ['controller' => 'ProductsController']);
$routes->resource('orders', ['controller' => 'OrdersController']);
$routes->resource('categories', ['controller' => 'CategoriesController']);
// ... 리소스마다 추가 필요
```

### 새로운 방식 (동적 라우팅)

```php
// Routes.php
// 동적 라우팅만 정의 (한 번만)
$routes->match(['get', 'post'], 'api/(v\d+)/(:segment)', 'RestfulRouteHandler::handle/$1/$2');
$routes->match(['get', 'put', 'patch', 'delete'], 'api/(v\d+)/(:segment)/(:segment)', 'RestfulRouteHandler::handle/$1/$2/$3');

// 컨트롤러만 생성하면 자동 라우팅
// app/Controllers/Api/V1/MembersController.php
// app/Controllers/Api/V1/ProductsController.php
// app/Controllers/Api/V1/OrdersController.php
// ...
```

---

## ⚠️ 주의사항

### 1. 커스텀 엔드포인트는 명시적 정의 필수

```php
// ❌ 동적 라우팅으로 처리 불가
GET /api/v1/members/test
GET /api/v1/products/search

// ✅ 명시적으로 정의 필요
$routes->get('api/v1/members/test', 'MembersController::test');
$routes->get('api/v1/products/search', 'ProductsController::search');
```

### 2. 컨트롤러 네이밍 규칙 준수

```
✅ 올바른 네이밍:
- MembersController (복수형)
- ProductsController (복수형)
- ProductCategoriesController (PascalCase)

❌ 잘못된 네이밍:
- MemberController (단수형)
- productsController (소문자 시작)
- Product_Controller (언더스코어)
```

### 3. 네임스페이스 규칙

```
✅ 올바른 네임스페이스:
- App\Controllers\Api\V1\ProductsController
- App\Controllers\Api\V2\ProductsController

❌ 잘못된 네임스페이스:
- App\Controllers\ProductsController (Api\V1 누락)
- App\Api\V1\ProductsController (Controllers 누락)
```

---

## 🎨 디렉토리 구조

```
app/Controllers/
├── Api/
│   ├── V1/
│   │   ├── MembersController.php      (자동 라우팅)
│   │   ├── ProductsController.php     (자동 라우팅)
│   │   ├── OrdersController.php       (자동 라우팅)
│   │   └── JobsController.php         (자동 라우팅)
│   └── V2/
│       ├── MembersController.php      (자동 라우팅)
│       └── ProductsController.php     (자동 라우팅)
├── RestfulRouteHandler.php            (동적 라우팅 핸들러)
└── RouteHandler.php                   (Legacy API 핸들러)
```

---

## 🚀 v2 API 추가 예시

### 1. v2 컨트롤러 생성

```php
// app/Controllers/Api/V2/MembersController.php
namespace App\Controllers\Api\V2;

use App\Libraries\RestApi\RestApiController;

class MembersController extends RestApiController
{
    protected $modelName = 'App\Models\MemberModel';
    protected $resourceName = 'members';
    protected $apiVersion = 'v2';
    
    // v2 특화 기능 추가
}
```

### 2. 즉시 사용 가능

```bash
# v1 API (기존)
GET /api/v1/members

# v2 API (새로 추가)
GET /api/v2/members
```

**라우팅 파일 수정 없이 자동으로 라우팅됩니다!**

---

## 📊 성능 고려사항

### 동적 라우팅의 오버헤드

1. **컨트롤러 존재 확인**: `class_exists()` - 매우 빠름 (autoload 캐시)
2. **메서드 존재 확인**: `method_exists()` - 매우 빠름
3. **문자열 변환**: kebab-case → PascalCase - 무시할 수준

### 최적화

명시적 라우팅과 성능 차이는 **무시할 수준**이며, 유지보수성 향상이 훨씬 큽니다.

자주 사용되는 리소스는 명시적으로 정의하여 약간의 성능 향상 가능:

```php
// 자주 사용되는 리소스는 명시적 정의 (선택적 최적화)
$routes->resource('api/v1/members', ['controller' => 'MembersController']);

// 나머지는 동적 라우팅
$routes->match(...);
```

---

## ✅ 체크리스트

### 새로운 리소스 추가 시

- ✅ 컨트롤러를 `App\Controllers\Api\V{version}\` 네임스페이스에 생성
- ✅ 컨트롤러명은 복수형 + `Controller` 접미사 (예: `ProductsController`)
- ✅ `RestApiController` 상속
- ✅ `$modelName`, `$resourceName`, `$apiVersion` 설정
- ✅ 유효성 검증 규칙 정의
- ⬜ Routes.php 수정 필요 없음 (자동 라우팅)

### 커스텀 엔드포인트 추가 시

- ✅ Routes.php에 명시적으로 정의
- ✅ resource 라우팅보다 **먼저** 정의 (우선순위)
- ✅ 컨트롤러에 해당 메서드 구현

---

## 📚 관련 문서

- [RESTful API 구조](./restful-api-structure.md)
- [RESTful API 가이드](./restful-api-guide.md)
- [라우팅 분리 가이드](./routing-separation.md)

---

## 🎯 결론

### 동적 라우팅의 이점

1. **개발 속도 향상**: 라우팅 파일 수정 불필요
2. **일관성 보장**: 모든 리소스가 동일한 패턴
3. **유지보수 간소화**: Routes.php 파일이 간결해짐
4. **확장성**: 새로운 버전 추가 용이

### 권장 사용 패턴

```php
// Routes.php

// 1. 커스텀 엔드포인트 (필요시)
$routes->get('api/v1/members/test', 'MembersController::test');

// 2. 자주 사용되는 리소스 (선택적 최적화)
$routes->resource('api/v1/members', ['controller' => 'MembersController']);

// 3. 동적 라우팅 (나머지 모든 리소스)
$routes->match(['get', 'post'], 'api/(v\d+)/(:segment)', 'RestfulRouteHandler::handle/$1/$2');
$routes->match(['get', 'put', 'patch', 'delete'], 'api/(v\d+)/(:segment)/(:segment)', 'RestfulRouteHandler::handle/$1/$2/$3');
```

---

## 버전

**v1.0.0** - 2025-11-03

RESTful API 동적 라우팅 기능 구현 완료

