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
                            <label for="menuCategory">카테고리</label>
                            <select class="custom-select" v-model="details.category_id">
                                <option value="0">없음</option>
                                <option v-for="category in categories" :value="category.no">{{ category.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="menuParent">상위 메뉴</label>
                            <select class="custom-select" v-model="details.parent_id">
                                <option value="0">없음</option>
                                <option v-for="parentMenu in parentMenus" :value="parentMenu.no">{{ parentMenu.title }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="menuIcon">아이콘</label>
                            <input type="text" class="form-control" id="menuIcon" v-model="details.icon" placeholder="아이콘">
                        </div>
                        <div class="form-group">
                            <label for="menuTitle">제목</label>
                            <input type="text" class="form-control" id="menuTitle" v-model="details.title" placeholder="제목">
                        </div>
                        <div class="form-group">
                            <label for="menuOrder">순서</label>
                            <input type="number" class="form-control" id="menuOrder" v-model="details.order" placeholder="순서">
                        </div>
                        <div class="form-group">
                            <label for="menuUrl">URL</label>
                            <input type="text" class="form-control" id="menuUrl" v-model="details.url" placeholder="URL">
                        </div>
                        <div class="form-group">
                            <label for="menuRoute">Route</label>
                            <input type="text" class="form-control" id="menuRoute" v-model="details.route" placeholder="Route">
                        </div>
                        <div class="form-group">
                            <label for="menuIsShow">노출여부</label>
                            <select class="form-control" id="menuIsShow" v-model="details.is_show">
                                <option value="1">노출</option>
                                <option value="0">비노출</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="menuIsActive">상태</label>
                            <select class="form-control" id="menuIsActive" v-model="details.is_active">
                                <option value="1">활성</option>
                                <option value="0">비활성</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dt_mode">DT Mode</label>
                            <select class="form-control" id="dt_mode" v-model="details.dt_mode">
                                <option value="1">활성</option>
                                <option value="0">비활성</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
                    <button v-if="isEditMode" type="button" class="btn btn-primary tx-13" @click="saveMenu">저장하기</button>
                    <button v-if="isEditMode" type="button" class="btn btn-danger tx-13" @click="deleteMenu">삭제하기</button>
                    <button v-else type="button" class="btn btn-success tx-13" @click="createMenu">추가하기</button>
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
                details: {
                    type: '',
                    category_id: '',
                    parent_id: '',
                    icon: '',
                    title: '',
                    order: '',
                    url: '',
                    route: '',
                    is_active: '',
                    dt_mode: '1',
                    is_show: '1'
                },
                isEditMode: false,
                categories: <?= json_encode($sidebar_menu['categories']) ?>,
                parentMenus: <?= json_encode($sidebar_menu['parentMenus']) ?>
            };
        },
        methods: {
            saveMenu() {
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/menu-api/update-menu', params, '메뉴 정보가 성공적으로 업데이트되었습니다.', 'no');
              //  $('#modal_save').modal('hide');
            },
            deleteMenu() {
                let params = new FormData();
                params.append('no', this.selectedNo);
                this.handleAxiosRequest('/api/menu-api/delete-menu', params, '메뉴가 성공적으로 삭제되었습니다.', 'no');
               // $('#modal_save').modal('hide');
            },
            createMenu() {
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/menu-api/create-menu', params, '메뉴가 성공적으로 생성되었습니다.', 'no');
              //  $('#modal_save').modal('hide');
            },
            openModal(no) {
                this.selectedNo = no;
                if (no === null) {
                    this.isEditMode = false;
                    this.common.modal_title = '신규 메뉴 등록';
                    this.resetdetails();
                    $('#modal_save').modal('show');
                } else {
                    this.isEditMode = true;
                    this.common.modal_title = '메뉴 수정';
                    this.fetchDetails('/api/menu-api/get-menu-details', no, (data) => {
                        this.details = data;
                    }, 'no');
                }
            },
            resetdetails() {
                this.details = {
                    type: 'admin',
                    category_id: null,
                    parent_id: '0',
                    icon: '',
                    title: '',
                    order: '',
                    url: '',
                    route: '',
                    is_active: '1',
                    is_show: '1',
                    dt_mode: '1'
                };
            }
        }
    }).mount('#modal_app');
</script>