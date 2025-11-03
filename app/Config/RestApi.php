<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * RESTful API 설정
 * 
 * API 프레임워크 전역 설정
 */
class RestApi extends BaseConfig
{
    /**
     * 지원하는 API 버전
     * 
     * @var array
     */
    public array $supportedVersions = ['v1'];

    /**
     * 기본 API 버전
     * 
     * @var string
     */
    public string $defaultVersion = 'v1';

    /**
     * Rate Limiting 설정
     * 
     * @var array
     */
    public array $rateLimit = [
        'enabled' => true,
        'windowSize' => 60, // 시간 윈도우 (초)
        'maxRequests' => 100, // 윈도우당 최대 요청 수
    ];

    /**
     * ETag 캐싱 활성화
     * 
     * @var bool
     */
    public bool $enableETag = true;

    /**
     * 기본 캐시 최대 시간 (초)
     * 
     * @var int
     */
    public int $defaultCacheMaxAge = 300; // 5분

    /**
     * CORS 설정
     * 
     * @var array
     */
    public array $cors = [
        'allowedOrigins' => ['*'],
        'allowedMethods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'allowedHeaders' => [
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'X-API-Version',
            'If-Match',
            'If-None-Match',
        ],
        'exposedHeaders' => [
            'X-RateLimit-Limit',
            'X-RateLimit-Remaining',
            'X-RateLimit-Reset',
            'X-API-Version',
            'ETag',
            'Location',
        ],
        'maxAge' => 3600, // Preflight 캐시 시간 (초)
        'allowCredentials' => true,
    ];

    /**
     * 페이지네이션 설정
     * 
     * @var array
     */
    public array $pagination = [
        'defaultLimit' => 20,
        'maxLimit' => 100,
    ];

    /**
     * 비동기 작업 설정
     * 
     * @var array
     */
    public array $asyncJobs = [
        'ttl' => 3600, // 작업 상태 캐시 유효 시간 (초)
    ];

    /**
     * API 문서화 설정
     * 
     * @var array
     */
    public array $documentation = [
        'enabled' => true,
        'title' => 'Kelly RESTful API',
        'description' => 'Azure API Design Best Practices 기반 RESTful API',
        'version' => '1.0.0',
        'contact' => [
            'name' => 'API Support',
            'email' => 'support@example.com',
        ],
    ];

    /**
     * 에러 로깅 설정
     * 
     * @var array
     */
    public array $errorLogging = [
        'enabled' => true,
        'logLevel' => 'error', // debug, info, warning, error, critical
        'includeStackTrace' => false, // 프로덕션에서는 false 권장
    ];

    /**
     * 응답 압축 설정
     * 
     * @var array
     */
    public array $compression = [
        'enabled' => true,
        'minSize' => 1024, // 최소 크기 (바이트)
    ];

    /**
     * 허용된 응답 형식
     * 
     * @var array
     */
    public array $allowedFormats = [
        'json' => 'application/json',
        'xml' => 'application/xml',
    ];

    /**
     * 기본 응답 형식
     * 
     * @var string
     */
    public string $defaultFormat = 'json';
}

