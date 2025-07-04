<?php

namespace App\Controllers\Admin;

class WebAppsController extends BaseAdminController
{
    public function calendar()
    {
        $data = $this->loadSidebarData();
        return view('/admin/webapps/calendar', $data);
    }

    public function chat()
    {
        $data = $this->loadSidebarData();
        return view('/admin/webapps/chat', $data);
    }
    public function contacts()
    {
        $data = $this->loadSidebarData();
        return view('/admin/webapps/contacts', $data);
    }
    public function fileManager()
    {
        $data = $this->loadSidebarData();
        return view('/admin/webapps/fileManager', $data);
    }
    public function mail()
    {
        $data = $this->loadSidebarData();
        return view('/admin/webapps/mail', $data);
    }
}
