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
                    <div v-if="details">
                        <div class="form-group">
                            <label for="bgrKey">그룹코드</label>
                            <input type="text" class="form-control" id="bgrKey" v-model="details.bgr_key" placeholder="그룹코드" required>
                        </div>
                        <div class="form-group">
                            <label for="bgrName">그룹명</label>
                            <input type="text" class="form-control" id="bgrName" v-model="details.bgr_name" placeholder="그룹명" required>
                        </div>
                        <div class="form-group">
                            <label for="bgrOrder">정렬순서</label>
                            <input type="number" class="form-control" id="bgrOrder" v-model="details.bgr_order" placeholder="정렬순서" required>
                        </div>
                        <div class="form-group">
                            <label for="useing">사용 여부</label>
                            <select class="form-control" id="useing" v-model="details.useing" required>
                                <option value="1">사용</option>
                                <option value="0">미사용</option>
                            </select>
                        </div>                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">닫기</button>
                    <button v-if="isEditMode" type="button" class="btn btn-primary tx-13" @click="updateBoardGroup">수정하기</button>
                    <button v-if="isEditMode" type="button" class="btn btn-danger tx-13" @click="deleteBoardGroup">삭제하기</button>
                    <button v-else type="button" class="btn btn-success tx-13" @click="createBoardGroup">추가하기</button>
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
                    modal_title: '게시판 그룹 수정'
                },
                selectedNo: null,
                details: {
                    bgr_id: '',
                    bgr_key: '',
                    bgr_name: '',
                    bgr_nameType: 'like',
                    bg_count: '',
                    bgr_order: '',
                    update_date: '',
                    insert_date: '',
                    insert_id: '',
                    update_id: '',
                    useing: '',
                    is_deleted: '',
                    no: '',
                    pkey: '',
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
            updateBoardGroup() {
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/board-api/update-board-group', params, '게시판 그룹 정보가 성공적으로 업데이트되었습니다.', 'no');
                //$('#modal_save').modal('hide');
            },
            deleteBoardGroup() {
                let params = new FormData();
                params.append('no', this.selectedNo);
                this.handleAxiosRequest('/api/board-api/delete-board-group', params, '게시판 그룹이 성공적으로 삭제되었습니다.', 'no');
                //$('#modal_save').modal('hide');
            },
            createBoardGroup() {
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/board-api/create-board-group', params, '게시판 그룹이 성공적으로 생성되었습니다.', 'no');
              //  $('#modal_save').modal('hide');
            },
            openModal(no) {
                this.selectedNo = no;
                if (no === null) {
                    this.isEditMode = false;
                    this.common.modal_title = '신규 게시판 그룹 등록';
                    this.resetdetails();
                    $('#modal_save').modal('show');
                } else {
                    this.isEditMode = true;
                    this.common.modal_title = '게시판 그룹 수정';
                    this.fetchDetails('/api/board-api/get-board-group-details', no, (data) => {
                        this.details = data;
                    }, 'no');
                }
            },
            resetdetails() {
                this.details = {
                    type: 'admin',
                    category_id: '0',
                    parent_id: '0',
                    icon: '',
                    title: '',
                    order: '',
                    url: '',
                    route: '',
                    is_active: '1',
                    dt_mode: 1
                };
            }
        }
    }).mount('#modal_app');
</script>