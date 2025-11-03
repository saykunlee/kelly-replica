<?php

namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\RestApi\AsyncJobHandler;
use App\Libraries\RestApi\RestApiResponse;

/**
 * 비동기 작업 상태 조회 컨트롤러
 * 
 * GET /api/v1/jobs/{jobId} - 작업 상태 조회
 */
class JobsController extends ResourceController
{
    protected $format = 'json';
    protected $asyncJobHandler;

    public function __construct()
    {
        $this->asyncJobHandler = new AsyncJobHandler();
    }

    /**
     * 작업 상태 조회
     * 
     * @param string $jobId 작업 ID
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function show($jobId = null)
    {
        if (!$jobId) {
            $response = RestApiResponse::error('작업 ID가 필요합니다', 400);
            return $this->respond($response, $response['status']);
        }

        $response = $this->asyncJobHandler->createStatusResponse($jobId);

        // 303 See Other인 경우 Location 헤더 추가
        if ($response['status'] === 303 && isset($response['location'])) {
            $this->response->setHeader('Location', $response['location']);
        }

        return $this->respond($response, $response['status']);
    }

    /**
     * 작업 취소
     * 
     * @param string $jobId 작업 ID
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function delete($jobId = null)
    {
        if (!$jobId) {
            $response = RestApiResponse::error('작업 ID가 필요합니다', 400);
            return $this->respond($response, $response['status']);
        }

        $success = $this->asyncJobHandler->cancelJob($jobId);

        if ($success) {
            $response = RestApiResponse::success(['message' => '작업이 취소되었습니다']);
        } else {
            $response = RestApiResponse::error('작업 취소에 실패했습니다', 500);
        }

        return $this->respond($response, $response['status']);
    }
}

