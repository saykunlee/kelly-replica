<?php

namespace App\Controllers\Base;

use App\Controllers\Base\BaseUserController;

class ReservationController extends BaseUserController
{       

    protected $sidebarMenu;
    protected $currentMenu; // Define currentMenu as a protected property
    public function __construct()
    {
        parent::__construct();
        // Initialize currentMenu in the constructor
        $this->currentMenu = $this->menuModel->getMenuByUrl($_SERVER['REQUEST_URI']);
    }
    public function searchRoom()
    {
        return $this->loadDataAndView('user/search_room');
    }
    public function checkReser()
    {
        $data = $this->loadSidebarData('user', 'side');
        return view('user/search_room_side', ['data' => $data]);
    }
    public function reserRoom()
    {
        return $this->loadDataAndView('user/reser_room');

       
    }

    // 다른 admin 페이지들도 동일한 방식으로 추가합니다.
}
