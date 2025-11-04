<?php

namespace App\Libraries\RestApi;

/**
 * RestApiResponse 클래스
 * 
 * RESTful API 응답 표준화 및 HATEOAS 링크 생성 지원
 * Azure API Design Best Practices 기반으로 구현
 */
class RestApiResponse
{
    /**
     * 표준화된 성공 응답 생성
     * 
     * @param mixed $data 응답 데이터
     * @param int $statusCode HTTP 상태 코드
     * @param array $links HATEOAS 링크 배열
     * @param array $meta 메타데이터 (페이지네이션 등)
     * @return array
     */
    public static function success($data = null, int $statusCode = 200, array $links = [], array $meta = []): array
    {
        $response = [
            'status' => $statusCode,
            'success' => true,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($links)) {
            $response['links'] = $links;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return $response;
    }

    /**
     * 표준화된 에러 응답 생성
     * 
     * @param string $message 에러 메시지
     * @param int $statusCode HTTP 상태 코드
     * @param array $errors 상세 에러 정보
     * @param string|null $errorCode 에러 코드
     * @return array
     */
    public static function error(string $message, int $statusCode = 400, array $errors = [], ?string $errorCode = null): array
    {
        $response = [
            'status' => $statusCode,
            'success' => false,
            'message' => $message,
        ];

        if ($errorCode) {
            $response['errorCode'] = $errorCode;
        }

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }

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
    public static function paginated(array $items, int $total, int $page, int $limit, string $baseUrl): array
    {
        $totalPages = ceil($total / $limit);
        $hasNext = $page < $totalPages;
        $hasPrev = $page > 1;

        $meta = [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];

        $links = [
            self::createLink('self', $baseUrl . "?page={$page}&limit={$limit}", 'GET'),
            self::createLink('first', $baseUrl . "?page=1&limit={$limit}", 'GET'),
            self::createLink('last', $baseUrl . "?page={$totalPages}&limit={$limit}", 'GET'),
        ];

        if ($hasNext) {
            $links[] = self::createLink('next', $baseUrl . "?page=" . ($page + 1) . "&limit={$limit}", 'GET');
        }

        if ($hasPrev) {
            $links[] = self::createLink('prev', $baseUrl . "?page=" . ($page - 1) . "&limit={$limit}", 'GET');
        }

        return self::success($items, 200, $links, $meta);
    }

    /**
     * HATEOAS 링크 생성
     * 
     * @param string $rel 관계명 (self, next, prev, create, update, delete 등)
     * @param string $href URI
     * @param string $method HTTP 메서드
     * @param array $types MIME 타입 배열
     * @return array
     */
    public static function createLink(string $rel, string $href, string $method = 'GET', array $types = ['application/json']): array
    {
        return [
            'rel' => $rel,
            'href' => $href,
            'method' => $method,
        ];
    }

    /**
     * 리소스에 HATEOAS 링크 추가
     * 
     * @param array $resource 리소스 데이터
     * @param array $links HATEOAS 링크 배열
     * @return array
     */
    public static function addLinks(array $resource, array $links): array
    {
        $resource['links'] = $links;
        return $resource;
    }

    /**
     * 생성 성공 응답 (201 Created)
     * 
     * @param mixed $data 생성된 리소스 데이터
     * @param string $location 리소스 위치 URL (HTTP Header용, 응답 본문에는 포함되지 않음)
     * @param array $links HATEOAS 링크
     * @return array
     */
    public static function created($data, string $location, array $links = []): array
    {
        $response = self::success($data, 201, $links);
        $response['message'] = '리소스가 생성되었습니다';
        return $response;
    }

    /**
     * 비동기 작업 수락 응답 (202 Accepted)
     * 
     * @param string $statusUrl 작업 상태 조회 URL
     * @param string|null $message 메시지
     * @return array
     */
    public static function accepted(string $statusUrl, ?string $message = null): array
    {
        $response = self::success(null, 202);
        $response['statusUrl'] = $statusUrl;
        
        if ($message) {
            $response['message'] = $message;
        }

        $response['links'] = [
            self::createLink('status', $statusUrl, 'GET'),
        ];

        return $response;
    }

    /**
     * 내용 없음 응답 (204 No Content)
     * 
     * @return array
     */
    public static function noContent(): array
    {
        return [
            'status' => 204,
            'success' => true,
        ];
    }

    /**
     * 유효성 검증 실패 응답 (422 Unprocessable Entity)
     * 
     * @param array $errors 검증 에러 배열
     * @return array
     */
    public static function validationError(array $errors): array
    {
        return self::error('유효성 검증 실패', 422, $errors, 'VALIDATION_ERROR');
    }

    /**
     * 인증 실패 응답 (401 Unauthorized)
     * 
     * @param string $message 에러 메시지
     * @return array
     */
    public static function unauthorized(string $message = '인증이 필요합니다'): array
    {
        return self::error($message, 401, [], 'UNAUTHORIZED');
    }

    /**
     * 권한 없음 응답 (403 Forbidden)
     * 
     * @param string $message 에러 메시지
     * @return array
     */
    public static function forbidden(string $message = '권한이 없습니다'): array
    {
        return self::error($message, 403, [], 'FORBIDDEN');
    }

    /**
     * 리소스 없음 응답 (404 Not Found)
     * 
     * @param string $message 에러 메시지
     * @return array
     */
    public static function notFound(string $message = '리소스를 찾을 수 없습니다'): array
    {
        return self::error($message, 404, [], 'NOT_FOUND');
    }

    /**
     * 충돌 응답 (409 Conflict)
     * 
     * @param string $message 에러 메시지
     * @return array
     */
    public static function conflict(string $message = '리소스 충돌이 발생했습니다'): array
    {
        return self::error($message, 409, [], 'CONFLICT');
    }

    /**
     * 조건부 요청 실패 응답 (412 Precondition Failed)
     * 
     * @param string $message 에러 메시지
     * @return array
     */
    public static function preconditionFailed(string $message = '조건부 요청이 실패했습니다'): array
    {
        return self::error($message, 412, [], 'PRECONDITION_FAILED');
    }

    /**
     * Rate Limit 초과 응답 (429 Too Many Requests)
     * 
     * @param int $retryAfter 재시도 대기 시간 (초)
     * @return array
     */
    public static function tooManyRequests(int $retryAfter): array
    {
        $response = self::error('요청 제한을 초과했습니다', 429, [], 'RATE_LIMIT_EXCEEDED');
        $response['retryAfter'] = $retryAfter;
        return $response;
    }

    /**
     * 서버 에러 응답 (500 Internal Server Error)
     * 
     * @param string $message 에러 메시지
     * @return array
     */
    public static function serverError(string $message = '서버 오류가 발생했습니다'): array
    {
        return self::error($message, 500, [], 'SERVER_ERROR');
    }

    /**
     * 서비스 사용 불가 응답 (503 Service Unavailable)
     * 
     * @param int|null $retryAfter 재시도 대기 시간 (초)
     * @return array
     */
    public static function serviceUnavailable(?int $retryAfter = null): array
    {
        $response = self::error('서비스를 사용할 수 없습니다', 503, [], 'SERVICE_UNAVAILABLE');
        
        if ($retryAfter) {
            $response['retryAfter'] = $retryAfter;
        }

        return $response;
    }
}

