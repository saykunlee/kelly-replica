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
 * RESTful API Routing (v1)
 * --------------------------------------------------------------------
 */

// API v1 라우트 그룹
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function ($routes) {
    // 비동기 작업 상태 조회
    $routes->resource('jobs', ['only' => ['show', 'delete']]);
    
    // 회원 리소스 (RESTful)
    $routes->resource('members', ['controller' => 'MembersController']);
    
    // 예제 리소스 (RESTful)
    $routes->resource('examples', ['controller' => 'ExampleResourceController']);
    
    // 커스텀 엔드포인트 (비동기 작업 예제)
    $routes->post('examples/async-task', 'ExampleResourceController::createAsyncTask');
    $routes->post('examples/bulk-import', 'ExampleResourceController::bulkImport');
});

/*
 * --------------------------------------------------------------------
 * Legacy API Routing
 * --------------------------------------------------------------------
 * 
 * 중요: RESTful API 버전 경로(v1, v2 등)는 제외됩니다.
 * Legacy API는 /api/{controller}/{method} 패턴만 처리합니다.
 * 
 * 예시:
 * - ✅ /api/member-api/get-member-list (Legacy API)
 * - ✅ /api/board-api/get-board-group-list (Legacy API)  
 * - ❌ /api/v1/members (RESTful API - 위에서 이미 처리됨)
 * - ❌ /api/v2/members (RESTful API - 위에서 이미 처리됨)
 */

// RESTful API 버전 경로가 아닌 경우만 Legacy API로 처리
// (?!v\d+) - negative lookahead: v1, v2, v3... 등으로 시작하지 않는 경우만 매칭
$routes->get('/api/(?!v\d+)(:segment)', 'RouteHandler::handle/$1'); // GET 요청: 기본 메서드는 'index'
$routes->post('/api/(?!v\d+)(:segment)/(:segment)', 'RouteHandler::handle/$1/$2'); // POST 요청: 메서드를 지정할 수 있음
$routes->get('/api/(?!v\d+)(:segment)/(:segment)', 'RouteHandler::handle/$1/$2'); // GET 요청: 메서드를 지정할 수 있음

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