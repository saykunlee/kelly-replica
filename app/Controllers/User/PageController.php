<?php

namespace App\Controllers\User;

use App\Controllers\Base\BaseUserController;

class PageController extends BaseUserController
{
    public function index()
    {

        return $this->loadDataAndView('/user/user_view', 'layouts/user_layout');
    }

    // 다른 admin 페이지들도 동일한 방식으로 추가합니다.
}