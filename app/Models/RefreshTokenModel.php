<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Refresh Token Model
 * 
 * Refresh Token을 DB에 저장하고 관리
 * - 로그아웃 시 토큰 무효화
 * - 동시 로그인 제한
 * - 토큰 재사용 감지
 */
class RefreshTokenModel extends Model
{
    protected $table = 'refresh_tokens';
    protected $primaryKey = 'rt_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'rt_user_id',
        'rt_token_hash',
        'rt_device_info',
        'rt_ip_address',
        'rt_expires_at',
        'rt_last_used_at',
        'rt_is_revoked',
        'rt_revoked_at',
        'rt_revoke_reason',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'rt_created_at';
    protected $updatedField = '';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'rt_user_id' => 'required|integer',
        'rt_token_hash' => 'required|max_length[64]',
        'rt_ip_address' => 'required|max_length[45]',
        'rt_expires_at' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'rt_user_id' => [
            'required' => '사용자 ID가 필요합니다',
            'integer' => '올바른 사용자 ID 형식이 아닙니다',
        ],
        'rt_token_hash' => [
            'required' => '토큰 해시가 필요합니다',
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
     * 토큰 저장
     * 
     * @param string $token 원본 토큰
     * @param int $userId 사용자 ID
     * @param string $ipAddress IP 주소
     * @param string|null $deviceInfo 디바이스 정보
     * @param int $expiresIn 만료 시간 (초)
     * @return int|false 생성된 ID 또는 false
     */
    public function storeToken(string $token, int $userId, string $ipAddress, ?string $deviceInfo = null, int $expiresIn = 604800)
    {
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

        $data = [
            'rt_user_id' => $userId,
            'rt_token_hash' => $tokenHash,
            'rt_device_info' => $deviceInfo,
            'rt_ip_address' => $ipAddress,
            'rt_expires_at' => $expiresAt,
        ];

        return $this->insert($data);
    }

    /**
     * 토큰 검증
     * 
     * @param string $token 원본 토큰
     * @return array|null 토큰 정보 또는 null
     */
    public function validateToken(string $token): ?array
    {
        $tokenHash = hash('sha256', $token);

        $result = $this->where('rt_token_hash', $tokenHash)
            ->where('rt_is_revoked', 0)
            ->where('rt_expires_at >', date('Y-m-d H:i:s'))
            ->first();

        return $result ?: null;
    }

    /**
     * 토큰 사용 시간 업데이트
     * 
     * @param string $token 원본 토큰
     * @return bool
     */
    public function updateLastUsed(string $token): bool
    {
        $tokenHash = hash('sha256', $token);

        return $this->where('rt_token_hash', $tokenHash)
            ->set('rt_last_used_at', date('Y-m-d H:i:s'))
            ->update();
    }

    /**
     * 토큰 폐기
     * 
     * @param string $token 원본 토큰
     * @param string|null $reason 폐기 사유
     * @return bool
     */
    public function revokeToken(string $token, ?string $reason = null): bool
    {
        $tokenHash = hash('sha256', $token);

        return $this->where('rt_token_hash', $tokenHash)
            ->set([
                'rt_is_revoked' => 1,
                'rt_revoked_at' => date('Y-m-d H:i:s'),
                'rt_revoke_reason' => $reason,
            ])
            ->update();
    }

    /**
     * 사용자의 모든 토큰 폐기 (강제 로그아웃)
     * 
     * @param int $userId 사용자 ID
     * @param string|null $reason 폐기 사유
     * @return bool
     */
    public function revokeAllByUserId(int $userId, ?string $reason = 'Force logout'): bool
    {
        return $this->where('rt_user_id', $userId)
            ->where('rt_is_revoked', 0)
            ->set([
                'rt_is_revoked' => 1,
                'rt_revoked_at' => date('Y-m-d H:i:s'),
                'rt_revoke_reason' => $reason,
            ])
            ->update();
    }

    /**
     * 사용자의 기존 토큰 폐기 (동시 로그인 제한)
     * 
     * @param int $userId 사용자 ID
     * @return bool
     */
    public function revokePreviousByUserId(int $userId): bool
    {
        return $this->revokeAllByUserId($userId, 'New login detected');
    }

    /**
     * 사용자의 활성 세션 수 조회
     * 
     * @param int $userId 사용자 ID
     * @return int
     */
    public function countActiveSessions(int $userId): int
    {
        return $this->where('rt_user_id', $userId)
            ->where('rt_is_revoked', 0)
            ->where('rt_expires_at >', date('Y-m-d H:i:s'))
            ->countAllResults();
    }

    /**
     * 사용자의 활성 세션 목록 조회
     * 
     * @param int $userId 사용자 ID
     * @return array
     */
    public function getActiveSessions(int $userId): array
    {
        return $this->where('rt_user_id', $userId)
            ->where('rt_is_revoked', 0)
            ->where('rt_expires_at >', date('Y-m-d H:i:s'))
            ->orderBy('rt_created_at', 'DESC')
            ->findAll();
    }

    /**
     * 만료된 토큰 정리
     * 
     * @param int $daysOld 몇 일 이전 데이터 삭제 (기본 30일)
     * @return int 삭제된 행 수
     */
    public function cleanExpiredTokens(int $daysOld = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        
        return $this->where('rt_expires_at <', $cutoffDate)
            ->delete();
    }

    /**
     * 토큰 해시 생성 (헬퍼 메서드)
     * 
     * @param string $token 원본 토큰
     * @return string
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}

