<?php

namespace App\Controllers\Admin;

class UserInterfaceController extends BaseAdminController
{
    public function collections()
    {
        $data = $this->loadSidebarData();
        return view('/admin/ui/collections', $data);
    }

    public function components()
    {
        $data = $this->loadSidebarData();
        return view('/admin/ui/components', $data);
    }
   
}
