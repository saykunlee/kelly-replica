<?php

namespace Config;

use App\Models\MenuModel;
use App\Models\TopMenuModel;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);
/*
 * --------------------------------------------------------------------
 * API Routing
 * --------------------------------------------------------------------
 */

// API 요청을 처리하기 위해 동적 라우트를 설정합니다.
// /api/{controller}/{method} 형식의 URL을 처리합니다.
$routes->get('/api/(:segment)', 'RouteHandler::handle/$1'); // GET 요청: 기본 메서드는 'index'
$routes->post('/api/(:segment)/(:segment)', 'RouteHandler::handle/$1/$2'); // POST 요청: 메서드를 지정할 수 있음
$routes->get('/api/(:segment)/(:segment)', 'RouteHandler::handle/$1/$2'); // GET 요청: 메서드를 지정할 수 있음

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// 기본 경로 설정
$routes->get('/', 'Admin\AdminController::index');
$routes->get('/admin', 'Admin\AdminController::index');
$routes->get('/user', 'User\PageController::index');


// 동적 라우트 설정
$menuModel = new MenuModel();
$menus = $menuModel->where('is_active', 1)->findAll();

foreach ($menus as $menu) {
    if (!empty($menu['url']) && !empty($menu['route'])) {
        // URL 패턴을 안전하게 처리
        $safeUrl = preg_quote($menu['url'], '#');
        $routes->get($safeUrl, $menu['route']);
        $routes->post($safeUrl, $menu['route']); // POST 요청도 처리
        //$1 파라이터 패턴
        $routes->get($safeUrl . '/(:num)', $menu['route'] . '/$1');
        $routes->post($safeUrl . '/(:num)', $menu['route'] . '/$1');
    }
}

$topmenuModel = new TopMenuModel();
$topmenus = $topmenuModel->where('is_active', 1)->findAll();

foreach ($topmenus as $topmenu) {
    if (!empty($topmenu['url']) && !empty($topmenu['route'])) {
        // URL 패턴을 안전하게 처리
        if ($topmenu['route'] !== '#') {
            $safeUrl = preg_quote($topmenu['url'], '#');
            $routes->get($safeUrl, $topmenu['route']);
            $routes->post($safeUrl, $topmenu['route']); // POST 요청도 처리
        }
    }
}

$routes->post('Upload_ckeditor', 'Upload_ckeditor::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. One way
 * to do that is by environment, so you have custom routes that apply
 * only in certain environments.
 *
 * require ENVIRONMENT . '/Config/Routes.php';
 */