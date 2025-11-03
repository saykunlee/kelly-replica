# 기존 (Legacy) API 구조 분석

## 📋 개요

기존 프로젝트에서 사용하던 API 라우팅 시스템의 구조와 동작 방식을 분석한 문서입니다.

---

## 🛤️ 라우팅 구조

### Routes.php 설정

```php
// 기존 API 요청을 처리하기 위한 동적 라우트
$routes->get('/api/(:segment)', 'RouteHandler::handle/$1');
$routes->post('/api/(:segment)/(:segment)', 'RouteHandler::handle/$1/$2');
$routes->get('/api/(:segment)/(:segment)', 'RouteHandler::handle/$1/$2');
```

**패턴**:
- `/api/{controller}` → `RouteHandler::handle($controller, 'index')`
- `/api/{controller}/{method}` → `RouteHandler::handle($controller, $method)`

---

## 🔄 요청 흐름

### 1단계: 클라이언트 요청

```
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
```

### 2단계: RouteHandler가 요청 수신

**URL 파싱**:
- `controller` = "member-api" (kebab-case)
- `method` = "get-member-search-list" (kebab-case)

### 3단계: 이름 변환 (kebab-case → CamelCase)

**RouteHandler::convertToCamelCase() 메서드**:

```php
// 컨트롤러 이름 변환 (첫 글자 대문자)
"member-api" → "MemberApi"

// 메서드 이름 변환 (첫 글자 소문자)
"get-member-search-list" → "getMemberSearchList"
```

**중요**: 컨트롤러명과 메서드명 **모두** kebab-case에서 camelCase로 자동 변환됩니다!

**변환 로직**:
```php
private function convertToCamelCase(string $string, bool $capitalizeFirstLetter = true): string
{
    // '-'(하이픈)을 기준으로 분리
    $words = explode('-', $string);
    $camelCaseString = '';
    
    // 각 단어의 첫 글자를 대문자로
    foreach ($words as $word) {
        $camelCaseString .= ucfirst($word);
    }
    
    // 메서드인 경우 첫 글자를 소문자로
    if (!$capitalizeFirstLetter) {
        $camelCaseString = lcfirst($camelCaseString);
    }
    
    return $camelCaseString;
}
```

### 4단계: 네임스페이스 생성

```php
$controllerClass = "App\Controllers\Api\\" . $controllerClass;
// 결과: "App\Controllers\Api\MemberApi"
```

### 5단계: 컨트롤러 존재 확인

```php
if (!class_exists($controllerClass)) {
    throw PageNotFoundException::forControllerNotFound($controllerClass);
}
```

### 6단계: 컨트롤러 인스턴스 생성 및 초기화

```php
$controllerInstance = new $controllerClass();
$controllerInstance->initController($request, $response, Services::logger());
```

### 7단계: 메서드 존재 확인 및 실행

```php
if (!method_exists($controllerInstance, $method)) {
    throw PageNotFoundException::forMethodNotFound($method);
}

return $controllerInstance->$method();
```

---

## 📁 파일 구조

```
app/
├── Config/
│   └── Routes.php                    # 라우팅 설정
├── Controllers/
│   ├── RouteHandler.php              # 동적 라우팅 핸들러
│   └── Api/
│       ├── MemberApi.php             # 회원 API 컨트롤러
│       ├── BoardApi.php              # 게시판 API 컨트롤러
│       ├── MenuApi.php               # 메뉴 API 컨트롤러
│       └── ...
└── Controllers/Base/
    └── BaseApiController.php         # API 컨트롤러 기본 클래스
```

---

## 🎯 실제 호출 예제

### 예제 1: 회원 검색 목록 조회 (kebab-case 메서드명)

**요청**:
```javascript
// 클라이언트 (JavaScript)
// URL의 메서드명을 kebab-case로 작성
fetch('/api/member-api/get-member-search-list', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        draw: 1,
        start: 0,
        length: 10,
        search: { mem_userid: 'test' }
    })
})
```

**처리 흐름**:
```
1. Routes.php
   URL: /api/member-api/get-member-search-list
   → RouteHandler::handle('member-api', 'get-member-search-list')

2. RouteHandler.php
   - 'member-api' → 'MemberApi' (첫 글자 대문자)
   - 'get-member-search-list' → 'getMemberSearchList' (첫 글자 소문자)
   - 네임스페이스: App\Controllers\Api\MemberApi
   
3. MemberApi.php
   - getMemberSearchList() 메서드 실행
   - BaseApiController의 기능 사용
   
4. 응답
   {
     "draw": 1,
     "recordsTotal": 100,
     "recordsFiltered": 10,
     "data": [...]
   }
```

**핵심 포인트**:
- URL: `/api/member-api/get-member-search-list` (kebab-case)
- 실제 메서드: `getMemberSearchList()` (camelCase)
- 자동 변환됨!

### 예제 2: 회원 상세 조회

**요청**:
```bash
curl -X POST http://localhost/api/member-api/getMemberDetails \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "mem_id=1"
```

**처리 흐름**:
```
1. URL 파싱: /api/member-api/getMemberDetails
   → controller: member-api
   → method: getMemberDetails

2. 변환
   → MemberApi::getMemberDetails()

3. 실행 및 응답
   {
     "status": "200",
     "data": {
       "mem_id": 1,
       "mem_userid": "admin",
       ...
     }
   }
```

---

## 🏗️ 컨트롤러 구조

### MemberApi 컨트롤러

```php
namespace App\Controllers\Api;

use App\Controllers\Base\BaseApiController;
use App\Models\MemberModel;

class MemberApi extends BaseApiController
{
    protected $modelName = MemberModel::class;

    public function index()
    {
        return $this->respond([
            'status' => '200',
            'message' => 'Member API index method'
        ]);
    }

    public function getMemberList()
    {
        // 회원 목록 조회 로직
    }

    public function getMemberDetails()
    {
        // 회원 상세 조회 로직
    }

    public function createMember()
    {
        // 회원 생성 로직
    }

    public function updateMember()
    {
        // 회원 수정 로직
    }

    public function deleteMember()
    {
        // 회원 삭제 로직
    }
}
```

---

## 📊 URL 매핑 테이블

### camelCase URL (기존 방식)

| 클라이언트 URL | 컨트롤러 | 메서드 |
|---------------|---------|--------|
| `/api/member-api` | `MemberApi` | `index()` |
| `/api/member-api/getMemberList` | `MemberApi` | `getMemberList()` |
| `/api/member-api/getMemberDetails` | `MemberApi` | `getMemberDetails()` |
| `/api/member-api/createMember` | `MemberApi` | `createMember()` |

### kebab-case URL (권장 방식)

| 클라이언트 URL | 컨트롤러 | 메서드 |
|---------------|---------|--------|
| `/api/member-api/get-member-list` | `MemberApi` | `getMemberList()` |
| `/api/member-api/get-member-search-list` | `MemberApi` | `getMemberSearchList()` |
| `/api/member-api/get-member-details` | `MemberApi` | `getMemberDetails()` |
| `/api/member-api/create-member` | `MemberApi` | `createMember()` |
| `/api/member-api/update-member` | `MemberApi` | `updateMember()` |
| `/api/member-api/delete-member` | `MemberApi` | `deleteMember()` |
| `/api/member-api/check-user-id` | `MemberApi` | `checkUserId()` |
| `/api/board-api/get-board-group-list` | `BoardApi` | `getBoardGroupList()` |
| `/api/menu-api/get-menu-list` | `MenuApi` | `getMenuList()` |

**참고**: 두 방식 모두 작동하지만, kebab-case가 URL 표준에 더 적합합니다.

---

## 🔍 Naming Convention (매우 중요!)

### Kebab-case → CamelCase 변환 규칙

RouteHandler는 **컨트롤러명과 메서드명 모두**를 자동 변환합니다.

#### 컨트롤러명 변환 (첫 글자 대문자)

| URL (kebab-case) | 컨트롤러 (CamelCase) |
|------------------|---------------------|
| `member-api` | `MemberApi` |
| `board-api` | `BoardApi` |
| `menu-api` | `MenuApi` |
| `estimate-api` | `EstimateApi` |

#### 메서드명 변환 (첫 글자 소문자)

| URL (kebab-case) | 메서드 (camelCase) |
|------------------|-------------------|
| `get-member-list` | `getMemberList()` |
| `get-member-search-list` | `getMemberSearchList()` |
| `create-member` | `createMember()` |
| `get-board-group-list` | `getBoardGroupList()` |
| `update-board-group` | `updateBoardGroup()` |
| `check-user-id` | `checkUserId()` |

#### 실제 URL → 메서드 매핑 예시

| 전체 URL | 컨트롤러 | 메서드 |
|---------|---------|--------|
| `/api/member-api/get-member-search-list` | `MemberApi` | `getMemberSearchList()` |
| `/api/board-api/get-board-group-list` | `BoardApi` | `getBoardGroupList()` |
| `/api/member-api/check-user-id` | `MemberApi` | `checkUserId()` |

**변환 규칙**:
1. 하이픈(`-`)으로 구분된 각 단어의 첫 글자를 대문자로 변환
2. 하이픈은 제거
3. **컨트롤러명**: 첫 글자 대문자 유지 (`MemberApi`)
4. **메서드명**: 첫 글자 소문자로 변경 (`getMemberSearchList`)

---

## ⚙️ BaseApiController 주요 기능

```php
class BaseApiController extends ResourceController
{
    // 유효성 검증
    protected function generateValidationRules($postData, $customRules = []);
    
    // CRUD 작업
    public function createRecord($tableName, $postData, $validationRules, $returnType);
    public function updateRecord($tableName, $postData, $validationRules, $returnType, $primaryKey);
    public function deleteRecord($tableName, $id, $primaryKey, $returnType);
    public function softDeleteRecord($tableName, $id, $primaryKey, $returnType, $deleteColumn);
    
    // 데이터 조회
    protected function getDataList($tableName, $postData, $orderColumn, $orderDirection);
    
    // 응답 처리
    protected function handleResponse($result, $successMessage);
    public function returnResponse($data, $status, $returnType, $code);
    
    // 인증
    public function required_user_login();
    public function required_user_login_response();
}
```

---

## 📝 응답 형식

### 성공 응답 (목록)

```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 10,
  "data": [
    {
      "mem_id": 1,
      "mem_userid": "admin",
      "mem_email": "admin@example.com"
    }
  ],
  "columns": ["mem_id", "mem_userid", "mem_email"]
}
```

### 성공 응답 (단일)

```json
{
  "status": "200",
  "data": {
    "mem_id": 1,
    "mem_userid": "admin",
    "mem_email": "admin@example.com"
  }
}
```

### 에러 응답 (유효성 검증)

```json
{
  "status": "422",
  "error": "유효성 검사 오류",
  "messages": [
    {
      "field": "mem_userid",
      "message": "사용자 ID 필드는 필수입니다."
    }
  ]
}
```

---

## 🔄 새로운 API vs 기존 API 비교

### 기존 API (Legacy)

**URL 구조**:
```
POST /api/member-api/getMemberList
POST /api/member-api/createMember
POST /api/member-api/updateMember
POST /api/member-api/deleteMember
```

**특징**:
- ❌ HTTP 메서드를 제대로 활용하지 않음 (대부분 POST)
- ❌ URL에 동작(메서드명)이 포함됨
- ❌ RPC 스타일 (Remote Procedure Call)
- ❌ 표준화되지 않은 응답 형식
- ❌ HATEOAS 미지원
- ❌ 버전 관리 없음
- ✅ 간단하고 직관적
- ✅ kebab-case 자동 변환

### 새로운 RESTful API

**URL 구조**:
```
GET    /api/v1/members          # 목록 조회
GET    /api/v1/members/1        # 상세 조회
POST   /api/v1/members          # 생성
PUT    /api/v1/members/1        # 전체 수정
PATCH  /api/v1/members/1        # 부분 수정
DELETE /api/v1/members/1        # 삭제
```

**특징**:
- ✅ HTTP 메서드를 올바르게 활용
- ✅ 리소스 중심 설계 (명사)
- ✅ RESTful 스타일
- ✅ 표준화된 응답 형식
- ✅ HATEOAS 지원
- ✅ 버전 관리 (v1, v2)
- ✅ ETag 캐싱
- ✅ Rate Limiting
- ✅ 페이지네이션 표준화

---

## 🔀 마이그레이션 예시

### 기존 방식 → RESTful 방식

#### 1. 목록 조회

**기존**:
```javascript
POST /api/member-api/getMemberList
{
  "draw": 1,
  "start": 0,
  "length": 10
}
```

**RESTful**:
```javascript
GET /api/v1/members?page=1&limit=10
```

#### 2. 상세 조회

**기존**:
```javascript
POST /api/member-api/getMemberDetails
{
  "mem_id": 1
}
```

**RESTful**:
```javascript
GET /api/v1/members/1
```

#### 3. 생성

**기존**:
```javascript
POST /api/member-api/createMember
{
  "mem_userid": "newuser",
  "mem_email": "new@example.com"
}
```

**RESTful**:
```javascript
POST /api/v1/members
{
  "mem_userid": "newuser",
  "mem_email": "new@example.com"
}
```

#### 4. 수정

**기존**:
```javascript
POST /api/member-api/updateMember
{
  "mem_id": 1,
  "mem_username": "수정된이름"
}
```

**RESTful**:
```javascript
PATCH /api/v1/members/1
{
  "mem_username": "수정된이름"
}
```

#### 5. 삭제

**기존**:
```javascript
POST /api/member-api/deleteMember
{
  "mem_id": 1
}
```

**RESTful**:
```javascript
DELETE /api/v1/members/1
```

---

## 🎯 호환성 전략

### 두 가지 API 동시 운영

현재 설정은 기존 API와 새로운 RESTful API가 **공존**할 수 있도록 구성되어 있습니다.

```php
// Routes.php

// 새로운 RESTful API (v1)
$routes->group('api/v1', function ($routes) {
    $routes->resource('members', ['controller' => 'MembersController']);
});

// 기존 Legacy API (유지)
$routes->get('/api/(:segment)', 'RouteHandler::handle/$1');
$routes->post('/api/(:segment)/(:segment)', 'RouteHandler::handle/$1/$2');
$routes->get('/api/(:segment)/(:segment)', 'RouteHandler::handle/$1/$2');
```

**장점**:
- ✅ 기존 시스템을 중단 없이 유지
- ✅ 점진적 마이그레이션 가능
- ✅ 새로운 기능은 RESTful로 개발
- ✅ 레거시 코드는 필요시 리팩토링

**URL 구분**:
- 기존 API: `/api/member-api/getMemberList`
- 새 API: `/api/v1/members`

---

## 📌 정리

### 기존 API의 핵심 동작

1. **URL 파싱**: kebab-case URL 파싱
2. **이름 변환**: RouteHandler가 CamelCase로 변환
3. **동적 라우팅**: 네임스페이스 기반 컨트롤러 로딩
4. **메서드 실행**: 리플렉션을 통한 메서드 호출

### 장점
- 간단한 라우팅 설정
- 직관적인 URL 구조
- kebab-case 자동 변환

### 단점
- RESTful 원칙 미준수
- HTTP 메서드 활용 부족
- 버전 관리 없음
- 표준화 부족

### 권장 사항
- 신규 개발: RESTful API 사용
- 기존 코드: 유지 또는 점진적 마이그레이션
- 두 시스템 병행 운영 가능

---

## 📚 참고 문서

- [RESTful API 프레임워크 가이드](./restful-api-guide.md)
- [RESTful API 구조 문서](./restful-api-structure.md)
- [BaseApiController 소스 코드](../app/Controllers/Base/BaseApiController.php)

