# Kelly API 테스트 가이드

## 🚀 시작하기

### 1. REST Client 확장 프로그램 설치

VS Code/Cursor Extensions에서 설치:
- **REST Client** (Huachao Mao)

### 2. 서버 실행

```bash
# Kelly 서버 시작
php spark serve --port=3005
```

### 3. 테스트 파일 열기

- `tests/api/members.http` 파일 열기
- 각 요청 위의 **"Send Request"** 클릭
- 또는 단축키 사용:
  - **Windows/Linux:** `Ctrl + Alt + R`
  - **Mac:** `Cmd + Alt + R`

---

## 📁 파일 구조

```
tests/api/
├── members.http              # Members API 테스트
├── http-client.env.json      # 환경 변수 설정
└── README.md                 # 이 파일
```

---

## 🔧 환경 설정

### 환경 전환

`http-client.env.json` 에서 환경별 설정 관리:

- **development**: 로컬 개발 (기본)
- **staging**: 스테이징 서버
- **production**: 프로덕션 서버

REST Client 하단에서 환경 선택 가능

---

## 📝 .http 파일 사용법

### 기본 요청

```http
### 회원 목록 조회
GET http://localhost:3005/api/v1/members
Accept: application/json
```

### 변수 사용

```http
@baseUrl = http://localhost:3005
@apiVersion = v1

GET {{baseUrl}}/api/{{apiVersion}}/members
```

### POST 요청

```http
POST {{baseUrl}}/api/v1/members
Content-Type: application/json

{
  "mem_userid": "testuser",
  "mem_password": "password123",
  "mem_email": "test@example.com",
  "mem_username": "테스트"
}
```

### 응답 저장 및 재사용

```http
# @name createMember
POST {{baseUrl}}/api/v1/members
Content-Type: application/json

{
  "mem_userid": "testuser"
}

### 생성된 ID로 조회
GET {{baseUrl}}/api/v1/members/{{createMember.response.body.data.mem_id}}
```

---

## 🎯 주요 테스트 시나리오

### 1. CRUD 테스트
- ✅ 목록 조회 (페이지네이션, 정렬, 필터링)
- ✅ 상세 조회
- ✅ 생성
- ✅ 전체 수정 (PUT)
- ✅ 부분 수정 (PATCH)
- ✅ 삭제

### 2. 유효성 검증 테스트
- ✅ 필수 필드 누락
- ✅ 짧은 비밀번호
- ✅ 중복 이메일/사용자ID
- ✅ 잘못된 이메일 형식

### 3. 캐싱 테스트
- ✅ ETag 생성 확인
- ✅ If-None-Match 조건부 요청 (304)
- ✅ If-Match 낙관적 동시성 제어

### 4. 에러 처리 테스트
- ✅ 404 (존재하지 않는 리소스)
- ✅ 400 (잘못된 요청)
- ✅ 412 (Precondition Failed - ETag 불일치)

### 5. HATEOAS 링크 검증
- ✅ self, collection, update, delete 링크
- ✅ 페이지네이션 링크 (prev, next)

---

## 🔥 유용한 기능

### 1. 내장 변수
```http
### 타임스탬프
GET {{baseUrl}}/test?timestamp={{$timestamp}}

### 랜덤 정수
GET {{baseUrl}}/test?random={{$randomInt}}

### GUID
GET {{baseUrl}}/test?guid={{$guid}}
```

### 2. 여러 요청 순차 실행
```http
# @name step1
POST {{baseUrl}}/api/v1/members
Content-Type: application/json

{
  "mem_userid": "user{{$timestamp}}"
}

### step1의 결과 사용
GET {{baseUrl}}/api/v1/members/{{step1.response.body.data.mem_id}}
```

### 3. 환경 변수 오버라이드
파일 상단에서 변수 재정의:
```http
@baseUrl = http://localhost:9000
```

---

## 📊 응답 확인

### 응답 헤더
- `Content-Type`: 응답 형식
- `ETag`: 캐시 태그
- `Cache-Control`: 캐시 제어
- `Location`: 생성된 리소스 위치 (POST)

### 응답 본문 구조
```json
{
  "status": "success",
  "data": { ... },
  "links": [
    {
      "rel": "self",
      "href": "http://...",
      "method": "GET"
    }
  ]
}
```

---

## 🐛 트러블슈팅

### 연결 오류
```
Error: connect ECONNREFUSED 127.0.0.1:3005
```
→ 서버가 실행 중인지 확인: `php spark serve --port=3005`

### 404 오류
→ 라우팅 설정 확인: `app/Config/Routes.php`

### 500 오류
→ 서버 로그 확인: `writable/logs/`

---

## 💡 팁

1. **주석 활용**: `###`로 섹션 구분
2. **변수 저장**: `@name`으로 응답 저장 후 재사용
3. **일괄 테스트**: 여러 요청을 순서대로 실행
4. **버전 관리**: `.http` 파일을 Git으로 관리하여 팀 공유
5. **환경 분리**: `http-client.env.json`으로 환경별 설정 관리

---

## 🔗 관련 문서

- [REST Client 공식 문서](https://marketplace.visualstudio.com/items?itemName=humao.rest-client)
- [Kelly RESTful API 가이드](../../doc/restful-api-guide.md)
- [API 설계 문서](../../doc/restful-api-structure.md)

---

## 📞 문의

문제가 있거나 추가할 테스트가 있으면 이슈를 생성해주세요.

