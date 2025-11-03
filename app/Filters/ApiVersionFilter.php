<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * API 버전 관리 필터
 * 
 * API 버전을 확인하고 검증하는 필터
 * 지원하는 버전 방식:
 * - URI: /api/v1/resources
 * - Query: /api/resources?version=1
 * - Header: api-version: 1.0
 */
class ApiVersionFilter implements FilterInterface
{
    /**
     * @var array 지원하는 API 버전
     */
    protected $supportedVersions = ['v1', 'v2'];

    /**
     * @var string 기본 API 버전
     */
    protected $defaultVersion = 'v1';

    /**
     * 요청 전 처리
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        // API 요청인지 확인
        if (strpos($path, '/api/') !== 0) {
            return $request;
        }

        $version = null;

        // 1. URI에서 버전 추출 (/api/v1/...)
        if (preg_match('#^/api/(v\d+)/#', $path, $matches)) {
            $version = $matches[1];
        }
        // 2. Query 파라미터에서 버전 확인
        elseif ($request->getGet('version')) {
            $version = 'v' . $request->getGet('version');
        }
        // 3. Header에서 버전 확인
        elseif ($request->hasHeader('api-version')) {
            $apiVersion = $request->getHeaderLine('api-version');
            $version = 'v' . str_replace('.', '', $apiVersion);
        }
        // 4. 기본 버전 사용
        else {
            $version = $this->defaultVersion;
        }

        // 버전 유효성 검증
        if (!in_array($version, $this->supportedVersions)) {
            $response = service('response');
            return $response->setJSON([
                'status' => 400,
                'success' => false,
                'message' => "지원하지 않는 API 버전입니다: {$version}",
                'supportedVersions' => $this->supportedVersions,
            ])->setStatusCode(400);
        }

        // 요청 속성에 버전 정보 저장
        $request->apiVersion = $version;

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
        // API 버전 헤더 추가
        if (isset($request->apiVersion)) {
            $response->setHeader('X-API-Version', $request->apiVersion);
        }

        return $response;
    }
}

