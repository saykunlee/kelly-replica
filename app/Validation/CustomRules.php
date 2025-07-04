<?php

namespace App\Validation;

use App\Models\MemberModel;
use App\Models\MemberDormantModel;
use CodeIgniter\Config\Services;

class CustomRules
{
    public function check_id_pw(string $str, string $fields, array $data): bool
    {
        $memberModel = new MemberModel();
        $memberDormantModel = new MemberDormantModel();
        $session = Services::session();
        $request = Services::request();
        $validation = Services::validation();
        $config = config('App');

        $max_login_try_count = (int) $config->maxLoginTryCount;
        $max_login_try_limit_second = (int) $config->maxLoginTryLimitSecond;

        $loginfailnum = 0;
        $loginfailmessage = '';
        if ($max_login_try_count && $max_login_try_limit_second) {
            $logindata = $this->getLoginData($memberModel, $data['mem_userid'], $request->getIPAddress(), $data['device_fingerprint']);

            if ($logindata) {
                foreach ($logindata as $val) {
                    if ((int) $val['mll_success'] === 0) {
                        $loginfailnum++;
                    } elseif ((int) $val['mll_success'] === 1) {
                        break;
                    }
                }
            }

            if ($this->isLoginAttemptLocked($loginfailnum, $max_login_try_count, $logindata, $max_login_try_limit_second)) {
                $session->setFlashdata('loginfailmessage', '회원님은 패스워드를 연속으로 ' . $loginfailnum . '회 잘못 입력하셨기 때문에 ' . $next_login . '초 후에 다시 로그인 시도가 가능합니다');
                return false;
            }

            $loginfailmessage = '<br />회원님은 ' . ($loginfailnum + 1) . '회 연속으로 패스워드를 잘못입력하셨습니다. ';
        }

        $userinfo = $this->getUserInfo($memberModel, $memberDormantModel, $data['mem_userid'], $config->useLoginAccount);
        $is_dormant_member = $userinfo['is_dormant_member'];
        unset($userinfo['is_dormant_member']);

        if (!$this->validateUserInfo($userinfo, $str, $validation, $data['mem_userid'], $loginfailmessage, $memberModel, $request, $config, $data['device_fingerprint'])) {
            return false;
        }

        if ($is_dormant_member) {
            $memberDormantModel->recoverFromDormant($userinfo['mem_id']);
        }

        $this->logLoginSuccess($memberModel, $userinfo['mem_id'], $data['mem_userid'], $request->getIPAddress(), $data['device_fingerprint']);

        return true;
    }

    private function getLoginData($memberModel, $userid, $ip, $device_fingerprint)
    {
        $select = 'mll_id, mll_success, mem_id, mll_ip, mll_datetime, mll_userid, mll_device_fingerprint';
        $where = [
            'mll_userid' => $userid,
            // 'mll_ip' => $ip,
            'mll_datetime > ' => date('Y-m-d H:i:s', strtotime('-30 days')),
            'mll_device_fingerprint' => $device_fingerprint
        ];

        return $memberModel->getLoginLogs($select, $where);
    }

    private function isLoginAttemptLocked($loginfailnum, $max_login_try_count, $logindata, $max_login_try_limit_second)
    {
        if ($loginfailnum > 0 && $loginfailnum % $max_login_try_count === 0) {
            $lastlogintrydatetime = $logindata[0]['mll_datetime'];
            $next_login = strtotime($lastlogintrydatetime) + $max_login_try_limit_second - time();
            return $next_login > 0;
        }
        return false;
    }

    private function getUserInfo($memberModel, $memberDormantModel, $userid, $use_login_account)
    {
        $userselect = 'mem_id, mem_password, mem_denied, mem_email_cert, mem_is_admin';
        $is_dormant_member = false;

        if ($use_login_account === 'both') {
            $userinfo = $memberModel->getByBoth($userid, $userselect);
            if (!$userinfo) {
                $userinfo = $memberDormantModel->getByBoth($userid, $userselect);
                $is_dormant_member = (bool) $userinfo;
            }
        } elseif ($use_login_account === 'email') {
            $userinfo = $memberModel->getByEmail($userid, $userselect);
            if (!$userinfo) {
                $userinfo = $memberDormantModel->getByEmail($userid, $userselect);
                $is_dormant_member = (bool) $userinfo;
            }
        } else {
            $userinfo = $memberModel->getByUserid($userid, $userselect);
            if (!$userinfo) {
                $userinfo = $memberDormantModel->getByUserid($userid, $userselect);
                $is_dormant_member = (bool) $userinfo;
            }
        }

        $userinfo['is_dormant_member'] = $is_dormant_member;
        return $userinfo;
    }

    private function validateUserInfo($userinfo, $password, $validation, $userid, $loginfailmessage, $memberModel, $request, $config, $device_fingerprint)
    {
        if (!isset($userinfo['mem_id']) || !isset($userinfo['mem_password'])) {
            $this->setValidationError($validation, 'mem_userid', '회원 아이디 또는 패스워드가 존재하지 않습니다' . $loginfailmessage);
            $this->logLoginFailure($memberModel, 0, $userid, '아이디 없음', $request, $device_fingerprint);
            return false;
        } elseif (!password_verify($password, $userinfo['mem_password'])) {
            $this->setValidationError($validation, 'mem_userid', '회원 아이디와 패스워드가 서로 맞지 않습니다' . $loginfailmessage);
            $this->logLoginFailure($memberModel, $userinfo['mem_id'], $userid, '패스워드 불일치', $request, $device_fingerprint);
            return false;
        } elseif ($userinfo['mem_denied']) {
            $this->setValidationError($validation, 'mem_userid', '회원님의 아이디는 접근이 금지된 아이디입니다');
            $this->logLoginFailure($memberModel, $userinfo['mem_id'], $userid, '접근 금지', $request, $device_fingerprint);
            return false;
        } elseif ($config->useRegisterEmailAuth && !$userinfo['mem_email_cert']) {
            $this->setValidationError($validation, 'mem_userid', '회원님은 아직 이메일 인증을 받지 않으셨습니다');
            $this->logLoginFailure($memberModel, $userinfo['mem_id'], $userid, '이메일 미인증', $request, $device_fingerprint);
            return false;
        } elseif ($userinfo['mem_is_admin'] && $request->getPost('autologin')) {
            $this->setValidationError($validation, 'mem_userid', '최고관리자는 자동로그인 기능을 사용할 수 없습니다');
            return false;
        }

        return true;
    }

    private function setValidationError($validation, $field, $message)
    {
        $validation->setError($field, $message);
    }

    /*  private function logLoginFailure($memberModel, $mem_id, $userid, $reason, $request)
    {
        $memberModel->updateLoginLog([
            'mll_success' => 0,
            'mem_id' => $mem_id,
            'mll_userid' => $userid,
            'mll_datetime' => date('Y-m-d H:i:s'),
            'mll_ip' => $request->getIPAddress(),
            'mll_reason' => $reason,
            'mll_useragent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'mll_url' => $_SERVER['REQUEST_URI'] ?? '',
            'mll_referer' => $_SERVER['HTTP_REFERER'] ?? ''
        ]);
    }

    private function logLoginSuccess($memberModel, $mem_id, $userid, $ip)
    {
        $memberModel->updateLoginLog([
            'mll_success' => 1,
            'mem_id' => $mem_id,
            'mll_userid' => $userid,
            'mll_datetime' => date('Y-m-d H:i:s'),
            'mll_ip' => $ip,
            'mll_reason' => '로그인 성공',
            'mll_useragent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'mll_url' => $_SERVER['REQUEST_URI'] ?? '',
            'mll_referer' => $_SERVER['HTTP_REFERER'] ?? ''
        ]);
    } */

    private function logLoginSuccess($memberModel, $mem_id, $userid, $ip, $device_fingerprint)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $deviceInfo = $this->getDeviceInfo($userAgent);
        $country = $this->getLocationFromIP($ip);

        $memberModel->updateLoginLog([
            'mll_success' => 1,
            'mem_id' => $mem_id,
            'mll_userid' => $userid,
            'mll_datetime' => date('Y-m-d H:i:s'),
            'mll_ip' => $ip,
            'mll_reason' => '로그인 성공',
            'mll_useragent' => $userAgent,
            'mll_url' => $_SERVER['REQUEST_URI'] ?? '',
            'mll_referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'mll_device_type' => $deviceInfo['device_type'],
            'mll_os' => $deviceInfo['os'],
            'mll_browser' => $deviceInfo['browser'],
            'mll_device_fingerprint' => $device_fingerprint,
            'mll_location' => $country, // 국가 정보 추가
        ]);
    }

    private function logLoginFailure($memberModel, $mem_id, $userid, $reason, $request, $device_fingerprint)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $deviceInfo = $this->getDeviceInfo($userAgent);
        $ip = $request->getIPAddress();
        $country = $this->getLocationFromIP($ip);

        $memberModel->updateLoginLog([
            'mll_success' => 0,
            'mem_id' => $mem_id,
            'mll_userid' => $userid,
            'mll_datetime' => date('Y-m-d H:i:s'),
            'mll_ip' => $ip,
            'mll_reason' => '로그인 실패',
            'mll_fail_reason' => $reason,
            'mll_useragent' => $userAgent,
            'mll_url' => $_SERVER['REQUEST_URI'] ?? '',
            'mll_referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'mll_device_type' => $deviceInfo['device_type'],
            'mll_os' => $deviceInfo['os'],
            'mll_browser' => $deviceInfo['browser'],
            'mll_device_fingerprint' => $device_fingerprint,
            'mll_location' => $country, // 국가 정보 추가
        ]);
    }

    private function getDeviceInfo($userAgent)
    {
        // 기본적인 장치 정보 파싱 (보다 정교한 라이브러리 사용 가능)
        $deviceType = 'desktop';
        if (preg_match('/mobile|android|tablet/i', $userAgent)) {
            $deviceType = 'mobile';
        }

        // 운영체제 파싱
        if (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
            $os = 'iOS';
        } else {
            $os = 'Unknown OS';
        }

        // 브라우저 파싱
        if (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } else {
            $browser = 'Unknown Browser';
        }

        // 장치 지문 (Device Fingerprint)는 별도의 라이브러리나 방법을 통해 구현 가능
        $deviceFingerprint = hash('sha256', $userAgent . $_SERVER['REMOTE_ADDR']);

        return [
            'device_type' => $deviceType,
            'os' => $os,
            'browser' => $browser,
            'device_fingerprint' => $deviceFingerprint,
        ];
    }
    private function getLocationFromIP($ip)
    {
        //$ip = '210.92.84.75';
        // IP 주소 유효성 검사
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return '';
        }

        $apiKey = '273bbf1ff1da74'; // ipinfo.io API 키를 여기에 입력하세요
        $url = "https://ipinfo.io/{$ip}?token={$apiKey}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 타임아웃 설정
        $response = curl_exec($ch);

        // API 호출이 성공적이고 결과가 유효한지 확인
        if (curl_errno($ch) || $response === false) {
            curl_close($ch);
            return '';
        }

        curl_close($ch);

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '';
        }

        $country = $data['country'] ?? '';
        $region = $data['region'] ?? '';
        $city = $data['city'] ?? '';

        // 알파벳이 아닌 경우 '-'로 대체
        $country = preg_match('/^[a-zA-Z]+$/', $country) ? $country : '-';
        $region = preg_match('/^[a-zA-Z]+$/', $region) ? $region : '-';
        $city = preg_match('/^[a-zA-Z]+$/', $city) ? $city : '-';

        return trim("{$country},{$region},{$city}");
    }
}
