<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JwtHandler;
use App\Libraries\RestApi\RestApiResponse;

/**
 * JWT 인증 필터
 * 
 * API 요청 시 JWT 토큰을 검증합니다.
 */
class JwtAuthFilter implements FilterInterface
{
    /**
     * Before 필터 - 요청 전 토큰 검증
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtHandler = new JwtHandler();
        
        // Authorization 헤더에서 토큰 추출
        $authHeader = $request->getHeaderLine('Authorization');
        $token = $jwtHandler->extractToken($authHeader);

        if (!$token) {
            return $this->unauthorizedResponse('인증 토큰이 필요합니다');
        }

        // 블랙리스트 체크
        $tokenBlacklistModel = model('App\Models\TokenBlacklistModel');
        if ($tokenBlacklistModel->isBlacklisted($token)) {
            return $this->unauthorizedResponse('무효화된 토큰입니다', 'TOKEN_REVOKED');
        }

        // 토큰 검증
        $decoded = $jwtHandler->validateToken($token);

        if (!$decoded) {
            return $this->unauthorizedResponse('유효하지 않은 토큰입니다');
        }

        // 토큰 타입 확인 (access token만 허용)
        if (!isset($decoded->type) || $decoded->type !== 'access') {
            return $this->unauthorizedResponse('잘못된 토큰 타입입니다');
        }

        // 만료 확인
        if (isset($decoded->exp) && $decoded->exp < time()) {
            return $this->unauthorizedResponse('만료된 토큰입니다', 'TOKEN_EXPIRED');
        }

        // 사용자 계정 상태 확인 (추가 보안)
        $memberModel = model('App\Models\MemberModel');
        $userId = $decoded->sub ?? null;
        if ($userId) {
            $member = $memberModel->find($userId);
            if (!$member || $member['mem_denied'] == 1) {
                return $this->unauthorizedResponse('차단된 계정입니다', 'ACCOUNT_DENIED');
            }
        }

        // 요청 객체에 사용자 정보 저장 (컨트롤러에서 사용 가능)
        $request->user = $decoded->data ?? null;
        $request->userId = $decoded->sub ?? null;

        return $request;
    }

    /**
     * After 필터
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 추가 처리 필요 시 구현
    }

    /**
     * 인증 실패 응답
     * 
     * @param string $message 에러 메시지
     * @param string $errorCode 에러 코드
     * @return ResponseInterface
     */
    private function unauthorizedResponse(string $message, string $errorCode = 'UNAUTHORIZED'): ResponseInterface
    {
        $response = RestApiResponse::error($message, 401, $errorCode);
        
        return response()
            ->setJSON($response)
            ->setStatusCode(401);
    }
}

