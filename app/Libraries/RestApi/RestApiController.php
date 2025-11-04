<?php

namespace App\Libraries\RestApi;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RestApiController 기본 클래스
 * 
 * RESTful API 설계 원칙 기반 컨트롤러
 * Azure API Design Best Practices 준수
 */
class RestApiController extends ResourceController
{
    /**
     * @var string 기본 응답 형식
     */
    protected $format = 'json';

    /**
     * @var string API 버전
     */
    protected $apiVersion = 'v1';

    /**
     * @var string 리소스 이름 (복수형)
     */
    protected $resourceName = '';

    /**
     * @var string 모델 클래스명
     */
    protected $modelName = '';

    /**
     * @var array 허용된 HTTP 메서드
     */
    protected $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * @var bool ETag 캐싱 활성화 여부
     */
    protected $enableETag = true;

    /**
     * @var int 캐시 최대 시간 (초)
     */
    protected $cacheMaxAge = 0;

    /**
     * 생성자
     */
    public function __construct()
    {
        helper(['url']);
    }

    /**
     * 리소스 목록 조회 (GET /resources)
     * 
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        try {
            // 쿼리 파라미터 가져오기
            $page = (int) ($this->request->getGet('page') ?? 1);
            $limit = (int) ($this->request->getGet('limit') ?? 20);
            $limit = min($limit, 100); // 최대 100개로 제한
            $offset = ($page - 1) * $limit;

            // 정렬 파라미터
            $sort = $this->request->getGet('sort') ?? 'id';
            $order = strtoupper($this->request->getGet('order') ?? 'DESC');
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';

            // 필터링 파라미터
            $filters = $this->getFilters();

            // 모델 인스턴스 생성
            $model = model($this->modelName);

            // 필터 적용
            if (!empty($filters)) {
                foreach ($filters as $field => $value) {
                    if ($value !== null && $value !== '') {
                        $model->where($field, $value);
                    }
                }
            }

            // 전체 개수
            $total = $model->countAllResults(false);

            // 데이터 조회
            $items = $model
                ->orderBy($sort, $order)
                ->findAll($limit, $offset);

            // HATEOAS 링크 추가
            $items = array_map(function ($item) {
                return $this->addResourceLinks($item);
            }, $items);

            // 페이지네이션 응답
            $baseUrl = base_url("api/{$this->apiVersion}/{$this->resourceName}");
            $response = RestApiResponse::paginated($items, $total, $page, $limit, $baseUrl);

            return $this->respond($response, $response['status']);
        } catch (\Exception $e) {
            log_message('error', '[RestAPI] Index error: ' . $e->getMessage());
            return $this->respondWithError('리소스 조회에 실패했습니다', 500);
        }
    }

    /**
     * 단일 리소스 조회 (GET /resources/{id})
     * 
     * @param int|string $id 리소스 ID
     * @return ResponseInterface
     */
    public function show($id = null): ResponseInterface
    {
        try {
            if (!$id) {
                return $this->respondWithError('리소스 ID가 필요합니다', 400);
            }

            $model = model($this->modelName);
            $item = $model->find($id);

            if (!$item) {
                return $this->respondWithError('리소스를 찾을 수 없습니다', 404);
            }

            // HATEOAS 링크 추가
            $item = $this->addResourceLinks($item);

            // ETag 생성 및 검증
            if ($this->enableETag) {
                $etag = $this->generateETag($item);
                $this->response->setHeader('ETag', $etag);

                // If-None-Match 헤더 확인
                $ifNoneMatch = $this->request->getHeaderLine('If-None-Match');
                if ($ifNoneMatch === $etag) {
                    return $this->response->setStatusCode(304);
                }
            }

            // 캐시 헤더 설정
            if ($this->cacheMaxAge > 0) {
                $this->response->setHeader('Cache-Control', "max-age={$this->cacheMaxAge}, private");
            }

            $response = RestApiResponse::success($item);
            return $this->respond($response, $response['status']);
        } catch (\Exception $e) {
            log_message('error', "[RestAPI] Show error: " . $e->getMessage());
            return $this->respondWithError('리소스 조회에 실패했습니다', 500);
        }
    }

    /**
     * 새 리소스 생성 (POST /resources)
     * 
     * @return ResponseInterface
     */
    public function create(): ResponseInterface
    {
        try {
            $data = $this->getRequestData();

            // 유효성 검증
            $validationResult = $this->validateRequestData($data, $this->getValidationRules('create'));
            if ($validationResult !== true) {
                return $this->respondWithValidationError($validationResult);
            }

            $model = model($this->modelName);

            // 데이터 삽입
            $id = $model->insert($data);

            if (!$id) {
                return $this->respondWithError('리소스 생성에 실패했습니다', 500);
            }

            // 생성된 리소스 조회
            $item = $model->find($id);
            $item = $this->addResourceLinks($item);

            // 리소스 위치 URL
            $location = base_url("api/{$this->apiVersion}/{$this->resourceName}/{$id}");

            $response = RestApiResponse::created($item, $location);
            $this->response->setHeader('Location', $location);

            return $this->respond($response, $response['status']);
        } catch (\Exception $e) {
            log_message('error', "[RestAPI] Create error: " . $e->getMessage());
            return $this->respondWithError('리소스 생성에 실패했습니다', 500);
        }
    }

    /**
     * 리소스 전체 업데이트 (PUT /resources/{id})
     * 
     * @param int|string $id 리소스 ID
     * @return ResponseInterface
     */
    public function update($id = null): ResponseInterface
    {
        try {
            if (!$id) {
                return $this->respondWithError('리소스 ID가 필요합니다', 400);
            }

            $model = model($this->modelName);
            $existing = $model->find($id);

            if (!$existing) {
                return $this->respondWithError('리소스를 찾을 수 없습니다', 404);
            }

            // ETag 기반 낙관적 동시성 제어
            if ($this->enableETag) {
                $ifMatch = $this->request->getHeaderLine('If-Match');
                if ($ifMatch) {
                    $currentETag = $this->generateETag($existing);
                    if ($ifMatch !== $currentETag) {
                        return $this->respondWithError('리소스가 이미 수정되었습니다', 412);
                    }
                }
            }

            $data = $this->getRequestData();

            // 유효성 검증
            $validationResult = $this->validateRequestData($data, $this->getValidationRules('update'));
            if ($validationResult !== true) {
                return $this->respondWithValidationError($validationResult);
            }

            // 데이터 업데이트
            $success = $model->update($id, $data);

            if (!$success) {
                return $this->respondWithError('리소스 업데이트에 실패했습니다', 500);
            }

            // 업데이트된 리소스 조회
            $item = $model->find($id);
            $item = $this->addResourceLinks($item);

            $response = RestApiResponse::success($item);
            return $this->respond($response, $response['status']);
        } catch (\Exception $e) {
            log_message('error', "[RestAPI] Update error: " . $e->getMessage());
            return $this->respondWithError('리소스 업데이트에 실패했습니다', 500);
        }
    }

    /**
     * 리소스 부분 업데이트 (PATCH /resources/{id})
     * 
     * @param int|string $id 리소스 ID
     * @return ResponseInterface
     */
    public function patch($id = null): ResponseInterface
    {
        try {
            if (!$id) {
                return $this->respondWithError('리소스 ID가 필요합니다', 400);
            }

            $model = model($this->modelName);
            $existing = $model->find($id);

            if (!$existing) {
                return $this->respondWithError('리소스를 찾을 수 없습니다', 404);
            }

            // ETag 기반 낙관적 동시성 제어
            if ($this->enableETag) {
                $ifMatch = $this->request->getHeaderLine('If-Match');
                if ($ifMatch) {
                    $currentETag = $this->generateETag($existing);
                    if ($ifMatch !== $currentETag) {
                        return $this->respondWithError('리소스가 이미 수정되었습니다', 412);
                    }
                }
            }

            $data = $this->getRequestData();

            // 부분 업데이트는 모든 필드가 선택적
            $validationResult = $this->validateRequestData($data, $this->getValidationRules('patch'), true);
            if ($validationResult !== true) {
                return $this->respondWithValidationError($validationResult);
            }

            // 데이터 업데이트
            $success = $model->update($id, $data);

            if (!$success) {
                return $this->respondWithError('리소스 업데이트에 실패했습니다', 500);
            }

            // 업데이트된 리소스 조회
            $item = $model->find($id);
            $item = $this->addResourceLinks($item);

            $response = RestApiResponse::success($item);
            return $this->respond($response, $response['status']);
        } catch (\Exception $e) {
            log_message('error', "[RestAPI] Patch error: " . $e->getMessage());
            return $this->respondWithError('리소스 업데이트에 실패했습니다', 500);
        }
    }

    /**
     * 리소스 삭제 (DELETE /resources/{id})
     * 
     * @param int|string $id 리소스 ID
     * @return ResponseInterface
     */
    public function delete($id = null): ResponseInterface
    {
        try {
            if (!$id) {
                return $this->respondWithError('리소스 ID가 필요합니다', 400);
            }

            $model = model($this->modelName);
            $existing = $model->find($id);

            if (!$existing) {
                return $this->respondWithError('리소스를 찾을 수 없습니다', 404);
            }

            // 삭제 실행
            $success = $model->delete($id);

            if (!$success) {
                return $this->respondWithError('리소스 삭제에 실패했습니다', 500);
            }

            $response = RestApiResponse::noContent();
            return $this->respond($response, $response['status']);
        } catch (\Exception $e) {
            log_message('error', "[RestAPI] Delete error: " . $e->getMessage());
            return $this->respondWithError('리소스 삭제에 실패했습니다', 500);
        }
    }

    /**
     * 리소스에 HATEOAS 링크 추가
     * 
     * @param array $item 리소스 데이터
     * @return array
     */
    protected function addResourceLinks(array $item): array
    {
        $id = $item['id'] ?? $item[$this->model->primaryKey] ?? null;

        if (!$id) {
            return $item;
        }

        $baseUrl = base_url("api/{$this->apiVersion}/{$this->resourceName}");

        $links = [
            RestApiResponse::createLink('self', "{$baseUrl}/{$id}", 'GET'),
        ];

        // 업데이트 링크 (PUT)
        if (in_array('PUT', $this->allowedMethods)) {
            $links[] = RestApiResponse::createLink('update', "{$baseUrl}/{$id}", 'PUT');
        }

        // 부분 업데이트 링크 (PATCH)
        if (in_array('PATCH', $this->allowedMethods)) {
            $links[] = RestApiResponse::createLink('patch', "{$baseUrl}/{$id}", 'PATCH');
        }

        // 삭제 링크 (DELETE)
        if (in_array('DELETE', $this->allowedMethods)) {
            $links[] = RestApiResponse::createLink('delete', "{$baseUrl}/{$id}", 'DELETE');
        }

        // 컬렉션 링크
        $links[] = RestApiResponse::createLink('collection', $baseUrl, 'GET');

        return RestApiResponse::addLinks($item, $links);
    }

    /**
     * 요청 데이터 가져오기
     * 
     * @return array
     */
    protected function getRequestData(): array
    {
        $contentType = $this->request->getHeaderLine('Content-Type');

        if (strpos($contentType, 'application/json') !== false) {
            return $this->request->getJSON(true) ?? [];
        }

        return $this->request->getPost() ?? [];
    }

    /**
     * 필터 파라미터 가져오기
     * 
     * @return array
     */
    protected function getFilters(): array
    {
        $filters = [];
        $queryParams = $this->request->getGet();

        // 예약된 파라미터 제외
        $reserved = ['page', 'limit', 'sort', 'order', 'fields'];

        foreach ($queryParams as $key => $value) {
            if (!in_array($key, $reserved)) {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }

    /**
     * 유효성 검증 규칙 가져오기 (오버라이드 필요)
     * 
     * @param string $action 액션 타입 (create, update, patch)
     * @return array
     */
    protected function getValidationRules(string $action): array
    {
        return [];
    }

    /**
     * 데이터 유효성 검증
     * 
     * @param array $data 검증할 데이터
     * @param array $rules 검증 규칙
     * @param bool $isPartial 부분 검증 여부
     * @return bool|array true 또는 에러 배열
     */
    protected function validateRequestData(array $data, array $rules, bool $isPartial = false): bool|array
    {
        if (empty($rules)) {
            return true;
        }

        // 부분 검증인 경우 존재하는 필드만 검증
        if ($isPartial) {
            $rules = array_filter($rules, function ($key) use ($data) {
                return array_key_exists($key, $data);
            }, ARRAY_FILTER_USE_KEY);
        }

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            return $validation->getErrors();
        }

        return true;
    }

    /**
     * 유효성 검증 에러 응답
     * 
     * @param array $errors 에러 배열
     * @return ResponseInterface
     */
    protected function respondWithValidationError(array $errors): ResponseInterface
    {
        $formattedErrors = [];
        foreach ($errors as $field => $message) {
            $formattedErrors[] = [
                'field' => $field,
                'message' => $message,
            ];
        }

        $response = RestApiResponse::validationError($formattedErrors);
        return $this->respond($response, $response['status']);
    }

    /**
     * 에러 응답
     * 
     * @param string $message 에러 메시지
     * @param int $statusCode HTTP 상태 코드
     * @return ResponseInterface
     */
    protected function respondWithError(string $message, int $statusCode): ResponseInterface
    {
        $response = RestApiResponse::error($message, $statusCode);
        return $this->respond($response, $response['status']);
    }

    /**
     * ETag 생성
     * 
     * @param array $data 데이터
     * @return string
     */
    protected function generateETag(array $data): string
    {
        return '"' . md5(json_encode($data)) . '"';
    }
}

