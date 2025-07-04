<?php

namespace App\Controllers\Base;

use App\Models\MenuCategoryModel;
use App\Models\MenuModel;
use App\Models\DatatableColumnModel;
use App\Models\DatatableSettingsModel;
use App\Models\TopMenuCategoryModel;
use App\Models\TopMenuModel;
use Config\Services;


class BaseAdminController extends BaseController
{
    protected $menuCategoryModel;
    protected $menuModel;
    protected $topMenuCategoryModel;
    protected $topMenuModel;
    protected $data = [];
    protected $sidebarMenu;
    protected $currentMenu;
    protected $validation; // Add this line

    public function __construct()
    {

        $this->menuCategoryModel = new MenuCategoryModel();
        $this->menuModel = new MenuModel();
        $this->topMenuCategoryModel = new TopMenuCategoryModel();
        $this->topMenuModel = new TopMenuModel();
        $this->currentMenu = $this->menuModel->getMenuByUrl($_SERVER['REQUEST_URI']);
        $this->data['sidebar_menu'] = $this->loadMenuData('admin', 'side'); // 사이드바 데이터 로드
        $this->data['top_menu'] = $this->loadMenuData('admin', 'top');
        $this->validation = Services::validation(); // Initialize validation service here
        $this->session = Services::session();
        // get member photo
        //$this->data['member_photo'] = $this->memberLibrary->get_member_photo();
      
    }

    //login check 
    public function required_user_login($type = 'alert')
    {
        $current_url = current_url();
        //check session
        if (!$this->session->has('mem_id')) {
            if ($type === 'alert') {
                $this->alert('로그인 후 이용이 가능합니다','/login?url=' . urlencode($current_url));
        
            } else {
                $this->session->set_flashdata(
                    'message',
                    '로그인 후 이용이 가능합니다'
                );
                
                redirect('/login?url=' . urlencode($current_url));
            }
        }
        return true;
    }


    /**
     * Alert 띄우기
     */

     public function alert($msg = '', $url = '')
    {
        if (empty($msg)) {
            $msg = '잘못된 접근입니다';
        }
        //charset from app 
        $charset = config('App')->charset;
        echo '<meta http-equiv="content-type" content="text/html; charset=' . $charset . '">';
        echo '<script type="text/javascript">alert("' . $msg . '");';
        if (empty($url)) {
            echo 'history.go(-1);';
        }
        if ($url) {
            echo 'document.location.href="' . $url . '"';
        }
        echo '</script>';
        exit;
    }



    /**
     * Alert 후 창 닫음
     */
    private function alert_close($msg = '')
    {
        if (empty($msg)) {
            $msg = '잘못된 접근입니다';
        }
        //charset from app 
        $charset = config('App')->charset;
        echo '<meta http-equiv="content-type" content="text/html; charset=' . $charset . '">';
        echo '<script type="text/javascript"> alert("' . $msg . '"); window.close(); </script>';
        exit;
    }



    /**
     * Alert 후 부모창 새로고침 후 창 닫음
     */
    private function alert_refresh_close($msg = '')
    {
        if (empty($msg)) {
            $msg = '잘못된 접근입니다';
        }
        //charset from app 
        $charset = config('App')->charset;
        echo '<meta http-equiv="content-type" content="text/html; charset=' . $charset . '">';
        echo '<script type="text/javascript"> alert("' . $msg . '"); window.opener.location.reload();window.close(); </script>';
        exit;
    }


    protected function handleValidation($data, $validationRules)
    {
        if (!$this->validation->setRules($validationRules)->run($data)) {
            $errors = $this->formatValidationErrors($this->validation->getErrors());
            return $errors;
        } else {
            // 검증 성공 성공시 status 추가 
            return ['status' => 'ok'];
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
            'status' => '400', // Custom status code for validation errors
            'error' => 'Validation Error',
            'messages' => $formattedErrors
        ];
    }
    protected function generateValidationRules($postData, $customRules = [])
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
    protected function loadMenuData($type = 'admin', $style = 'side')
    {
        if ($style == 'side') {
            $model =  $this->menuModel;
            $data['menuTree'] = $model->getMenuTreeByType($type);
            $data['categories'] = $model->getCategories();
            $data['parentMenus'] = $model->getParentMenus();
        } else {
            $model =  $this->topMenuModel;
            $data['categories'] = $model->getCategories();
            $data['parentMenus'] = $model->getParentMenus();
            $data['menuTree'] = $model->getTopMenuTree($type);
        }

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

    protected function loadDataAndView($view, $layout = 'layouts/admin_layout')
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
        $this->data['layout'] = $layout; // Add layout to data

        return view($view, $this->data);
    }
   
}
