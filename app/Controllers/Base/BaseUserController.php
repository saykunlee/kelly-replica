<?php

namespace App\Controllers\Base;
use App\Models\MenuCategoryModel;
use App\Models\MenuModel;
use App\Models\DatatableColumnModel;
use App\Models\DatatableSettingsModel;
use App\Models\TopMenuCategoryModel;
use App\Models\TopMenuModel;


class BaseUserController extends BaseController
{
    protected $menuCategoryModel;
    protected $menuModel;
    protected $topMenuCategoryModel;
    protected $topMenuModel;
    protected $data = [];
    protected $sidebarMenu;
    protected $currentMenu;

    public function __construct()
    {
        $this->menuCategoryModel = new MenuCategoryModel();
        $this->menuModel = new MenuModel();
        $this->topMenuCategoryModel = new TopMenuCategoryModel();
        $this->topMenuModel = new TopMenuModel();
        $this->data['sidebar_menu'] = $this->loadSidebarData('user', 'side'); // 사이드바 데이터 로드
        $this->data['top_menu'] = $this->loadSidebarData('user', 'top'); // 탑 메뉴 데이터 로드
        $this->currentMenu = $this->menuModel->getMenuByUrl($_SERVER['REQUEST_URI']);
    }

    protected function loadSidebarData($type = 'admin', $style = 'side')
    {
        if($style == 'side') {
            $model =  $this->menuModel;
            $data['menuTree'] = $model->getMenuTreeByType($type);
            $data['categories'] = $model->getCategories();
            $data['parentMenus'] = $model->getParentMenus();
        }else {
            $model =  $this->topMenuModel;
            $data['categories'] = $model->getCategories();
            $data['parentMenus'] = $model->getParentMenus();
            $data['menuTree'] = $model->getTopMenuTree($type);

        } 


       /*  if ($style != 'side') {
            $data['dt_mode'] = $model->getDatatableMode();
        } */
    
        return $data;
    }

    protected function loadDatatableData($menu_no)
    {
        $columnModel = new DatatableColumnModel();
        $settingsModel = new DatatableSettingsModel();
        $data = [];

        // DataTable 설정 가져오기
        $datatableSettings = $settingsModel->getDatatableSettings($menu_no);
        if ($datatableSettings) {
            $data['datatableSettings'] = $datatableSettings;
            $data['datatableColumns'] = $columnModel->getColumnsBySettingId($datatableSettings['id']);
           
        } else {
            $data['datatableSettings'] = [];
            $data['datatableColumns'] = [];
        }

        return $data;
    }

    protected function loadDataAndView($view)
    {
        if ($this->currentMenu) {
            // Use the current menu's id to load datatable data
            $this->data['datatableData'] = $this->loadDatatableData($this->currentMenu['no']);
        } else {
            // Handle the case where currentMenu is not found
            $this->data['datatableData'] = [];
        }

        // Add current menu to data
        $this->data['currentMenu'] = $this->currentMenu;
        $this->data['view_data'] = $this->data;
        return view($view, $this->data);
    }
}
