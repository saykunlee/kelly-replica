# API 도구 활용 가이드

Kelly API를 다양한 도구에서 사용하는 방법을 안내합니다.

---

## 📋 목차

1. [Postman으로 OpenAPI 임포트](#postman으로-openapi-임포트)
2. [Insomnia로 OpenAPI 임포트](#insomnia로-openapi-임포트)
3. [Swagger UI로 문서 보기](#swagger-ui로-문서-보기)
4. [클라이언트 코드 자동 생성](#클라이언트-코드-자동-생성)
5. [AI 에이전트 통합](#ai-에이전트-통합)

---

## Postman으로 OpenAPI 임포트

### 방법 1: 파일에서 임포트

1. Postman 실행
2. 좌측 상단 **"Import"** 버튼 클릭
3. **"Upload Files"** 선택
4. `doc/openapi.json` 파일 선택
5. **"Import"** 클릭

### 방법 2: URL에서 임포트 (서버에 배포된 경우)

1. Postman 실행
2. 좌측 상단 **"Import"** 버튼 클릭
3. **"Link"** 탭 선택
4. URL 입력: `https://your-domain.com/docs/openapi.json`
5. **"Continue"** → **"Import"** 클릭

### 임포트 후 사용

1. 좌측 사이드바에서 **"Kelly API"** 컬렉션 확인
2. 각 API 엔드포인트가 자동으로 생성됨
3. **Environment** 설정:
   - 우측 상단 환경 드롭다운 → **"Manage Environments"**
   - **"Add"** 클릭
   - 이름: `Kelly Local`
   - 변수 추가:
     - `baseUrl`: `http://localhost:3005/api/v1`
   - **"Save"** 클릭

4. 요청 테스트:
   - `GET /members` 선택
   - URL이 `{{baseUrl}}/members`로 표시됨
   - **"Send"** 클릭

### Postman Collection 내보내기

팀과 공유하려면:

1. Kelly API 컬렉션 우클릭
2. **"Export"** 선택
3. **"Collection v2.1"** 선택
4. 저장 위치 선택
5. 파일을 Git으로 관리하거나 공유

---

## Insomnia로 OpenAPI 임포트

### 임포트 방법

1. Insomnia 실행
2. 좌측 상단 **"Create"** → **"Import From"** 선택
3. **"File"** 클릭
4. `doc/openapi.json` 파일 선택
5. **"Import"** 클릭

### 환경 설정

1. 좌측 상단 드롭다운 → **"Manage Environments"**
2. **"Base Environment"** 선택
3. 다음 추가:
   ```json
   {
     "base_url": "http://localhost:3005/api/v1"
   }
   ```
4. **"Done"** 클릭

### 요청 테스트

1. `GET /members` 선택
2. URL: `{{ _.base_url }}/members`
3. **"Send"** 클릭
4. 응답 확인

---

## Swagger UI로 문서 보기

### 방법 1: 온라인 Swagger Editor

1. [Swagger Editor](https://editor.swagger.io/) 접속
2. 좌측 메뉴 **"File"** → **"Import file"**
3. `doc/openapi.json` 선택
4. 우측에 문서가 렌더링됨
5. **"Try it out"** 버튼으로 직접 테스트 가능

### 방법 2: 로컬 Swagger UI 실행

#### Docker 사용

```bash
# doc 폴더에서 실행
docker run -p 8080:8080 \
  -e SWAGGER_JSON=/openapi.json \
  -v $(pwd)/openapi.json:/openapi.json \
  swaggerapi/swagger-ui
```

브라우저에서 `http://localhost:8080` 접속

#### npx 사용

```bash
# 프로젝트 루트에서
npx swagger-ui-watcher doc/openapi.json
```

브라우저가 자동으로 열림

---

## 클라이언트 코드 자동 생성

### OpenAPI Generator 사용

#### JavaScript/TypeScript 클라이언트

```bash
# TypeScript Axios 클라이언트 생성
npx @openapitools/openapi-generator-cli generate \
  -i doc/openapi.json \
  -g typescript-axios \
  -o ./generated/kelly-api-client
```

#### Python 클라이언트

```bash
# Python 클라이언트 생성
npx @openapitools/openapi-generator-cli generate \
  -i doc/openapi.json \
  -g python \
  -o ./generated/kelly-api-python
```

#### Java 클라이언트

```bash
# Java 클라이언트 생성
npx @openapitools/openapi-generator-cli generate \
  -i doc/openapi.json \
  -g java \
  -o ./generated/kelly-api-java
```

### 생성된 클라이언트 사용 예시

#### TypeScript

```typescript
import { Configuration, MembersApi } from './generated/kelly-api-client';

const config = new Configuration({
  basePath: 'http://localhost:3005/api/v1'
});

const membersApi = new MembersApi(config);

// 회원 목록 조회
const members = await membersApi.getMembers(1, 20);
console.log(members.data);

// 회원 생성
const newMember = await membersApi.createMember({
  mem_userid: 'newuser',
  mem_password: 'password123',
  mem_email: 'user@example.com',
  mem_username: '신규 사용자'
});
console.log(newMember.data);
```

#### Python

```python
from kelly_api_python import Configuration, MembersApi

config = Configuration(
    host = "http://localhost:3005/api/v1"
)

with ApiClient(config) as api_client:
    members_api = MembersApi(api_client)
    
    # 회원 목록 조회
    members = members_api.get_members(page=1, limit=20)
    print(members)
    
    # 회원 생성
    new_member = members_api.create_member(
        mem_userid="newuser",
        mem_password="password123",
        mem_email="user@example.com",
        mem_username="신규 사용자"
    )
    print(new_member)
```

---

## AI 에이전트 통합

### OpenAI GPT / Claude 활용

#### 1. API 정의서를 컨텍스트로 제공

```python
import openai

# API 정의서 읽기
with open('doc/openapi.json', 'r') as f:
    api_spec = f.read()

# GPT에게 질문
response = openai.ChatCompletion.create(
    model="gpt-4",
    messages=[
        {
            "role": "system",
            "content": f"You are an API client. Here is the API specification:\n{api_spec}"
        },
        {
            "role": "user",
            "content": "회원 목록을 조회하는 Python 코드를 작성해줘"
        }
    ]
)

print(response.choices[0].message.content)
```

#### 2. Function Calling 활용

```python
import openai
import requests

# Function 정의
functions = [
    {
        "name": "get_members",
        "description": "회원 목록을 조회합니다",
        "parameters": {
            "type": "object",
            "properties": {
                "page": {"type": "integer", "description": "페이지 번호"},
                "limit": {"type": "integer", "description": "페이지당 항목 수"}
            }
        }
    },
    {
        "name": "create_member",
        "description": "새로운 회원을 생성합니다",
        "parameters": {
            "type": "object",
            "properties": {
                "mem_userid": {"type": "string", "description": "사용자 ID"},
                "mem_password": {"type": "string", "description": "비밀번호"},
                "mem_email": {"type": "string", "description": "이메일"},
                "mem_username": {"type": "string", "description": "이름"}
            },
            "required": ["mem_userid", "mem_password", "mem_email", "mem_username"]
        }
    }
]

# 실제 API 호출 함수
def call_api(function_name, arguments):
    base_url = "http://localhost:3005/api/v1"
    
    if function_name == "get_members":
        response = requests.get(
            f"{base_url}/members",
            params=arguments
        )
        return response.json()
    
    elif function_name == "create_member":
        response = requests.post(
            f"{base_url}/members",
            json=arguments
        )
        return response.json()

# GPT와 대화
response = openai.ChatCompletion.create(
    model="gpt-4",
    messages=[
        {"role": "user", "content": "회원 목록을 조회해줘"}
    ],
    functions=functions,
    function_call="auto"
)

# Function call 확인
if response.choices[0].message.get("function_call"):
    function_call = response.choices[0].message["function_call"]
    function_name = function_call["name"]
    arguments = json.loads(function_call["arguments"])
    
    # API 호출
    result = call_api(function_name, arguments)
    print(result)
```

### LangChain 통합

```python
from langchain.agents import initialize_agent, Tool
from langchain.llms import OpenAI
import requests

# API 호출 도구 정의
def get_members(query: str) -> str:
    """회원 목록을 조회합니다."""
    response = requests.get(
        "http://localhost:3005/api/v1/members",
        params={"page": 1, "limit": 10}
    )
    return str(response.json())

def create_member(member_data: str) -> str:
    """새로운 회원을 생성합니다. JSON 형식으로 입력하세요."""
    import json
    data = json.loads(member_data)
    response = requests.post(
        "http://localhost:3005/api/v1/members",
        json=data
    )
    return str(response.json())

# 도구 리스트
tools = [
    Tool(
        name="GetMembers",
        func=get_members,
        description="회원 목록을 조회합니다"
    ),
    Tool(
        name="CreateMember",
        func=create_member,
        description="새로운 회원을 생성합니다. JSON 형식으로 member_data를 전달하세요"
    )
]

# 에이전트 초기화
llm = OpenAI(temperature=0)
agent = initialize_agent(tools, llm, agent="zero-shot-react-description", verbose=True)

# 에이전트 실행
result = agent.run("회원 목록을 조회하고, 새로운 회원 'testuser'를 생성해줘")
print(result)
```

---

## 자동화 스크립트 예시

### API 헬스체크 스크립트

```bash
#!/bin/bash
# api-healthcheck.sh

BASE_URL="http://localhost:3005/api/v1"

echo "🔍 Kelly API Health Check"
echo "=========================="

# 테스트 엔드포인트 확인
echo -n "Testing endpoint... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/members/test")

if [ $response -eq 200 ]; then
    echo "✅ OK (HTTP $response)"
else
    echo "❌ FAIL (HTTP $response)"
    exit 1
fi

# 회원 목록 조회 테스트
echo -n "GET /members... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/members")

if [ $response -eq 200 ]; then
    echo "✅ OK"
else
    echo "❌ FAIL"
    exit 1
fi

echo ""
echo "✅ All checks passed!"
```

### 데이터 동기화 스크립트

```python
#!/usr/bin/env python3
# sync_members.py

import requests
import json

SOURCE_API = "http://source-system.com/api/members"
TARGET_API = "http://localhost:3005/api/v1/members"

def sync_members():
    """회원 데이터를 소스에서 타겟으로 동기화"""
    
    # 소스에서 회원 조회
    response = requests.get(SOURCE_API)
    source_members = response.json()
    
    print(f"📥 Found {len(source_members)} members to sync")
    
    # 각 회원을 타겟 API에 생성
    for member in source_members:
        try:
            response = requests.post(
                TARGET_API,
                json={
                    "mem_userid": member["userid"],
                    "mem_password": "default123!",
                    "mem_email": member["email"],
                    "mem_username": member["name"]
                }
            )
            
            if response.status_code == 201:
                print(f"✅ Synced: {member['userid']}")
            else:
                print(f"⚠️  Failed: {member['userid']} - {response.json()}")
                
        except Exception as e:
            print(f"❌ Error: {member['userid']} - {str(e)}")
    
    print("\n✅ Sync complete!")

if __name__ == "__main__":
    sync_members()
```

---

## CI/CD 통합

### GitHub Actions 예시

```yaml
# .github/workflows/api-test.yml
name: API Tests

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Start Kelly API
        run: |
          php spark serve --port=3005 &
          sleep 5
      
      - name: Install Newman (Postman CLI)
        run: npm install -g newman
      
      - name: Import OpenAPI to Postman
        run: |
          # OpenAPI를 Postman Collection으로 변환
          npx openapi-to-postman \
            -s doc/openapi.json \
            -o postman-collection.json
      
      - name: Run API Tests
        run: |
          newman run postman-collection.json \
            --environment postman-env.json \
            --reporters cli,json
      
      - name: Upload Test Results
        uses: actions/upload-artifact@v2
        with:
          name: api-test-results
          path: newman-results.json
```

---

## 모니터링 설정

### Postman Monitor (Postman Pro)

1. Postman에서 Kelly API 컬렉션 선택
2. 우측 **"..."** → **"Monitor Collection"**
3. 모니터 이름 입력: `Kelly API Health`
4. 실행 주기 선택: 5분, 15분, 1시간 등
5. 알림 설정 (이메일, Slack 등)
6. **"Create"** 클릭

### Uptime 모니터링

```bash
# uptime-check.sh
#!/bin/bash

while true; do
    response=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:3005/api/v1/members/test")
    
    if [ $response -ne 200 ]; then
        echo "🚨 API Down! Sending alert..."
        # Slack webhook, 이메일 등 알림 전송
        curl -X POST YOUR_SLACK_WEBHOOK \
            -H 'Content-Type: application/json' \
            -d '{"text":"Kelly API is down!"}'
    fi
    
    sleep 60
done
```

---

## 참고 자료

- [Postman 공식 문서](https://learning.postman.com/)
- [OpenAPI Generator 가이드](https://openapi-generator.tech/)
- [Swagger UI 문서](https://swagger.io/tools/swagger-ui/)
- [LangChain 공식 문서](https://python.langchain.com/)

---

## 추가 지원

도구 사용 중 문제가 있으면:
- [api-reference.md](./api-reference.md) - API 정의서 확인
- [restful-api-guide.md](./restful-api-guide.md) - API 사용 가이드
- GitHub Issues - 이슈 등록

