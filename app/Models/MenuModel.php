<?php

namespace App\Models;

class MenuModel extends BaseModel
{
    protected $table = 'menus';
    protected $primaryKey = 'no';

    // 카테고리 모델 로드
    protected $menuCategoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->menuCategoryModel = new \App\Models\MenuCategoryModel();
    }

    public function getActiveMenus()
    {
        return $this->db->table($this->table)
            ->select('menus.*, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('member as creator', 'creator.mem_id = menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = menus.updated_by', 'left')
            ->where('menus.is_deleted', 0)
            ->where('menus.is_active', 1)
            ->get()
            ->getResultArray();
    }

    public function getMenuTreeByType($type = 'admin')
    {
        // Fetch categories based on type
        $categories = $this->menuCategoryModel
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->where('type', $type)
            ->orderBy('order', 'ASC')
            ->findAll();

        $menuTree = [];
        $subMenus = [];

        foreach ($categories as $category) {
            // Fetch menus for the specific category
            $menus = $this->db->table($this->table)
                ->select('menus.*, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
                ->join('member as creator', 'creator.mem_id = menus.created_by', 'left')
                ->join('member as updater', 'updater.mem_id = menus.updated_by', 'left')
                ->where('menus.is_deleted', 0)
                ->where('menus.is_active', 1)
                ->where('menus.category_id', $category['no'])
                ->orderBy('menus.order', 'ASC')
                ->get()
                ->getResultArray();

            $menuTree[$category['no']] = [
                'name' => $category['name'],
                'menus' => []
            ];

            foreach ($menus as $menu) {
                if ($menu['parent_id'] == null || $menu['parent_id'] == 0) {
                    $menuTree[$category['no']]['menus'][$menu['no']] = [
                        'title' => $menu['title'],
                        'url' => $menu['url'],
                        'icon' => $menu['icon'],
                        'created_by_name' => $menu['created_by_name'],
                        'updated_by_name' => $menu['updated_by_name'],
                        'is_show' => $menu['is_show'],
                        'sub_menus' => []
                    ];
                } else {
                    // 자식 메뉴를 임시 배열에 저장
                    $subMenus[] = $menu;
                }
            }

            // 자식 메뉴를 부모 메뉴에 추가
            foreach ($subMenus as $subMenu) {
                if (isset($menuTree[$category['no']]['menus'][$subMenu['parent_id']])) {
                    $menuTree[$category['no']]['menus'][$subMenu['parent_id']]['sub_menus'][] = [
                        'title' => $subMenu['title'],
                        'url' => $subMenu['url'],
                        'is_show' => $subMenu['is_show'],
                        'created_by_name' => $subMenu['created_by_name'],
                        'updated_by_name' => $subMenu['updated_by_name']
                    ];
                }
            }
        }

        return $menuTree;
    }

    public function countFilteredResults($table, $where)
    {
        $builder = $this->db->table($table);

        // Add joins as necessary
        $builder->join('menu_categories', 'menus.category_id = menu_categories.no', 'left')
            ->join('member as creator', 'creator.mem_id = menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = menus.updated_by', 'left');

        // Apply the where conditions
        $builder->where('menus.is_deleted !=', 1);

        // Check if category_id is an array and use IN clause
        if (isset($where['category_id']) && is_array($where['category_id'])) {
            $builder->whereIn('menus.category_id', $where['category_id']); // Use IN clause
        } elseif (isset($where['category_id'])) {
            $builder->where('menus.category_id', $where['category_id']); // Single value
        }

        return $builder->countAllResults();
    }

    public function getMenuListWithDetails($where, $orderBy, $orderDir, $start, $length)
    {
        $query = $this->db->table('menus')
            ->select('menus.*, menu_categories.type as category_type, menu_categories.name as category_name, parent_menus.title as parent_title, menu_categories.type, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('menu_categories', 'menus.category_id = menu_categories.no', 'left')
            ->join('menus as parent_menus', 'menus.parent_id = parent_menus.no', 'left')
            ->join('member as creator', 'creator.mem_id = menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = menus.updated_by', 'left')

            ->orderBy('menu_categories.type', 'ASC') // 1. type
            ->orderBy('menu_categories.order', 'ASC') // 2. 같은 type 안에서 카테고리 순서
            ->orderBy('(CASE WHEN menus.parent_id IS NULL THEN menus.order ELSE parent_menus.order END)', 'ASC') // 3. 부모 메뉴와 자식 메뉴를 구분하여 정렬
            ->orderBy('menus.parent_id', 'ASC') // 4. 같은 부모 메뉴에서 순서
            ->orderBy('menus.order', 'ASC'); // 4. 같은 부모 메뉴에서 순서
        // WHERE 조건 추가
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        // length가 -1이 아닌 경우에만 limit을 설정합니다.
        if ($length != -1) {
            $query->limit($length, $start);
        }

        $result = $query->get();

        return [
            'total_rows' => $result->getNumRows(),
            'list' => $result->getResultArray()
        ];
    }

    public function getCategories()
    {
        return $this->menuCategoryModel->where('is_deleted', 0)->where('is_active', 1)->findAll();
    }

    public function getParentMenus()
    {
        return $this->db->table($this->table)
            ->select('menus.*, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('member as creator', 'creator.mem_id = menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = menus.updated_by', 'left')
            ->where('menus.is_deleted', 0)
            ->whereIn('menus.parent_id', [null, 0]) // Check for null or 0
            ->get()
            ->getResultArray();
    }

    public function getMenuDetailsWithCategory($no)
    {
        return $this->db->table($this->table)
            ->select('menus.*,menu_categories.type, menu_categories.name as category_name, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('menu_categories', 'menu_categories.no = menus.category_id', 'left')
            ->join('member as creator', 'creator.mem_id = menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = menus.updated_by', 'left')
            ->where('menus.no', $no)
            ->get()
            ->getRowArray();
    }

    // Add the getColumns method
    public function getColumns()
    {
        return $this->db->getFieldNames($this->table);
    }

    public function getMenuByUrl($url)
    {
         // url에서 ?가 있다면 ?를 포함한 뒤의 파라미터를 잘라냅니다.
         $url = explode('?', $url)[0];
        return $this->where('url', $url)
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();
    }

    public function getCategoryDetails($no)
    {
        return $this->db->table('menu_categories')
            ->select('menu_categories.*, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('member as creator', 'creator.mem_id = menu_categories.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = menu_categories.updated_by', 'left')
            ->where('menu_categories.no', $no)
            ->get()
            ->getRowArray();
    }
    public function getTopcateDetails($no)
    {
        return $this->db->table('top_menu_categories')
            ->select('top_menu_categories.*, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('member as creator', 'creator.mem_id = top_menu_categories.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = top_menu_categories.updated_by', 'left')
            ->where('top_menu_categories.no', $no)
            ->get()
            ->getRowArray();
    }
}
