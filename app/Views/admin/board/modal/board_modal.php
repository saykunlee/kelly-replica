<div id="modal_app">
    <!-- modal start -->
    <div class="modal fade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content tx-14">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel2">{{ common.modal_title }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="detail">
                        <div class="row">
                            <div class="col-lg-12 d-flex">
                                <div class="form-group flex-fill mg-r-15 ">
                                    <label>게시판그룹<span class="tx-danger"></span></label>
                                    <select class="form-control" id="board_group_list_select" style="width: 100%;" v-model="detail.bgr_id">
                                        <option value="">전체</option>
                                        <option v-for="(code,idx) in group_list" :value="code.bgr_id">{{code.bgr_name}}</option>
                                    </select>
                                </div>
                                <div class="form-group flex-fill">
                                    <label>게시판ID(KEY)<span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="게시판ID(KEY) 입력" v-model="detail.brd_key">
                                </div>
                            </div>
                            <div class="col-lg-12 d-flex">
                                <div class="form-group flex-fill">
                                    <label>게시판명<span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="게시판명 입력" v-model="detail.brd_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 d-flex justify-content-between">
                                <div class="form-group mg-r-15">
                                    <label>작성권한<!-- <span class="tx-danger">*</span> --></label>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="customRadio_access_write1" name="customRadio_access_write1" class="custom-control-input" value="" v-model="detail.board_meta.access_write">
                                        <label class="custom-control-label" for="customRadio_access_write1">모두</label>
                                    </div>
                                </div>
                                <div class="form-group  mg-r-15">
                                    <div class="custom-control custom-radio ">
                                        <input type="radio" id="customRadio_access_write2" name="customRadio_access_write2" class="custom-control-input" value="100" v-model="detail.board_meta.access_write">
                                        <label class="custom-control-label mg-t-30" for="customRadio_access_write2">관리자</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 d-flex justify-content-between">
                                <!-- 정렬순서 -->
                                <div class="form-group ">
                                    <label>정렬순서</label>
                                    <input type="number" class="form-control tx-center wd-90" placeholder="정렬순서 입력" v-model="detail.brd_order">
                                </div>

                                <!-- 검색여부 -->
                                <div class="form-group ">
                                    <label>검색여부</label>
                                    <div class="form-inline">
                                        <select class="form-control tx-center wd-120" v-model="detail.brd_search">
                                            <option value="0">미사용</option>
                                            <option value="1">사용</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- 노출여부 -->
                                <div class="form-group">
                                    <label>노출여부</label>
                                    <div class="form-inline">
                                        <select class="form-control tx-center wd-120" v-model="detail.useing">
                                            <option value="0">미노출</option>
                                            <option value="1">노출</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="mg-t-10 mg-b-20">

                        <div class="row">
                            <!-- 게시판 형태 -->
                            <div class="col-lg-12 d-flex justify-content-start">
                                <div class="form-group mg-r-15">
                                    <label>게시판 형태</label>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="use_gallery_list1" name="customRadio" class="custom-control-input" value="0" v-model="detail.board_meta.use_gallery_list">
                                        <label class="custom-control-label" for="use_gallery_list1">리스트형</label>
                                    </div>
                                </div>
                                <div class="form-group mg-r-15">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="use_gallery_list2" name="customRadio" class="custom-control-input" value="1" v-model="detail.board_meta.use_gallery_list">
                                        <label class="custom-control-label mg-t-30" for="use_gallery_list2">갤러리형</label>
                                    </div>
                                </div>
                            </div>
                            <!-- New아이콘 -->
                            <div class="col-lg-12 d-flex ">
                                <div class="form-group mg-r-5 ">
                                    <label>New아이콘<span class="tx-danger"></span></label>
                                    <input type="text" class="form-control wd-100" placeholder="노출시간 설정" v-model="detail.board_meta.new_icon_hour">
                                </div>
                                <div class="form-group mg-r-15 ">
                                    <small class="form-text text-second mg-t-40">설정시간 동안 New아이콘 노출</small>
                                </div>
                            </div>
                            <!-- Hot아이콘 -->
                            <div class="col-lg-12 d-flex ">
                                <div class="form-group mg-r-5">
                                    <label>Hot아이콘<span class="tx-danger"></span></label>
                                    <input type="text" class="form-control wd-100" placeholder="Hot아이콘 설정" v-model="detail.board_meta.hot_icon_hit">
                                </div>
                                <div class="form-group mg-r-15 ">
                                    <small class="form-text text-second mg-t-40">조회수가 설정값 이상이면 Hot아이콘 노출</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">닫기</button>
                    <button v-if="isEditMode" type="button" class="btn btn-primary tx-13" @click="updateBoard">수정하기</button>
                    <button v-if="isEditMode" type="button" class="btn btn-danger tx-13" @click="deleteBoard">삭제하기</button>
                    <button v-else type="button" class="btn btn-success tx-13" @click="createBoard">추가하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    <?= $this->include('components/modal_common.js') ?>
    const modalApp = Vue.createApp({
        mixins: [modalCommon],
        data() {
            return {
                app_name: 'modalApp',
                common: {
                    modal_title: '게시판 수정'
                },
                selectedNo: null,
                group_list: [],
                detail: {
                    brd_id: '',
                    bgr_id: '',
                    brd_key: '',
                    brd_type: '',
                    brd_form: '',
                    brd_name: '',
                    board_meta: {},
                    brd_mobile_name: '',
                    brd_order: '',
                    brd_search: '',
                    update_date: '',
                    insert_date: '',
                    insert_id: '',
                    update_id: '',
                    useing: '',
                    is_deleted: '',
                    no: '',
                    pkey: '',
                    tags: '',
                    all_tags: '',
                    post_cnt: '',
                    bgr_name: '',
                    insert_member: '',
                    update_member: '',
                    insert_date_ymd: '',
                    update_date_ymd: '',
                    useing_text: ''
                },
                isEditMode: false,
                categories: <?= json_encode($top_menu['categories']) ?>,
                parentMenus: <?= json_encode($top_menu['parentMenus']) ?>
            };
        },
        methods: {
            updateBoard() {
                //push access_write key / value
                // 변환된 배열
                let board_meta_array = Object.entries(this.detail.board_meta).map(([key, value], index) => {
                    return {
                        brd_id: this.detail.brd_id, // this.detail.brd_id는 실제 값으로 대체해야 합니다.
                        bmt_key: key,
                        bmt_value: value
                    };
                });
                this.detail.board_meta_array = board_meta_array;
                let params = this.createFormData(this.detail);
                

                this.handleAxiosRequest('/api/board-api/update-board', params, '게시판 정보가 성공적으로 업데이트되었습니다.', 'no');
                //$('#modal_save').modal('hide');
            },
            deleteBoard() {
                let params = new FormData();
                params.append('no', this.selectedNo);
                this.handleAxiosRequest('/api/board-api/delete-board', params, '게시판이 성공적으로 삭제되었습니다.', 'no');
                //$('#modal_save').modal('hide');
            },
            createBoard() {
                  //push access_write key / value
                // 변환된 배열
                let board_meta_array = Object.entries(this.detail.board_meta).map(([key, value], index) => {
                    return {
                        brd_id: this.detail.brd_id, // this.detail.brd_id는 실제 값으로 대체해야 합니다.
                        bmt_key: key,
                        bmt_value: value
                    };
                });
                this.detail.board_meta_array = board_meta_array;
                let params = this.createFormData(this.detail);
                this.handleAxiosRequest('/api/board-api/create-board', params, '게시판이 성공적으로 생성되었습니다.', 'no');
                //  $('#modal_save').modal('hide');
            },
            openModal(no, board_group_list) {
                this.selectedNo = no;
                this.group_list = board_group_list;
                console.log("🚀 ~ openModal ~ board_group_list:", this.group_list)

                if (no === null) {
                    this.isEditMode = false;
                    this.common.modal_title = '신규 게시판 등록';
                    this.resetdetails();
                    $('#modal_save').modal('show');
                } else {
                    this.isEditMode = true;
                    this.common.modal_title = '게시판 수정';
                    this.fetchDetails('/api/board-api/get-board-details', no, (data) => {
                        this.detail = data;
                        if (!this.detail.board_meta) {
                            this.detail.board_meta = {};
                        }
                    }, 'no');
                }



            },
            resetdetails() {
                this.detail = {
                    brd_id: '',
                    bgr_id: '',
                    brd_key: '',
                    brd_type: '',
                    brd_form: '',
                    brd_name: '',
                    board_meta: {},
                    brd_mobile_name: '',
                    brd_order: '',
                    brd_search: '',
                    useing: 1,
                    is_deleted: 0,
                    no: '',
                    pkey: '',
                    tags: '',
                    all_tags: '',
                };
            }
        },
        mounted() {

        }
    }).mount('#modal_app');
</script>