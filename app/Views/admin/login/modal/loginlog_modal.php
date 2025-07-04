<div id="modal_app">
    <!-- modal start -->
    <div class="modal fade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content tx-14">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel2">{{ common.modal_title }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="details">
                        <form id="loginLogForm" data-parsley-validate>
                            <!-- Login Log Information -->
                            <fieldset class="form-fieldset">
                                <legend>로그인 로그 정보</legend>
                                <div class="form-row">
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_userid">사용자 ID</label>
                                        <input type="text" class="form-control" id="mll_userid" v-model="details.mll_userid" placeholder="사용자 ID" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_datetime">로그인 시간</label>
                                        <input type="text" class="form-control" id="mll_datetime" v-model="details.mll_datetime" placeholder="로그인 시간" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_ip">IP 주소</label>
                                        <input type="text" class="form-control" id="mll_ip" v-model="details.mll_ip" placeholder="IP 주소" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_success">성공 여부</label>
                                        <input type="text" class="form-control" id="mll_success" :value="successStatus" placeholder="성공 여부" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_fail_reason">실패 이유</label>
                                        <input type="text" class="form-control" id="mll_fail_reason" v-model="details.mll_fail_reason" placeholder="실패 이유" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_device_fingerprint">장치 식별자</label>
                                        <input type="text" class="form-control" id="mll_device_fingerprint" v-model="details.mll_device_fingerprint" placeholder="장치 식별자" disabled>
                                    </div>
                                    <div class="col-12 col-lg-12 form-group">
                                        <label for="mll_useragent">사용자 에이전트</label>
                                        <textarea class="form-control" id="mll_useragent" v-model="details.mll_useragent" placeholder="사용자 에이전트" disabled></textarea>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_device_type">장치 유형</label>
                                        <input type="text" class="form-control" id="mll_device_type" v-model="details.mll_device_type" placeholder="장치 유형" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_os">운영 체제</label>
                                        <input type="text" class="form-control" id="mll_os" v-model="details.mll_os" placeholder="운영 체제" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_browser">브라우저</label>
                                        <input type="text" class="form-control" id="mll_browser" v-model="details.mll_browser" placeholder="브라우저" disabled>
                                    </div>
                                    <div class="col-12 col-lg-6 form-group">
                                        <label for="mll_location">위치</label>
                                        <input type="text" class="form-control" id="mll_location" v-model="details.mll_location" placeholder="위치" disabled>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">닫기</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    <?= $this->include('components/modal_common.js') ?>
    const modalApp = Vue.createApp({
        mixins: [modalCommon],
        data() {
            return {
                common: {
                    modal_title: ''
                },
                details: {},
                selectedNo: null,
                isEditMode: false
            };
        },
        computed: {
            successStatus() {
                return this.details.mll_success === '1' ? '성공' : '실패';
            }
        },
        methods: {
            openModal(selectedNo) {
                console.log("🚀 ~ openModal ~ selectedNo:", selectedNo)
                this.selectedNo = selectedNo;
                if (selectedNo === null) {
                    this.isEditMode = false;
                    this.common.modal_title = '신규 등록';
                    this.resetDetails();
                    $('#modal_save').modal('show');
                } else {
                    this.isEditMode = true;
                    this.common.modal_title = '로그인 로그 상세';
                  
                    this.fetchDetails('/api/member-api/get-log-detail', selectedNo, (data) => {
                        this.details = data;
                        
                        $('#modal_save').modal('show');
                    }, 'mll_id');
                }
                // Initialize Parsley validation
                $('#loginLogForm').parsley().reset();
            },
           
            resetDetails() {
                this.details = {
                    mll_userid: '',
                    mll_datetime: '',
                    mll_ip: '',
                    mll_success: '',
                    mll_fail_reason: '',
                    mll_useragent: '',
                    mll_device_type: '',
                    mll_os: '',
                    mll_browser: '',
                    mll_location: ''
                };
            }
        }
    }).mount('#modal_app');
</script>