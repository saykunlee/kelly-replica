<?php

namespace App\Controllers\Api\V1;

use App\Libraries\RestApi\RestApiController;
use App\Libraries\RestApi\RestApiResponse;

/**
 * 회원 리소스 API 컨트롤러
 * 
 * RESTful API 엔드포인트:
 * - GET    /api/v1/members          - 회원 목록 조회
 * - GET    /api/v1/members/{id}     - 회원 상세 조회
 * - POST   /api/v1/members          - 회원 생성
 * - PUT    /api/v1/members/{id}     - 회원 전체 업데이트
 * - PATCH  /api/v1/members/{id}     - 회원 부분 업데이트
 * - DELETE /api/v1/members/{id}     - 회원 삭제
 */
class MembersController extends RestApiController
{
    protected $modelName = 'App\Models\MemberModel';
    protected $resourceName = 'members';
    protected $apiVersion = 'v1';
    protected $enableETag = true;
    protected $cacheMaxAge = 300; // 5분

    /**
     * 유효성 검증 규칙 정의
     * 
     * @param string $action 액션 타입
     * @return array
     */
    protected function getValidationRules(string $action): array
    {
        $rules = [];

        switch ($action) {
            case 'create':
                $rules = [
                    'mem_userid' => [
                        'label' => '사용자 ID',
                        'rules' => 'required|min_length[4]|max_length[50]|is_unique[member.mem_userid]',
                        'errors' => [
                            'required' => '사용자 ID는 필수입니다',
                            'min_length' => '사용자 ID는 최소 4자 이상이어야 합니다',
                            'max_length' => '사용자 ID는 최대 50자까지 가능합니다',
                            'is_unique' => '이미 사용 중인 사용자 ID입니다',
                        ],
                    ],
                    'mem_password' => [
                        'label' => '비밀번호',
                        'rules' => 'required|min_length[8]',
                        'errors' => [
                            'required' => '비밀번호는 필수입니다',
                            'min_length' => '비밀번호는 최소 8자 이상이어야 합니다',
                        ],
                    ],
                    'mem_email' => [
                        'label' => '이메일',
                        'rules' => 'required|valid_email|is_unique[member.mem_email]',
                        'errors' => [
                            'required' => '이메일은 필수입니다',
                            'valid_email' => '올바른 이메일 형식이 아닙니다',
                            'is_unique' => '이미 사용 중인 이메일입니다',
                        ],
                    ],
                    'mem_username' => [
                        'label' => '이름',
                        'rules' => 'required|max_length[100]',
                        'errors' => [
                            'required' => '이름은 필수입니다',
                            'max_length' => '이름은 최대 100자까지 가능합니다',
                        ],
                    ],
                ];
                break;

            case 'update':
                $rules = [
                    'mem_email' => [
                        'label' => '이메일',
                        'rules' => 'required|valid_email',
                        'errors' => [
                            'required' => '이메일은 필수입니다',
                            'valid_email' => '올바른 이메일 형식이 아닙니다',
                        ],
                    ],
                    'mem_username' => [
                        'label' => '이름',
                        'rules' => 'required|max_length[100]',
                        'errors' => [
                            'required' => '이름은 필수입니다',
                            'max_length' => '이름은 최대 100자까지 가능합니다',
                        ],
                    ],
                ];
                break;

            case 'patch':
                $rules = [
                    'mem_email' => [
                        'label' => '이메일',
                        'rules' => 'permit_empty|valid_email',
                        'errors' => [
                            'valid_email' => '올바른 이메일 형식이 아닙니다',
                        ],
                    ],
                    'mem_username' => [
                        'label' => '이름',
                        'rules' => 'permit_empty|max_length[100]',
                        'errors' => [
                            'max_length' => '이름은 최대 100자까지 가능합니다',
                        ],
                    ],
                    'mem_phone' => [
                        'label' => '전화번호',
                        'rules' => 'permit_empty|max_length[20]',
                    ],
                ];
                break;
        }

        return $rules;
    }

    /**
     * 회원 생성 전 비밀번호 해싱
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function create(): \CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->getRequestData();

        // 비밀번호 해싱
        if (isset($data['mem_password'])) {
            $data['mem_password'] = password_hash($data['mem_password'], PASSWORD_DEFAULT);
        }

        // 임시로 데이터를 요청에 다시 설정 (실제로는 모델에서 처리하는 것이 좋음)
        $_POST = $data;
        $this->request = \Config\Services::request();

        return parent::create();
    }

    /**
     * HATEOAS 링크에 추가 관계 링크 포함
     * 
     * @param array $item 리소스 데이터
     * @return array
     */
    protected function addResourceLinks(array $item): array
    {
        $item = parent::addResourceLinks($item);

        $id = $item['mem_id'] ?? null;

        if ($id) {
            $baseUrl = base_url("api/{$this->apiVersion}");
            
            // 회원의 주문 목록 링크 (예시)
            // $item['links'][] = RestApiResponse::createLink(
            //     'orders',
            //     "{$baseUrl}/members/{$id}/orders",
            //     'GET'
            // );
        }

        return $item;
    }

    /**
     * 테스트용 API 엔드포인트
     * GET /api/v1/members/test
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function test(): \CodeIgniter\HTTP\ResponseInterface
    {
        // 기본 요청 정보 수집
        $testData = [
            'message' => 'Members API 테스트 엔드포인트',
            'timestamp' => date('Y-m-d H:i:s'),
            'request_info' => [
                'method' => $this->request->getMethod(),
                'uri' => (string) $this->request->getUri(),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
            ],
            'headers' => [
                'content_type' => $this->request->getHeaderLine('Content-Type'),
                'accept' => $this->request->getHeaderLine('Accept'),
                'api_version' => $this->request->getHeaderLine('X-API-Version'),
            ],
            'query_params' => $this->request->getGet(),
            'controller_info' => [
                'resource_name' => $this->resourceName,
                'api_version' => $this->apiVersion,
                'model_name' => $this->modelName,
                'etag_enabled' => $this->enableETag,
                'cache_max_age' => $this->cacheMaxAge,
            ],
            'environment' => [
                'ci_environment' => ENVIRONMENT,
                'php_version' => PHP_VERSION,
            ],
        ];

        $links = [
            RestApiResponse::createLink('self', base_url("api/{$this->apiVersion}/members/test"), 'GET'),
            RestApiResponse::createLink('collection', base_url("api/{$this->apiVersion}/members"), 'GET'),
        ];

        $response = RestApiResponse::success($testData, 200, $links);
        return $this->respond($response, $response['status']);
    }
}

