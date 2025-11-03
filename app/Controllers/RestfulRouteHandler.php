<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * RESTful API 동적 라우팅 핸들러
 * 
 * /api/v{version}/{resource} 패턴의 요청을 자동으로 해당 컨트롤러로 라우팅
 * 
 * 예시:
 * - GET /api/v1/members → App\Controllers\Api\V1\MembersController::index()
 * - GET /api/v1/members/123 → App\Controllers\Api\V1\MembersController::show(123)
 * - POST /api/v1/products → App\Controllers\Api\V1\ProductsController::create()
 */
class RestfulRouteHandler extends BaseController
{
    /**
     * RESTful 요청 처리
     * 
     * @param string $version API 버전 (v1, v2 등)
     * @param string $resource 리소스명 (복수형, 예: members, products)
     * @param int|string|null $id 리소스 ID (선택적)
     * @return ResponseInterface
     */
    public function handle(string $version, string $resource, $id = null): ResponseInterface
    {
        // HTTP 메서드 가져오기
        $method = strtoupper($this->request->getMethod());
        
        // 리소스명을 Pascal Case 컨트롤러명으로 변환
        // members → MembersController
        // product-categories → ProductCategoriesController
        $controllerName = $this->convertResourceToController($resource);
        
        // 버전 문자열을 네임스페이스 형식으로 변환
        // v1 → V1, v2 → V2
        $versionNamespace = strtoupper($version);
        
        // 컨트롤러 전체 경로 생성
        $controllerClass = "App\\Controllers\\Api\\{$versionNamespace}\\{$controllerName}";
        
        // 컨트롤러 존재 여부 확인
        if (!class_exists($controllerClass)) {
            return $this->failNotFound("API 리소스를 찾을 수 없습니다: {$resource}");
        }
        
        // 컨트롤러 인스턴스 생성
        $controller = new $controllerClass();
        
        // RESTful 메서드 매핑
        $action = $this->mapMethodToAction($method, $id);
        
        // 메서드 존재 여부 확인
        if (!method_exists($controller, $action)) {
            return $this->failMethodNotAllowed("{$method} 메서드는 이 리소스에서 지원되지 않습니다");
        }
        
        // 컨트롤러 메서드 호출
        if ($id !== null) {
            return $controller->{$action}($id);
        } else {
            return $controller->{$action}();
        }
    }
    
    /**
     * 리소스명을 컨트롤러명으로 변환
     * 
     * @param string $resource 리소스명 (kebab-case 또는 일반 문자열)
     * @return string 컨트롤러명 (PascalCase)
     */
    private function convertResourceToController(string $resource): string
    {
        // kebab-case 또는 snake_case를 공백으로 변환
        $resource = str_replace(['-', '_'], ' ', $resource);
        
        // 각 단어의 첫 글자를 대문자로
        $resource = ucwords($resource);
        
        // 공백 제거
        $resource = str_replace(' ', '', $resource);
        
        // Controller 접미사 추가
        return $resource . 'Controller';
    }
    
    /**
     * HTTP 메서드와 ID 존재 여부를 기반으로 액션 매핑
     * 
     * @param string $method HTTP 메서드
     * @param mixed $id 리소스 ID
     * @return string 액션명
     */
    private function mapMethodToAction(string $method, $id): string
    {
        $actionMap = [
            'GET' => $id ? 'show' : 'index',      // GET /resources → index(), GET /resources/{id} → show()
            'POST' => 'create',                    // POST /resources → create()
            'PUT' => 'update',                     // PUT /resources/{id} → update()
            'PATCH' => 'patch',                    // PATCH /resources/{id} → patch()
            'DELETE' => 'delete',                  // DELETE /resources/{id} → delete()
        ];
        
        return $actionMap[$method] ?? 'index';
    }
    
    /**
     * 404 응답 반환
     * 
     * @param string $message 에러 메시지
     * @return ResponseInterface
     */
    private function failNotFound(string $message = 'Not Found'): ResponseInterface
    {
        return $this->response
            ->setStatusCode(404)
            ->setJSON([
                'status' => 404,
                'success' => false,
                'message' => $message,
                'errorCode' => 'RESOURCE_NOT_FOUND',
            ]);
    }
    
    /**
     * 405 응답 반환
     * 
     * @param string $message 에러 메시지
     * @return ResponseInterface
     */
    private function failMethodNotAllowed(string $message = 'Method Not Allowed'): ResponseInterface
    {
        return $this->response
            ->setStatusCode(405)
            ->setJSON([
                'status' => 405,
                'success' => false,
                'message' => $message,
                'errorCode' => 'METHOD_NOT_ALLOWED',
            ]);
    }
}

