# Legacy 시스템 문서

기존 API 시스템 분석 및 RESTful API와의 비교 문서입니다.

---

## 📚 문서 목록

### 1. [api-structure.md](./api-structure.md) 🔍
**기존 API 시스템 구조 분석**
- 기존 라우팅 시스템 분석
- RouteHandler 동작 원리
- URL → Controller 매핑 과정
- kebab-case → camelCase 변환 규칙
- 실제 호출 흐름 예시

**기존 API가 어떻게 동작하는지 이해하세요.**

### 2. [naming-conventions.md](./naming-conventions.md) 🏷️
**Legacy API Naming Convention 상세**
- 컨트롤러명/메서드명 변환 규칙
- 실제 URL → 메서드 매핑 100개 이상
- 변환 알고리즘 상세 설명
- 테스트 예시

**Legacy URL이 어떤 메서드로 매핑되는지 확인하세요.**

### 3. [routing-separation.md](./routing-separation.md) 🛤️
**API 라우팅 분리 가이드**
- Legacy vs RESTful API 라우팅 분리 방법
- Negative Lookahead 패턴 설명
- 라우팅 처리 순서
- 테스트 시나리오

**두 시스템이 어떻게 공존하는지 이해하세요.**

### 4. [comparison.md](./comparison.md) 📊
**Legacy API vs RESTful API 비교**
- 상세 비교표
- 실제 사용 예시 비교
- 시나리오별 권장 API
- 마이그레이션 전략
- 성능 비교

**어떤 API를 사용해야 할지 결정하세요.**

---

## 🚀 사용 시나리오

### Legacy API 유지보수
1. [api-structure.md](./api-structure.md)로 구조 파악
2. [naming-conventions.md](./naming-conventions.md)로 URL 매핑 확인
3. 기존 컨트롤러 수정

### RESTful API로 마이그레이션
1. [comparison.md](./comparison.md)로 차이점 파악
2. [routing-separation.md](./routing-separation.md)로 공존 방법 이해
3. 단계적 마이그레이션 계획 수립

---

## 📋 주요 차이점 요약

| 항목 | Legacy API | RESTful API |
|-----|-----------|-------------|
| **URL 형식** | `/controller/method` | `/api/v1/resources` |
| **네이밍** | kebab-case | 표준 RESTful |
| **HTTP 메서드** | 주로 GET/POST | GET, POST, PUT, PATCH, DELETE |
| **응답 형식** | 비표준 | 표준 JSON (status, data, links) |
| **버전 관리** | 없음 | URI 버전 (`/v1`) |
| **HATEOAS** | 없음 | 링크 포함 |

---

## 💡 마이그레이션 권장사항

### 우선순위

1. **신규 기능** → RESTful API 사용 (필수)
2. **자주 사용되는 API** → 단계적 마이그레이션
3. **레거시 API** → 가능한 유지 (호환성)

### 마이그레이션 단계

1. RESTful API 엔드포인트 개발
2. 병렬 운영 (두 API 동시 제공)
3. 프론트엔드 점진적 전환
4. Legacy API deprecation 공지
5. Legacy API 제거 (충분한 전환 기간 후)

---

## 🔗 관련 링크

- [백엔드 가이드](../backend/) - RESTful API 개발
- [API 레퍼런스](../api/) - RESTful API 스펙
- [설계 문서](../design/) - API 설계 원칙

---

**Legacy 시스템을 이해하고 현대화하세요! 🔄**

