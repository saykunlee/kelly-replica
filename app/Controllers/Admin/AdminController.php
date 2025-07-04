<?php

namespace App\Controllers\Admin;
use App\Controllers\Base\BaseAdminController;

class AdminController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->required_user_login();
    }
    public function index()
    {
        $methodName = __FUNCTION__; // 현재 함수명 가져오기
        $this->data['datatableData'] = $this->loadDatatableData($methodName);
        $this->data['view_data'] = $this->data;
        return view('admin/admin_view', $this->data);
    }


    // 다른 admin 페이지들도 동일한 방식으로 추가합니다.
}
