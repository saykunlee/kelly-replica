<?php

namespace App\Libraries\RestApi;

/**
 * 비동기 작업 처리 핸들러
 * 
 * 장기 실행 작업을 비동기로 처리하고 상태를 추적
 * 202 Accepted 패턴 구현
 */
class AsyncJobHandler
{
    /**
     * @var \CodeIgniter\Cache\CacheInterface 캐시 인스턴스
     */
    protected $cache;

    /**
     * @var int 작업 상태 캐시 유효 시간 (초)
     */
    protected $ttl = 3600; // 1시간

    /**
     * 생성자
     */
    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    /**
     * 비동기 작업 생성
     * 
     * @param callable $callback 실행할 콜백 함수
     * @param array $params 콜백 파라미터
     * @return string 작업 ID
     */
    public function createJob(callable $callback, array $params = []): string
    {
        // 고유 작업 ID 생성
        $jobId = $this->generateJobId();

        // 초기 상태 저장
        $this->updateJobStatus($jobId, [
            'status' => 'pending',
            'progress' => 0,
            'message' => '작업이 대기 중입니다',
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s'),
        ]);

        // 백그라운드에서 작업 실행 (실제 프로덕션에서는 큐 시스템 사용 권장)
        $this->executeJobAsync($jobId, $callback, $params);

        return $jobId;
    }

    /**
     * 작업 상태 조회
     * 
     * @param string $jobId 작업 ID
     * @return array|null 작업 상태 정보
     */
    public function getJobStatus(string $jobId): ?array
    {
        $key = $this->getJobKey($jobId);
        return $this->cache->get($key);
    }

    /**
     * 작업 상태 업데이트
     * 
     * @param string $jobId 작업 ID
     * @param array $status 상태 정보
     * @return bool
     */
    public function updateJobStatus(string $jobId, array $status): bool
    {
        $key = $this->getJobKey($jobId);
        $status['updatedAt'] = date('Y-m-d H:i:s');
        return $this->cache->save($key, $status, $this->ttl);
    }

    /**
     * 작업 완료 처리
     * 
     * @param string $jobId 작업 ID
     * @param mixed $result 작업 결과
     * @param string|null $resourceUrl 생성된 리소스 URL
     * @return bool
     */
    public function completeJob(string $jobId, $result = null, ?string $resourceUrl = null): bool
    {
        return $this->updateJobStatus($jobId, [
            'status' => 'completed',
            'progress' => 100,
            'message' => '작업이 완료되었습니다',
            'result' => $result,
            'resourceUrl' => $resourceUrl,
            'completedAt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 작업 실패 처리
     * 
     * @param string $jobId 작업 ID
     * @param string $errorMessage 에러 메시지
     * @return bool
     */
    public function failJob(string $jobId, string $errorMessage): bool
    {
        return $this->updateJobStatus($jobId, [
            'status' => 'failed',
            'message' => $errorMessage,
            'failedAt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 작업 진행 상태 업데이트
     * 
     * @param string $jobId 작업 ID
     * @param int $progress 진행률 (0-100)
     * @param string|null $message 메시지
     * @return bool
     */
    public function updateProgress(string $jobId, int $progress, ?string $message = null): bool
    {
        $progress = max(0, min(100, $progress)); // 0-100 범위로 제한

        $status = [
            'status' => 'processing',
            'progress' => $progress,
        ];

        if ($message) {
            $status['message'] = $message;
        }

        return $this->updateJobStatus($jobId, $status);
    }

    /**
     * 작업 취소
     * 
     * @param string $jobId 작업 ID
     * @return bool
     */
    public function cancelJob(string $jobId): bool
    {
        return $this->updateJobStatus($jobId, [
            'status' => 'cancelled',
            'message' => '작업이 취소되었습니다',
            'cancelledAt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 작업 삭제
     * 
     * @param string $jobId 작업 ID
     * @return bool
     */
    public function deleteJob(string $jobId): bool
    {
        $key = $this->getJobKey($jobId);
        return $this->cache->delete($key);
    }

    /**
     * 비동기로 작업 실행
     * 
     * @param string $jobId 작업 ID
     * @param callable $callback 콜백 함수
     * @param array $params 파라미터
     * @return void
     */
    protected function executeJobAsync(string $jobId, callable $callback, array $params): void
    {
        // 간단한 백그라운드 실행 (실제로는 큐 시스템 사용 권장)
        // CLI 모드에서 백그라운드 프로세스로 실행
        
        // 작업 시작 상태로 변경
        $this->updateJobStatus($jobId, [
            'status' => 'processing',
            'progress' => 0,
            'message' => '작업을 처리 중입니다',
            'startedAt' => date('Y-m-d H:i:s'),
        ]);

        try {
            // 콜백 실행
            $result = call_user_func_array($callback, array_merge([$jobId], $params));

            // 작업 완료
            $resourceUrl = is_array($result) && isset($result['resourceUrl']) 
                ? $result['resourceUrl'] 
                : null;

            $this->completeJob($jobId, $result, $resourceUrl);
        } catch (\Exception $e) {
            // 작업 실패
            log_message('error', "[AsyncJob] Job {$jobId} failed: " . $e->getMessage());
            $this->failJob($jobId, $e->getMessage());
        }
    }

    /**
     * 고유 작업 ID 생성
     * 
     * @return string
     */
    protected function generateJobId(): string
    {
        return uniqid('job_', true);
    }

    /**
     * 작업 캐시 키 생성
     * 
     * @param string $jobId 작업 ID
     * @return string
     */
    protected function getJobKey(string $jobId): string
    {
        // 캐시 키에서 예약된 문자 제거 (콜론을 언더스코어로 변경)
        return "async_job_" . str_replace([':', '@', '/', '\\', '{', '}', '(', ')'], '_', $jobId);
    }

    /**
     * 202 Accepted 응답 생성
     * 
     * @param string $jobId 작업 ID
     * @param string $apiVersion API 버전
     * @return array
     */
    public function createAcceptedResponse(string $jobId, string $apiVersion = 'v1'): array
    {
        $statusUrl = base_url("api/{$apiVersion}/jobs/{$jobId}");
        return RestApiResponse::accepted($statusUrl, '작업이 접수되었습니다');
    }

    /**
     * 작업 상태 응답 생성
     * 
     * @param string $jobId 작업 ID
     * @return array
     */
    public function createStatusResponse(string $jobId): array
    {
        $status = $this->getJobStatus($jobId);

        if (!$status) {
            return RestApiResponse::notFound('작업을 찾을 수 없습니다');
        }

        // 작업이 완료되고 리소스 URL이 있으면 303 See Other 응답
        if ($status['status'] === 'completed' && !empty($status['resourceUrl'])) {
            return [
                'status' => 303,
                'success' => true,
                'message' => '작업이 완료되었습니다',
                'location' => $status['resourceUrl'],
                'data' => $status,
            ];
        }

        // 일반 상태 응답
        return RestApiResponse::success($status);
    }
}

