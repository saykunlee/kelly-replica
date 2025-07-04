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
                            <label for="menuType">타입</label>
                            <select class="form-control" id="menuType" v-model="details.type">
                                <option value="admin">관리자</option>
                                <option value="user">사용자</option>
                            </select>
                        </div>
                       
                        <div class="form-group">
                            <label for="menuTitle">카테고리명</label>
                            <input type="text" class="form-control" id="menuTitle" v-model="details.name" placeholder="카테고리명">
                        </div>
                        <div class="form-group">
                            <label for="menuOrder">순서</label>
                            <input type="number" class="form-control" id="menuOrder" v-model="details.order" placeholder="순서">
                        </div>
                       
                        <div class="form-group">
                            <label for="menuIsActive">상태</label>
                            <select class="form-control" id="menuIsActive" v-model="details.is_active">
                                <option value="1">활성</option>
                                <option value="0">비활성</option>
                            </select>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
                    <button v-if="isEditMode" type="button" class="btn btn-primary tx-13" @click="save">저장하기</button>
                    <button v-if="isEditMode" type="button" class="btn btn-danger tx-13" @click="deleteDetails">삭제하기</button>
                    <button v-else type="button" class="btn btn-success tx-13" @click="create">추가하기</button>
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
                    modal_title: '메뉴 수정'
                },
                selectedNo: null,
                /**
                 * 
                 *  no         int AUTO_INCREMENT
                    PRIMARY KEY,
                    name       varchar(255)                 NOT NULL,
                    `order`    int                          NOT NULL,
                    type       varchar(100) DEFAULT 'admin' NOT NULL,
                    created_by int                          NOT NULL,
                    created_at datetime                     NOT NULL,
                    updated_by int                          NULL,
                    updated_at datetime                     NULL,
                    is_deleted tinyint(1)   DEFAULT 0       NOT NULL,
                    is_active  tinyint(1)   DEFAULT 1       NOT NULL
                 */
                details: {
                    no: '',
                    name: '',
                    order: '',
                    type: '',
                    created_by: '',
                    created_at: '',
                    updated_by: '',
                    updated_at: '',
                    is_deleted: '',
                    is_active: '',
                },
                isEditMode: false,
                isEditMode: false,
            
            };
        },
        methods: {
            save() {
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/menu-api/update-topcate', params, '메뉴 정보가 성공적으로 업데이트되었습니다.', 'no');
                $('#modal_save').modal('hide');
            },
            deleteDetails() {
                let params = new FormData();
                params.append('no', this.selectedNo);
                this.handleAxiosRequest('/api/menu-api/delete-topcate', params, '메뉴가 성공적으로 삭제되었습니다.', 'no');
                $('#modal_save').modal('hide');
            },
            create() {
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/menu-api/create-topcate', params, '메뉴가 성공적으로 생성되었습니다.', 'no');
                $('#modal_save').modal('hide');
            },
            openModal(no) {
                this.selectedNo = no;
                if (no === null) {
                    this.isEditMode = false;
                    this.common.modal_title = '신규 탑메뉴 카테고리 등록';
                    this.resetdetails();
                    $('#modal_save').modal('show');
                } else {
                    this.isEditMode = true;
                    this.common.modal_title = '탑메뉴 카테고리 수정';
                    this.fetchDetails('/api/menu-api/get-topcate-details', no, (data) => {
                        this.details = data;
                    }, 'no');
                }
            },
            resetdetails() {
                this.details = {
                    no: '',
                    name: '',
                    order: '',
                    type: 'admin',
                    created_by: '',
                    created_at: '',
                    updated_by: '',
                    updated_at: '',
                    is_deleted: 0,
                    is_active: 1,
                };
            }
        }
    }).mount('#modal_app');
</script>