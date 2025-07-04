<?php

namespace App\Controllers\Api;

use App\Controllers\Base\BaseApiController;
use App\Models\BaseModel;
use App\Models\PermissionModel;
use App\Models\MemberModel;

class BoardApi extends BaseApiController
{
    protected $modelName = BaseModel::class;



    public function __invoke($method)
    {
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    public function index()
    {
        return $this->respond([
            'status' => '200',
            'message' => 'Menu API index method'
        ]);
    }
    /**
     * 게시판 그룹 검색 목록을 가져오는 메서드
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function getBoardGroupList()
    {
        $this->required_user_login_response();

        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
        $table_name1 = 'v_board_group';
        $postData->search->is_deleted = 0;
        $responseData = $this->getDataList($table_name1, $postData, 'bgr_order', 'asc');

        return $this->respond($responseData);
    }
    public function getBoardGroupDetails()
    {
        $no = $this->request->getPost('no');
        $model = new BaseModel();
        // 게시판 그룹 상세 조회 select * from v_board_group where no = $no , is_delete = 0
        $query = $model->db->table('v_board_group')->where('no', $no)->where('is_deleted', 0)->get();
        $detail = $query->getRowArray();

        if ($detail) {
            return $this->respond(['status' => '200', 'data' => $detail]);
        } else {
            return $this->failNotFound('Menu not found');
        }
    }
    public function updateBoardGroup()
    {
        $postData = $this->request->getPost(); // post 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'bgr_name' => [
                'label' => '그룹명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '그룹명은 필수입니다.',
                    'max_length' => '그룹명은 255자 이하로 입력해주세요.'
                ]
            ],
            'bgr_key' => [
                'label' => '그룹키',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '그룹키는 필수입니다.',
                    'max_length' => '그룹키는 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
            'bgr_order' => [
                'label' => '정렬순서',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => '정렬순서는 필수입니다.',
                    'integer' => '정렬순서는 정수여야 합니다.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Update top menu record
        $result = $this->updateRecord('board_group', $postData, $validationRules, 'array', 'bgr_id');
        $response = $this->handleResponse($result, '게시판 그룹이 성공적으로 수정되었습니다.');
        return $response;
    }
    public function deleteBoardGroup()
    {
        // Delete the top menu record
        $postData = $this->request->getPost(); // post 데이터 가져오기
        // public function softDeleteRecord($tableName, $id = null, $primaryKey = 'no', $returnType = 'response')
        $result = $this->softDeleteRecord('board_group', $postData['no'], 'bgr_id', 'array');

        $response = $this->handleResponse($result, '게시판 그룹이 성공적으로 삭제되었습니다.');
        return $response;
    }
    public function createBoardGroup()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'bgr_name' => [
                'label' => '그룹명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '그룹명은 필수입니다.',
                    'max_length' => '그룹명은 255자 이하로 입력해주세요.'
                ]
            ],
            'bgr_key' => [
                'label' => '그룹키',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '그룹키는 필수입니다.',
                    'max_length' => '그룹키는 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
            'bgr_order' => [
                'label' => '정렬순서',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => '정렬순서는 필수입니다.',
                    'integer' => '정렬순서는 정수여야 합니다.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Create menu record
        $result = $this->createRecord('board_group', $postData, $validationRules, 'array');
        $response = $this->handleResponse($result, '게시판 그룹이 성공적으로 생성되었습니다.');
        return $response;
    }


    public function getBoardList()
    {
        $this->required_user_login_response();

        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
        $table_name1 = 'v_board';
        $postData->search->is_deleted = 0;
        $responseData = $this->getDataList($table_name1, $postData, 'brd_order', 'asc');

        return $this->respond($responseData);
    }
    public function getBoardDetails()
    {
        $no = $this->request->getPost('no');
        $model = new BaseModel();
        // 게시판 상세 조회 select * from v_board where no = $no , is_delete = 0
        $query = $model->db->table('v_board')->where('no', $no)->where('is_deleted', 0)->get();
        $detail = $query->getRowArray();
        //board_meta 조회
        $query_meta = $model->db->table('board_meta')->where('brd_id', $no)->get();
        $board_meta_rows = $query_meta->getResultArray();
        $board_meta = [];
        if ($board_meta_rows && is_array($board_meta_rows)) {
            foreach ($board_meta_rows as $row) {
                $board_meta[$row['bmt_key']] = $row['bmt_value'];
            }
        }
        $detail['board_meta'] = $board_meta;

        //board_group 조회
        $query_group = $model->db->table('v_board_group')->where('useing', 1)->where('is_deleted', 0)->get();
        $board_group_list = $query_group->getResultArray();
        $detail['board_group_list'] = $board_group_list;

        if ($detail) {
            return $this->respond(['status' => '200', 'data' => $detail]);
        } else {
            return $this->failNotFound('Menu not found');
        }
    }
    public function updateBoard()
    {
        $postData = $this->request->getPost(); // post 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'brd_name' => [
                'label' => '게시판명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '게시판명은 필수입니다.',
                    'max_length' => '게시판명은 255자 이하로 입력해주세요.'
                ]
            ],
            'brd_key' => [
                'label' => '게시판키',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '게시판키는 필수입니다.',
                    'max_length' => '게시판키는 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Update top menu record
        $result = $this->updateRecord('board', $postData, $validationRules, 'array', 'brd_id');
        if ($result['status'] == 'success') {
            if (isset($postData['board_meta_array']) && is_array($postData['board_meta_array'])) {
                // board_meta 삭제
                $model = new BaseModel();
                $model->db->table('board_meta')->where('brd_id', $postData['no'])->delete();

                // board_meta 추가
                foreach ($postData['board_meta_array'] as $key => $value) {
                    $model->db->query("INSERT INTO board_meta (brd_id, bmt_key, bmt_value) VALUES (?, ?, ?)", [
                        $value['brd_id'], // 게시판 ID
                        $value['bmt_key'],            // 메타 키
                        $value['bmt_value']           // 메타 값
                    ]);
                }
            }
        }

        $response = $this->handleResponse($result, '게시판 그룹이 성공적으로 수정되었습니다.');
        return $response;
    }
    public function deleteBoard()
    {
        // Delete the top menu record
        $postData = $this->request->getPost(); // post 데이터 가져오기
        // public function softDeleteRecord($tableName, $id = null, $primaryKey = 'no', $returnType = 'response')
        $result = $this->softDeleteRecord('board', $postData['no'], 'brd_id', 'array');

        $response = $this->handleResponse($result, '게시판이 성공적으로 삭제되었습니다.');
        return $response;
    }
    public function createBoard()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'brd_name' => [
                'label' => '게시판명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '게시판명은 필수입니다.',
                    'max_length' => '게시판명은 255자 이하로 입력해주세요.'
                ]
            ],
            'brd_key' => [
                'label' => '게시판키',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '게시판키는 필수입니다.',
                    'max_length' => '그룹키는 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed

            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        //
        // Create board record
        $result = $this->createRecord('board', $postData, $validationRules, 'array');
        if ($result['status'] == 'success') {
            if (isset($postData['board_meta_array']) && is_array($postData['board_meta_array'])) {
                $model = new BaseModel();
                foreach ($postData['board_meta_array'] as $key => $value) {
                    $model->db->query("INSERT INTO board_meta (brd_id, bmt_key, bmt_value) VALUES (?, ?, ?)", [
                        $result['id'], // 새로 생성된 게시판 ID
                        $value['bmt_key'],    // 메타 키
                        $value['bmt_value']   // 메타 값
                    ]);
                }
            }
        }

        $response = $this->handleResponse($result, '게시판이 성공적으로 생성되었습니다.');
        return $response;
    }

    //get-post-admin-list
    public function getPostAdminList()
    {
        $this->required_user_login_response();

        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
        $table_name1 = 'v_post_short';

        // 기본 조건 
        $postData->search->post_del = 0;
        $postData->search->is_temp = 0;

        $responseData = $this->getDataList($table_name1, $postData, 'no', 'desc');

        return $this->respond($responseData);
    }
    public function updatePost()
    {
        $postData = $this->request->getPost(); // post 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'post_title' => [
                'label' => '게시글제목',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '게시글제목은 필수입니다.',
                    'max_length' => '게시글제목은 255자 이하로 입력해주세요.'
                ]
            ],
            'brd_id' => [
                'label' => '게시판',
                'rules' => 'required',
                'errors' => [
                    'required' => '게시판 선택은 필수입니다.',
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Update top menu record
        $result = $this->updateRecord('post', $postData, $validationRules, 'array', 'post_id');
        $response = $this->handleResponse($result, '게시글이 성공적으로 수정되었습니다.');
        return $response;
    }
    public function deletePost()
    {
        // Delete the top menu record
        $postData = $this->request->getPost(); // post 데이터 가져오기
        // public function softDeleteRecord($tableName, $id = null, $primaryKey = 'no', $returnType = 'response')
        $result = $this->softDeleteRecord('post', $postData['post_id'], 'post_id', 'array', 'post_del');
        $result['data'] = 'ss';

        $response = $this->handleResponse($result, '게시글이 성공적으로 삭제되었습니다.');
        return $response;
    }
    public function createPost()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'post_title' => [
                'label' => '게시글제목',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '게시글제목은 필수입니다.',
                    'max_length' => '게시글제목은 255자 이하로 입력해주세요.'
                ]
            ],
            'brd_id' => [
                'label' => '게시판',
                'rules' => 'required',
                'errors' => [
                    'required' => '게시판 선택은 필수입니다.',
                ]
            ],
            // Add more rules as needed
        ];
        $postData['post_num'] = $this->next_post_num();
        unset($postData['post_datetime']);
        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        //
        // Create board record
        $result = $this->createRecord('post', $postData, $validationRules, 'array');
        $response = $this->handleResponse($result, '게시글이 성공적으로 생성되었습니다.');
        return $response;
    }
    public function next_post_num()
    {
        $model = new BaseModel();
        $query = $model->db->table('post')->select('post_num')->get();
        $row = $query->getRowArray();
        $row['post_num'] = (isset($row['post_num'])) ? $row['post_num'] : 0;
        $post_num = $row['post_num'] - 1;
        return $post_num;
    }
}
