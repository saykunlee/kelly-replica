<?php

namespace App\Controllers\Api\V1;

use App\Libraries\RestApi\RestApiController;
use App\Libraries\RestApi\AsyncJobHandler;
use App\Libraries\RestApi\RestApiResponse;

/**
 * 예제 리소스 API 컨트롤러
 * 
 * RESTful API 프레임워크 사용 예제
 * 비동기 작업 처리 예제 포함
 */
class ExampleResourceController extends RestApiController
{
    protected $modelName = 'App\Models\BaseModel'; // 실제 모델로 교체 필요
    protected $resourceName = 'examples';
    protected $apiVersion = 'v1';
    protected $enableETag = true;
    protected $cacheMaxAge = 600; // 10분

    /**
     * 유효성 검증 규칙
     * 
     * @param string $action 액션 타입
     * @return array
     */
    protected function getValidationRules(string $action): array
    {
        $rules = [];

        switch ($action) {
            case 'create':
            case 'update':
                $rules = [
                    'name' => [
                        'label' => '이름',
                        'rules' => 'required|max_length[255]',
                    ],
                    'description' => [
                        'label' => '설명',
                        'rules' => 'permit_empty|max_length[1000]',
                    ],
                ];
                break;

            case 'patch':
                $rules = [
                    'name' => [
                        'label' => '이름',
                        'rules' => 'permit_empty|max_length[255]',
                    ],
                    'description' => [
                        'label' => '설명',
                        'rules' => 'permit_empty|max_length[1000]',
                    ],
                ];
                break;
        }

        return $rules;
    }

    /**
     * 비동기 작업 생성 예제
     * 
     * POST /api/v1/examples/async-task
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function createAsyncTask()
    {
        $data = $this->getRequestData();

        // AsyncJobHandler 인스턴스 생성
        $asyncHandler = new AsyncJobHandler();

        // 비동기 작업 생성
        $jobId = $asyncHandler->createJob(
            function ($jobId) use ($data) {
                // 장기 실행 작업 시뮬레이션
                sleep(2); // 실제로는 복잡한 처리 로직

                // 진행 상태 업데이트 예제
                $handler = new AsyncJobHandler();
                $handler->updateProgress($jobId, 50, '작업 진행 중...');

                sleep(2);

                // 작업 완료
                return [
                    'resourceUrl' => base_url("api/v1/examples/123"),
                    'data' => ['id' => 123, 'result' => 'success'],
                ];
            },
            [$data]
        );

        // 202 Accepted 응답 생성
        $response = $asyncHandler->createAcceptedResponse($jobId, $this->apiVersion);

        // Location 헤더 설정
        $statusUrl = base_url("api/{$this->apiVersion}/jobs/{$jobId}");
        $this->response->setHeader('Location', $statusUrl);

        return $this->respond($response, $response['status']);
    }

    /**
     * 대량 작업 예제 (비동기)
     * 
     * POST /api/v1/examples/bulk-import
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function bulkImport()
    {
        $data = $this->getRequestData();

        // 입력 데이터가 많은 경우 비동기 처리
        $items = $data['items'] ?? [];

        if (count($items) > 100) {
            // 대량 데이터는 비동기로 처리
            $asyncHandler = new AsyncJobHandler();

            $jobId = $asyncHandler->createJob(
                function ($jobId) use ($items) {
                    $handler = new AsyncJobHandler();
                    $total = count($items);

                    foreach ($items as $index => $item) {
                        // 각 항목 처리
                        // ... 처리 로직 ...

                        // 진행 상태 업데이트
                        $progress = (int)(($index + 1) / $total * 100);
                        $handler->updateProgress($jobId, $progress, "처리 중: {$index}/{$total}");
                    }

                    return ['imported' => $total];
                },
                [$items]
            );

            $response = $asyncHandler->createAcceptedResponse($jobId, $this->apiVersion);
            return $this->respond($response, $response['status']);
        }

        // 소량 데이터는 동기적으로 처리
        // ... 처리 로직 ...

        $response = RestApiResponse::success(['imported' => count($items)]);
        return $this->respond($response, $response['status']);
    }
}

