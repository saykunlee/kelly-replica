# 🚀 Kelly API 빠른 시작 가이드

**5분 안에 API 연동 시작하기**

---

## ✅ 체크리스트

시작하기 전 확인:

- [ ] 백엔드 API 서버 실행 중 (`http://localhost:3005`)
- [ ] 테스트 계정 있음 (admin / admin1234)
- [ ] 프론트엔드 프로젝트 준비 완료

---

## 1️⃣ 로그인 테스트 (30초)

브라우저 콘솔에서:

```javascript
// 로그인
const response = await fetch('http://localhost:3005/api/v1/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ userid: 'admin', password: 'admin1234' })
});

const data = await response.json();
console.log(data);

// ✅ Access Token 확인
console.log('Access Token:', data.data.access_token);
```

---

## 2️⃣ API Client 설정 (3분)

**lib/api-client.ts**

```typescript
import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://localhost:3005/api/v1',
  headers: { 'Content-Type': 'application/json' },
});

// 요청 시 토큰 자동 첨부
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// 401 에러 시 자동 리다이렉트
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('access_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

---

## 3️⃣ 로그인 구현 (2분)

```typescript
import apiClient from '@/lib/api-client';

async function login(userid: string, password: string) {
  try {
    const response = await apiClient.post('/auth/login', {
      userid,
      password,
    });

    const { access_token, user } = response.data.data;
    
    // 토큰 저장
    localStorage.setItem('access_token', access_token);
    
    console.log('로그인 성공:', user);
    return user;
    
  } catch (error: any) {
    console.error('로그인 실패:', error.response?.data?.message);
    throw error;
  }
}

// 사용
await login('admin', 'admin1234');
```

---

## 4️⃣ 인증된 요청 (1분)

```typescript
// 사용자 정보 조회
async function getCurrentUser() {
  const response = await apiClient.get('/auth/me');
  return response.data.data;
}

// 회원 목록 조회
async function getMembers() {
  const response = await apiClient.get('/members?page=1&limit=20');
  return response.data.data;
}

// 사용
const user = await getCurrentUser();
console.log('현재 사용자:', user);

const members = await getMembers();
console.log('회원 목록:', members);
```

---

## 🎯 다음 단계

### 필수

1. **에러 처리 추가**
   - API 에러를 사용자 친화적으로 표시
   - 토큰 갱신 로직 구현

2. **보안 강화**
   - httpOnly 쿠키로 토큰 저장 (localStorage 대신)
   - HTTPS 사용 (프로덕션)

### 선택

3. **상세 구현 가이드 읽기**
   - [frontend-integration-guide.md](./frontend-integration-guide.md)
   - 토큰 자동 갱신, Protected Route 등

4. **API 테스트**
   - [tests/api/auth.http](../tests/api/auth.http)
   - REST Client로 모든 API 테스트

---

## 📚 주요 API 엔드포인트

| 엔드포인트 | 메서드 | 인증 | 설명 |
|----------|--------|------|------|
| `/auth/login` | POST | ❌ | 로그인 |
| `/auth/me` | GET | ✅ | 사용자 정보 |
| `/auth/logout` | POST | ✅ | 로그아웃 |
| `/members` | GET | ✅ | 회원 목록 |
| `/members/{id}` | GET | ✅ | 회원 상세 |
| `/members` | POST | ✅ | 회원 생성 |
| `/members/{id}` | PATCH | ✅ | 회원 수정 |
| `/members/{id}` | DELETE | ✅ | 회원 삭제 |

---

## ❓ 자주 묻는 질문

### Q: 401 에러가 계속 발생해요
**A:** Access Token이 만료되었습니다. Refresh Token으로 갱신하거나 다시 로그인하세요.

### Q: CORS 에러가 발생해요
**A:** 백엔드 팀에 프론트엔드 도메인(`http://localhost:3000`)을 CORS 허용 목록에 추가 요청하세요.

### Q: 토큰을 어디에 저장해야 하나요?
**A:** 
- **개발 환경**: localStorage (빠른 테스트)
- **프로덕션**: httpOnly 쿠키 (보안)

---

## 🆘 도움이 필요하면

- **전체 가이드**: [frontend-integration-guide.md](./frontend-integration-guide.md)
- **API 스펙**: [api-reference.md](./api-reference.md)
- **테스트 파일**: [tests/api/auth.http](../tests/api/auth.http)

---

**완성! 🎉 이제 API 연동을 시작하세요!**


