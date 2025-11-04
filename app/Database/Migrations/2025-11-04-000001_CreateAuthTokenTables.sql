-- ============================================================================
-- JWT 인증 시스템 데이터베이스 스키마
-- ============================================================================
-- 작성일: 2025-11-04
-- 설명: Refresh Token 관리, 토큰 블랙리스트, 로그인 이력 추적
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. refresh_tokens 테이블
-- ----------------------------------------------------------------------------
-- Refresh Token을 DB에 저장하여 다음 기능 제공:
-- - 로그아웃 시 토큰 무효화
-- - 동시 로그인 제한
-- - 토큰 재사용 감지
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `refresh_tokens` (
  `rt_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Refresh Token ID (Primary Key)',
  `rt_user_id` INT UNSIGNED NOT NULL COMMENT '사용자 ID (member 테이블 FK)',
  `rt_token_hash` VARCHAR(64) NOT NULL COMMENT 'Token Hash (SHA-256)',
  `rt_device_info` VARCHAR(255) NULL DEFAULT NULL COMMENT '디바이스 정보 (User-Agent)',
  `rt_ip_address` VARCHAR(45) NOT NULL COMMENT 'IP 주소 (IPv4/IPv6)',
  `rt_expires_at` DATETIME NOT NULL COMMENT '만료 시간',
  `rt_created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성 시간',
  `rt_last_used_at` DATETIME NULL DEFAULT NULL COMMENT '마지막 사용 시간',
  `rt_is_revoked` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '폐기 여부 (0: 유효, 1: 폐기)',
  `rt_revoked_at` DATETIME NULL DEFAULT NULL COMMENT '폐기 시간',
  `rt_revoke_reason` VARCHAR(100) NULL DEFAULT NULL COMMENT '폐기 사유',
  
  PRIMARY KEY (`rt_id`),
  INDEX `idx_user_id` (`rt_user_id`),
  INDEX `idx_token_hash` (`rt_token_hash`),
  INDEX `idx_expires_at` (`rt_expires_at`),
  INDEX `idx_is_revoked` (`rt_is_revoked`),
  
  CONSTRAINT `fk_refresh_tokens_user_id` 
    FOREIGN KEY (`rt_user_id`) 
    REFERENCES `member` (`mem_id`) 
    ON DELETE CASCADE
    ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Refresh Token 저장 테이블';

-- ----------------------------------------------------------------------------
-- 2. token_blacklist 테이블
-- ----------------------------------------------------------------------------
-- 무효화된 Access Token을 저장하여 다음 기능 제공:
-- - 로그아웃 후 토큰 즉시 무효화
-- - 강제 로그아웃
-- - 보안 사고 시 긴급 토큰 차단
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `token_blacklist` (
  `bl_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Blacklist ID (Primary Key)',
  `bl_token_hash` VARCHAR(64) NOT NULL COMMENT 'Token Hash (SHA-256)',
  `bl_user_id` INT UNSIGNED NOT NULL COMMENT '사용자 ID',
  `bl_token_type` VARCHAR(20) NOT NULL COMMENT '토큰 타입 (access, refresh)',
  `bl_expires_at` DATETIME NOT NULL COMMENT '원래 토큰 만료 시간',
  `bl_blacklisted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '블랙리스트 추가 시간',
  `bl_reason` VARCHAR(100) NULL DEFAULT NULL COMMENT '블랙리스트 사유',
  `bl_ip_address` VARCHAR(45) NULL DEFAULT NULL COMMENT '요청 IP 주소',
  
  PRIMARY KEY (`bl_id`),
  UNIQUE KEY `uk_token_hash` (`bl_token_hash`),
  INDEX `idx_user_id` (`bl_user_id`),
  INDEX `idx_expires_at` (`bl_expires_at`),
  INDEX `idx_blacklisted_at` (`bl_blacklisted_at`),
  
  CONSTRAINT `fk_token_blacklist_user_id` 
    FOREIGN KEY (`bl_user_id`) 
    REFERENCES `member` (`mem_id`) 
    ON DELETE CASCADE
    ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='토큰 블랙리스트 (무효화된 토큰)';

-- ----------------------------------------------------------------------------
-- 3. login_history 테이블
-- ----------------------------------------------------------------------------
-- 로그인 이력을 추적하여 다음 기능 제공:
-- - 보안 감사 로그
-- - 동시 로그인 모니터링
-- - 의심스러운 로그인 탐지
-- - 사용자별 로그인 통계
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `login_history` (
  `lh_id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Login History ID (Primary Key)',
  `lh_user_id` INT UNSIGNED NOT NULL COMMENT '사용자 ID',
  `lh_action` VARCHAR(20) NOT NULL COMMENT '액션 (login, logout, refresh, revoke)',
  `lh_status` VARCHAR(20) NOT NULL COMMENT '상태 (success, failed, blocked)',
  `lh_ip_address` VARCHAR(45) NOT NULL COMMENT 'IP 주소',
  `lh_user_agent` VARCHAR(500) NULL DEFAULT NULL COMMENT 'User-Agent',
  `lh_device_info` VARCHAR(255) NULL DEFAULT NULL COMMENT '디바이스 정보',
  `lh_location` VARCHAR(100) NULL DEFAULT NULL COMMENT '위치 정보 (국가/도시)',
  `lh_token_hash` VARCHAR(64) NULL DEFAULT NULL COMMENT '관련 토큰 해시',
  `lh_failure_reason` VARCHAR(255) NULL DEFAULT NULL COMMENT '실패 사유',
  `lh_created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성 시간',
  
  PRIMARY KEY (`lh_id`),
  INDEX `idx_user_id` (`lh_user_id`),
  INDEX `idx_action` (`lh_action`),
  INDEX `idx_status` (`lh_status`),
  INDEX `idx_created_at` (`lh_created_at`),
  INDEX `idx_ip_address` (`lh_ip_address`),
  
  CONSTRAINT `fk_login_history_user_id` 
    FOREIGN KEY (`lh_user_id`) 
    REFERENCES `member` (`mem_id`) 
    ON DELETE CASCADE
    ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='로그인 이력 추적';

-- ============================================================================
-- 샘플 쿼리 및 사용 예제
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Refresh Token 관리 쿼리
-- ----------------------------------------------------------------------------

-- 1. Refresh Token 저장 (로그인 시)
-- INSERT INTO refresh_tokens (rt_user_id, rt_token_hash, rt_device_info, rt_ip_address, rt_expires_at)
-- VALUES (1, SHA2('refresh_token_string', 256), 'Mozilla/5.0...', '192.168.1.1', DATE_ADD(NOW(), INTERVAL 7 DAY));

-- 2. Refresh Token 검증 (갱신 시)
-- SELECT * FROM refresh_tokens 
-- WHERE rt_token_hash = SHA2('refresh_token_string', 256)
--   AND rt_is_revoked = 0
--   AND rt_expires_at > NOW();

-- 3. Refresh Token 사용 시간 업데이트
-- UPDATE refresh_tokens 
-- SET rt_last_used_at = NOW()
-- WHERE rt_token_hash = SHA2('refresh_token_string', 256);

-- 4. 특정 사용자의 모든 토큰 폐기 (강제 로그아웃)
-- UPDATE refresh_tokens 
-- SET rt_is_revoked = 1, 
--     rt_revoked_at = NOW(),
--     rt_revoke_reason = 'Admin forced logout'
-- WHERE rt_user_id = 1
--   AND rt_is_revoked = 0;

-- 5. 동시 로그인 제한 - 기존 토큰 폐기 (신규 로그인 시)
-- UPDATE refresh_tokens 
-- SET rt_is_revoked = 1,
--     rt_revoked_at = NOW(),
--     rt_revoke_reason = 'New login detected'
-- WHERE rt_user_id = 1 
--   AND rt_is_revoked = 0;

-- 6. 만료된 토큰 정리 (배치 작업)
-- DELETE FROM refresh_tokens 
-- WHERE rt_expires_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- 7. 사용자의 활성 세션 수 조회
-- SELECT COUNT(*) as active_sessions
-- FROM refresh_tokens
-- WHERE rt_user_id = 1
--   AND rt_is_revoked = 0
--   AND rt_expires_at > NOW();

-- ----------------------------------------------------------------------------
-- 토큰 블랙리스트 쿼리
-- ----------------------------------------------------------------------------

-- 1. 토큰을 블랙리스트에 추가 (로그아웃 시)
-- INSERT INTO token_blacklist (bl_token_hash, bl_user_id, bl_token_type, bl_expires_at, bl_reason)
-- VALUES (SHA2('access_token_string', 256), 1, 'access', DATE_ADD(NOW(), INTERVAL 1 HOUR), 'User logout');

-- 2. 토큰이 블랙리스트에 있는지 확인
-- SELECT COUNT(*) as is_blacklisted
-- FROM token_blacklist
-- WHERE bl_token_hash = SHA2('access_token_string', 256)
--   AND bl_expires_at > NOW();

-- 3. 만료된 블랙리스트 항목 정리 (배치 작업)
-- DELETE FROM token_blacklist
-- WHERE bl_expires_at < NOW();

-- 4. 특정 사용자의 모든 토큰 블랙리스트 추가 (긴급 차단)
-- INSERT INTO token_blacklist (bl_token_hash, bl_user_id, bl_token_type, bl_expires_at, bl_reason)
-- SELECT rt_token_hash, rt_user_id, 'refresh', rt_expires_at, 'Security incident'
-- FROM refresh_tokens
-- WHERE rt_user_id = 1
--   AND rt_is_revoked = 0
--   AND rt_expires_at > NOW();

-- ----------------------------------------------------------------------------
-- 로그인 이력 쿼리
-- ----------------------------------------------------------------------------

-- 1. 로그인 이력 기록 (성공)
-- INSERT INTO login_history (lh_user_id, lh_action, lh_status, lh_ip_address, lh_user_agent, lh_device_info)
-- VALUES (1, 'login', 'success', '192.168.1.1', 'Mozilla/5.0...', 'Chrome on Windows');

-- 2. 로그인 실패 기록
-- INSERT INTO login_history (lh_user_id, lh_action, lh_status, lh_ip_address, lh_failure_reason)
-- VALUES (1, 'login', 'failed', '192.168.1.1', 'Invalid password');

-- 3. 최근 로그인 이력 조회
-- SELECT * FROM login_history
-- WHERE lh_user_id = 1
-- ORDER BY lh_created_at DESC
-- LIMIT 10;

-- 4. 실패한 로그인 시도 조회 (무차별 대입 공격 탐지)
-- SELECT lh_ip_address, COUNT(*) as failed_attempts
-- FROM login_history
-- WHERE lh_action = 'login'
--   AND lh_status = 'failed'
--   AND lh_created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
-- GROUP BY lh_ip_address
-- HAVING failed_attempts >= 5;

-- 5. 사용자별 로그인 통계
-- SELECT 
--   lh_user_id,
--   COUNT(*) as total_logins,
--   COUNT(CASE WHEN lh_status = 'success' THEN 1 END) as successful_logins,
--   COUNT(CASE WHEN lh_status = 'failed' THEN 1 END) as failed_logins,
--   MAX(lh_created_at) as last_login
-- FROM login_history
-- WHERE lh_action = 'login'
--   AND lh_created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
-- GROUP BY lh_user_id;

-- 6. 의심스러운 로그인 탐지 (새로운 위치에서의 로그인)
-- SELECT lh.*
-- FROM login_history lh
-- WHERE lh.lh_user_id = 1
--   AND lh.lh_action = 'login'
--   AND lh.lh_status = 'success'
--   AND lh.lh_location NOT IN (
--     SELECT DISTINCT lh2.lh_location
--     FROM login_history lh2
--     WHERE lh2.lh_user_id = 1
--       AND lh2.lh_created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
--   );

-- ============================================================================
-- 유지보수 및 최적화
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 배치 작업 (Cron Job 또는 스케줄러에서 실행)
-- ----------------------------------------------------------------------------

-- 1. 만료된 Refresh Token 정리 (매일 실행)
-- DELETE FROM refresh_tokens 
-- WHERE rt_expires_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- 2. 만료된 블랙리스트 정리 (매시간 실행)
-- DELETE FROM token_blacklist
-- WHERE bl_expires_at < NOW();

-- 3. 오래된 로그인 이력 아카이브 (매월 실행)
-- -- 90일 이상 된 이력을 별도 테이블로 이동
-- INSERT INTO login_history_archive 
-- SELECT * FROM login_history
-- WHERE lh_created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
-- 
-- DELETE FROM login_history
-- WHERE lh_created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- ----------------------------------------------------------------------------
-- 성능 모니터링 쿼리
-- ----------------------------------------------------------------------------

-- 1. 테이블 크기 확인
-- SELECT 
--   TABLE_NAME,
--   ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)',
--   TABLE_ROWS
-- FROM information_schema.TABLES
-- WHERE TABLE_SCHEMA = DATABASE()
--   AND TABLE_NAME IN ('refresh_tokens', 'token_blacklist', 'login_history');

-- 2. 인덱스 사용률 확인
-- SHOW INDEX FROM refresh_tokens;
-- SHOW INDEX FROM token_blacklist;
-- SHOW INDEX FROM login_history;

-- ============================================================================
-- 보안 권장사항
-- ============================================================================
-- 
-- 1. Refresh Token
--    - 토큰은 반드시 SHA-256 해시로 저장
--    - 원본 토큰은 DB에 저장하지 않음
--    - 만료 시간 후 30일이 지나면 자동 삭제
--
-- 2. 블랙리스트
--    - 만료된 토큰은 즉시 삭제하여 테이블 크기 최소화
--    - Redis를 사용할 수 있다면 Redis 캐시 활용 권장
--
-- 3. 로그인 이력
--    - 민감한 정보(비밀번호 등)는 절대 저장하지 않음
--    - 90일 이상 된 이력은 아카이브 테이블로 이동
--    - GDPR 준수: 사용자 삭제 시 이력도 함께 삭제 (CASCADE)
--
-- 4. 성능 최적화
--    - 정기적인 배치 작업으로 오래된 데이터 정리
--    - 인덱스 최적화 유지
--    - 파티셔닝 고려 (대용량 데이터의 경우)
--
-- ============================================================================

