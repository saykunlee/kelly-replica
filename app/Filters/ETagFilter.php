<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ETag 캐싱 필터
 * 
 * ETag 기반 캐싱 및 조건부 요청 처리
 * If-None-Match, If-Match 헤더 지원
 */
class ETagFilter implements FilterInterface
{
    /**
     * 요청 전 처리
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // GET, HEAD 요청에만 적용
        $method = $request->getMethod();
        if (!in_array($method, ['get', 'head'])) {
            return $request;
        }

        return $request;
    }

    /**
     * 응답 후 처리
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $method = $request->getMethod();

        // GET, HEAD 요청에만 적용
        if (!in_array($method, ['get', 'head'])) {
            return $response;
        }

        // 성공 응답에만 적용
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            return $response;
        }

        // 응답 본문 가져오기
        $body = $response->getBody();
        
        if (empty($body)) {
            return $response;
        }

        // ETag 생성
        $etag = '"' . md5($body) . '"';
        
        // If-None-Match 헤더 확인
        $ifNoneMatch = $request->getHeaderLine('If-None-Match');
        
        if ($ifNoneMatch && $ifNoneMatch === $etag) {
            // 리소스가 변경되지 않음 - 304 Not Modified 반환
            $response->setStatusCode(304);
            $response->setBody('');
        } else {
            // ETag 헤더 설정
            $response->setHeader('ETag', $etag);
        }

        return $response;
    }
}

