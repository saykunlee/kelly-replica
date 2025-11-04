# Kelly API 프론트엔드 통합 가이드

**Version:** 1.0.0  
**Last Updated:** 2025-11-04  
**Target:** 프론트엔드 개발팀 (Next.js, React, Vue 등)

---

## 📋 목차

1. [시작하기](#시작하기)
2. [인증 시스템](#인증-시스템)
3. [API 엔드포인트](#api-엔드포인트)
4. [실전 구현 예제](#실전-구현-예제)
5. [에러 처리](#에러-처리)
6. [테스트 방법](#테스트-방법)
7. [보안 가이드](#보안-가이드)
8. [FAQ](#faq)

---

## 시작하기

### API 기본 정보

```
Base URL: http://localhost:3005/api/v1
Protocol: HTTP/HTTPS
Content-Type: application/json
```

### 빠른 체크리스트

프론트엔드 개발을 시작하기 전 확인사항:

- [ ] 백엔드 API 서버 실행 중 (`http://localhost:3005`)
- [ ] 환경 변수 설정 완료 (`.env.local`)
- [ ] API 테스트 완료 (`tests/api/auth.http` 참고)
- [ ] JWT 인증 흐름 이해
- [ ] 에러 처리 방식 확인

### 필수 문서

| 문서 | 용도 | 위치 |
|-----|------|------|
| **이 문서** | 전체 통합 가이드 | `doc/frontend-integration-guide.md` |
| **API 정의서** | 전체 API 스펙 | `doc/api-reference.md` |
| **JWT 인증 가이드** | 상세 인증 구현 | `doc/frontend-auth-guide.md` |
| **OpenAPI 스펙** | Postman/Swagger | `doc/openapi.json` |
| **테스트 파일** | API 테스트 | `tests/api/auth.http` |

---

## 인증 시스템

### 개요

Kelly API는 **JWT (JSON Web Token)** 기반 인증을 사용합니다.

| 특징 | 설명 |
|-----|------|
| **인증 방식** | Bearer Token (JWT) |
| **Access Token** | 1시간 유효, API 요청 인증 |
| **Refresh Token** | 7일 유효, Access Token 갱신 |
| **저장 위치** | httpOnly 쿠키 (권장) 또는 메모리 |
| **헤더 형식** | `Authorization: Bearer <token>` |

### 인증 흐름

```
┌─────────────┐      ① 로그인      ┌─────────────┐
│             │ ──────────────────> │             │
│  Frontend   │                     │  Backend    │
│             │ <────────────────── │  (CI4 API)  │
└─────────────┘    ② JWT 토큰      └─────────────┘
       │                                   
       │ ③ 토큰 저장 (httpOnly 쿠키)        
       │                                   
       │ ④ API 요청 (Bearer Token)         
       │ ───────────────────────────────>  
       │                                   
       │ ⑤ 데이터 응답                      
       │ <───────────────────────────────  
       │                                   
       │ ⑥ 토큰 만료 시 Refresh           
       │ ───────────────────────────────>  
```

### 주요 인증 API

#### 1. 로그인

**Endpoint:** `POST /auth/login`

**Request:**
```json
{
  "userid": "admin",
  "password": "admin1234"
}
```

**Response (200 OK):**
```json
{
  "status": 200,
  "success": true,
  "data": {
    "access_token": "eyJhbGc...",
    "refresh_token": "eyJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "mem_id": 1,
      "mem_userid": "admin",
      "mem_email": "admin@example.com",
      "mem_username": "관리자",
      "mem_level": 10
    }
  }
}
```

#### 2. 사용자 정보 조회

**Endpoint:** `GET /auth/me`

**Headers:**
```
Authorization: Bearer <access_token>
```

**Response (200 OK):**
```json
{
  "status": 200,
  "success": true,
  "data": {
    "mem_id": 1,
    "mem_userid": "admin",
    "mem_email": "admin@example.com",
    "mem_username": "관리자",
    "mem_level": 10
  }
}
```

#### 3. 토큰 갱신

**Endpoint:** `POST /auth/refresh`

**Request:**
```json
{
  "refresh_token": "eyJhbGc..."
}
```

**Response (200 OK):**
```json
{
  "status": 200,
  "success": true,
  "data": {
    "access_token": "eyJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

#### 4. 로그아웃

**Endpoint:** `POST /auth/logout`

**Headers:**
```
Authorization: Bearer <access_token>
```

**Response (200 OK):**
```json
{
  "status": 200,
  "success": true,
  "data": {
    "message": "로그아웃되었습니다"
  }
}
```

---

## API 엔드포인트

### 공통 사항

**Request Headers:**

| 헤더 | 필수 | 설명 |
|-----|------|------|
| `Content-Type` | POST/PUT/PATCH | `application/json` |
| `Accept` | 권장 | `application/json` |
| `Authorization` | 인증 필요 시 | `Bearer <token>` |

**HTTP 상태 코드:**

| 코드 | 의미 | 설명 |
|-----|------|------|
| 200 | OK | 요청 성공 |
| 201 | Created | 리소스 생성 성공 |
| 204 | No Content | 삭제 성공 (응답 본문 없음) |
| 400 | Bad Request | 잘못된 요청 |
| 401 | Unauthorized | 인증 실패 또는 토큰 만료 |
| 404 | Not Found | 리소스 없음 |
| 422 | Unprocessable Entity | 유효성 검증 실패 |
| 429 | Too Many Requests | Rate Limit 초과 |
| 500 | Internal Server Error | 서버 오류 |

### Members API

회원 관리 API입니다.

#### 회원 목록 조회

**Endpoint:** `GET /members`

**Query Parameters:**

| 파라미터 | 타입 | 기본값 | 설명 |
|---------|-----|-------|------|
| `page` | integer | 1 | 페이지 번호 |
| `limit` | integer | 20 | 페이지당 항목 수 (최대 100) |
| `sort` | string | `mem_id` | 정렬 필드 |
| `order` | string | `DESC` | 정렬 순서 (`ASC` 또는 `DESC`) |

**Example:**
```javascript
const response = await fetch(
  'http://localhost:3005/api/v1/members?page=1&limit=20',
  {
    headers: {
      'Accept': 'application/json',
      'Authorization': `Bearer ${accessToken}`
    }
  }
);
const data = await response.json();
```

**Response:**
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
      "mem_level": 1
    }
  ],
  "meta": {
    "total": 100,
    "page": 1,
    "limit": 20,
    "totalPages": 5
  }
}
```

#### 회원 상세 조회

**Endpoint:** `GET /members/{id}`

**Example:**
```javascript
const response = await fetch(
  `http://localhost:3005/api/v1/members/${memberId}`,
  {
    headers: {
      'Accept': 'application/json',
      'Authorization': `Bearer ${accessToken}`
    }
  }
);
```

#### 회원 생성

**Endpoint:** `POST /members`

**Request Body:**
```json
{
  "mem_userid": "newuser",
  "mem_password": "password123!",
  "mem_email": "newuser@example.com",
  "mem_username": "신규 사용자",
  "mem_phone": "010-1234-5678"
}
```

**Example:**
```javascript
const response = await fetch(
  'http://localhost:3005/api/v1/members',
  {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${accessToken}`
    },
    body: JSON.stringify(memberData)
  }
);
```

#### 회원 수정

**Endpoint:** `PATCH /members/{id}`

**Request Body:** (수정할 필드만 포함)
```json
{
  "mem_phone": "010-9999-8888"
}
```

#### 회원 삭제

**Endpoint:** `DELETE /members/{id}`

**Response:** `204 No Content` (본문 없음)

---

## 실전 구현 예제

### 1. 환경 설정

**.env.local**
```env
NEXT_PUBLIC_API_BASE_URL=http://localhost:3005/api/v1
JWT_SECRET_KEY=your-secret-key
```

### 2. API Client (TypeScript/JavaScript)

**lib/api-client.ts**
```typescript
import axios, { AxiosInstance, AxiosError } from 'axios';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL || 'http://localhost:3005/api/v1';

class ApiClient {
  private client: AxiosInstance;
  private refreshPromise: Promise<string> | null = null;

  constructor() {
    this.client = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
      },
      withCredentials: true,
    });

    // 요청 인터셉터 - Access Token 자동 첨부
    this.client.interceptors.request.use(
      (config) => {
        const token = this.getAccessToken();
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // 응답 인터셉터 - 토큰 만료 시 자동 갱신
    this.client.interceptors.response.use(
      (response) => response,
      async (error: AxiosError) => {
        const originalRequest = error.config as any;

        // 401 에러이고 재시도하지 않은 경우
        if (error.response?.status === 401 && !originalRequest._retry) {
          originalRequest._retry = true;

          try {
            // 토큰 갱신
            const newToken = await this.refreshAccessToken();
            
            // 새 토큰으로 원래 요청 재시도
            originalRequest.headers.Authorization = `Bearer ${newToken}`;
            return this.client(originalRequest);
          } catch (refreshError) {
            // Refresh 실패 시 로그아웃
            this.logout();
            return Promise.reject(refreshError);
          }
        }

        return Promise.reject(error);
      }
    );
  }

  private getAccessToken(): string | null {
    if (typeof window === 'undefined') return null;
    return localStorage.getItem('access_token');
  }

  private async refreshAccessToken(): Promise<string> {
    if (this.refreshPromise) {
      return this.refreshPromise;
    }

    this.refreshPromise = (async () => {
      try {
        const refreshToken = localStorage.getItem('refresh_token');
        if (!refreshToken) throw new Error('No refresh token');

        const response = await axios.post(`${API_BASE_URL}/auth/refresh`, {
          refresh_token: refreshToken,
        });

        const { access_token } = response.data.data;
        localStorage.setItem('access_token', access_token);
        return access_token;
      } finally {
        this.refreshPromise = null;
      }
    })();

    return this.refreshPromise;
  }

  private logout() {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    window.location.href = '/login';
  }

  // Public Methods
  get<T>(url: string, config?: any) {
    return this.client.get<T>(url, config);
  }

  post<T>(url: string, data?: any, config?: any) {
    return this.client.post<T>(url, data, config);
  }

  patch<T>(url: string, data?: any, config?: any) {
    return this.client.patch<T>(url, data, config);
  }

  delete<T>(url: string, config?: any) {
    return this.client.delete<T>(url, config);
  }
}

export const apiClient = new ApiClient();
```

### 3. Auth Service

**lib/auth-service.ts**
```typescript
import { apiClient } from './api-client';

export interface LoginCredentials {
  userid: string;
  password: string;
}

export interface User {
  mem_id: number;
  mem_userid: string;
  mem_email: string;
  mem_username: string;
  mem_level: number;
}

class AuthService {
  async login(credentials: LoginCredentials) {
    const response = await apiClient.post<{ data: any }>('/auth/login', credentials);
    const { access_token, refresh_token, user } = response.data.data;

    // 토큰 저장
    localStorage.setItem('access_token', access_token);
    localStorage.setItem('refresh_token', refresh_token);

    return { user };
  }

  async logout() {
    try {
      await apiClient.post('/auth/logout');
    } finally {
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
    }
  }

  async getCurrentUser(): Promise<User> {
    const response = await apiClient.get<{ data: User }>('/auth/me');
    return response.data.data;
  }

  isAuthenticated(): boolean {
    return !!localStorage.getItem('access_token');
  }
}

export const authService = new AuthService();
```

### 4. React Hook (Zustand)

**hooks/use-auth.ts**
```typescript
import { create } from 'zustand';
import { authService, User, LoginCredentials } from '@/lib/auth-service';

interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  login: (credentials: LoginCredentials) => Promise<void>;
  logout: () => Promise<void>;
  fetchUser: () => Promise<void>;
}

export const useAuth = create<AuthState>((set) => ({
  user: null,
  isAuthenticated: false,
  isLoading: false,
  error: null,

  login: async (credentials) => {
    set({ isLoading: true, error: null });
    try {
      const { user } = await authService.login(credentials);
      set({ isAuthenticated: true, user, isLoading: false });
    } catch (error: any) {
      set({
        isLoading: false,
        error: error.response?.data?.message || '로그인에 실패했습니다',
      });
      throw error;
    }
  },

  logout: async () => {
    set({ isLoading: true });
    try {
      await authService.logout();
      set({ user: null, isAuthenticated: false, isLoading: false });
    } catch (error) {
      set({ isLoading: false });
    }
  },

  fetchUser: async () => {
    if (!authService.isAuthenticated()) return;

    set({ isLoading: true });
    try {
      const user = await authService.getCurrentUser();
      set({ user, isAuthenticated: true, isLoading: false });
    } catch (error) {
      set({ user: null, isAuthenticated: false, isLoading: false });
    }
  },
}));
```

### 5. 로그인 페이지

**app/login/page.tsx**
```typescript
'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { useAuth } from '@/hooks/use-auth';

export default function LoginPage() {
  const router = useRouter();
  const { login, error, isLoading } = useAuth();
  const [credentials, setCredentials] = useState({
    userid: '',
    password: '',
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    try {
      await login(credentials);
      router.push('/dashboard');
    } catch (error) {
      // 에러는 useAuth에서 처리됨
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <div className="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <h2 className="text-3xl font-bold text-center mb-6">로그인</h2>
        
        {error && (
          <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-1">
              사용자 ID
            </label>
            <input
              type="text"
              required
              value={credentials.userid}
              onChange={(e) => setCredentials({ ...credentials, userid: e.target.value })}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">
              비밀번호
            </label>
            <input
              type="password"
              required
              value={credentials.password}
              onChange={(e) => setCredentials({ ...credentials, password: e.target.value })}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <button
            type="submit"
            disabled={isLoading}
            className="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isLoading ? '로그인 중...' : '로그인'}
          </button>
        </form>
      </div>
    </div>
  );
}
```

### 6. Protected Route 미들웨어

**middleware.ts**
```typescript
import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(request: NextRequest) {
  const accessToken = request.cookies.get('access_token');
  
  const protectedPaths = ['/dashboard', '/profile', '/admin'];
  const isProtectedPath = protectedPaths.some(path => 
    request.nextUrl.pathname.startsWith(path)
  );

  // 인증 필요한 페이지인데 토큰이 없으면 로그인 페이지로
  if (isProtectedPath && !accessToken) {
    return NextResponse.redirect(new URL('/login', request.url));
  }

  // 이미 로그인했는데 로그인 페이지 접근 시 대시보드로
  if (request.nextUrl.pathname === '/login' && accessToken) {
    return NextResponse.redirect(new URL('/dashboard', request.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: ['/((?!api|_next/static|_next/image|favicon.ico).*)'],
};
```

---

## 에러 처리

### 에러 응답 형식

```json
{
  "status": 401,
  "success": false,
  "message": "에러 메시지",
  "errorCode": "ERROR_CODE",
  "errors": [
    {
      "field": "mem_userid",
      "message": "필드별 에러 메시지"
    }
  ]
}
```

### 주요 에러 코드

| HTTP 코드 | errorCode | 메시지 | 처리 방법 |
|-----------|-----------|--------|----------|
| 400 | BAD_REQUEST | 잘못된 요청 | 요청 데이터 확인 |
| 401 | UNAUTHORIZED | 인증 실패 | 로그인 페이지로 리다이렉트 |
| 401 | TOKEN_EXPIRED | 토큰 만료 | Refresh Token으로 갱신 |
| 403 | FORBIDDEN | 권한 없음 | 접근 거부 메시지 |
| 404 | NOT_FOUND | 리소스 없음 | 404 페이지 표시 |
| 422 | VALIDATION_ERROR | 유효성 검증 실패 | 필드별 에러 표시 |
| 429 | RATE_LIMIT_EXCEEDED | 요청 제한 초과 | 재시도 대기 |

### 에러 처리 예제

```typescript
import { AxiosError } from 'axios';

// 공통 에러 핸들러
export function handleApiError(error: unknown): string {
  if (error instanceof AxiosError) {
    const response = error.response;
    
    if (!response) {
      return '네트워크 오류가 발생했습니다';
    }

    switch (response.status) {
      case 401:
        // 자동으로 토큰 갱신 시도 (인터셉터에서 처리)
        return '인증이 필요합니다';
      
      case 403:
        return '접근 권한이 없습니다';
      
      case 404:
        return '요청하신 리소스를 찾을 수 없습니다';
      
      case 422:
        // 유효성 검증 에러
        const errors = response.data.errors;
        if (errors && errors.length > 0) {
          return errors.map((e: any) => e.message).join('\n');
        }
        return response.data.message || '유효성 검증에 실패했습니다';
      
      case 429:
        return '요청이 너무 많습니다. 잠시 후 다시 시도해주세요';
      
      default:
        return response.data.message || '오류가 발생했습니다';
    }
  }

  return '알 수 없는 오류가 발생했습니다';
}

// 사용 예시
try {
  await apiClient.post('/members', memberData);
} catch (error) {
  const errorMessage = handleApiError(error);
  alert(errorMessage);
  // 또는 toast.error(errorMessage);
}
```

---

## 테스트 방법

### 1. REST Client 사용 (권장)

**VS Code/Cursor 확장 설치:**
- REST Client (Huachao Mao)

**테스트 파일 실행:**
1. `tests/api/auth.http` 열기
2. 각 요청 위의 "Send Request" 클릭
3. 응답 확인

### 2. Postman 사용

**OpenAPI 임포트:**
1. Postman 실행
2. "Import" 버튼 클릭
3. `doc/openapi.json` 파일 선택
4. Import 완료

**Environment 설정:**
- `baseUrl`: `http://localhost:3005/api/v1`

### 3. 브라우저 Console

```javascript
// 로그인
const loginResponse = await fetch('http://localhost:3005/api/v1/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ userid: 'admin', password: 'admin1234' })
});
const loginData = await loginResponse.json();
console.log(loginData);

// Access Token 저장
const token = loginData.data.access_token;

// 사용자 정보 조회
const userResponse = await fetch('http://localhost:3005/api/v1/auth/me', {
  headers: { 'Authorization': `Bearer ${token}` }
});
const userData = await userResponse.json();
console.log(userData);
```

---

## 보안 가이드

### 토큰 저장 방식

#### ❌ 하지 말아야 할 것

```javascript
// LocalStorage에 저장 (XSS 공격 위험)
localStorage.setItem('token', accessToken); // 위험!

// 전역 변수에 저장
window.accessToken = token; // 위험!
```

#### ✅ 권장 방식

```javascript
// 1. httpOnly 쿠키 (서버에서 설정)
// - XSS 공격 방지
// - JavaScript로 접근 불가

// 2. 메모리 저장 (React State)
const [accessToken, setAccessToken] = useState<string | null>(null);

// 3. Secure Storage (React Native)
// - 모바일 앱에서는 Secure Storage 사용
```

### HTTPS 사용

```javascript
// 프로덕션 환경
const API_BASE_URL = process.env.NODE_ENV === 'production'
  ? 'https://api.yourdomain.com/api/v1'  // HTTPS 필수
  : 'http://localhost:3005/api/v1';
```

### CORS 설정

백엔드 팀에 다음 도메인 허용 요청:

```
개발: http://localhost:3000
스테이징: https://staging.yourdomain.com
프로덕션: https://yourdomain.com
```

### 민감한 정보 처리

```typescript
// ❌ 비밀번호를 콘솔에 출력하지 않음
console.log('Password:', password); // 위험!

// ❌ 토큰을 콘솔에 출력하지 않음
console.log('Token:', accessToken); // 위험!

// ✅ 개발 환경에서만 필요 시 마스킹하여 출력
if (process.env.NODE_ENV === 'development') {
  console.log('Token (masked):', accessToken.substring(0, 10) + '...');
}
```

### Rate Limiting 대응

```typescript
// Exponential Backoff 구현
async function retryWithBackoff<T>(
  fn: () => Promise<T>,
  maxRetries: number = 3
): Promise<T> {
  for (let i = 0; i < maxRetries; i++) {
    try {
      return await fn();
    } catch (error: any) {
      if (error.response?.status === 429) {
        const retryAfter = error.response.data.retryAfter || Math.pow(2, i);
        await new Promise(resolve => setTimeout(resolve, retryAfter * 1000));
        continue;
      }
      throw error;
    }
  }
  throw new Error('Max retries exceeded');
}

// 사용 예시
const members = await retryWithBackoff(() => 
  apiClient.get('/members')
);
```

---

## FAQ

### Q1. Access Token이 만료되면 어떻게 되나요?

**A:** API Client의 인터셉터가 자동으로 Refresh Token을 사용해 새 Access Token을 발급받고 원래 요청을 재시도합니다. 위의 API Client 예제 참고.

### Q2. Refresh Token도 만료되면?

**A:** 사용자를 로그인 페이지로 리다이렉트합니다. 다시 로그인해야 합니다.

### Q3. 여러 탭에서 동시에 사용하면?

**A:** httpOnly 쿠키를 사용하면 모든 탭에서 동일한 토큰을 공유합니다. 한 탭에서 로그아웃하면 다른 탭에서도 로그아웃됩니다.

### Q4. SSR (Server-Side Rendering)에서는?

**A:** Next.js App Router 예제:

```typescript
import { cookies } from 'next/headers';

export default async function DashboardPage() {
  const cookieStore = cookies();
  const accessToken = cookieStore.get('access_token');

  if (!accessToken) {
    redirect('/login');
  }

  // API 호출
  const response = await fetch('http://localhost:3005/api/v1/auth/me', {
    headers: {
      'Authorization': `Bearer ${accessToken.value}`,
    },
  });

  const user = await response.json();
  return <div>Welcome, {user.data.mem_username}</div>;
}
```

### Q5. 개발 중에 CORS 에러가 발생합니다.

**A:** 백엔드 팀에 프론트엔드 개발 서버 도메인(`http://localhost:3000`)을 CORS 허용 목록에 추가 요청하세요.

### Q6. API 응답이 느립니다.

**A:**
- 페이지네이션 `limit`를 줄여서 요청
- 불필요한 API 호출 최소화
- React Query 등으로 캐싱 활용

### Q7. 토큰을 언제 갱신해야 하나요?

**A:** 두 가지 방식:
1. **반응적 갱신** (권장): 401 에러 발생 시 자동 갱신
2. **사전 갱신**: 토큰 만료 5분 전 자동 갱신

위의 API Client 예제는 반응적 갱신 방식을 사용합니다.

### Q8. 모바일 앱에서도 같은 API를 사용할 수 있나요?

**A:** 네, 동일한 API를 사용할 수 있습니다. 다만 쿠키 대신 Secure Storage에 토큰을 저장하세요.

---

## 추가 리소스

### 문서

- [API 전체 정의서](./api-reference.md) - 모든 API 엔드포인트 상세 스펙
- [JWT 인증 상세 가이드](./frontend-auth-guide.md) - 더 많은 예제와 설명
- [API 도구 활용 가이드](./api-tools-guide.md) - Postman, Swagger 등
- [OpenAPI 스펙](./openapi.json) - 기계 판독 가능한 API 스펙

### 테스트

- [인증 API 테스트](../tests/api/auth.http) - REST Client 테스트 파일
- 40+ 테스트 케이스 포함

### 지원

문의사항이 있으면:
- API 문서: `doc/api-reference.md` 참고
- 백엔드 팀에 문의
- GitHub Issues

---

## 변경 이력

### v1.0.0 (2025-11-04)
- ✅ 프론트엔드 통합 가이드 초안 작성
- ✅ JWT 인증 시스템 가이드
- ✅ 실전 구현 예제 (Next.js, React)
- ✅ 에러 처리 가이드
- ✅ 보안 가이드

---

**문서 버전:** 1.0.0  
**마지막 업데이트:** 2025-11-04  
**작성자:** Kelly Development Team
**대상:** 프론트엔드 개발팀


