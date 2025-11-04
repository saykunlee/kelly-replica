# Legacy API Naming Convention 상세 예시

## 📋 개요

Legacy API의 RouteHandler는 kebab-case URL을 camelCase 클래스/메서드로 **자동 변환**합니다.

---

## 🔄 변환 규칙

### 1. 컨트롤러명 변환 (첫 글자 대문자)

```
URL 세그먼트          →  PHP 클래스명
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
member-api           →  MemberApi
board-api            →  BoardApi
menu-api             →  MenuApi
estimate-api         →  EstimateApi
datatable-settings-api → DatatableSettingsApi
```

### 2. 메서드명 변환 (첫 글자 소문자)

```
URL 세그먼트                →  PHP 메서드명
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
get-member-list           →  getMemberList()
get-member-search-list    →  getMemberSearchList()
get-member-details        →  getMemberDetails()
create-member             →  createMember()
update-member             →  updateMember()
delete-member             →  deleteMember()
check-user-id             →  checkUserId()
check-email               →  checkEmail()
check-nickname            →  checkNickname()
get-login-logs            →  getLoginLogs()
get-log-detail            →  getLogDetail()
get-board-group-list      →  getBoardGroupList()
get-board-group-details   →  getBoardGroupDetails()
update-board-group        →  updateBoardGroup()
delete-board-group        →  deleteBoardGroup()
create-board-group        →  createBoardGroup()
```

---

## 📊 실제 매핑 예시

### MemberApi 컨트롤러

| URL | 실제 호출되는 메서드 |
|-----|-------------------|
| `/api/member-api` | `MemberApi::index()` |
| `/api/member-api/get-member-list` | `MemberApi::getMemberList()` |
| `/api/member-api/get-member-search-list` | `MemberApi::getMemberSearchList()` |
| `/api/member-api/get-member-details` | `MemberApi::getMemberDetails()` |
| `/api/member-api/create-member` | `MemberApi::createMember()` |
| `/api/member-api/update-member` | `MemberApi::updateMember()` |
| `/api/member-api/delete-member` | `MemberApi::deleteMember()` |
| `/api/member-api/check-user-id` | `MemberApi::checkUserId()` |
| `/api/member-api/check-email` | `MemberApi::checkEmail()` |
| `/api/member-api/check-nickname` | `MemberApi::checkNickname()` |
| `/api/member-api/get-login-logs` | `MemberApi::getLoginLogs()` |
| `/api/member-api/get-log-detail` | `MemberApi::getLogDetail()` |

### BoardApi 컨트롤러

| URL | 실제 호출되는 메서드 |
|-----|-------------------|
| `/api/board-api` | `BoardApi::index()` |
| `/api/board-api/get-board-group-list` | `BoardApi::getBoardGroupList()` |
| `/api/board-api/get-board-group-details` | `BoardApi::getBoardGroupDetails()` |
| `/api/board-api/update-board-group` | `BoardApi::updateBoardGroup()` |
| `/api/board-api/delete-board-group` | `BoardApi::deleteBoardGroup()` |
| `/api/board-api/create-board-group` | `BoardApi::createBoardGroup()` |
| `/api/board-api/get-board-list` | `BoardApi::getBoardList()` |
| `/api/board-api/get-board-details` | `BoardApi::getBoardDetails()` |
| `/api/board-api/update-board` | `BoardApi::updateBoard()` |
| `/api/board-api/delete-board` | `BoardApi::deleteBoard()` |
| `/api/board-api/create-board` | `BoardApi::createBoard()` |
| `/api/board-api/get-post-admin-list` | `BoardApi::getPostAdminList()` |
| `/api/board-api/update-post` | `BoardApi::updatePost()` |
| `/api/board-api/delete-post` | `BoardApi::deletePost()` |
| `/api/board-api/create-post` | `BoardApi::createPost()` |

---

## 🔍 변환 알고리즘

### RouteHandler::convertToCamelCase()

```php
private function convertToCamelCase(string $string, bool $capitalizeFirstLetter = true): string
{
    // 1. 하이픈으로 분리
    $words = explode('-', $string);
    $camelCaseString = '';

    // 2. 각 단어의 첫 글자를 대문자로
    foreach ($words as $word) {
        $camelCaseString .= ucfirst($word);
    }

    // 3. 메서드명인 경우 첫 글자를 소문자로
    if (!$capitalizeFirstLetter) {
        $camelCaseString = lcfirst($camelCaseString);
    }

    return $camelCaseString;
}
```

### 변환 과정 예시

#### 예시 1: `get-member-search-list`

```
입력: "get-member-search-list"
capitalizeFirstLetter: false (메서드명)

1단계: explode('-')
  → ["get", "member", "search", "list"]

2단계: ucfirst() 각 단어
  → "Get" + "Member" + "Search" + "List"
  → "GetMemberSearchList"

3단계: lcfirst() (첫 글자 소문자)
  → "getMemberSearchList"

결과: getMemberSearchList()
```

#### 예시 2: `member-api`

```
입력: "member-api"
capitalizeFirstLetter: true (컨트롤러명)

1단계: explode('-')
  → ["member", "api"]

2단계: ucfirst() 각 단어
  → "Member" + "Api"
  → "MemberApi"

3단계: 첫 글자 대문자 유지
  → "MemberApi"

결과: MemberApi
```

---

## 💡 주의사항

### ✅ 올바른 사용법

```javascript
// kebab-case 사용 (권장)
POST /api/member-api/get-member-search-list
POST /api/board-api/get-board-group-list
POST /api/member-api/check-user-id
```

### ⚠️ 가능하지만 권장하지 않음

```javascript
// camelCase 사용 (가능하지만 URL 표준에 맞지 않음)
POST /api/member-api/getMemberSearchList
POST /api/board-api/getBoardGroupList
POST /api/member-api/checkUserId
```

이 경우도 작동하지만, URL에는 kebab-case를 사용하는 것이 웹 표준입니다.

### ❌ 작동하지 않는 경우

```javascript
// snake_case (변환 안됨)
POST /api/member_api/get_member_search_list

// PascalCase (변환 안됨)
POST /api/MemberApi/GetMemberSearchList

// 공백 포함 (변환 안됨)
POST /api/member api/get member search list
```

RouteHandler는 **하이픈(`-`)만** 인식하여 변환합니다.

---

## 🧪 테스트 예시

### curl을 사용한 테스트

```bash
# 회원 검색 목록 조회
curl -X POST "http://localhost/api/member-api/get-member-search-list" \
  -H "Content-Type: application/json" \
  -d '{
    "draw": 1,
    "start": 0,
    "length": 10,
    "search": {
      "mem_userid": "test"
    }
  }'

# 사용자 ID 중복 체크
curl -X POST "http://localhost/api/member-api/check-user-id" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "mem_userid=testuser"

# 게시판 그룹 목록 조회
curl -X POST "http://localhost/api/board-api/get-board-group-list" \
  -H "Content-Type: application/json" \
  -d '{
    "draw": 1,
    "start": 0,
    "length": 10
  }'
```

### JavaScript fetch 예시

```javascript
// 회원 검색 목록
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
.then(res => res.json())
.then(data => console.log(data));

// 사용자 ID 체크
fetch('/api/member-api/check-user-id', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'mem_userid=testuser'
})
.then(res => res.json())
.then(data => console.log(data));
```

---

## 📝 정리

### 핵심 포인트

1. **컨트롤러명**: kebab-case → PascalCase (첫 글자 대문자)
   - `member-api` → `MemberApi`

2. **메서드명**: kebab-case → camelCase (첫 글자 소문자)
   - `get-member-search-list` → `getMemberSearchList()`

3. **하이픈(`-`)만 인식**: 다른 구분자는 사용 불가

4. **자동 변환**: RouteHandler가 모든 변환 처리

5. **URL 표준**: kebab-case 사용 권장

---

## 🔗 관련 문서

- [Legacy API 구조 분석](./legacy-api-structure.md)
- [Legacy vs RESTful 비교](./api-comparison.md)
- [RouteHandler 소스 코드](../app/Controllers/RouteHandler.php)

