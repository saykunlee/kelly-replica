<?php

namespace App\Controllers\Admin;

use App\Models\MemberGroupModel;
use App\Controllers\Base\BaseAdminController;

class MemberManageController extends BaseAdminController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->required_user_login();
    }

    public function members()
    {
        //get all group list
        $memberGroupModel = new MemberGroupModel();
        $this->data['groups'] = $memberGroupModel->findAllGroups();
        return $this->loadDataAndView('/admin/member/members','layouts/admin_layout');
    }   
   

}
