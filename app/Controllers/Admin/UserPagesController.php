<?php

namespace App\Controllers\Admin;

class UserPagesController extends BaseAdminController
{
    public function connections()
    {
        $data = $this->loadSidebarData();
        return view('/admin/userpage/connections', $data);
    }

    public function events()
    {
        $data = $this->loadSidebarData();
        return view('/admin/userpage/events', $data);
    }
    public function viewProfile()
    {
        $data = $this->loadSidebarData();
        return view('/admin/userpage/viewProfile', $data);
    }
    public function groups()
    {
        $data = $this->loadSidebarData();
        return view('/admin/userpage/groups', $data);
    }
}
