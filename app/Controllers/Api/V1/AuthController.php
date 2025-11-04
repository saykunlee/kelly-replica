<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\JwtHandler;
use App\Libraries\RestApi\RestApiResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 인증 API 컨트롤러
 * 
 * JWT 기반 인증 시스템
 * - 로그인 (JWT 발급)
 * - 토큰 갱신 (Refresh Token)
 * - 로그아웃
 * - 사용자 정보 조회
 */
class AuthController extends ResourceController
{
    protected $format = 'json';
    protected $jwtHandler;
    protected $memberModel;
    protected $refreshTokenModel;
    protected $tokenBlacklistModel;
    protected $loginHistoryModel;

    public function __construct()
    {
        $this->jwtHandler = new JwtHandler();
        $this->memberModel = model('App\Models\MemberModel');
        $this->refreshTokenModel = model('App\Models\RefreshTokenModel');
        $this->tokenBlacklistModel = model('App\Models\TokenBlacklistModel');
        $this->loginHistoryModel = model('App\Models\LoginHistoryModel');
    }

    /**
     * 로그인 (JWT 발급)
     * POST /api/v1/auth/login
     * 
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        // JSON 본문 데이터 가져오기
        $data = $this->request->getJSON(true) ?? [];
        
        $rules = [
            'userid' => 'required',
            'password' => 'required|min_length[4]',
        ];

        if (!$this->validate($rules, $data)) {
            $response = RestApiResponse::validationError($this->validator->getErrors());
            return $this->respond($response, $response['status']);
        }

        $userid = $data['userid'] ?? '';
        $password = $data['password'] ?? '';
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent()->getAgentString();

        // 무차별 대입 공격 방지 - IP 차단 체크
        if ($this->loginHistoryModel->shouldBlockIp($ipAddress)) {
            $response = RestApiResponse::error('로그인 시도 횟수가 초과되었습니다. 잠시 후 다시 시도해주세요.', 429);
            return $this->respond($response, $response['status']);
        }

        // 사용자 조회
        $member = $this->memberModel->where('mem_userid', $userid)->first();

        if (!$member) {
            // 실패 이력 기록 (존재하지 않는 사용자)
            $this->loginHistoryModel->logAction(
                0, // 사용자 없음
                'login',
                'failed',
                $ipAddress,
                [
                    'user_agent' => $userAgent,
                    'failure_reason' => 'User not found: ' . $userid,
                ]
            );
            
            $response = RestApiResponse::error('사용자를 찾을 수 없습니다', 404);
            return $this->respond($response, $response['status']);
        }

        // 비밀번호 검증
        if (!password_verify($password, $member['mem_password'])) {
            // 실패 이력 기록
            $this->loginHistoryModel->logLoginFailure(
                $member['mem_id'],
                $ipAddress,
                'Invalid password',
                $userAgent
            );
            
            $response = RestApiResponse::error('비밀번호가 일치하지 않습니다', 401);
            return $this->respond($response, $response['status']);
        }

        // 계정 상태 확인
        if ($member['mem_denied'] == 1) {
            // 차단된 계정 로그인 시도 기록
            $this->loginHistoryModel->logAction(
                $member['mem_id'],
                'login',
                'blocked',
                $ipAddress,
                [
                    'user_agent' => $userAgent,
                    'failure_reason' => 'Account is denied',
                ]
            );
            
            $response = RestApiResponse::error('차단된 계정입니다', 403);
            return $this->respond($response, $response['status']);
        }

        // JWT 토큰 생성
        $accessToken = $this->jwtHandler->generateAccessToken($member);
        $refreshToken = $this->jwtHandler->generateRefreshToken($member['mem_id']);

        // 동시 로그인 제한 옵션 (환경변수로 제어 가능)
        $limitConcurrentSessions = getenv('LIMIT_CONCURRENT_SESSIONS') === 'true';
        if ($limitConcurrentSessions) {
            // 기존 Refresh Token 모두 폐기
            $this->refreshTokenModel->revokePreviousByUserId($member['mem_id']);
        }

        // Refresh Token을 DB에 저장
        $deviceInfo = $this->extractDeviceInfo($userAgent);
        $this->refreshTokenModel->storeToken(
            $refreshToken,
            $member['mem_id'],
            $ipAddress,
            $deviceInfo,
            $this->jwtHandler->getRefreshTokenExpire()
        );

        // 마지막 로그인 정보 업데이트
        $this->memberModel->update($member['mem_id'], [
            'mem_lastlogin_datetime' => date('Y-m-d H:i:s'),
            'mem_lastlogin_ip' => $ipAddress,
        ]);

        // 로그인 성공 이력 기록
        $this->loginHistoryModel->logLoginSuccess(
            $member['mem_id'],
            $ipAddress,
            $userAgent,
            hash('sha256', $refreshToken)
        );

        $response = RestApiResponse::success([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->jwtHandler->getAccessTokenExpire(),
            'user' => [
                'mem_id' => $member['mem_id'],
                'mem_userid' => $member['mem_userid'],
                'mem_email' => $member['mem_email'],
                'mem_username' => $member['mem_username'],
                'mem_level' => $member['mem_level'],
                'mem_is_admin' => $member['mem_is_admin'],
            ],
        ]);

        return $this->respond($response, $response['status']);
    }

    /**
     * User-Agent에서 디바이스 정보 추출
     * 
     * @param string $userAgent User-Agent 문자열
     * @return string
     */
    private function extractDeviceInfo(string $userAgent): string
    {
        // 간단한 디바이스 정보 추출
        if (stripos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        } elseif (stripos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * 토큰 갱신
     * POST /api/v1/auth/refresh
     * 
     * @return ResponseInterface
     */
    public function refresh(): ResponseInterface
    {
        // JSON 본문 데이터 가져오기
        $data = $this->request->getJSON(true) ?? [];
        $refreshToken = $data['refresh_token'] ?? '';
        $ipAddress = $this->request->getIPAddress();

        if (!$refreshToken) {
            $response = RestApiResponse::error('Refresh Token이 필요합니다', 400);
            return $this->respond($response, $response['status']);
        }

        // 블랙리스트 체크
        if ($this->tokenBlacklistModel->isBlacklisted($refreshToken)) {
            $response = RestApiResponse::error('무효화된 토큰입니다', 401);
            return $this->respond($response, $response['status']);
        }

        // Refresh Token 검증
        $decoded = $this->jwtHandler->validateToken($refreshToken);

        if (!$decoded) {
            $response = RestApiResponse::error('유효하지 않은 Refresh Token입니다', 401);
            return $this->respond($response, $response['status']);
        }

        // 토큰 타입 확인
        if (!isset($decoded->type) || $decoded->type !== 'refresh') {
            $response = RestApiResponse::error('잘못된 토큰 타입입니다', 401);
            return $this->respond($response, $response['status']);
        }

        // DB에서 Refresh Token 검증
        $tokenData = $this->refreshTokenModel->validateToken($refreshToken);
        if (!$tokenData) {
            $response = RestApiResponse::error('유효하지 않거나 만료된 Refresh Token입니다', 401);
            return $this->respond($response, $response['status']);
        }

        // 사용자 조회
        $userId = $decoded->sub;
        $member = $this->memberModel->find($userId);

        if (!$member) {
            $response = RestApiResponse::error('사용자를 찾을 수 없습니다', 404);
            return $this->respond($response, $response['status']);
        }

        // 계정 상태 확인
        if ($member['mem_denied'] == 1) {
            $response = RestApiResponse::error('차단된 계정입니다', 403);
            return $this->respond($response, $response['status']);
        }

        // 토큰 사용 시간 업데이트
        $this->refreshTokenModel->updateLastUsed($refreshToken);

        // 토큰 갱신 이력 기록
        $this->loginHistoryModel->logTokenRefresh(
            $userId,
            $ipAddress,
            hash('sha256', $refreshToken)
        );

        // 새로운 Access Token 생성
        $newAccessToken = $this->jwtHandler->generateAccessToken($member);

        $response = RestApiResponse::success([
            'access_token' => $newAccessToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->jwtHandler->getAccessTokenExpire(),
        ]);

        return $this->respond($response, $response['status']);
    }

    /**
     * 로그아웃
     * POST /api/v1/auth/logout
     * 
     * @return ResponseInterface
     */
    public function logout(): ResponseInterface
    {
        // JwtAuthFilter에서 설정한 사용자 정보 가져오기
        $userId = $this->request->userId ?? null;
        $ipAddress = $this->request->getIPAddress();

        // Authorization 헤더에서 Access Token 추출
        $authHeader = $this->request->getHeaderLine('Authorization');
        $accessToken = $this->jwtHandler->extractToken($authHeader);

        // Refresh Token 가져오기 (요청 본문에서)
        $data = $this->request->getJSON(true) ?? [];
        $refreshToken = $data['refresh_token'] ?? null;

        if ($userId) {
            // Access Token을 블랙리스트에 추가
            if ($accessToken) {
                $this->tokenBlacklistModel->addToBlacklist(
                    $accessToken,
                    $userId,
                    'access',
                    $this->jwtHandler->getAccessTokenExpire(),
                    'User logout',
                    $ipAddress
                );
            }

            // Refresh Token을 블랙리스트에 추가 및 폐기
            if ($refreshToken) {
                $this->tokenBlacklistModel->addToBlacklist(
                    $refreshToken,
                    $userId,
                    'refresh',
                    $this->jwtHandler->getRefreshTokenExpire(),
                    'User logout',
                    $ipAddress
                );

                $this->refreshTokenModel->revokeToken($refreshToken, 'User logout');
            }

            // 로그아웃 이력 기록
            $this->loginHistoryModel->logLogout(
                $userId,
                $ipAddress,
                $accessToken ? hash('sha256', $accessToken) : null
            );
        }

        $response = RestApiResponse::success([
            'message' => '로그아웃되었습니다',
        ]);

        return $this->respond($response, $response['status']);
    }

    /**
     * 현재 인증된 사용자 정보 조회
     * GET /api/v1/auth/me
     * 
     * @return ResponseInterface
     */
    public function me(): ResponseInterface
    {
        // JwtAuthFilter에서 설정한 사용자 정보 가져오기
        $userId = $this->request->userId ?? null;

        if (!$userId) {
            $response = RestApiResponse::error('인증 정보를 찾을 수 없습니다', 401);
            return $this->respond($response, $response['status']);
        }

        // 최신 사용자 정보 조회
        $member = $this->memberModel->find($userId);

        if (!$member) {
            $response = RestApiResponse::error('사용자를 찾을 수 없습니다', 404);
            return $this->respond($response, $response['status']);
        }

        // 민감한 정보 제외
        unset($member['mem_password']);

        $response = RestApiResponse::success($member);
        return $this->respond($response, $response['status']);
    }

    /**
     * 토큰 검증
     * POST /api/v1/auth/verify
     * 
     * @return ResponseInterface
     */
    public function verify(): ResponseInterface
    {
        // JSON 본문 데이터 가져오기
        $data = $this->request->getJSON(true) ?? [];
        $token = $data['token'] ?? '';

        if (!$token) {
            // Authorization 헤더에서 추출 시도
            $authHeader = $this->request->getHeaderLine('Authorization');
            $token = $this->jwtHandler->extractToken($authHeader);
        }

        if (!$token) {
            $response = RestApiResponse::error('토큰이 필요합니다', 400);
            return $this->respond($response, $response['status']);
        }

        $decoded = $this->jwtHandler->validateToken($token);

        if (!$decoded) {
            $response = RestApiResponse::error('유효하지 않은 토큰입니다', 401);
            return $this->respond($response, $response['status']);
        }

        $response = RestApiResponse::success([
            'valid' => true,
            'token_type' => $decoded->type ?? 'unknown',
            'user_id' => $decoded->sub ?? null,
            'expires_at' => date('Y-m-d H:i:s', $decoded->exp),
        ]);

        return $this->respond($response, $response['status']);
    }

    /**
     * 강제 로그아웃 (관리자용)
     * POST /api/v1/auth/force-logout
     * 
     * @return ResponseInterface
     */
    public function forceLogout(): ResponseInterface
    {
        // 관리자 권한 확인
        $currentUserId = $this->request->userId ?? null;
        $currentUser = $this->memberModel->find($currentUserId);

        if (!$currentUser || $currentUser['mem_is_admin'] != 1) {
            $response = RestApiResponse::error('관리자 권한이 필요합니다', 403);
            return $this->respond($response, $response['status']);
        }

        // JSON 본문 데이터 가져오기
        $data = $this->request->getJSON(true) ?? [];
        $targetUserId = $data['user_id'] ?? null;
        $reason = $data['reason'] ?? 'Admin forced logout';

        if (!$targetUserId) {
            $response = RestApiResponse::error('사용자 ID가 필요합니다', 400);
            return $this->respond($response, $response['status']);
        }

        // 대상 사용자 확인
        $targetUser = $this->memberModel->find($targetUserId);
        if (!$targetUser) {
            $response = RestApiResponse::error('사용자를 찾을 수 없습니다', 404);
            return $this->respond($response, $response['status']);
        }

        $ipAddress = $this->request->getIPAddress();

        // 모든 Refresh Token 폐기
        $this->refreshTokenModel->revokeAllByUserId($targetUserId, $reason);

        // 활성 토큰을 블랙리스트에 추가
        $blacklistedCount = $this->tokenBlacklistModel->blacklistAllUserTokens($targetUserId, $reason);

        // 강제 로그아웃 이력 기록
        $this->loginHistoryModel->logForceLogout(
            $targetUserId,
            $ipAddress,
            $reason . ' (by admin: ' . $currentUser['mem_userid'] . ')'
        );

        $response = RestApiResponse::success([
            'message' => '사용자의 모든 세션이 종료되었습니다',
            'user_id' => $targetUserId,
            'blacklisted_tokens' => $blacklistedCount,
        ]);

        return $this->respond($response, $response['status']);
    }

    /**
     * 활성 세션 조회
     * GET /api/v1/auth/sessions
     * 
     * @return ResponseInterface
     */
    public function getSessions(): ResponseInterface
    {
        $userId = $this->request->userId ?? null;

        if (!$userId) {
            $response = RestApiResponse::error('인증 정보를 찾을 수 없습니다', 401);
            return $this->respond($response, $response['status']);
        }

        // 활성 세션 조회
        $sessions = $this->refreshTokenModel->getActiveSessions($userId);

        // 세션 수 조회
        $count = $this->refreshTokenModel->countActiveSessions($userId);

        $response = RestApiResponse::success([
            'active_sessions' => $count,
            'sessions' => array_map(function($session) {
                return [
                    'id' => $session['rt_id'],
                    'device_info' => $session['rt_device_info'],
                    'ip_address' => $session['rt_ip_address'],
                    'created_at' => $session['rt_created_at'],
                    'last_used_at' => $session['rt_last_used_at'],
                    'expires_at' => $session['rt_expires_at'],
                ];
            }, $sessions),
        ]);

        return $this->respond($response, $response['status']);
    }

    /**
     * 비밀번호 변경
     * POST /api/v1/auth/change-password
     * 
     * @return ResponseInterface
     */
    public function changePassword(): ResponseInterface
    {
        $userId = $this->request->userId ?? null;

        if (!$userId) {
            $response = RestApiResponse::error('인증 정보를 찾을 수 없습니다', 401);
            return $this->respond($response, $response['status']);
        }

        // JSON 본문 데이터 가져오기
        $data = $this->request->getJSON(true) ?? [];
        
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules, $data)) {
            $response = RestApiResponse::validationError($this->validator->getErrors());
            return $this->respond($response, $response['status']);
        }

        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        // 사용자 조회
        $member = $this->memberModel->find($userId);

        if (!$member) {
            $response = RestApiResponse::error('사용자를 찾을 수 없습니다', 404);
            return $this->respond($response, $response['status']);
        }

        // 현재 비밀번호 검증
        if (!password_verify($currentPassword, $member['mem_password'])) {
            $response = RestApiResponse::error('현재 비밀번호가 일치하지 않습니다', 401);
            return $this->respond($response, $response['status']);
        }

        // 새 비밀번호 해싱 및 업데이트
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->memberModel->update($userId, [
            'mem_password' => $hashedPassword,
        ]);

        $response = RestApiResponse::success([
            'message' => '비밀번호가 변경되었습니다',
        ]);

        return $this->respond($response, $response['status']);
    }
}

