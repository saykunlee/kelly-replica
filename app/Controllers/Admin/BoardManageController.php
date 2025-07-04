<?php

namespace App\Controllers\Admin;

use App\Controllers\Base\BaseAdminController;
use App\Models\CodeModel;
use App\Models\BaseModel;

class BoardManageController extends BaseAdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->required_user_login();
        $this->data['show_add_button'] = false; // 또는 false로 설정하여 숨김
        $this->data['show_add_button_page'] = false; // 또는 false로 설정하여 숨김
    }

    public function manageboardgroups()
    {


        $this->data['breadcrumb'] = [
            ['title' => '게시판관리', 'url' => '#'],
            ['title' => '게시판그룹관리', 'url' => null]
        ];

        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = true; // 또는 false로 설정하여 숨김

        return $this->loadDataAndView('/admin/board/grouplist', 'layouts/admin_layout');
    }
    public function manageboard()
    {
        $baseModel = new BaseModel();

        $query_group = $baseModel->db->table('v_board_group')->where('useing', 1)->where('is_deleted', 0)->get();
        $board_group_list = $query_group->getResultArray();
        $this->data['board_group_list'] = $board_group_list;



        $this->data['breadcrumb'] = [
            ['title' => '게시판관리', 'url' => '#'],
            ['title' => '게시판관리', 'url' => null]
        ];

        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = true; // 또는 false로 설정하여 숨김

        return $this->loadDataAndView('/admin/board/boardlist', 'layouts/admin_layout');
    }

    //게시판 글 관리 - 리스트
    public function manageposts()
    {
        $baseModel = new BaseModel();

        //게시판 그룹 리스트
        $query_group = $baseModel->db->table('v_board_group')->where('useing', 1)->where('is_deleted', 0)->get();
        $board_group_list = $query_group->getResultArray();
        $this->data['board_group_list'] = $board_group_list;

        //게시판 리스트
        $query_board = $baseModel->db->table('v_board')->where('useing', 1)->where('is_deleted', 0)->get();
        $board_list = $query_board->getResultArray();
        $this->data['board_list'] = $board_list;


        $this->data['breadcrumb'] = [
            ['title' => '게시판관리', 'url' => '#'],
            ['title' => '게시판글관리', 'url' => null]
        ];

        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = false; // 또는 false로 설정하여 숨김
        $this->data['show_add_button_page'] = true; // 또는 false로 설정하여 숨김
        $this->data['add_button_page'] = '/admin/board/detail-post?no=create';
        return $this->loadDataAndView('/admin/board/postlist', 'layouts/admin_layout');
    }
    //게시판 상세
    public function detailpost($no = null)
    {

        if ($no == null) {
            //get no from get parameter
            $no = $this->request->getVar('no');
            if ($no == null) {
                $this->alert('게시글 번호가 없습니다', '/admin/board/manage-posts');
            }
        }

        $baseModel = new BaseModel();

        //게시판 그룹 리스트
        $query_group = $baseModel->db->table('v_board_group')->where('useing', 1)->where('is_deleted', 0)->get();
        $board_group_list = $query_group->getResultArray();
        $this->data['board_group_list'] = $board_group_list;

        //게시판 리스트
        $query_board = $baseModel->db->table('v_board')->where('useing', 1)->where('is_deleted', 0)->get();
        $board_list = $query_board->getResultArray();
        $this->data['board_list'] = $board_list;

        if ($no == 'create') {
            $this->data['isEditMode'] = false;
            $this->data['post'] = [];
            $this->data['post']['brd_id'] = '';
            $this->data['post']['post_title'] = '';
            $this->data['post']['post_content'] = '';
            $this->data['post']['post_password'] = '';
            $this->data['post']['post_datetime'] = '';
            $this->data['post']['useing'] = 1;
            $this->data['post']['post_nickname'] = '';
            $this->data['post']['insert_date_ymd'] = date('Y-m-d');
        } else {
            $this->data['isEditMode'] = true;
            //게시글 조회
            $query_post = $baseModel->db->table('v_post')->where('post_id', $no)->get();
            $post = $query_post->getRowArray();

            //조회된 게시글이 없는 경우
            if ($post == null) {
                $this->alert('게시글 정보가 없습니다', '/admin/board/manage-posts');
            }
            //삭제된 게시글인 경우 알림창 표시
            if ($post['is_deleted'] == 1) {
                $this->alert('삭제된 게시글입니다', '/admin/board/manage-posts');
            }
            $this->data['post'] = $post;
        }

        $this->data['breadcrumb'] = [
            ['title' => '게시판관리', 'url' => '#'],
            ['title' => '게시글상세', 'url' => null]
        ];
        // 버튼 표시 여부 설정
        $this->data['show_add_button'] = false; // 또는 false로 설정하여 숨김

        return $this->loadDataAndView('/admin/board/postdetail', 'layouts/admin_layout');
    }
}
