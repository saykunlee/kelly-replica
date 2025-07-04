<?php

namespace App\Controllers\Api;

use App\Controllers\Base\BaseApiController;
use App\Models\MenuModel;
use App\Models\TopMenuModel;
use App\Models\TopMenuCategoryModel;

class MenuApi extends BaseApiController
{
    protected $modelName = MenuModel::class;

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

    public function getMenuList()
    {
        $postData = $this->request->getJSON(); // JSON 데이터 가져오기

        $where = ['menus.is_deleted !=' => 1];
        if (!empty($postData->search->category_id)) $where['menus.category_id'] = $postData->search->category_id;
        if (!empty($postData->search->parent_id)) $where['menus.parent_id'] = $postData->search->parent_id;
        if (isset($postData->search->is_active) && $postData->search->is_active !== '') {
            $where['menus.is_active'] = $postData->search->is_active;
        }
        if (isset($postData->search->is_show) && $postData->search->is_show !== '') {
            $where['menus.is_show'] = $postData->search->is_show;
        }
        if (!empty($postData->search->type)) $where['menu_categories.type'] = $postData->search->type;
        if (!empty($postData->search->title)) $where['menus.title LIKE'] = '%' . $postData->search->title . '%';

        if (!empty($postData->search->category_id) && is_array($postData->search->category_id)) {
            // code_cate_no 배열에서 각 요소의 'key'를 추출하여 새로운 배열 생성
            $categoryIdArray = array_map(function ($item) {
                // 각 요소가 배열인지 확인
                return is_array($item) && isset($item['key']) ? $item['key'] : null; // 'key'가 존재하는 경우에만 반���
            }, $postData->search->category_id);

            // null 값을 제거하고 변환된 배열을 where 조건에 추가
            $categoryIdArray = array_filter($categoryIdArray); // null 값을 제거
            if (!empty($categoryIdArray)) {
                $where['menus.category_id IN'] = $categoryIdArray; // 'IN' 조건 사용
            }
        }

        $draw = $postData->draw ?? 1;
        $start = $postData->start ?? 0;
        $length = $postData->length ?? 10;

        // Handle sorting
        $orderColumnIndex = $postData->order[0]->column ?? 0; // Get the column index
        $orderDirection = $postData->order[0]->dir ?? 'asc'; // Get the sorting direction
        $orderColumn = $postData->columns[$orderColumnIndex]->data ?? ''; // Get the column name

        $model = new MenuModel();
        $totalRecords = $model->db->table('menus')->countAllResults();
        $totalFiltered = $model->countFilteredResults('menus', $where);
        $data = $model->getMenuListWithDetails($where, $orderColumn, $orderDirection, $start, $length);

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

    public function getMenuDetails()
    {
        $no = $this->request->getPost('no');
        $model = new MenuModel();
        $menuDetails = $model->getMenuDetailsWithCategory($no);

        if ($menuDetails) {
            return $this->respond(['status' => '200', 'data' => $menuDetails]);
        } else {
            return $this->failNotFound('Menu not found');
        }
    }



    public function createMenu()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'title' => [
                'label' => '제목',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '제목은 필수입니다.',
                    'max_length' => '제목은 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
            'category_id' => [
                'label' => '카테고리',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => '카테고리 ID는 필수입니다.',
                    'integer' => '카테고리 ID는 정수여야 합니다.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Create menu record
        $result = $this->createRecord('menus', $postData, $validationRules, 'array');
        $response = $this->handleResponse($result, '메뉴가 성공적으로 생성되었습니다.');
        return $response;
    }

    public function updateMenu()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'title' => [
                'label' => 'Title',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Title is required.',
                    'max_length' => 'Title must be less than 255 characters.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Update menu record
        $result = $this->updateRecord('menus', $postData, $validationRules, 'array', 'no');
        $response = $this->handleResponse($result, '메뉴 정보가 성공적으로 업데이트되었습니다.');
        return $response;
    }

    public function deleteMenu()
    {
        // Delete the menu record
        $result = $this->deleteRecord('menus', null, 'no', 'array');
        $response = $this->handleResponse($result, '메뉴가 성공적으로 삭제되었습니다.');
        return $response;
    }


    public function getTopMenuList()
    {
        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
        $draw = $postData->draw ?? 1;
        $start = $postData->start ?? 0;
        $length = $postData->length ?? 10;


        $where = ['top_menus.is_deleted !=' => 1];
        if (!empty($postData->search->category_id)) $where['top_menus.category_id'] = $postData->search->category_id;
        if (!empty($postData->search->parent_id)) $where['top_menus.parent_id'] = $postData->search->parent_id;
        if (isset($postData->search->is_active) && $postData->search->is_active !== '') {
            $where['top_menus.is_active'] = $postData->search->is_active;
        }
        if (isset($postData->search->is_show) && $postData->search->is_show !== '') {
            $where['top_menus.is_show'] = $postData->search->is_show;
        }
        if (!empty($postData->search->type)) $where['top_menu_categories.type'] = $postData->search->type;
        if (!empty($postData->search->title)) $where['top_menus.title LIKE'] = '%' . $postData->search->title . '%';

        if (!empty($postData->search->category_id) && is_array($postData->search->category_id)) {
            // code_cate_no 배열에서 각 요소의 'key'를 추출하여 새로운 배열 생성
            $categoryIdArray = array_map(function ($item) {
                // 각 요소가 배열인지 확인
                return is_array($item) && isset($item['key']) ? $item['key'] : null; // 'key'가 존재하는 경우에만 반환
            }, $postData->search->category_id);

            // null 값을 제거하고 변환된 배열을 where 조건에 추가
            $categoryIdArray = array_filter($categoryIdArray); // null 값을 제거
            if (!empty($categoryIdArray)) {
                $where['top_menus.category_id IN'] = $categoryIdArray; // 'IN' 조건 사용
            }
        }

        // Handle sorting
        $orderColumnIndex = $postData->order[0]->column ?? 0; // Get the column index
        $orderDirection = $postData->order[0]->dir ?? 'asc'; // Get the sorting direction
        $orderColumn = $postData->columns[$orderColumnIndex]->data ?? ''; // Get the column name


        $model = new TopMenuModel();
        $totalRecords = $model->db->table('top_menus')->where('is_deleted !=', 1)->countAllResults(); // Count non-deleted categories
        $totalFiltered = $model->countFilteredResults('top_menus', $where); // Count non-deleted categories
        $data = $model->getTopMenuListWithDetails($where, $orderColumn, $orderDirection, $start, $length);



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

    public function getTopMenuDetails()
    {
        $no = $this->request->getPost('no');
        $model = new TopMenuModel();
        $menuDetails = $model->getTopMenuDetailsWithCategory($no);

        if ($menuDetails) {
            return $this->respond(['status' => '200', 'data' => $menuDetails]);
        } else {
            return $this->failNotFound('Menu not found');
        }
    }

    public function createTopMenu()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'title' => [
                'label' => 'Title',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '제목은 필수입니다.',
                    'max_length' => '제목은 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
            'category_id' => [
                'label' => 'Category ID',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => '카테고리 ID는 필수입니다.',
                    'integer' => '카테고리 ID는 정수여야 합니다.'
                ]
            ],
        ];
        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Create top menu record
        $result = $this->createRecord('top_menus', $postData, $validationRules, 'array');
        // Check the result and respond accordingly
        $response = $this->handleResponse($result, '상단 메뉴가 성공적으로 생성되었습니다.');
        return $response;
    }

    public function updateTopMenu()
    {
        $postData = $this->request->getPost(); // post 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'title' => [
                'label' => '제목',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '제목은 필수입니다.',
                    'max_length' => '제목은 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
            'category_id' => [
                'label' => '카테고리',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => '카테고리 ID는 필수입니다.',
                    'integer' => '카테고리 ID는 정수여야 합니다.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);
        // Update top menu record
        $result = $this->updateRecord('top_menus', $postData, $validationRules, 'array', 'no');
        $response = $this->handleResponse($result, '상단 메뉴가 성공적으로 수정되었습니다.');
        return $response;
    }

    public function deleteTopMenu()
    {
        // Delete the top menu record
        $result = $this->deleteRecord('top_menus', null, 'no', 'array');

        $response = $this->handleResponse($result, '상단 메뉴가 성공적으로 삭제되었습니다.');
        return $response;
    }

    public function getSidecateList()
    {
        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
        $draw = $postData->draw ?? 1;
        $start = $postData->start ?? 0;
        $length = $postData->length ?? 10;


        $where = ['menu_categories.is_deleted' => 0]; // Only fetch non-deleted categories
        if (isset($postData->search->is_active) && $postData->search->is_active !== '') {
            $where['menu_categories.is_active'] = $postData->search->is_active; // Filter by active status if provided
        }
        if (!empty($postData->search->type)) {
            $where['menu_categories.type'] = $postData->search->type; // Filter by type if provided
        }
        if (!empty($postData->search->title)) {
            $where['menu_categories.name LIKE'] = '%' . $postData->search->title . '%'; // Filter by name if provided
        }
        // Handle sorting
        $orderColumnIndex = $postData->order[0]->column ?? 0; // Get the column index
        $orderDirection = $postData->order[0]->dir ?? 'asc'; // Get the sorting direction
        $orderColumn = $postData->columns[$orderColumnIndex]->data ?? 'order'; // Get the column name

        $model = new MenuModel();
        $totalRecords = $model->db->table('menu_categories')->where('is_deleted', 0)->countAllResults(); // Count non-deleted categories
        $totalFiltered = $model->db->table('menu_categories')->where($where)->countAllResults(); // Count non-deleted categories
        $data = $model->db->table('menu_categories')
            ->select('*')
            ->where($where)
            ->orderBy('`order`', 'ASC') // Order by the `order` column
            ->limit($length, $start) // Apply pagination
            ->get()
            ->getResultArray();



        // Get the column names from the first row of data
        $columnNames = !empty($data) ? array_keys($data[0]) : [];

        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
            'columns' => $columnNames // Add the column names to the response
        ]);
    }

    public function getSidecateDetails()
    {
        $no = $this->request->getPost('no');
        $model = new MenuModel();
        $categoryDetails = $model->getCategoryDetails($no); // Updated to use getCategoryDetails

        if ($categoryDetails) {
            return $this->respond(['status' => '200', 'data' => $categoryDetails]);
        } else {
            return $this->failNotFound('Category not found'); // Updated message
        }
    }
    //create sidecate
    public function createSidecate()
    {
        $postData = $this->request->getPost();

        // Define custom validation rules
        $customRules = [
            'name' => [
                'label' => '카테고리명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '카테고리명은 필수입니다.',
                    'max_length' => '카테고리명은 255자 이하로 입력해주세요.'
                ]
            ],

        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);

        // Create top menu record
        $result = $this->createRecord('menu_categories', $postData, $validationRules, 'array');
        // Check the result and respond accordingly
        $response = $this->handleResponse($result, '카테고리가 성공적으로 생성되었습니다.');
        return $response;
    }
    //update sidecate
    public function updateSidecate()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'name' => [
                'label' => '카테고리명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '카테고리명은 필수입니다.',
                    'max_length' => '카테고리명은 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);

        // Update top menu record
        $result = $this->updateRecord('menu_categories', $postData, $validationRules, 'array', 'no');

        $response = $this->handleResponse($result, '카테고리가 성공적으로 수정되었습니다.');
        return $response;
    }
    //delete sidecate

    public function deleteSidecate()
    {
        // Delete the top menu record
        $result = $this->deleteRecord('menu_categories', null, 'no', 'array');

        // Check the result and respond accordingly
        $response = $this->handleResponse($result, '카테고리가 성공적으로 삭제되었습니다.');
        return $response;
    }


    //topmenucate
    public function getTopcateList()
    {
        $postData = $this->request->getJSON(); // JSON 데이터 가져오기
        $draw = $postData->draw ?? 1;
        $start = $postData->start ?? 0;
        $length = $postData->length ?? 10;


        $where = ['top_menu_categories.is_deleted' => 0]; // Only fetch non-deleted categories
        if (isset($postData->search->is_active) && $postData->search->is_active !== '') {
            $where['top_menu_categories.is_active'] = $postData->search->is_active; // Filter by active status if provided
        }
        if (!empty($postData->search->type)) {
            $where['top_menu_categories.type'] = $postData->search->type; // Filter by type if provided
        }
        if (!empty($postData->search->title)) {
            $where['top_menu_categories.name LIKE'] = '%' . $postData->search->title . '%'; // Filter by name if provided
        }
        // Handle sorting
        $orderColumnIndex = $postData->order[0]->column ?? 0; // Get the column index
        $orderDirection = $postData->order[0]->dir ?? 'asc'; // Get the sorting direction
        $orderColumn = $postData->columns[$orderColumnIndex]->data ?? 'order'; // Get the column name

        $model = new TopMenuCategoryModel();

        $totalRecords = $model->db->table('top_menu_categories')->where('is_deleted', 0)->countAllResults(); // Count non-deleted categories
        $totalFiltered = $model->db->table('top_menu_categories')->where($where)->countAllResults(); // Count non-deleted categories
        $data = $model->getListWithDetails('top_menu_categories', $where, $orderColumn, $orderDirection, $start, $length);

        // 컬럼 이름 가져오기
        $columnNames = !empty($data['list']) ? array_keys($data['list'][0]) : [];

        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data['list'],
            'columns' => $columnNames // Add the column names to the response
        ]);
    }

    public function getTopcateDetails()
    {
        $no = $this->request->getPost('no');
        $model = new MenuModel();
        $categoryDetails = $model->getTopcateDetails($no); // Updated to use getCategoryDetails

        if ($categoryDetails) {
            return $this->respond(['status' => '200', 'data' => $categoryDetails]);
        } else {
            return $this->failNotFound('Category not found'); // Updated message
        }
    }
    //create topcate
    public function createTopcate()
    {
        $postData = $this->request->getPost();

        // Define custom validation rules
        $customRules = [
            'name' => [
                'label' => '카테고리명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '카테고리명은 필수입니다.',
                    'max_length' => '카테고리명은 255자 이하로 입력해주세요.'
                ]
            ],

        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);

        // Create top menu record
        $result = $this->createRecord('top_menu_categories', $postData, $validationRules, 'array');
        // Check the result and respond accordingly
        $response = $this->handleResponse($result, '카테고리가 성공적으로 생성되었습니다.');
        return $response;
    }
    //update topcate
    public function updateTopcate()
    {
        $postData = $this->request->getPost(); // JSON 데이터 가져오기

        // Define custom validation rules
        $customRules = [
            'name' => [
                'label' => '카테고리명',
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => '카테고리명은 필수입니다.',
                    'max_length' => '카테고리명은 255자 이하로 입력해주세요.'
                ]
            ],
            // Add more rules as needed
        ];

        // Generate validation rules
        $validationRules = $this->generateValidationRules($postData, $customRules);

        // Update top menu record
        $result = $this->updateRecord('top_menu_categories', $postData, $validationRules, 'array', 'no');

        $response = $this->handleResponse($result, '카테고리가 성공적으로 수정되었습니다.');
        return $response;
    }
    //delete sidecate

    public function deleteTopcate()
    {
        // Delete the top menu record
        $result = $this->deleteRecord('top_menu_categories', null, 'no', 'array');

        // Check the result and respond accordingly
        $response = $this->handleResponse($result, '카테고리가 성공적으로 삭제되었습니다.');
        return $response;
    }
}
