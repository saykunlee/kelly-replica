<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Token Blacklist Model
 * 
 * 무효화된 Access Token을 저장하여 즉시 차단
 * - 로그아웃 후 토큰 즉시 무효화
 * - 강제 로그아웃
 * - 보안 사고 시 긴급 토큰 차단
 */
class TokenBlacklistModel extends Model
{
    protected $table = 'token_blacklist';
    protected $primaryKey = 'bl_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'bl_token_hash',
        'bl_user_id',
        'bl_token_type',
        'bl_expires_at',
        'bl_reason',
        'bl_ip_address',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'bl_blacklisted_at';
    protected $updatedField = '';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'bl_token_hash' => 'required|max_length[64]',
        'bl_user_id' => 'required|integer',
        'bl_token_type' => 'required|in_list[access,refresh]',
        'bl_expires_at' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'bl_token_hash' => [
            'required' => '토큰 해시가 필요합니다',
        ],
        'bl_user_id' => [
            'required' => '사용자 ID가 필요합니다',
            'integer' => '올바른 사용자 ID 형식이 아닙니다',
        ],
        'bl_token_type' => [
            'in_list' => '토큰 타입은 access 또는 refresh여야 합니다',
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
     * 토큰을 블랙리스트에 추가
     * 
     * @param string $token 원본 토큰
     * @param int $userId 사용자 ID
     * @param string $tokenType 토큰 타입 (access, refresh)
     * @param int $expiresIn 만료 시간 (초)
     * @param string|null $reason 블랙리스트 사유
     * @param string|null $ipAddress IP 주소
     * @return int|false 생성된 ID 또는 false
     */
    public function addToBlacklist(
        string $token, 
        int $userId, 
        string $tokenType, 
        int $expiresIn, 
        ?string $reason = null,
        ?string $ipAddress = null
    ) {
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

        $data = [
            'bl_token_hash' => $tokenHash,
            'bl_user_id' => $userId,
            'bl_token_type' => $tokenType,
            'bl_expires_at' => $expiresAt,
            'bl_reason' => $reason,
            'bl_ip_address' => $ipAddress,
        ];

        // 중복 체크 (이미 블랙리스트에 있으면 추가하지 않음)
        $existing = $this->where('bl_token_hash', $tokenHash)->first();
        if ($existing) {
            return (int) $existing['bl_id'];
        }

        return $this->insert($data);
    }

    /**
     * 토큰이 블랙리스트에 있는지 확인
     * 
     * @param string $token 원본 토큰
     * @return bool
     */
    public function isBlacklisted(string $token): bool
    {
        $tokenHash = hash('sha256', $token);

        $count = $this->where('bl_token_hash', $tokenHash)
            ->where('bl_expires_at >', date('Y-m-d H:i:s'))
            ->countAllResults();

        return $count > 0;
    }

    /**
     * 토큰 해시로 블랙리스트 확인
     * 
     * @param string $tokenHash 토큰 해시
     * @return bool
     */
    public function isBlacklistedByHash(string $tokenHash): bool
    {
        $count = $this->where('bl_token_hash', $tokenHash)
            ->where('bl_expires_at >', date('Y-m-d H:i:s'))
            ->countAllResults();

        return $count > 0;
    }

    /**
     * 사용자의 모든 활성 토큰을 블랙리스트에 추가
     * 
     * @param int $userId 사용자 ID
     * @param string|null $reason 블랙리스트 사유
     * @return int 추가된 토큰 수
     */
    public function blacklistAllUserTokens(int $userId, ?string $reason = 'Force logout'): int
    {
        // RefreshTokenModel에서 활성 토큰 조회
        $refreshTokenModel = model('App\Models\RefreshTokenModel');
        $activeTokens = $refreshTokenModel->getActiveSessions($userId);

        $count = 0;
        foreach ($activeTokens as $tokenData) {
            // 토큰 해시를 직접 사용하여 블랙리스트에 추가
            $data = [
                'bl_token_hash' => $tokenData['rt_token_hash'],
                'bl_user_id' => $userId,
                'bl_token_type' => 'refresh',
                'bl_expires_at' => $tokenData['rt_expires_at'],
                'bl_reason' => $reason,
            ];

            if ($this->insert($data)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * 만료된 블랙리스트 항목 정리
     * 
     * @return int 삭제된 행 수
     */
    public function cleanExpiredEntries(): int
    {
        return $this->where('bl_expires_at <', date('Y-m-d H:i:s'))
            ->delete();
    }

    /**
     * 사용자별 블랙리스트 통계
     * 
     * @param int $userId 사용자 ID
     * @return array
     */
    public function getUserBlacklistStats(int $userId): array
    {
        $total = $this->where('bl_user_id', $userId)->countAllResults();
        $active = $this->where('bl_user_id', $userId)
            ->where('bl_expires_at >', date('Y-m-d H:i:s'))
            ->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $total - $active,
        ];
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

