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

// API v1 라우트 그룹 - 커스텀 엔드포인트만 명시적 정의
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function ($routes) {
    // Auth API (인증 관련 엔드포인트)
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'jwtAuth']);
    $routes->post('auth/refresh', 'AuthController::refresh');
    $routes->post('auth/verify', 'AuthController::verify');
    $routes->get('auth/me', 'AuthController::me', ['filter' => 'jwtAuth']);
    $routes->post('auth/change-password', 'AuthController::changePassword', ['filter' => 'jwtAuth']);
    $routes->post('auth/force-logout', 'AuthController::forceLogout', ['filter' => 'jwtAuth']);
    $routes->get('auth/sessions', 'AuthController::getSessions', ['filter' => 'jwtAuth']);
    
    // Members API 커스텀 엔드포인트
    $routes->get('members/test', 'MembersController::test');
    
    // Examples API 커스텀 엔드포인트
    $routes->post('examples/async-task', 'ExampleResourceController::createAsyncTask');
    $routes->post('examples/bulk-import', 'ExampleResourceController::bulkImport');
    
    // 참고: 일반 RESTful 엔드포인트는 아래 동적 라우팅으로 자동 처리됨
    // - GET/POST    /api/v1/members, /api/v1/examples, /api/v1/jobs
    // - GET/PUT/PATCH/DELETE /api/v1/members/{id}, /api/v1/examples/{id}, /api/v1/jobs/{id}
});

/*
 * --------------------------------------------------------------------
 * RESTful API 동적 라우팅
 * --------------------------------------------------------------------
 * 
 * 명시적으로 정의되지 않은 RESTful API 리소스를 자동으로 라우팅
 * 
 * 패턴: /api/{version}/{resource}[/{id}]
 * 
 * 예시:
 * - GET    /api/v1/products          → ProductsController::index()
 * - GET    /api/v1/products/123      → ProductsController::show(123)
 * - POST   /api/v1/products          → ProductsController::create()
 * - PUT    /api/v1/products/123      → ProductsController::update(123)
 * - PATCH  /api/v1/products/123      → ProductsController::patch(123)
 * - DELETE /api/v1/products/123      → ProductsController::delete(123)
 * 
 * 동작 방식:
 * 1. 위에서 명시적으로 정의된 라우트가 우선 매칭됨
 * 2. 매칭되지 않은 /api/v{숫자}/{리소스} 패턴은 동적 라우팅으로 처리
 * 3. RestfulRouteHandler가 해당 컨트롤러 존재 여부 확인 후 라우팅
 * 4. 컨트롤러가 없으면 404 반환
 */

// RESTful API 동적 라우팅 (리소스 ID 있음)
$routes->match(
    ['get', 'put', 'patch', 'delete'],
    'api/(v\d+)/(:segment)/(:segment)',
    'RestfulRouteHandler::handle/$1/$2/$3'
);

// RESTful API 동적 라우팅 (리소스 ID 없음)
$routes->match(
    ['get', 'post'],
    'api/(v\d+)/(:segment)',
    'RestfulRouteHandler::handle/$1/$2'
);

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