<?php

namespace App\Controllers\Admin;
use App\Controllers\Base\UnyictController;

class PagesController extends UnyictController
{
    public function menuConfig()
    {
        $data = $this->loadSidebarData('admin','side');
        return view('/admin/pages/menuconfig', $data);
    }

   
}
