<?php

/**
 * RESTful API 헬퍼 함수
 * 
 * API 개발 시 유용한 헬퍼 함수 모음
 */

if (!function_exists('api_response')) {
    /**
     * 표준화된 API 응답 생성
     * 
     * @param mixed $data 응답 데이터
     * @param int $statusCode HTTP 상태 코드
     * @param array $links HATEOAS 링크
     * @return array
     */
    function api_response($data = null, int $statusCode = 200, array $links = []): array
    {
        return \App\Libraries\RestApi\RestApiResponse::success($data, $statusCode, $links);
    }
}

if (!function_exists('api_error')) {
    /**
     * API 에러 응답 생성
     * 
     * @param string $message 에러 메시지
     * @param int $statusCode HTTP 상태 코드
     * @param array $errors 상세 에러 정보
     * @return array
     */
    function api_error(string $message, int $statusCode = 400, array $errors = []): array
    {
        return \App\Libraries\RestApi\RestApiResponse::error($message, $statusCode, $errors);
    }
}

if (!function_exists('api_link')) {
    /**
     * HATEOAS 링크 생성
     * 
     * @param string $rel 관계명
     * @param string $href URI
     * @param string $action HTTP 메서드
     * @return array
     */
    function api_link(string $rel, string $href, string $action = 'GET'): array
    {
        return \App\Libraries\RestApi\RestApiResponse::createLink($rel, $href, $action);
    }
}

if (!function_exists('api_paginated')) {
    /**
     * 페이지네이션 응답 생성
     * 
     * @param array $items 데이터 항목
     * @param int $total 전체 항목 수
     * @param int $page 현재 페이지
     * @param int $limit 페이지당 항목 수
     * @param string $baseUrl 기본 URL
     * @return array
     */
    function api_paginated(array $items, int $total, int $page, int $limit, string $baseUrl): array
    {
        return \App\Libraries\RestApi\RestApiResponse::paginated($items, $total, $page, $limit, $baseUrl);
    }
}

if (!function_exists('api_created')) {
    /**
     * 리소스 생성 성공 응답
     * 
     * @param mixed $data 생성된 리소스
     * @param string $location 리소스 위치 URL
     * @return array
     */
    function api_created($data, string $location): array
    {
        return \App\Libraries\RestApi\RestApiResponse::created($data, $location);
    }
}

if (!function_exists('api_accepted')) {
    /**
     * 비동기 작업 수락 응답
     * 
     * @param string $statusUrl 상태 조회 URL
     * @param string|null $message 메시지
     * @return array
     */
    function api_accepted(string $statusUrl, ?string $message = null): array
    {
        return \App\Libraries\RestApi\RestApiResponse::accepted($statusUrl, $message);
    }
}

if (!function_exists('generate_etag')) {
    /**
     * ETag 생성
     * 
     * @param mixed $data 데이터
     * @return string
     */
    function generate_etag($data): string
    {
        return '"' . md5(json_encode($data)) . '"';
    }
}

if (!function_exists('validate_etag')) {
    /**
     * ETag 검증
     * 
     * @param string $etag 비교할 ETag
     * @param mixed $data 데이터
     * @return bool
     */
    function validate_etag(string $etag, $data): bool
    {
        return $etag === generate_etag($data);
    }
}

if (!function_exists('api_version_from_request')) {
    /**
     * 요청에서 API 버전 추출
     * 
     * @return string
     */
    function api_version_from_request(): string
    {
        $request = service('request');
        
        // URI에서 추출
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        if (preg_match('#/api/(v\d+)/#', $path, $matches)) {
            return $matches[1];
        }
        
        // Query 파라미터
        if ($version = $request->getGet('version')) {
            return 'v' . $version;
        }
        
        // Header
        if ($request->hasHeader('api-version')) {
            $apiVersion = $request->getHeaderLine('api-version');
            return 'v' . str_replace('.', '', $apiVersion);
        }
        
        return 'v1'; // 기본값
    }
}

if (!function_exists('sanitize_api_input')) {
    /**
     * API 입력 데이터 정제
     * 
     * @param array $data 입력 데이터
     * @param array $allowedFields 허용된 필드 목록
     * @return array
     */
    function sanitize_api_input(array $data, array $allowedFields): array
    {
        return array_filter(
            $data,
            function ($key) use ($allowedFields) {
                return in_array($key, $allowedFields);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}

if (!function_exists('api_resource_url')) {
    /**
     * 리소스 URL 생성
     * 
     * @param string $resource 리소스명
     * @param string|int|null $id 리소스 ID
     * @param string $version API 버전
     * @return string
     */
    function api_resource_url(string $resource, $id = null, string $version = 'v1'): string
    {
        $url = base_url("api/{$version}/{$resource}");
        
        if ($id !== null) {
            $url .= "/{$id}";
        }
        
        return $url;
    }
}

if (!function_exists('parse_accept_header')) {
    /**
     * Accept 헤더 파싱
     * 
     * @param string $acceptHeader Accept 헤더 값
     * @return array 우선순위가 높은 순서로 정렬된 MIME 타입 배열
     */
    function parse_accept_header(string $acceptHeader): array
    {
        $types = [];
        $parts = explode(',', $acceptHeader);
        
        foreach ($parts as $part) {
            $subParts = explode(';', trim($part));
            $type = trim($subParts[0]);
            $quality = 1.0;
            
            // q 값 파싱
            if (count($subParts) > 1) {
                foreach ($subParts as $param) {
                    if (strpos($param, 'q=') === 0) {
                        $quality = (float) substr($param, 2);
                        break;
                    }
                }
            }
            
            $types[] = ['type' => $type, 'quality' => $quality];
        }
        
        // 품질 값으로 정렬 (내림차순)
        usort($types, function ($a, $b) {
            return $b['quality'] <=> $a['quality'];
        });
        
        return array_column($types, 'type');
    }
}

