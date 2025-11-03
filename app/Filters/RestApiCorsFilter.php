<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CORS 필터 (REST API용)
 * 
 * Cross-Origin Resource Sharing 헤더 설정
 */
class RestApiCorsFilter implements FilterInterface
{
    /**
     * @var array 허용된 오리진
     */
    protected $allowedOrigins = ['*'];

    /**
     * @var array 허용된 HTTP 메서드
     */
    protected $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * @var array 허용된 헤더
     */
    protected $allowedHeaders = [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-API-Version',
        'If-Match',
        'If-None-Match',
    ];

    /**
     * @var array 노출할 헤더
     */
    protected $exposedHeaders = [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
        'X-API-Version',
        'ETag',
        'Location',
    ];

    /**
     * @var int Preflight 캐시 시간 (초)
     */
    protected $maxAge = 3600;

    /**
     * @var bool 인증 정보 허용 여부
     */
    protected $allowCredentials = true;

    /**
     * 요청 전 처리
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // OPTIONS 요청 (Preflight)인 경우
        if ($request->getMethod() === 'options') {
            $response = service('response');
            return $this->addCorsHeaders($request, $response)->setStatusCode(204);
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
        return $this->addCorsHeaders($request, $response);
    }

    /**
     * CORS 헤더 추가
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function addCorsHeaders(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');

        // Access-Control-Allow-Origin 설정
        if (in_array('*', $this->allowedOrigins)) {
            $response->setHeader('Access-Control-Allow-Origin', '*');
        } elseif ($origin && in_array($origin, $this->allowedOrigins)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            $response->setHeader('Vary', 'Origin');
        }

        // Access-Control-Allow-Methods
        $response->setHeader('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods));

        // Access-Control-Allow-Headers
        $response->setHeader('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders));

        // Access-Control-Expose-Headers
        $response->setHeader('Access-Control-Expose-Headers', implode(', ', $this->exposedHeaders));

        // Access-Control-Allow-Credentials
        if ($this->allowCredentials) {
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
        }

        // Access-Control-Max-Age (Preflight 캐시)
        if ($request->getMethod() === 'options') {
            $response->setHeader('Access-Control-Max-Age', (string)$this->maxAge);
        }

        return $response;
    }
}

