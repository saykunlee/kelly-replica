<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

/**
 * JWT Handler Library
 * 
 * JWT 토큰 생성 및 검증을 담당
 */
class JwtHandler
{
    private string $secretKey;
    private string $algorithm = 'HS256';
    private int $accessTokenExpire;
    private int $refreshTokenExpire;

    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET_KEY') ?: 'your-secret-key-change-this';
        $this->accessTokenExpire = (int) (getenv('JWT_ACCESS_EXPIRE') ?: 3600); // 1시간
        $this->refreshTokenExpire = (int) (getenv('JWT_REFRESH_EXPIRE') ?: 604800); // 7일
    }

    /**
     * Access Token 생성
     * 
     * @param array $userData 사용자 데이터
     * @return string
     */
    public function generateAccessToken(array $userData): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->accessTokenExpire;

        $payload = [
            'iat' => $issuedAt,           // Issued At
            'exp' => $expire,             // Expire
            'sub' => $userData['mem_id'], // Subject (사용자 ID)
            'type' => 'access',           // Token Type
            'data' => [
                'mem_id' => $userData['mem_id'],
                'mem_userid' => $userData['mem_userid'],
                'mem_email' => $userData['mem_email'],
                'mem_level' => $userData['mem_level'],
                'mem_is_admin' => $userData['mem_is_admin'] ?? 0,
            ]
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Refresh Token 생성
     * 
     * @param int $userId 사용자 ID
     * @return string
     */
    public function generateRefreshToken(int $userId): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->refreshTokenExpire;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'sub' => $userId,
            'type' => 'refresh',
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * 토큰 검증 및 디코드
     * 
     * @param string $token JWT 토큰
     * @return object|false
     */
    public function validateToken(string $token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded;
        } catch (Exception $e) {
            log_message('error', '[JWT] Token validation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Authorization 헤더에서 토큰 추출
     * 
     * @param string|null $authHeader Authorization 헤더
     * @return string|null
     */
    public function extractToken(?string $authHeader): ?string
    {
        if (!$authHeader) {
            return null;
        }

        // Bearer <token> 형식에서 토큰만 추출
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * 토큰에서 사용자 ID 추출
     * 
     * @param string $token JWT 토큰
     * @return int|null
     */
    public function getUserIdFromToken(string $token): ?int
    {
        $decoded = $this->validateToken($token);
        if (!$decoded) {
            return null;
        }

        return (int) ($decoded->sub ?? null);
    }

    /**
     * 토큰에서 사용자 데이터 추출
     * 
     * @param string $token JWT 토큰
     * @return array|null
     */
    public function getUserDataFromToken(string $token): ?array
    {
        $decoded = $this->validateToken($token);
        if (!$decoded || !isset($decoded->data)) {
            return null;
        }

        return (array) $decoded->data;
    }

    /**
     * Access Token 만료 시간 반환 (초)
     * 
     * @return int
     */
    public function getAccessTokenExpire(): int
    {
        return $this->accessTokenExpire;
    }

    /**
     * Refresh Token 만료 시간 반환 (초)
     * 
     * @return int
     */
    public function getRefreshTokenExpire(): int
    {
        return $this->refreshTokenExpire;
    }
}

