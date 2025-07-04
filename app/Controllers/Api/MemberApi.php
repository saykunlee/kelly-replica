<?php

namespace App\Controllers\Api;

use App\Controllers\Base\BaseApiController;
use App\Models\MemberModel;
use App\Models\MemberGroupModel;

class MemberApi extends BaseApiController
{
    protected $modelName = MemberModel::class;

    

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
            'message' => 'Member API index method'
        ]);
    }

    /**
     * 회원 검색 목록을 가져오는 메서드
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function getMemberSearchList()
    {
        // 사용자가 로그인했는지 확인
        if (!$this->required_user_login()) {
            $result = ['errors' => ['message' => 'required_user_login']];
            return $this->respond($result['errors'] ?? ['message' => $result['messages']], 200);
        }

        // 세션 서비스 초기화
        $session = \Config\Services::session();
        $model = new MemberModel();

        // 클라이언트로부터 전달된 JSON 데이터 가져오기
        $postData = $this->request->getJSON();
        $draw = $postData->draw ?? 1; // DataTables에서 사용하는 draw 값
        $start = $postData->start ?? 0; // 데이터 시작 인덱스
        $length = $postData->length ?? 10; // 한 번에 가져올 데이터 수

        // 기본 검색 조건 설정
        $where = [
            //  'mem_member_no_confirm' => 1, // 회원 번호가 확인된 회원만 ->회원번호가 없어도 조회
            'mem_denied' => 0, // 접근이 거부되지 않은 회원만
           // 'mem_id >' => 2 // 특정 ID 이상의 회원만
        ];


        // 검색 필드와 데이터 매핑
        
        // group_id: [], // Initialize as an array
        
        $searchFields = [
            'mem_userid' => 'mem_userid',
            'mem_denied' => 'mem_denied',
            'mem_is_admin' => 'mem_is_admin',
           // 'mem_username' => 'mem_username',
           // 'mem_nickname' => 'mem_nickname',
            
        ];

        // 검색 조건에 따라 where 절 추가
        foreach ($searchFields as $key => $field) {
            if (!empty($postData->search->$key)) {
                $where[$field] = $postData->search->$key;
            } else if ($key === 'mem_password_changed' && $postData->search->$key === '0') {
                $where[$field] = 0;
            }
        }

        // 이름으로 검색 조건 추가
        if (!empty($postData->search->mem_username)) {
            $where['mem_username LIKE'] = '%' . $postData->search->name . '%';
        }
        // 별명으로 검색 조건 추가
        if (!empty($postData->search->mem_nickname)) {
            $where['mem_nickname LIKE'] = '%' . $postData->search->mem_nickname . '%';
        }

        // 정렬 조건 설정
        $orderColumnIndex = $postData->order[0]->column ?? 2; // 정렬할 컬럼 인덱스
        $orderDirection = $postData->order[0]->dir ?? 'asc'; // 정렬 방향
        $orderColumn = $postData->columns[$orderColumnIndex]->data ?? 'mem_id'; // 정렬할 컬럼 이름

        // 총 레코드 수를 가져오기 위한 조건 설정

        $total_where = [
            //'mem_member_no_confirm' => 1, // 회원 번호가 확인된 회원만 -> 회원번호가 없어도 조회
            'mem_denied' => 0, // 접근이 거부되지 않은 회원만
          //  'mem_id >' => 2 // 특정 ID 이상의 회원만
        ];
        
        // 총 레코드 수 가져오기
        $totalRecords = $model->db->table('member')
            ->where($total_where)
            ->countAllResults();

        // 필터링된 레코드 수 가져오기
        $totalFiltered = $model->countMemberFilteredResults('member', $where);

        // 데이터 가져오기
        $data = $model->getListMemberWithDetails('member', $where, $orderColumn, $orderDirection, $start, $length);

        // 컬럼 이름 가져오기
        $columnNames = !empty($data['list']) ? array_keys($data['list'][0]) : [];

        // 회원 그룹 정보 가져오기
        $memberGroupModel = new MemberGroupModel();
        $memberGroups = $memberGroupModel->getMemberGroups();

        // 각 회원에 대해 그룹 정보를 추가
        foreach ($data['list'] as &$member) {
            $memberId = $member['mem_id'];
            $groups = array_filter($memberGroups, fn($group) => $group['mem_id'] == $memberId);
            $groupTitles = array_column($groups, 'mgr_title');
            $member['attend_groups'] = implode(', ', $groupTitles);
            $member['group_ids'] = array_column($groups, 'mgr_id');
        }

        // 그룹 ID로 필터링
        // 검색 조건에 그룹 ID가 포함되어 있고, 그룹 ID가 배열인 경우
        if (!empty($postData->search->group_id) && is_array($postData->search->group_id)) {
            // 그룹 ID를 정수형(int)으로 변환하여 배열에 저장
            $groupIds = array_map('intval', $postData->search->group_id);
            // 필터링된 데이터를 저장할 배열 초기화
            $filteredData = [];
            // 데이터 리스트를 순회하며 각 회원의 정보를 확인
            foreach ($data['list'] as $member) {
                // 회원의 그룹 ID를 정수형(int)으로 변환하여 배열에 저장
                $memberGroupIds = array_map('intval', $member['group_ids']);
                // 회원의 그룹 ID와 검색 조건의 그룹 ID가 겹치는지 확인
                if (!empty(array_intersect($groupIds, $memberGroupIds))) {
                    // 겹치는 그룹 ID가 있는 회원을 필터링된 데이터 배열에 추가
                    $filteredData[] = $member;
                }
            }
            // 필터링된 데이터를 원본 데이터 리스트에 저장
            $data['list'] = $filteredData;
            // 필터링된 데이터의 총 개수를 계산하여 저장
            $totalFiltered = count($filteredData);
            // 필터링된 데이터의 총 개수를 데이터 배열에 저장
            $data['total'] = $totalFiltered;
        }

        // 응답 반환
        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data['list'],
            'columns' => $columnNames,
            'member_group' => $memberGroups
        ]);
    }
    public function getMemberList()
    {
        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
          $draw = $postData->draw ?? 1;
        $start = $postData->start ?? 0;
        $length = $postData->length ?? 10;


        $where = [];
        if (isset($postData->search->mem_is_admin) && ($postData->search->mem_is_admin === "0" || $postData->search->mem_is_admin === "1")) {
            $where['mem_is_admin'] = $postData->search->mem_is_admin;
        }
        if (!empty($postData->search->mem_userid)) $where['mem_userid LIKE'] = '%' . $postData->search->mem_userid . '%';
        if (!empty($postData->search->mem_email)) $where['mem_email LIKE'] = '%' . $postData->search->mem_email . '%';
        if (!empty($postData->search->mem_username)) $where['mem_username LIKE'] = '%' . $postData->search->mem_username . '%';
        if (!empty($postData->search->mem_denied)) $where['mem_denied'] = $postData->search->mem_denied;
        if (!empty($postData->search->mem_nickname)) $where['mem_nickname LIKE'] = '%' . $postData->search->mem_nickname . '%';
        if (!empty($postData->search->mem_level)) $where['mem_level'] = $postData->search->mem_level;

         // Handle sorting
         $orderColumnIndex = $postData->order[0]->column ?? 0; // Get the column index
         $orderDirection = $postData->order[0]->dir ?? 'asc'; // Get the sorting direction
         $orderColumn = $postData->columns[$orderColumnIndex]->data ?? ''; // Get the column name
 
 
 

        $model = new MemberModel();
        $totalRecords = $model->db->table('member')->countAllResults();
        $totalFiltered = $model->countFilteredResults('member', $where);
        $data = $model->getListWithDetails('member', $where,  $orderColumn, $orderDirection, $start, $length);

        // Get the column names from the first row of data
        $columnNames = !empty($data['list']) ? array_keys($data['list'][0]) : [];

        // Fetch member group information
        $memberGroupModel = new MemberGroupModel();
        $memberGroups = $memberGroupModel->getMemberGroups();

        // Add attend_groups to each member
        foreach ($data['list'] as &$member) {
            $memberId = $member['mem_id'];
            $groups = array_filter($memberGroups, function ($group) use ($memberId) {
                return $group['mem_id'] == $memberId;
            });
            $groupTitles = array_column($groups, 'mgr_title');
            $member['attend_groups'] = implode(', ', $groupTitles);
            $member['group_ids'] = array_column($groups, 'mgr_id'); // Add group IDs to each member
        }

        // Filter by group_id if provided

        if (!empty($postData->search->group_id) && is_array($postData->search->group_id)) {
            $data['list'] = array_filter($data['list'], function ($member) use ($postData) {
                return empty(array_diff($postData->search->group_id, $member['group_ids']));
            });
            $totalFiltered = count($data['list']);
        }


        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data['list'],
            'columns' => $columnNames, // Add the column names to the response
            'member_group' => $memberGroups // Add member group information to the response
        ]);
    }

    public function getMemberDetails()
    {
        $mem_id = $this->request->getPost('mem_id');
        $model = new MemberModel();
        $memberDetails = $model->getMemberDetails($mem_id);

        if ($memberDetails) {
            // add member groups which the member is attending
            //load member groups model
            $memberGroupsModel = new MemberGroupModel();
            $memberGroups = $memberGroupsModel->getMemberGroups($mem_id);
            //make it 0: mgr_id, 1: mgr_id 

            // Extract group IDs
            $groupIds = array_column($memberGroups, 'mgr_id');


            $memberDetails['group_id'] = $groupIds;

            $memberDetails['member_groups'] = $memberGroups;
            return $this->respond(['status' => '200', 'data' => $memberDetails]);
        } else {
            return $this->failNotFound('Member not found');
        }
    }
    public function createMember()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기
        if (!empty($postData['mem_password'])) {
            $postData['mem_password'] = password_hash($postData['mem_password'], PASSWORD_BCRYPT);
        }

        // 사용자 정의 검증 규칙 정의
        $customRules = [
            'mem_userid' => [
                'label' => '사용자 ID',
                'rules' => 'required|alpha_numeric|max_length[100]',
                'errors' => [
                    'required' => '사용자 ID 필드는 필수입니다.',
                    'alpha_numeric' => '사용자 ID 필드는 알파벳 문자와 숫자만 포함해야 합니다.',
                    'max_length' => '사용자 ID 필드는 100자 미만이어야 합니다.'
                ]
            ],
            'mem_email' => [
                'label' => '이메일',
                'rules' => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required' => '이메일 필드는 필수입니다.',
                    'valid_email' => '유효한 이메일 주소여야 합니다.',
                    'max_length' => '이메일 필드는 100자 미만이어야 합니다.',
                    'is_unique' => '이 이메일 주소는 이미 사용 중입니다.'
                ]
            ],
            'mem_password' => [
                'label' => '비밀번호',
                'rules' => 'permit_empty|string|max_length[255]',
                'errors' => [
                    'string' => '비밀번호는 문자열이어야 합니다.',
                    'max_length' => '비밀번호는 255자 미만이어야 합니다.'
                ]
            ],
            // 필요한 경우 더 많은 규칙 추가
        ];

        // 검증 규칙 생성
        $validationRules = $this->generateValidationRules($postData, $customRules);

        // 디버깅: 생성된 검증 규칙을 로그에 기록
        //log_message('debug', 'Validation Rules: ' . print_r($validationRules, true));

        // 데이터베이스 트랜잭션 시작
        $db = \Config\Database::connect();
        $db->transStart();


        //mem_register_datetime 현재 시간으로 설정
        $postData['mem_register_datetime'] = date('Y-m-d H:i:s');
        //mem_register_ip 클라이언트 ip 주소로 설정
        $postData['mem_register_ip'] = $this->request->getIPAddress();


        // 회원 레코드 생성
        $result = $this->createRecord('member', $postData, $validationRules, 'array');

        // 결과를 확인하고 응답
        if ($result['status'] === 'success') {
            $memberId = $result['id'];

            // 회원 그룹 추가
            if (isset($postData['group_id'])) {
                $memberGroupModel = new MemberGroupModel();
                $memberGroupModel->replaceMemberGroups($memberId, $postData['group_id']);
            }

            // MemberModel을 사용하여 member_userid 테이블에 mem_userid 추가
            $memberModel = new MemberModel();
            $memberModel->addMemberUserId($memberId, $postData['mem_userid']);

            //mem_nickname member_nickname 저장

            $model = new \App\Models\BaseModel();
            $model->setTable('member_nickname');
            $model->insert(['mem_id' => $memberId, 'mni_nickname' => $postData['mem_nickname']]);


            // 트랜잭션 커밋
            $db->transComplete();

            if ($db->transStatus() === false) {
                // 트랜잭션 실패 시 롤백
                return $this->respond(['status' => '500', 'message' => '회원 생성 중 오류가 발생했습니다.'], 500);
            }

            return $this->respond(['status' => '200', 'message' => 'Member created successfully']);
        } else {
            // 트랜잭션 실패 시 롤백
            $db->transRollback();
            return $this->respond($result['errors'] ?? ['status' => $result['status'], 'message' => $result['messages']], 200);
        }
    }
    public function updateMember()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기
        if (!empty($postData['mem_password'])) {
            $postData['mem_password'] = password_hash($postData['mem_password'], PASSWORD_BCRYPT);
        }
        //패스워드가 빈값이면, 패스워드를 변경하지 않는다.
        if (empty($postData['mem_password'])) {
            unset($postData['mem_password']);
        }

        // Define custom validation rules
        $customRules = [
            'mem_userid' => [
                'label' => '사용자 ID',
                'rules' => 'required|alpha|max_length[100]',
                'errors' => [
                    'required' => '사용자 ID 필드는 필수입니다.',
                    'alpha' => '사용자 ID 필드는 알파벳 문자만 포함해야 합니다.',
                    'max_length' => '사용자 ID 필드는 100자 미만이어야 합니다.'
                ]
            ],
            'mem_email' => [
                'label' => '이메일',
                'rules' => 'required|valid_email|max_length[100]|is_unique[member.mem_email,mem_id,' . $postData['mem_id'] . ']',
                'errors' => [
                    'required' => '이메일 필드는 필수입니다.',
                    'valid_email' => '유효한 이메일 주소여야 합니다.',
                    'max_length' => '이메일 필드는 100자 미만이어야 합니다.',
                    'is_unique' => '이 이메일 주소는 이미 사용 중입니다.'
                ]
            ],
            //우편번호 5자리 숫자만 허용
            'mem_zipcode' => [
                'label' => '우편번호',
                'rules' => 'permit_empty|numeric|max_length[5]',
                'errors' => [
                    'numeric' => '우편번호는 숫자만 허용됩니다.',
                    'max_length' => '우편번호는 5자리 숫자만 허용됩니다.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);

        // Update member record
        $result = $this->updateRecord('member', $postData, $validationRules, 'array', 'mem_id');
        
        if ($result['status'] === 'success') {
            // Replace member groups
            if (isset($postData['group_id'])) {
                $memberGroupModel = new MemberGroupModel();
                $memberGroupModel->replaceMemberGroups($postData['mem_id'], $postData['group_id']);
            }
        }
        $response = $this->handleResponse($result, '회원 정보가 성공적으로 업데이트되었습니다.');
        return $response;   

    }
    public function deleteMember()
    {
        // 회원 레코드 삭제
        $result = $this->deleteRecord('member', null, 'mem_id', 'array');
        $memberId = $this->request->getPost('mem_id');
    
        // 결과를 확인하고 추가 삭제를 수행
        if ($result['status'] === 'success') {
            
            // 회원과 관련된 추가 삭제 수행
            $result_member_group_member = $this->deleteRecord('member_group_member', $memberId, 'mem_id', 'array');
            if ($result_member_group_member['status'] === 'success') {
                return $this->respond(['status' => '200', 'message' => '회원과 관련된 기록이 성공적으로 삭제되었습니다.']);
            } else {
                return $this->respond($result_member_group_member['errors'] ?? ['status' => $result_member_group_member['status'], 'message' => $result_member_group_member['messages']], 200);
            }
        } else {
            return $this->respond($result['errors'] ?? ['message' => $result['messages']], 200);
        }
    }

    public function checkUserId()
    {
        $userId = $this->request->getPost('mem_userid');
        $memberModel = new MemberModel();
        $isAvailable = $memberModel->isUserIdAvailable($userId);
        return $this->response->setJSON(['isAvailable' => $isAvailable]);
    }
    public function checkEmail()
    {
        $email = $this->request->getPost('mem_email');
        $memberModel = new MemberModel();
        $isAvailable = $memberModel->isEmailAvailable($email);
        return $this->response->setJSON(['isAvailable' => $isAvailable]);
    }
    public function checkNickname()
    {
        $nickname = $this->request->getPost('mem_nickname');
        $memberModel = new MemberModel();
        $isAvailable = $memberModel->isNicknameAvailable($nickname);
        return $this->response->setJSON(['isAvailable' => $isAvailable]);
    }

    public function getLoginLogs()
    {
        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
          $draw = $postData->draw ?? 1;
        $start = $postData->start ?? 0;
        $length = $postData->length ?? 10;


        $where = [];
        if (!empty($postData->search->mll_userid)) {
            $where['mll_userid'] = $postData->search->mll_userid;
        }
        if (!empty($postData->search->mll_userid)) {
            $where['mll_userid LIKE'] = '%' . $postData->search->mll_userid . '%';
        }
        if (!empty($postData->search->mll_ip)) {
            $where['mll_ip LIKE'] = '%' . $postData->search->mll_ip . '%';
        }
        if (isset($postData->search->mll_success) && $postData->search->mll_success !== '') {
            $where['mll_success'] = $postData->search->mll_success;
        }

        $model = new MemberModel();
        $totalRecords = $model->db->table('member_login_log')->countAllResults();
        $totalFiltered = $model->countFilteredResults('member_login_log', $where);
        $data = $model->getLoginLoglist($where, 'mll_datetime', 'DESC', $start, $length);

        // Get the column names from the first row of data
        $columnNames = !empty($data['list']) ? array_keys($data['list'][0]) : [];

        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data['list'],
            'columns' => $columnNames // Add the column names to the response
        ]);
    }
    // Add this method to get login log details
    public function getLogDetail()
    {
        
        $mll_id = $this->request->getPost('mll_id');
        if (!$mll_id) {
            return $this->respond(['status' => 'error', 'message' => 'mll_id is required'], 400);
        }

        $model = new MemberModel();
        $logDetails = $model->getLoginLogDetails($mll_id);
      

        if ($logDetails) {
            return $this->respond(['status' => '200', 'data' => $logDetails]);
        } else {
            return $this->failNotFound('Log not found');
        }
    }
}
