<?php

namespace App\Controllers\Admin;

use App\Controllers\Base\BaseAdminController;
use App\Models\MemberModel;
use App\Models\MemberGroupModel; // 추가


class LoginController extends BaseAdminController
{
    protected $memberModel;
    protected $session;


    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
        //$this->required_user_login();
    }

    public function login()
    {
        return $this->loadDataAndView('/admin/login/login', 'layouts/admin_login_layout');
    }
    public function loginlog()
    {
           //get all group list
           $memberGroupModel = new MemberGroupModel();
           $this->data['groups'] = $memberGroupModel->findAllGroups();
        return $this->loadDataAndView('/admin/login/loginlog', 'layouts/admin_layout');
    }
    public function index()
    {

        // 사용자가 이미 로그인되어 있는지 확인
        if ($this->session->has('mem_id')) {                        
            $url_after_login = $this->request->getGet('url') ? urldecode($this->request->getGet('url')) : site_url('/admin');
            return redirect()->to($url_after_login);
        }
        if ($this->request->getMethod() === 'POST') {

            $use_login_account = config('App')->useLoginAccount;

            $label = '';
            $rules = '';

            if ($use_login_account === 'both') {
                $label = '아이디 또는 이메일';
                $rules = 'trim|required';
            } elseif ($use_login_account === 'email') {
                $label = '이메일';
                $rules = 'trim|required|valid_email';
            } else {
                $label = '아이디';
                $rules = 'trim|required|alpha_numeric|min_length[3]|max_length[20]';
            }

            $mem_userid = $this->request->getPost('mem_userid') ?? '';
            $mem_password = $this->request->getPost('mem_password') ?? '';
            $redirect_url = $this->request->getPost('url') ?? '';
            $device_fingerprint = $this->request->getPost('device_fingerprint') ?? '';

            $postData = [
                'mem_userid' => $mem_userid,
                'mem_password' => $mem_password,
                'device_fingerprint' => $device_fingerprint,
            ];

            $customRules = [
                'mem_userid' => [
                    'label' => $label,
                    'rules' => $rules,
                    'errors' => [
                        'required' => '아이디 또는 이메일 필드는 필수입니다.',
                        'valid_email' => '유효한 이메일 주소여야 합니다.',
                        'alpha_numeric' => '아이디는 알파벳 문자와 숫자만 포함해야 합니다.',
                        'min_length' => '아이디는 최소 3자 이상이어야 합니다.',
                        'max_length' => '아이디는 20자 미만이어야 합니다.'
                    ]
                ],
                'mem_password' => [
                    'label' => '패스워드',
                    'rules' => 'trim|required|min_length[4]|check_id_pw[mem_userid]',
                    'errors' => [
                        'required' => '패스워드 필드는 필수입니다.',
                        'min_length' => '패스워드는 최소 4자 이상이어야 합니다.'
                    ]
                ]
            ];

            // 검증 규칙 설정
            $this->validation->setRules($customRules);

            // Validate the data
            $validationResponse = $this->handleValidation($postData, $customRules);

            if ($validationResponse['status'] !== 'ok') {
                // Set error message in session
                $this->session->setFlashdata('validationResponse', $validationResponse);
                // Set into data
                $this->data['validationResponse'] = $validationResponse;
                $this->data['validationResponse']['error_message'] = $validationResponse['messages'][0]['message'];

                return $this->loadDataAndView('/admin/login/login', 'layouts/admin_login_layout');
            } else {
                //로그인 성공
                // 로그인 계정 유형에 따라 사용자 정보 가져오기
                if ($use_login_account === 'both') {
                    $userinfo = $this->memberModel->getByBoth($this->request->getPost('mem_userid'));
                } elseif ($use_login_account === 'email') {
                    $userinfo = $this->memberModel->getByEmail($this->request->getPost('mem_userid'));
                } else {
                    $userinfo = $this->memberModel->getByUserid($this->request->getPost('mem_userid'));
                }

                // 회원 그룹 정보 가져오기
                $memberGroupModel = new MemberGroupModel();
                $memberGroups = $memberGroupModel->getMemberGroups($userinfo['mem_id']);

                // 회원 메타 데이터 가져오기
                $memberMeta = $this->memberModel->getMemberMeta($userinfo['mem_id']);
                $metaData = [];
                foreach ($memberMeta as $meta) {
                    $metaData[$meta['mmt_key']] = $meta['mmt_value'];
                }

                // 세션에 사용자 정보 설정
                $this->session->set([
                    'mem_id' => $userinfo['mem_id'],
                    'mem_nickname' => $userinfo['mem_nickname'],
                    'mem_username' => $userinfo['mem_username'],
                    'mem_is_admin' => $userinfo['mem_is_admin'],
                    'mem_is_developer' => $userinfo['mem_is_developer'],
                    'mem_is_superadmin' => $userinfo['mem_is_superadmin'],
                    'member_groups' => $memberGroups,
                    'meta_data' => $metaData
                ]);

                // 비밀번호 변경 주기 확인
                $change_password_date = config('App')->changePasswordDate;
                $site_title = config('App')->siteTitle;
                if ($change_password_date) {
                    $meta_change_pw_datetime = $this->session->get('meta_data')['meta_change_pw_datetime'] ?? null;
                    if ($meta_change_pw_datetime && (time() - strtotime($meta_change_pw_datetime) > $change_password_date * 86400)) {
                        $this->session->set('membermodify', '1');
                        $this->session->setFlashdata('message', esc($site_title) . ' 은(는) 회원님의 비밀번호를 주기적으로 변경하도록 권장합니다.<br /> 오래된 비밀번호를 사용중인 회원님께서는 안전한 서비스 이용을 위해 비밀번호 변경을 권합니다');
                        return redirect()->to('membermodify/password_modify');
                    }
                }

                $url_after_login = $redirect_url ? urldecode($redirect_url) : site_url('/admin');
                return redirect()->to($url_after_login);
            }
        }

        // Pass any flashdata to the view

        $this->data['validationResponse'] = $this->session->getFlashdata('validationResponse');
        return $this->loadDataAndView('/admin/login/login', 'layouts/admin_login_layout');
    }
    /**
     * 로그아웃합니다
     */
    public function logout()
    {
        if (!$this->session->has('mem_id')) {
            return redirect()->to('/');
        }

        $this->session->destroy();


        $url_after_logout = config('App')->urlAfterLogout;
        if ($url_after_logout) {
            $url_after_logout = site_url($url_after_logout);
        } else {
            $url_after_logout = $this->request->getGet('url') ? $this->request->getGet('url') : site_url();
        }

        return redirect()->to($url_after_logout);
    }
}
