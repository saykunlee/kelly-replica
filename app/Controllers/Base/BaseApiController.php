<?php

namespace App\Controllers\Base;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Validation\Validation; // Import the Validation class
use App\Models\BaseModel;

class BaseApiController extends ResourceController
{
    protected $modelName;
    protected $validation;
    protected $session;
    public function __construct()
    {
        $this->validation = \Config\Services::validation(); // Load the validation service
        $this->session = \Config\Services::session();
    }
    public function __invoke($method)
    {
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        throw new \BadMethodCallException("Method {$method} does not exist.");
    }
    public function required_user_login()
    {

        // 세션에 로그인 여부 확인
        $session = \Config\Services::session();
        if (!$session->has('mem_id')) {
            return false;
        } else {
            return true;
        }
    }
    public function required_user_login_response()
    {

        // 세션에 로그인 여부 확인
        $session = \Config\Services::session();
        if (!$session->has('mem_id')) {
            return $this->returnResponse(['message' => '로그인이 필요합니다.'], 'error', 'response');
        } else {
            return true;
        }
    }

    public function generateValidationRules($postData, $customRules = [])
    {
        $defaultRules = [];

        // Set default rules to bypass validation for all fields
        foreach ($postData as $key => $value) {
            $defaultRules[$key] = 'permit_empty';
        }

        // Override default rules with custom rules
        foreach ($customRules as $key => $rule) {
            $defaultRules[$key] = $rule;
        }

        return $defaultRules;
    }
    private function handleValidation($data, $validationRules, $returnType)
    {
        if (!$this->validation->setRules($validationRules)->run($data)) {
            $errors = $this->formatValidationErrors($this->validation->getErrors());
            return $this->returnResponse($errors, 'error', $returnType, 200);
        }
        return null;
    }

    /**
     * 응답을 생성하여 반환하는 메서드
     *
     * @param array $data 응답에 포함할 데이터
     * @param string $status 응답의 상태 ('success' 또는 'error')
     * @param string $returnType 응답 형태 ('response' 또는 'array')
     * @param int $code HTTP 상태 코드 (기본값: 200)
     * @return array|\CodeIgniter\HTTP\Response 응답 객체 또는 배열
     */
    public function returnResponse($data, $status, $returnType, $code = 200)
    {
        // 응답 형태가 'response'인 경우
        if ($returnType === 'response') {
            return $this->respond($data, $code); // CodeIgniter의 응답 객체 반환
        } else {
            // 배열 형태로 응답 반환
            return array_merge(['status' => $status], $data);
        }
    }

    /**
     * 데이터베이스에 새로운 레코드를 생성하는 메서드
     *
     * @param string $tableName 데이터를 삽입할 테이블 이름
     * @param array|null $postData 삽입할 데이터 배열
     * @param array $validationRules 데이터 유효성 검사 규칙
     * @param string $returnType 응답 형태 ('response' 또는 'array')
     * @return array|\CodeIgniter\HTTP\Response 생성 결과에 대한 응답
     * 
     * 반환 형태:
     * - 성공 시:
     *   - `returnType`이 'response'인 경우:
     *     ```json
     *     {
     *         "status": "200",
     *         "message": "성공 메시지",
     *         "data": {
     *             "id": 123 // 생성된 레코드의 ID
     *         }
     *     }
     *     ```
     *   - `returnType`이 'array'인 경우:
     *     ```json
     *     {
     *         "status": "success",
     *         "data": {
     *             "id": 123 // 생성된 레코드의 ID
     *         }
     *     }
     *     ```
     * - 실패 시:
     *   - `returnType`이 'response'인 경우:
     *     ```json
     *     {
     *         "status": "error",
     *         "message": "Failed to create record"
     *     }
     *     ```
     *   - `returnType`이 'array'인 경우:
     *     ```json
     *     {
     *         "status": "error",
     *         "message": "Failed to create record"
     *     }
     *     ```
     */
    public function createRecord($tableName, $postData = null, $validationRules = [], $returnType = 'response')
    {
        // postData가 null이면 request에서 데이터를 가져옴
        $data = $postData ?? $this->request->getPost();

        // 데이터 유효성 검사 수행
        if ($validationResponse = $this->handleValidation($data, $validationRules, $returnType)) {
            return $validationResponse; // 유효성 검사 실패 시 응답 반환
        }

        // BaseModel 인스턴스 생성 및 테이블 설정
        $model = new \App\Models\BaseModel();
        $model->setTable($tableName);

        // 데이터 삽입 시도
        if ($model->insert($data)) {
            // 삽입 성공 시 성공 응답 반환
            return $this->returnResponse(['status' => 'success', 'data' => ['id' => $model->getInsertID()]], 'success', $returnType);
        } else {
            // 삽입 실패 시 오류 응답 반환
            return $this->returnResponse(['message' => 'Failed to create record'], 'error', $returnType);
        }
    }

    public function updateRecord($tableName, $postData = null, $validationRules = [], $returnType = 'response', $primaryKey = 'id')
    {
        $data = $postData ?? $this->request->getPost();

        // Validate the data
        if ($validationResponse = $this->handleValidation($data, $validationRules, $returnType)) {
            return $validationResponse;
        }

        $model = new \App\Models\BaseModel();
        $model->setTable($tableName)->setPrimaryKey($primaryKey);

        if ($model->update($data[$primaryKey], $data)) {
            return $this->returnResponse(['message' => 'Record updated successfully'], 'success', $returnType);
        } else {
            return $this->returnResponse(['message' => 'Failed to update record'], 'error', $returnType);
        }
    }

    public function deleteRecord($tableName, $id = null, $primaryKey = 'no', $returnType = 'response')
    {
        $id = $id ?? $this->request->getPost($primaryKey);

        if (!$id) {
            return $this->returnResponse(['message' => 'No ID provided for deletion'], 'error', $returnType);
        }

        $model = new \App\Models\BaseModel();
        $model->setTable($tableName)->setPrimaryKey($primaryKey);

        if ($model->where($primaryKey, $id)->delete()) {
            return $this->returnResponse(['message' => 'Record deleted successfully'], 'success', $returnType);
        } else {
            return $this->returnResponse(['message' => 'Failed to delete record'], 'error', $returnType);
        }
    }

    public function softDeleteRecord($tableName, $id = null, $primaryKey = 'no', $returnType = 'response', $deleteColumn = 'is_deleted')
    {
        $id = $id ?? $this->request->getPost($primaryKey);
        if (!$id) {
            return $this->returnResponse(['message' => 'No ID provided for deletion'], 'error', $returnType);
        }
        $model = new \App\Models\BaseModel();
        $model->setTable($tableName)->setPrimaryKey($primaryKey);

        if ($model->update($id, [$deleteColumn => 1])) {
            return $this->returnResponse(['message' => 'Record soft deleted successfully'], 'success', $returnType);
        } else {
            return $this->returnResponse(['message' => 'Failed to soft delete record'], 'error', $returnType);
        }
    }

    private function formatValidationErrors($errors)
    {
        $formattedErrors = [];
        foreach ($errors as $field => $error) {
            $formattedErrors[] = [
                'field' => $field,
                'message' => $error // Directly use the error message without translation
            ];
        }
        return [
            'status' => '422', // Custom status code for validation errors
            'error' => 'Validation Error',
            'messages' => $formattedErrors
        ];
    }

    /**
     * 응답을 처리하는 공용 메서드
     *
     * @param array $result 결과 배열, 'status' 키를 포함해야 함
     * @param string $successMessage 성공 시 반환할 메시지
     * @return \CodeIgniter\HTTP\Response 응답 객체
     * 
     * 반환 형태:
     * - 성공 응답:
     *   {
     *     "status": "200",
     *     "message": "성공 메시지",
     *     "data": { ... } // 추가 데이터
     *   }
     * 
     * - 유효성 검사 오류 응답:
     *   {
     *     "status": "422",
     *     "error": "유효성 검사 오류",
     *     "messages": [
     *       {
     *         "field": "필드 이름",
     *         "message": "에러 메시지"
     *       },
     *       ...
     *     ]
     *   }
     * 
     * - 기타 오류 응답:
     *   {
     *     "status": "400",
     *     "message": "오류 메시지"
     *   }
     */
    protected function handleResponse($result, $successMessage)
    {
        // 성공 시 응답
        if ($result['status'] === 'success') {
            $response = [
                'status' => '200',
                'message' => $successMessage
            ];

            // $result['data']가 존재하면 응답에 포함
            if (isset($result['data'])) {
                $response['data'] = $result['data'];
            }

            return $this->respond($response);
        } else {
            // 유효성 검사 오류 처리
            if ($result['status'] === '422') {
                return $this->respond([
                    'status' => '422',
                    'error' => '유효성 검사 오류',
                    'messages' => array_map(function ($msg) {
                        return [
                            'field' => $msg['field'], // 필드 이름
                            'message' => $msg['message'] // 에러 메시지
                        ];
                    }, $result['messages']) // 유효성 검사 메시지 배열
                ], 200); // HTTP 상태 코드 422
            }

            // 다른 오류의 경우
            return $this->respond($result['errors'] ?? ['message' => $result['messages']], 400); // 기본적으로 400 Bad Request
        }
    }

    /**
     * 정렬 및 페이징 정보를 처리하는 메서드
     *
     * @param object $postData 클라이언트로부터 전달된 데이터
     * @param string $defaultColumn 기본 정렬 컬럼명
     * @param string $defaultDirection 기본 정렬 방향
     * @return array 정렬 및 페이징 정보를 포함한 배열
     */
    protected function handleSortingAndPaging($postData, $defaultColumn = 'created_at', $defaultDirection = 'desc')
    {
        $draw = $postData->draw ?? 1;
        $start = $postData->start ?? 0;
        $length = $postData->length ?? 10;

        $orderColumnIndex = $postData->order[0]->column ?? 0;
        $orderDirection = $postData->order[0]->dir ?? $defaultDirection;
        $orderColumn = !empty($postData->columns[$orderColumnIndex]->data) ? $postData->columns[$orderColumnIndex]->data : $defaultColumn;

        return [$draw, $start, $length, $orderColumn, $orderDirection];
    }

    /**
     * 공용 데이터 목록을 가져오는 메서드
     *
     * 이 함수는 주어진 테이블 이름과 요청 데이터를 기반으로 데이터 목록을 가져옵니다.
     * 정렬, 페이징, 검색 조건을 처리하여 결과를 반환합니다.
     *
     * @param string $tableName 데이터베이스 테이블 이름
     * @param object $postData 요청 데이터 (JSON 형식)
     *   - $postData->search: 검색 조건을 포함하는 객체
     *     - 각 검색 필드는 검색어와 검색 유형을 포함합니다.
     *     - 예: $postData->search->title: 제목 검색어 (string)
     *     - 예: $postData->search->titleType: 제목 검색 유형 ('like' 또는 'equal')
     *   - $postData->order: 정렬 정보 (array)
     *   - $postData->start: 페이징 시작 위치 (int)
     *   - $postData->length: 페이징 길이 (int)
     * @return array 응답 데이터
     *   - 'draw': 요청의 draw 카운터 (int)
     *   - 'recordsTotal': 전체 레코드 수 (int)
     *   - 'recordsFiltered': 필터링된 레코드 수 (int)
     *   - 'data': 데이터 목록 (array)
     *   - 'columns': 컬럼 이름 목록 (array)
     */
    protected function getDataList($tableName, $postData, $orderColumn = 'insert_date', $orderDirection = 'desc')
    {
        $model = new BaseModel();
        $where = [];

        // 정렬 및 페이징 정보 처리
        list($draw, $start, $length, $orderColumn, $orderDirection) = $this->handleSortingAndPaging($postData, $orderColumn, $orderDirection);

        // 검색 조건 설정
        if (!empty($postData->search)) {
            foreach ($postData->search as $field => $value) {
                if (strpos($field, 'Type') !== false) {
                    continue; // 검색 유형 필드는 건너뜀
                }

                $searchTypeField = $field . 'Type';
                $searchType = $postData->search->$searchTypeField ?? 'equal'; // 기본값은 'equal'

                // 값이 비었거나 null이면 패스 (값이 0인 경우는 포함하지 않음)
                if ($value === null || $value === '') {
                    continue;
                }

                if ($searchType === 'like') {
                    $where["{$field} LIKE"] = '%' . $value . '%';
                } else {
                    $where[$field] = $value;
                }
            }
        }

        // 데이터베이스 쿼리 실행
        $totalRecords = $model->db->table($tableName)->countAllResults();
        $totalFiltered = $model->countFilteredResults($tableName, $where);
        $data = $model->getListWithDetails($tableName, $where, $orderColumn, $orderDirection, $start, $length);
        $columnNames = !empty($data['list']) ? array_keys($data['list'][0]) : [];

        // 결과 반환
        return [
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data['list'],
            'columns' => $columnNames
        ];
    }

    //사용자 member 테이블에서 mem_is_admin 컬럼 값 가져오기
    protected function getAdminStatus()
    {
        $this->required_user_login_response();

        $model = new BaseModel();
        $adminStatus = $model->db->table('member')->where('mem_id', $this->session->get('mem_id'))->get()->getRowArray()['mem_is_admin'];
        return $adminStatus;
    }
}
