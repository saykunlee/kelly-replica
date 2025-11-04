# Kelly API 보안 기능 가이드

**Version:** 2.0.0  
**Last Updated:** 2025-11-04  
**Target:** 프론트엔드 개발팀

---

## 📋 목차

1. [신규 보안 기능 개요](#신규-보안-기능-개요)
2. [Refresh Token DB 저장](#refresh-token-db-저장)
3. [토큰 블랙리스트](#토큰-블랙리스트)
4. [동시 로그인 제한](#동시-로그인-제한)
5. [로그인 이력 추적](#로그인-이력-추적)
6. [활성 세션 관리](#활성-세션-관리)
7. [프론트엔드 구현 가이드](#프론트엔드-구현-가이드)

---

## 신규 보안 기능 개요

### 추가된 기능

| 기능 | 설명 | 혜택 |
|-----|------|------|
| **Refresh Token DB 저장** | Refresh Token을 DB에 저장 | 로그아웃 시 즉시 무효화 |
| **토큰 블랙리스트** | 무효화된 토큰 관리 | 강제 로그아웃 가능 |
| **동시 로그인 제한** | 하나의 디바이스만 허용 (옵션) | 계정 도용 방지 |
| **로그인 이력 추적** | 모든 로그인 시도 기록 | 보안 감사 및 의심스러운 활동 탐지 |
| **무차별 대입 방지** | IP 기반 로그인 시도 제한 | 자동화된 공격 차단 |

### 변경사항 요약

#### 로그인 (POST /auth/login)
- ✅ 무차별 대입 공격 방지 추가
- ✅ Refresh Token DB 저장
- ✅ 로그인 이력 기록
- ✅ 동시 로그인 제한 (옵션)

#### 로그아웃 (POST /auth/logout)
- ✅ Access Token 블랙리스트 추가
- ✅ Refresh Token 무효화
- ✅ 로그아웃 이력 기록

#### 토큰 갱신 (POST /auth/refresh)
- ✅ 블랙리스트 체크 추가
- ✅ DB에서 Refresh Token 검증
- ✅ 사용 시간 업데이트

---

## Refresh Token DB 저장

### 개요

Refresh Token을 데이터베이스에 저장하여 다음 기능을 제공합니다:
- 로그아웃 시 토큰 즉시 무효화
- 동시 로그인 제한
- 토큰 재사용 감지
- 활성 세션 목록 조회

### 저장되는 정보

```sql
refresh_tokens 테이블:
- rt_user_id: 사용자 ID
- rt_token_hash: 토큰 해시 (SHA-256)
- rt_device_info: 디바이스 정보
- rt_ip_address: IP 주소
- rt_expires_at: 만료 시간
- rt_is_revoked: 폐기 여부
```

### 프론트엔드 영향

**변경 없음!** 기존 프론트엔드 코드는 그대로 작동합니다.

```typescript
// 기존 코드 그대로 사용 가능
const response = await apiClient.post('/auth/login', {
  userid: 'user',
  password: 'password'
});

const { access_token, refresh_token } = response.data.data;
```

---

## 토큰 블랙리스트

### 개요

로그아웃 후 토큰이 즉시 무효화됩니다.

**이전 방식:**
```
로그아웃 → 클라이언트에서 토큰 삭제
문제: 토큰이 만료될 때까지 사용 가능 (보안 취약점)
```

**새로운 방식:**
```
로그아웃 → 서버 블랙리스트에 추가 + 클라이언트 삭제
결과: 즉시 무효화, 재사용 불가능
```

### 프론트엔드 구현

#### 로그아웃 시 Refresh Token 전송

```typescript
// lib/auth-service.ts
async logout() {
  try {
    const refreshToken = localStorage.getItem('refresh_token');
    
    // Refresh Token을 함께 전송
    await apiClient.post('/auth/logout', {
      refresh_token: refreshToken
    });
  } finally {
    // 로컬 토큰 삭제
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
  }
}
```

### 에러 처리

```typescript
// 블랙리스트에 있는 토큰 사용 시
{
  "status": 401,
  "success": false,
  "message": "무효화된 토큰입니다",
  "errorCode": "TOKEN_REVOKED"
}
```

---

## 동시 로그인 제한

### 개요

환경변수 설정으로 하나의 계정에서 하나의 디바이스만 로그인할 수 있도록 제한합니다.

### 서버 설정

```env
# .env
LIMIT_CONCURRENT_SESSIONS=true  # 동시 로그인 제한 활성화
LIMIT_CONCURRENT_SESSIONS=false # 여러 디바이스 허용 (기본값)
```

### 동작 방식

```
사용자가 PC에서 로그인 (세션 A 생성)
    ↓
사용자가 모바일에서 로그인 (세션 B 생성)
    ↓
[LIMIT_CONCURRENT_SESSIONS=true 인 경우]
    → 세션 A 자동 무효화
    → PC에서 401 에러 발생
    
[LIMIT_CONCURRENT_SESSIONS=false 인 경우]
    → 세션 A, B 모두 유효
    → 여러 디바이스 동시 사용 가능
```

### 프론트엔드 처리

```typescript
// API Client에서 자동 처리
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    if (error.response?.status === 401) {
      const errorCode = error.response.data?.errorCode;
      
      if (errorCode === 'TOKEN_REVOKED') {
        // 다른 디바이스에서 로그인한 경우
        alert('다른 디바이스에서 로그인하여 로그아웃되었습니다.');
        // 로그인 페이지로 리다이렉트
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);
```

---

## 로그인 이력 추적

### 개요

모든 로그인 시도가 기록됩니다:
- 로그인 성공/실패
- 로그아웃
- 토큰 갱신
- 강제 로그아웃

### 저장되는 정보

```sql
login_history 테이블:
- lh_user_id: 사용자 ID
- lh_action: login, logout, refresh, revoke
- lh_status: success, failed, blocked
- lh_ip_address: IP 주소
- lh_user_agent: User-Agent
- lh_device_info: 디바이스 정보
- lh_failure_reason: 실패 사유
```

### 무차별 대입 공격 방지

**자동 차단:**
```
동일 IP에서 1시간 내 5회 이상 로그인 실패
    ↓
자동으로 IP 차단
    ↓
HTTP 429 (Too Many Requests) 반환
```

**에러 응답:**
```json
{
  "status": 429,
  "success": false,
  "message": "로그인 시도 횟수가 초과되었습니다. 잠시 후 다시 시도해주세요.",
  "errorCode": "TOO_MANY_REQUESTS"
}
```

**프론트엔드 처리:**
```typescript
try {
  await authService.login(credentials);
} catch (error: any) {
  if (error.response?.status === 429) {
    alert('로그인 시도가 너무 많습니다. 1시간 후 다시 시도해주세요.');
  }
}
```

---

## 활성 세션 관리

### 신규 API: 활성 세션 조회

**Endpoint:** `GET /auth/sessions`

**Response:**
```json
{
  "status": 200,
  "success": true,
  "data": {
    "active_sessions": 2,
    "sessions": [
      {
        "id": 1,
        "device_info": "Desktop",
        "ip_address": "192.168.1.100",
        "created_at": "2025-11-04 10:00:00",
        "last_used_at": "2025-11-04 15:30:00",
        "expires_at": "2025-11-11 10:00:00"
      },
      {
        "id": 2,
        "device_info": "Mobile",
        "ip_address": "192.168.1.101",
        "created_at": "2025-11-04 12:00:00",
        "last_used_at": "2025-11-04 14:00:00",
        "expires_at": "2025-11-11 12:00:00"
      }
    ]
  }
}
```

### 프론트엔드 구현

```typescript
// 활성 세션 조회
async function getActiveSessions() {
  const response = await apiClient.get('/auth/sessions');
  return response.data.data;
}

// 사용 예시 - 프로필 페이지
export default function ProfilePage() {
  const [sessions, setSessions] = useState([]);

  useEffect(() => {
    async function loadSessions() {
      const data = await getActiveSessions();
      setSessions(data.sessions);
    }
    loadSessions();
  }, []);

  return (
    <div>
      <h2>활성 세션 ({sessions.length})</h2>
      <ul>
        {sessions.map(session => (
          <li key={session.id}>
            <div>{session.device_info}</div>
            <div>{session.ip_address}</div>
            <div>마지막 사용: {session.last_used_at}</div>
          </li>
        ))}
      </ul>
    </div>
  );
}
```

---

## 프론트엔드 구현 가이드

### 1. 기본 구현 (필수)

기존 프론트엔드 코드는 **변경 없이** 그대로 작동합니다!

```typescript
// ✅ 기존 코드 그대로 사용
const { login, logout } = useAuth();

// 로그인
await login({ userid: 'user', password: 'pass' });

// 로그아웃
await logout();
```

### 2. 로그아웃 개선 (권장)

Refresh Token을 함께 전송하여 즉시 무효화:

```typescript
// lib/auth-service.ts
class AuthService {
  async logout() {
    try {
      const refreshToken = localStorage.getItem('refresh_token');
      
      // Refresh Token 함께 전송
      await apiClient.post('/auth/logout', {
        refresh_token: refreshToken
      });
    } finally {
      // 로컬 토큰 삭제
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
    }
  }
}
```

### 3. 에러 처리 개선 (권장)

새로운 에러 코드 처리:

```typescript
// lib/api-client.ts
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    if (error.response?.status === 401) {
      const errorCode = error.response.data?.errorCode;
      
      switch (errorCode) {
        case 'TOKEN_REVOKED':
          // 토큰이 무효화됨 (로그아웃 또는 강제 로그아웃)
          alert('로그아웃되었습니다.');
          window.location.href = '/login';
          break;
          
        case 'ACCOUNT_DENIED':
          // 계정이 차단됨
          alert('계정이 차단되었습니다. 관리자에게 문의하세요.');
          window.location.href = '/login';
          break;
          
        case 'TOKEN_EXPIRED':
          // 토큰 만료 - 자동 갱신 시도
          // (기존 로직)
          break;
      }
    } else if (error.response?.status === 429) {
      // Rate Limiting
      alert('요청이 너무 많습니다. 잠시 후 다시 시도해주세요.');
    }
    
    return Promise.reject(error);
  }
);
```

### 4. 활성 세션 표시 (선택)

프로필 페이지에 활성 세션 표시:

```typescript
// components/ActiveSessions.tsx
import { useState, useEffect } from 'react';
import { apiClient } from '@/lib/api-client';

export function ActiveSessions() {
  const [sessions, setSessions] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadSessions() {
      try {
        const response = await apiClient.get('/auth/sessions');
        setSessions(response.data.data.sessions);
      } catch (error) {
        console.error('Failed to load sessions:', error);
      } finally {
        setLoading(false);
      }
    }
    loadSessions();
  }, []);

  if (loading) return <div>로딩중...</div>;

  return (
    <div className="space-y-4">
      <h3 className="text-lg font-semibold">활성 세션</h3>
      {sessions.length === 0 ? (
        <p>활성 세션이 없습니다.</p>
      ) : (
        <ul className="space-y-2">
          {sessions.map((session: any) => (
            <li key={session.id} className="border p-3 rounded">
              <div className="flex justify-between">
                <div>
                  <p className="font-medium">{session.device_info}</p>
                  <p className="text-sm text-gray-600">{session.ip_address}</p>
                  <p className="text-xs text-gray-500">
                    마지막 사용: {new Date(session.last_used_at).toLocaleString('ko-KR')}
                  </p>
                </div>
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
```

---

## 보안 권장사항

### 프로덕션 체크리스트

- [ ] **HTTPS 필수**: 프로덕션에서는 반드시 HTTPS 사용
- [ ] **Secure Cookie**: httpOnly, Secure, SameSite 설정
- [ ] **CORS 설정**: 허용된 도메인만 접근 가능하도록 제한
- [ ] **Rate Limiting**: 환경에 맞게 임계값 조정
- [ ] **로그 모니터링**: 의심스러운 로그인 시도 모니터링
- [ ] **사용자 알림**: 새로운 위치에서 로그인 시 이메일 알림

### 동시 로그인 제한 사용 여부

**활성화 권장 (LIMIT_CONCURRENT_SESSIONS=true):**
- 금융 서비스
- 기업용 애플리케이션
- 라이선스 기반 서비스

**비활성화 권장 (LIMIT_CONCURRENT_SESSIONS=false):**
- 일반 웹 서비스
- 멀티 디바이스 사용이 일반적인 서비스
- 가족 공유 계정

---

## FAQ

### Q1. 기존 프론트엔드 코드를 수정해야 하나요?

**A:** 아니요. 기존 코드는 그대로 작동합니다. 다만 로그아웃 시 Refresh Token을 함께 전송하면 보안이 강화됩니다.

### Q2. 로그아웃 후에도 토큰이 작동하나요?

**A:** 아니요. 로그아웃 시 토큰이 블랙리스트에 추가되어 즉시 무효화됩니다.

### Q3. 동시 로그인 제한을 사용하면 어떻게 되나요?

**A:** 새로운 디바이스에서 로그인하면 기존 디바이스의 세션이 자동으로 무효화됩니다. 기존 디바이스에서는 401 에러가 발생하고 다시 로그인해야 합니다.

### Q4. 로그인 시도가 차단되면 어떻게 하나요?

**A:** 1시간 후 자동으로 차단이 해제됩니다. 또는 관리자가 수동으로 해제할 수 있습니다.

### Q5. 활성 세션 목록에서 특정 세션을 로그아웃할 수 있나요?

**A:** 현재 버전에서는 지원하지 않습니다. 향후 버전에서 추가될 예정입니다.

---

## 관련 문서

- [integration-guide.md](./integration-guide.md) - 프론트엔드 통합 가이드
- [auth-guide.md](./auth-guide.md) - JWT 인증 상세 가이드
- [../api/reference.md](../api/reference.md) - API 정의서

---

**문서 버전:** 2.0.0  
**마지막 업데이트:** 2025-11-04  
**작성자:** Kelly Development Team


