#!/bin/bash

# API 라우팅 분리 테스트 스크립트
# 사용법: ./test-routing.sh [BASE_URL]
# 예시: ./test-routing.sh http://localhost

BASE_URL=${1:-"http://localhost"}

echo "======================================"
echo "API 라우팅 분리 테스트"
echo "======================================"
echo "Base URL: $BASE_URL"
echo ""

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 테스트 함수
test_endpoint() {
    local method=$1
    local url=$2
    local expected_pattern=$3
    local description=$4
    
    echo -n "Testing: $description ... "
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL$url")
    else
        response=$(curl -s -o /dev/null -w "%{http_code}" -X $method "$BASE_URL$url" \
            -H "Content-Type: application/json" \
            -d '{}')
    fi
    
    if [ "$response" = "$expected_pattern" ]; then
        echo -e "${GREEN}✓ PASS${NC} (HTTP $response)"
    else
        echo -e "${RED}✗ FAIL${NC} (Expected: $expected_pattern, Got: $response)"
    fi
}

echo "======================================"
echo "1. RESTful API 테스트 (v1)"
echo "======================================"
echo ""

test_endpoint "GET" "/api/v1/members" "200" "RESTful: GET /api/v1/members"
test_endpoint "GET" "/api/v1/members/1" "200" "RESTful: GET /api/v1/members/1"
test_endpoint "GET" "/api/v1/examples" "200" "RESTful: GET /api/v1/examples"
test_endpoint "GET" "/api/v1/jobs/job_123" "200" "RESTful: GET /api/v1/jobs/job_123"

echo ""
echo "======================================"
echo "2. Legacy API 테스트"
echo "======================================"
echo ""

test_endpoint "POST" "/api/member-api/get-member-list" "200" "Legacy: POST /api/member-api/get-member-list"
test_endpoint "POST" "/api/board-api/get-board-list" "200" "Legacy: POST /api/board-api/get-board-list"
test_endpoint "GET" "/api/member-api" "200" "Legacy: GET /api/member-api"

echo ""
echo "======================================"
echo "3. 라우팅 분리 확인 (충돌 테스트)"
echo "======================================"
echo ""

echo -e "${YELLOW}Info:${NC} 다음 테스트들은 404를 반환해야 정상입니다."
echo ""

test_endpoint "GET" "/api/v2/members" "404" "버전 미정의: GET /api/v2/members (404 예상)"
test_endpoint "GET" "/api/v10/test" "404" "버전 미정의: GET /api/v10/test (404 예상)"

echo ""
echo "======================================"
echo "4. Negative Lookahead 검증"
echo "======================================"
echo ""

echo -e "${YELLOW}Info:${NC} v로 시작하는 Legacy API는 RouteHandler로 가야 합니다."
echo -e "${YELLOW}Info:${NC} 하지만 v{숫자}로 시작하면 RESTful로 간주되어 제외됩니다."
echo ""

test_endpoint "GET" "/api/version-api" "200" "Legacy: /api/version-api (v뒤 숫자 아님)"
test_endpoint "POST" "/api/video-api/get-list" "200" "Legacy: /api/video-api/get-list (v뒤 숫자 아님)"

echo ""
echo "======================================"
echo "테스트 완료"
echo "======================================"
echo ""
echo "참고:"
echo "- RESTful API는 /api/v{숫자}/* 패턴"
echo "- Legacy API는 /api/{controller}/{method} 패턴 (v{숫자} 제외)"
echo "- Negative Lookahead (?!v\\d+)로 버전 경로 제외"
echo ""

