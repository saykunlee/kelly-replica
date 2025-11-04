<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Login History Model
 * 
 * 로그인 이력을 추적하여 보안 감사 로그 제공
 * - 로그인/로그아웃 이력
 * - 토큰 갱신 이력
 * - 실패한 로그인 시도
 * - 의심스러운 활동 탐지
 */
class LoginHistoryModel extends Model
{
    protected $table = 'login_history';
    protected $primaryKey = 'lh_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'lh_user_id',
        'lh_action',
        'lh_status',
        'lh_ip_address',
        'lh_user_agent',
        'lh_device_info',
        'lh_location',
        'lh_token_hash',
        'lh_failure_reason',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'lh_created_at';
    protected $updatedField = '';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'lh_user_id' => 'required|integer',
        'lh_action' => 'required|in_list[login,logout,refresh,revoke]',
        'lh_status' => 'required|in_list[success,failed,blocked]',
        'lh_ip_address' => 'required|max_length[45]',
    ];

    protected $validationMessages = [
        'lh_user_id' => [
            'required' => '사용자 ID가 필요합니다',
            'integer' => '올바른 사용자 ID 형식이 아닙니다',
        ],
        'lh_action' => [
            'in_list' => '액션은 login, logout, refresh, revoke 중 하나여야 합니다',
        ],
        'lh_status' => [
            'in_list' => '상태는 success, failed, blocked 중 하나여야 합니다',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * 로그인 이력 기록
     * 
     * @param int $userId 사용자 ID
     * @param string $action 액션 (login, logout, refresh, revoke)
     * @param string $status 상태 (success, failed, blocked)
     * @param string $ipAddress IP 주소
     * @param array $additionalData 추가 데이터
     * @return int|false 생성된 ID 또는 false
     */
    public function logAction(
        int $userId,
        string $action,
        string $status,
        string $ipAddress,
        array $additionalData = []
    ) {
        $data = [
            'lh_user_id' => $userId,
            'lh_action' => $action,
            'lh_status' => $status,
            'lh_ip_address' => $ipAddress,
            'lh_user_agent' => $additionalData['user_agent'] ?? null,
            'lh_device_info' => $additionalData['device_info'] ?? null,
            'lh_location' => $additionalData['location'] ?? null,
            'lh_token_hash' => $additionalData['token_hash'] ?? null,
            'lh_failure_reason' => $additionalData['failure_reason'] ?? null,
        ];

        return $this->insert($data);
    }

    /**
     * 로그인 성공 기록
     * 
     * @param int $userId 사용자 ID
     * @param string $ipAddress IP 주소
     * @param string|null $userAgent User-Agent
     * @param string|null $tokenHash 토큰 해시
     * @return int|false
     */
    public function logLoginSuccess(int $userId, string $ipAddress, ?string $userAgent = null, ?string $tokenHash = null)
    {
        return $this->logAction($userId, 'login', 'success', $ipAddress, [
            'user_agent' => $userAgent,
            'token_hash' => $tokenHash,
        ]);
    }

    /**
     * 로그인 실패 기록
     * 
     * @param int $userId 사용자 ID
     * @param string $ipAddress IP 주소
     * @param string $reason 실패 사유
     * @param string|null $userAgent User-Agent
     * @return int|false
     */
    public function logLoginFailure(int $userId, string $ipAddress, string $reason, ?string $userAgent = null)
    {
        return $this->logAction($userId, 'login', 'failed', $ipAddress, [
            'user_agent' => $userAgent,
            'failure_reason' => $reason,
        ]);
    }

    /**
     * 로그아웃 기록
     * 
     * @param int $userId 사용자 ID
     * @param string $ipAddress IP 주소
     * @param string|null $tokenHash 토큰 해시
     * @return int|false
     */
    public function logLogout(int $userId, string $ipAddress, ?string $tokenHash = null)
    {
        return $this->logAction($userId, 'logout', 'success', $ipAddress, [
            'token_hash' => $tokenHash,
        ]);
    }

    /**
     * 토큰 갱신 기록
     * 
     * @param int $userId 사용자 ID
     * @param string $ipAddress IP 주소
     * @param string|null $tokenHash 토큰 해시
     * @return int|false
     */
    public function logTokenRefresh(int $userId, string $ipAddress, ?string $tokenHash = null)
    {
        return $this->logAction($userId, 'refresh', 'success', $ipAddress, [
            'token_hash' => $tokenHash,
        ]);
    }

    /**
     * 강제 로그아웃 기록
     * 
     * @param int $userId 사용자 ID
     * @param string $ipAddress IP 주소
     * @param string|null $reason 폐기 사유
     * @return int|false
     */
    public function logForceLogout(int $userId, string $ipAddress, ?string $reason = null)
    {
        return $this->logAction($userId, 'revoke', 'success', $ipAddress, [
            'failure_reason' => $reason,
        ]);
    }

    /**
     * 사용자의 최근 로그인 이력 조회
     * 
     * @param int $userId 사용자 ID
     * @param int $limit 조회할 개수
     * @return array
     */
    public function getRecentHistory(int $userId, int $limit = 10): array
    {
        return $this->where('lh_user_id', $userId)
            ->orderBy('lh_created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * 실패한 로그인 시도 조회 (무차별 대입 공격 탐지)
     * 
     * @param string $ipAddress IP 주소
     * @param int $minutesAgo 몇 분 전부터
     * @param int $threshold 임계값
     * @return int 실패 시도 횟수
     */
    public function countFailedAttempts(string $ipAddress, int $minutesAgo = 60, int $threshold = 5): int
    {
        $cutoffTime = date('Y-m-d H:i:s', strtotime("-{$minutesAgo} minutes"));

        return $this->where('lh_ip_address', $ipAddress)
            ->where('lh_action', 'login')
            ->where('lh_status', 'failed')
            ->where('lh_created_at >', $cutoffTime)
            ->countAllResults();
    }

    /**
     * IP 주소가 차단되어야 하는지 확인
     * 
     * @param string $ipAddress IP 주소
     * @param int $threshold 임계값 (기본 5회)
     * @return bool
     */
    public function shouldBlockIp(string $ipAddress, int $threshold = 5): bool
    {
        $failedAttempts = $this->countFailedAttempts($ipAddress, 60, $threshold);
        return $failedAttempts >= $threshold;
    }

    /**
     * 사용자별 로그인 통계
     * 
     * @param int $userId 사용자 ID
     * @param int $daysAgo 몇 일 전부터
     * @return array
     */
    public function getUserStats(int $userId, int $daysAgo = 30): array
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

        $history = $this->where('lh_user_id', $userId)
            ->where('lh_action', 'login')
            ->where('lh_created_at >', $cutoffDate)
            ->findAll();

        $totalLogins = count($history);
        $successfulLogins = count(array_filter($history, fn($h) => $h['lh_status'] === 'success'));
        $failedLogins = $totalLogins - $successfulLogins;
        $lastLogin = $history[0]['lh_created_at'] ?? null;

        return [
            'total_logins' => $totalLogins,
            'successful_logins' => $successfulLogins,
            'failed_logins' => $failedLogins,
            'last_login' => $lastLogin,
            'unique_ips' => count(array_unique(array_column($history, 'lh_ip_address'))),
        ];
    }

    /**
     * 의심스러운 로그인 탐지 (새로운 위치에서의 로그인)
     * 
     * @param int $userId 사용자 ID
     * @param string $currentLocation 현재 위치
     * @param int $daysAgo 기준 일수
     * @return bool
     */
    public function isNewLocation(int $userId, string $currentLocation, int $daysAgo = 7): bool
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

        $previousLocations = $this->select('lh_location')
            ->where('lh_user_id', $userId)
            ->where('lh_action', 'login')
            ->where('lh_status', 'success')
            ->where('lh_created_at <', $cutoffDate)
            ->where('lh_location IS NOT NULL')
            ->groupBy('lh_location')
            ->findAll();

        $knownLocations = array_column($previousLocations, 'lh_location');

        return !in_array($currentLocation, $knownLocations);
    }

    /**
     * 오래된 로그인 이력 아카이브 (정리)
     * 
     * @param int $daysOld 몇 일 이전 데이터 삭제
     * @return int 삭제된 행 수
     */
    public function archiveOldHistory(int $daysOld = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        
        // 실제로는 아카이브 테이블로 이동 후 삭제
        // 여기서는 단순 삭제만 수행
        return $this->where('lh_created_at <', $cutoffDate)
            ->delete();
    }
}

