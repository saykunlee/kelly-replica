<?php

namespace App\Controllers\Admin;

use App\Controllers\Base\BaseAdminController;

class MenuManageController extends BaseAdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->required_user_login();
    }

    public function sidemenu()
    {
        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Admin', 'url' => '#'],
            ['title' => '메뉴관리', 'url' => '#'],
            ['title' => '사이드메뉴 관리', 'url' => null]
        ];
        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = true; // 기존 버튼
        $this->data['show_add_button_page'] = true; // 페이지 이동 버튼
        $this->data['add_button_page'] = '/admin/menu/add'; // 원하는 경로로 수정
        return $this->loadDataAndView('/admin/menu/sidemenu', 'layouts/admin_layout');
    }
    public function sidecate()
    {
        // Breadcrumb data
        $this->data['breadcrumb'] = [
            ['title' => 'Admin', 'url' => '#'],
            ['title' => '메뉴관리', 'url' => '#'],
            ['title' => '사이드메뉴 카테고리관리', 'url' => null]
        ];
        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = true; // 또는 false로 설정하여 숨김
        $this->data['show_add_button_page'] = true;
        $this->data['add_button_page'] = '/admin/menu/add';
        return $this->loadDataAndView('/admin/menu/sidecate', 'layouts/admin_layout');
    }

    public function topmenu()
    {
         // Breadcrumb data
         $this->data['breadcrumb'] = [
            ['title' => 'Admin', 'url' => '#'],
            ['title' => '메뉴관리', 'url' => '#'],
            ['title' => '탑메뉴 관리', 'url' => null]
        ];
        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = true; // 또는 false로 설정하여 숨김
        $this->data['show_add_button_page'] = true;
        $this->data['add_button_page'] = '/admin/menu/add';
        return $this->loadDataAndView('/admin/menu/topmenu', 'layouts/admin_layout');
    }
    public function topcate()
    {
        // Breadcrumb data

        $this->data['breadcrumb'] = [
            ['title' => 'Admin', 'url' => '#'],
            ['title' => '메뉴관리', 'url' => '#'],
            ['title' => '탑메뉴 카테고리관리', 'url' => null]
        ];
        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = true; // 또는 false로 설정하여 숨김
        $this->data['show_add_button_page'] = true;
        $this->data['add_button_page'] = '/admin/menu/add';

        return $this->loadDataAndView('/admin/menu/topcate', 'layouts/admin_layout');
    }
}
