# 📚 API 문서 디렉토리

이 폴더는 RESTful API 프레임워크와 관련된 모든 문서를 포함하고 있습니다.

## 📖 문서 목록

### 1. RESTful API 프레임워크 문서

- **[restful-api-guide.md](./restful-api-guide.md)** ⭐  
  RESTful API 프레임워크 사용 가이드 (한글)
  - 빠른 시작 가이드
  - API 엔드포인트 사용법
  - HTTP 메서드 가이드
  - 고급 기능 (ETag, 비동기 작업, Rate Limiting)
  - 예제 코드 및 테스트

- **[restful-api-structure.md](./restful-api-structure.md)**  
  RESTful API 프레임워크 구조 및 아키텍처 문서 (한글)
  - 프로젝트 구조
  - 아키텍처 개요
  - 주요 컴포넌트 설명
  - 확장 가이드

- **[dynamic-restful-routing.md](./dynamic-restful-routing.md)** 🚀  
  RESTful API 동적 라우팅 가이드 (한글)
  - 자동 라우팅 기능
  - 라우팅 우선순위 설명
  - 네이밍 규칙
  - 성능 고려사항
  - 실전 예시

### 2. 기존 (Legacy) API 분석

- **[legacy-api-structure.md](./legacy-api-structure.md)** 🔍  
  기존 API 시스템 구조 분석 (한글)
  - 기존 라우팅 시스템 분석
  - RouteHandler 동작 원리
  - URL → Controller 매핑 과정
  - kebab-case → camelCase 변환 규칙
  - 실제 호출 흐름 예시

- **[naming-convention-examples.md](./naming-convention-examples.md)** 🏷️  
  Legacy API Naming Convention 상세 예시 (한글)
  - 컨트롤러명/메서드명 변환 규칙
  - 실제 URL → 메서드 매핑 100개 이상
  - 변환 알고리즘 상세 설명
  - 테스트 예시

- **[routing-separation.md](./routing-separation.md)** 🛤️  
  API 라우팅 분리 가이드 (한글)
  - Legacy vs RESTful API 라우팅 분리 방법
  - Negative Lookahead 패턴 설명
  - 라우팅 처리 순서
  - 테스트 시나리오

- **[api-comparison.md](./api-comparison.md)** 📊  
  Legacy API vs RESTful API 비교 (한글)
  - 상세 비교표
  - 실제 사용 예시 비교
  - 시나리오별 권장 API
  - 마이그레이션 전략
  - 성능 비교

### 3. API 설계 원칙

- **[api-design](./api-design)**  
  Azure API Design Best Practices 기반 API 개발 로드맵 (한글)
  - API 설계 원칙 및 개념
  - 리소스 및 URI 설계
  - HTTP 메서드 및 응답 처리
  - 데이터 최적화 및 동시성 관리
  - API 운영, 보안, 테스트

### 4. Azure 공식 가이드 (PDF)

- **[Web API Design Best Practices - Azure Architecture Center _ Microsoft Learn.pdf](./Web%20API%20Design%20Best%20Practices%20-%20Azure%20Architecture%20Center%20_%20Microsoft%20Learn.pdf)**  
  Azure의 웹 API 설계 모범 사례 가이드 (영문)

- **[Web API Implementation - Azure Architecture Center _ Microsoft Learn.pdf](./Web%20API%20Implementation%20-%20Azure%20Architecture%20Center%20_%20Microsoft%20Learn.pdf)**  
  Azure의 웹 API 구현 가이드 (영문)

---

## 🚀 빠른 시작

### 기존 API 이해하기
1. **기존 시스템 분석**  
   → [legacy-api-structure.md](./legacy-api-structure.md) 읽기
   - RouteHandler 동작 원리
   - URL → Controller 매핑
   - kebab-case → camelCase 변환

2. **Naming Convention 상세**  
   → [naming-convention-examples.md](./naming-convention-examples.md) 참조 🏷️
   - 컨트롤러명/메서드명 변환 예시
   - 실제 URL 매핑 테이블

3. **라우팅 분리 이해**  
   → [routing-separation.md](./routing-separation.md) 참조 🛤️
   - Legacy vs RESTful 라우팅 분리
   - Negative Lookahead 패턴

4. **Legacy vs RESTful 비교**  
   → [api-comparison.md](./api-comparison.md) 참조
   - 실제 사용 예시
   - 마이그레이션 전략

### 새로운 RESTful API 사용하기
1. **API 프레임워크 이해하기**  
   → [restful-api-structure.md](./restful-api-structure.md) 읽기

2. **API 개발 시작하기**  
   → [restful-api-guide.md](./restful-api-guide.md) 참조 ⭐

3. **동적 라우팅 활용하기** 🚀  
   → [dynamic-restful-routing.md](./dynamic-restful-routing.md) 참조  
   - 컨트롤러만 만들면 자동 라우팅
   - Routes.php 수정 불필요

4. **심화 학습**  
   → [api-design](./api-design) 문서 및 Azure PDF 가이드 참조

---

## 📁 문서 구성

```
doc/
├── README.md                                    # 이 문서
│
├── restful-api-guide.md                        # ⭐ RESTful API 사용 가이드
├── restful-api-structure.md                    # RESTful API 구조 문서
├── dynamic-restful-routing.md                  # 🚀 동적 라우팅 가이드
│
├── legacy-api-structure.md                     # 🔍 기존 API 분석
├── naming-convention-examples.md               # 🏷️ Naming Convention 예시
├── routing-separation.md                       # 🛤️ 라우팅 분리 가이드
├── api-comparison.md                           # 📊 API 비교
│
├── api-design                                   # API 설계 로드맵
├── Web API Design Best Practices...pdf         # Azure 설계 가이드
└── Web API Implementation...pdf                 # Azure 구현 가이드
```

---

## 🔗 관련 링크

- [Azure API Design Best Practices](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design)
- [Azure API Implementation Guide](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-implementation)
- [Richardson Maturity Model](https://martinfowler.com/articles/richardsonMaturityModel.html)
- [REST API Tutorial](https://restfulapi.net/)

---

## 📝 문서 업데이트 이력

- **2025-11-03**: RESTful API 동적 라우팅 기능 추가 🚀
  - RestfulRouteHandler 구현
  - 자동 라우팅 문서 작성 (dynamic-restful-routing.md)
  - Routes.php 간소화 (커스텀 엔드포인트만 명시적 정의)
- **2025-11-03**: 라우팅 분리 가이드 추가 (Negative Lookahead 패턴)
- **2025-11-03**: Routes.php 업데이트 (Legacy와 RESTful API 명확히 분리)
- **2025-11-03**: Naming Convention 상세 예시 문서 추가 (100개 이상 매핑 예시)
- **2025-11-03**: Legacy API 구조 분석 문서 업데이트 (메서드명 변환 강조)
- **2025-11-03**: Legacy API 구조 분석 문서 추가
- **2025-11-03**: Legacy vs RESTful API 비교 문서 추가
- **2025-11-01**: RESTful API 프레임워크 문서 작성
- **2025-11-01**: doc 폴더로 문서 정리 및 이동

---

## 💡 문서 활용 팁

### 기존 개발자라면
1. **기존 시스템 이해**  
   `legacy-api-structure.md` 읽기
   
2. **새 시스템과 비교**  
   `api-comparison.md`로 차이점 파악
   
3. **마이그레이션 계획**  
   비교 문서의 마이그레이션 전략 참조

### 신규 개발자라면
1. **RESTful API부터 시작**  
   `restful-api-guide.md` → `restful-api-structure.md` 순서로 읽기

2. **기존 시스템 이해**  
   필요시 `legacy-api-structure.md` 참조

3. **특정 기능 구현 시**  
   `restful-api-guide.md`에서 해당 섹션 검색

4. **설계 원칙 이해**  
   `api-design` 문서 참조

5. **심화 학습**  
   Azure PDF 가이드 참조

---

## 📮 문의 및 피드백

API 프레임워크 관련 질문이나 개선 사항이 있으면 개발팀에 문의해주세요.

