<?php

namespace App\Controllers\Admin;

class OtherPagesController extends BaseAdminController
{
    public function timeline()
    {
        $data = $this->loadSidebarData();
        return view('/admin/otherpage/timeline', $data);
    }

}
