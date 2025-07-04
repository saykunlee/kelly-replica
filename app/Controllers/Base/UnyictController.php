<?php

namespace App\Controllers\Base;

use App\Models\MenuCategoryModel;
use App\Models\MenuModel;
use App\Models\TopMenuCategoryModel;
use App\Models\TopMenuModel;

class UnyictController extends BaseController
{
    protected $menuCategoryModel;
    protected $menuModel;
    protected $topMenuCategoryModel;
    protected $topMenuModel;

    public function __construct()
    {
        $this->menuCategoryModel = new MenuCategoryModel();
        $this->menuModel = new MenuModel();
        $this->topMenuCategoryModel = new TopMenuCategoryModel();
        $this->topMenuModel = new TopMenuModel();
    }

    protected function loadSidebarData($type = 'user', $style = 'side')
    {

        if ($style == 'side') {
            $data['menuTree'] = $this->menuModel->getMenuTreeByType($type);
        } else {
            $data['menuTree'] = $this->topMenuModel->getTopMenuTree($type);
        }

        return $data;
    }
}
