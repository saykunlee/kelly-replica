<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Rate Limiting 필터
 * 
 * API 요청 빈도를 제한하는 필터
 * 슬라이딩 윈도우 방식 사용
 */
class RateLimitFilter implements FilterInterface
{
    /**
     * @var int 시간 윈도우 (초)
     */
    protected $windowSize = 60;

    /**
     * @var int 윈도우당 최대 요청 수
     */
    protected $maxRequests = 100;

    /**
     * @var \CodeIgniter\Cache\CacheInterface 캐시 인스턴스
     */
    protected $cache;

    /**
     * 생성자
     */
    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    /**
     * 요청 전 처리
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 클라이언트 식별자 생성 (IP + User-Agent)
        $identifier = $this->getClientIdentifier($request);
        // 캐시 키에서 예약된 문자 제거 ({}()/\@:)
        $key = $this->sanitizeCacheKey("rate_limit_{$identifier}");

        // 현재 요청 수 가져오기
        $currentRequests = $this->cache->get($key);

        if ($currentRequests === null) {
            // 첫 요청
            $this->cache->save($key, 1, $this->windowSize);
            $remaining = $this->maxRequests - 1;
        } else {
            // 기존 요청 존재
            if ($currentRequests >= $this->maxRequests) {
                // Rate limit 초과
                $retryAfter = $this->cache->getCacheInfo($key)['expire'] ?? $this->windowSize;
                
                $response = service('response');
                return $response->setJSON([
                    'status' => 429,
                    'success' => false,
                    'message' => '요청 제한을 초과했습니다',
                    'errorCode' => 'RATE_LIMIT_EXCEEDED',
                    'retryAfter' => $retryAfter,
                ])->setStatusCode(429)
                  ->setHeader('X-RateLimit-Limit', (string)$this->maxRequests)
                  ->setHeader('X-RateLimit-Remaining', '0')
                  ->setHeader('X-RateLimit-Reset', (string)(time() + $retryAfter))
                  ->setHeader('Retry-After', (string)$retryAfter);
            }

            // 요청 수 증가
            $this->cache->save($key, $currentRequests + 1, $this->windowSize);
            $remaining = $this->maxRequests - ($currentRequests + 1);
        }

        // Rate limit 정보를 요청에 저장
        $request->rateLimitRemaining = $remaining;
        $request->rateLimitLimit = $this->maxRequests;
        $request->rateLimitReset = time() + $this->windowSize;

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
        // Rate limit 헤더 추가
        if (isset($request->rateLimitLimit)) {
            $response->setHeader('X-RateLimit-Limit', (string)$request->rateLimitLimit);
            $response->setHeader('X-RateLimit-Remaining', (string)$request->rateLimitRemaining);
            $response->setHeader('X-RateLimit-Reset', (string)$request->rateLimitReset);
        }

        return $response;
    }

    /**
     * 클라이언트 식별자 생성
     * 
     * @param RequestInterface $request
     * @return string
     */
    protected function getClientIdentifier(RequestInterface $request): string
    {
        // 인증된 사용자 ID 사용 (세션에서 가져오기)
        $session = \Config\Services::session();
        if ($session->has('mem_id')) {
            return 'user_' . $session->get('mem_id');
        }

        // IP 주소 사용 (점과 콜론을 언더스코어로 변경)
        $ipAddress = $request->getIPAddress();
        return 'ip_' . str_replace([':', '.'], '_', $ipAddress);
    }

    /**
     * 캐시 키에서 예약된 문자 제거
     * 
     * @param string $key 원본 캐시 키
     * @return string 안전한 캐시 키
     */
    protected function sanitizeCacheKey(string $key): string
    {
        // 예약된 문자: {}()/\@:
        // 이들을 언더스코어로 변경하거나 MD5 해시 사용
        $reserved = '{}()/\\@:';
        
        // 예약된 문자가 있는지 확인
        if (strpbrk($key, $reserved) !== false) {
            // 예약된 문자를 언더스코어로 변경
            $key = str_replace(
                ['@', ':', '/', '\\', '{', '}', '(', ')'],
                '_',
                $key
            );
        }
        
        return $key;
    }
}

