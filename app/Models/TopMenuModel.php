<?php

namespace App\Models;

use CodeIgniter\Model;

class TopMenuModel extends Model
{
    protected $table = 'top_menus';
    protected $primaryKey = 'no';
    protected $allowedFields = ['title', 'url', 'route', 'icon', 'parent_id', 'order', 'category_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'is_deleted', 'is_active', 'dt_mode'];

    protected $topMenuCategoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->topMenuCategoryModel = new \App\Models\TopMenuCategoryModel();
    }
    public function getCategories()
    {
        return $this->topMenuCategoryModel->where('is_deleted', 0)->where('is_active', 1)->findAll();
    }
    public function getParentMenus()
    {
        return $this->db->table($this->table)
            ->select('top_menus.*, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('member as creator', 'creator.mem_id = top_menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = top_menus.updated_by', 'left')
            ->where('top_menus.is_deleted', 0)
            ->whereIn('top_menus.parent_id', [null, 0]) // Check for null or 0
            ->get()
            ->getResultArray();
    }
    public function getTopMenuTree($type = 'admin')
    {
        $categories = $this->topMenuCategoryModel
            ->getActiveCategories($type);

        $menuTree = [];
        $subMenus = [];

        // category_id가 0인 메뉴 가져오기
        $rootMenus = $this
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->where('category_id', 0)
            ->where('type', $type)
            ->orderBy('order', 'ASC')
            ->findAll();



        foreach ($categories as $category) {
            // 해당 카테고리의 메뉴 가져오기
            $menus = $this
                ->where('is_deleted', 0)
                ->where('is_active', 1)
                ->where('category_id', $category['no'])
                ->orderBy('order', 'ASC')
                ->findAll();

            $menuTree[$category['no']] = [
                'name' => $category['name'],
                'icon' => $category['icon'],
                'menus' => []
            ];

            foreach ($menus as $menu) {
                if ($menu['parent_id'] == null) {
                    $menuTree[$category['no']]['menus'][$menu['no']] = [
                        'title' => $menu['title'],
                        'url' => $menu['url'],
                        'icon' => $menu['icon'],
                        'is_visible' => $menu['is_visible'],
                        'sub_menus' => []
                    ];
                } else {
                    $subMenus[] = $menu;
                }
            }
        }

        // 자식 메뉴를 부모 메뉴에 추가
        foreach ($subMenus as $subMenu) {
            foreach ($menuTree as &$category) {
                if (isset($category['menus'][$subMenu['parent_id']])) {
                    $category['menus'][$subMenu['parent_id']]['sub_menus'][] = [
                        'title' => $subMenu['title'],
                        'icon' => $subMenu['icon'],
                        'is_visible' => $subMenu['is_visible'],
                        'url' => $subMenu['url']
                    ];
                    break;
                }
            }
        }

        // category_id가 0인 메뉴를 별도의 카테고리로 제일 마지막 인덱스에 추가
        if (!empty($rootMenus)) {
            $singleMenuCategory = [
                'name' => 'SingleMenus',
                'icon' => 'default-icon', // 필요에 따라 아이콘 설정
                'menus' => []
            ];

            foreach ($rootMenus as $menu) {
                if ($menu['parent_id'] == 0) {
                    $singleMenuCategory['menus'][$menu['no']] = [
                        'title' => $menu['title'],
                        'url' => $menu['url'],
                        'icon' => $menu['icon'],
                        'is_visible' => $menu['is_visible'],
                        'sub_menus' => []
                    ];
                } else {
                    $subMenus[] = $menu;
                }
            }

            array_push($menuTree, $singleMenuCategory);
        }

        return $menuTree;
    }
    public function getTopMenuDetailsWithCategory($no)
    {
        return $this->db->table($this->table)
            ->select('top_menus.*,top_menu_categories.type cat_type, top_menu_categories.name as category_name, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('top_menu_categories', 'top_menu_categories.no = top_menus.category_id', 'left')
            ->join('member as creator', 'creator.mem_id = top_menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = top_menus.updated_by', 'left')
            ->where('top_menus.no', $no)
            ->get()
            ->getRowArray();
    }
    public function getTopMenuListWithDetails($where, $orderBy, $orderDir, $start, $length)
    {
        $query = $this->db->table('top_menus')
            ->select('top_menus.*, top_menu_categories.type as category_type, top_menu_categories.name as category_name, parent_menus.title as parent_title, top_menu_categories.type cat_type, creator.mem_username as created_by_name, updater.mem_username as updated_by_name')
            ->join('top_menu_categories', 'top_menus.category_id = top_menu_categories.no', 'left')
            ->join('top_menus as parent_menus', 'top_menus.parent_id = parent_menus.no', 'left')
            ->join('member as creator', 'creator.mem_id = top_menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = top_menus.updated_by', 'left')
           
            
            ->orderBy('top_menu_categories.type', 'ASC') // 1. type
            ->orderBy('top_menu_categories.order', 'ASC') // 2. 같은 type 안에서 카테고리 순서
            ->orderBy('(CASE WHEN top_menus.parent_id IS NULL THEN top_menus.order ELSE parent_menus.order END)', 'ASC') // 3. 부모 메뉴와 자식 메뉴를 구분하여 정렬
            ->orderBy('top_menus.parent_id', 'ASC') // 4. 같은 부모 메뉴에서 순서
            ->orderBy('top_menus.order', 'ASC'); // 4. 같은 부모 메뉴에서 순서
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
    public function countFilteredResults($table, $where)
    {
        $builder = $this->db->table('top_menus');

        // Add joins as necessary
        $builder->join('top_menu_categories', 'top_menus.category_id = top_menu_categories.no', 'left')
            ->join('member as creator', 'creator.mem_id = top_menus.created_by', 'left')
            ->join('member as updater', 'updater.mem_id = top_menus.updated_by', 'left')
            ->where('top_menus.is_deleted !=', 1);
            

        // Check if category_id is an array and use IN clause
        if (isset($where['category_id']) && is_array($where['category_id'])) {
            $builder->whereIn('top_menus.category_id', $where['category_id']);
        } elseif (isset($where['category_id'])) {           
            $builder->where('top_menus.category_id', $where['category_id']);

        }


        return $builder->countAllResults();
    }

    public function getMenuByUrl($url)
    {
        return $this->where('url', $url)
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();
    }
}
