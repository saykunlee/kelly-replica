<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;

/**
 * RouteHandler 클래스
 * 
 * 이 클래스는 API 요청을 동적으로 처리하기 위해 사용됩니다. 
 * URL의 컨트롤러와 메서드 이름을 변환하고, 요청을 적절한 컨트롤러와 메서드에 라우팅합니다.
 */
class RouteHandler extends Controller
{
    /**
     * handle 메서드
     * 
     * 이 메서드는 API 요청을 받아서 적절한 컨트롤러와 메서드에 라우팅합니다.
     * 
     * @param string $controller URL의 첫 번째 세그먼트로 전달되는 컨트롤러 이름 (kebab-case)
     * @param string $method URL의 두 번째 세그먼트로 전달되는 메서드 이름 (kebab-case, 기본값은 'index')
     * @return mixed 컨트롤러 메서드의 실행 결과
     * 
     * @throws PageNotFoundException 컨트롤러 또는 메서드가 존재하지 않을 경우 예외를 발생시킴
     */
    public function handle($controller, $method = 'index')
    {
        // 컨트롤러 이름을 CamelCase로 변환
        $controllerClass = $this->convertToCamelCase($controller);
        // 완전한 네임스페이스를 포함한 컨트롤러 클래스 경로를 생성
        $controllerClass = "App\Controllers\Api\\" . $controllerClass;

        // 메서드 이름을 lowerCamelCase로 변환
        $method = $this->convertToCamelCase($method, false);

        // 변환된 컨트롤러 클래스와 메서드 이름을 로그로 기록하여 디버깅에 도움을 줌
        log_message('info', "Request routed to: {$controllerClass}::{$method}");

        // 컨트롤러 클래스가 존재하는지 확인
        if (!class_exists($controllerClass)) {
            // 존재하지 않을 경우 로그를 남기고 404 예외를 발생시킴
            log_message('error', "Controller class not found: {$controllerClass}");
            throw PageNotFoundException::forControllerNotFound($controllerClass);
        }

        // 요청 객체와 응답 객체를 초기화
        $request = Services::request();
        $response = Services::response();

        // 컨트롤러 인스턴스를 생성하고 초기화
        $controllerInstance = new $controllerClass();
        $controllerInstance->initController($request, $response, Services::logger());

        // 메서드가 컨트롤러에 존재하는지 확인하고 호출
        if (!method_exists($controllerInstance, $method)) {
            // 메서드가 존재하지 않을 경우 로그를 남기고 404 예외를 발생시킴
            log_message('error', "Method not found: {$controllerClass}::{$method}");
            throw PageNotFoundException::forMethodNotFound($method);
        }

        // 메서드를 호출하고 결과를 반환
        return $controllerInstance->$method();
    }

    /**
     * convertToCamelCase 메서드
     * 
     * kebab-case로 전달된 문자열을 CamelCase 또는 lowerCamelCase로 변환합니다.
     * 
     * @param string $string 변환할 문자열 (kebab-case)
     * @param bool $capitalizeFirstLetter 첫 번째 단어의 첫 글자를 대문자로 변환할지 여부
     * @return string 변환된 CamelCase 또는 lowerCamelCase 문자열
     */
    private function convertToCamelCase(string $string, bool $capitalizeFirstLetter = true): string
    {
        // '-'(하이픈)을 기준으로 문자열을 분리하여 단어 배열을 생성
        $words = explode('-', $string);
        $camelCaseString = '';

        // 각 단어의 첫 글자를 대문자로 변환하고 결합
        foreach ($words as $word) {
            $camelCaseString .= ucfirst($word);
        }

        // 첫 번째 단어의 첫 글자를 소문자로 변환 (lowerCamelCase가 필요한 경우)
        if (!$capitalizeFirstLetter) {
            $camelCaseString = lcfirst($camelCaseString);
        }

        return $camelCaseString;
    }
}
