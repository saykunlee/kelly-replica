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
                        <form id="memberForm" data-parsley-validate>
                            <!-- User Information -->
                            <fieldset class="form-fieldset">
                                <legend>사용자 정보</legend>

                                <div class="form-row">
                                    <div class="col-12 col-lg-6 form-group" v-if="isEditMode">
                                        <label for="mem_userid">사용자 ID</label>
                                        <input type="text" class="form-control" id="mem_userid" v-model="details.mem_userid" placeholder="사용자 ID" required data-parsley-required-message="사용자 ID를 입력하세요." :disabled="isEditMode">
                                    </div>
                                    <div class="col-9 col-lg-4 form-group" v-else>
                                        <label for="mem_userid">사용자 ID</label>
                                        <input type="text" class="form-control" id="mem_userid" v-model="details.mem_userid" placeholder="사용자 ID" required data-parsley-required-message="사용자 ID를 입력하세요." :disabled="isEditMode">
                                    </div>
                                    <div class="col-3 col-lg-2 form-group" v-if="!isEditMode">
                                        <div class="mg-t-10 mg-sm-t-25 mg-xs-t-25 pd-t-4">
                                            <button type="button" class="btn btn-primary pd-x-20 tx-12" @click="checkUserId" :disabled="!isUserIdModified">ID 확인</button>
                                        </div>
                                    </div>

                                    <div class="col-8 col-lg-4 form-group">
                                        <label for="mem_email">이메일</label>
                                        <input type="email" class="form-control" id="mem_email" v-model="details.mem_email" placeholder="이메일" required data-parsley-type="email" data-parsley-required-message="이메일을 입력하세요." data-parsley-type-message="유효한 이메일 주소를 입력하세요.">
                                    </div>
                                    <div class="col-4 col-lg-2 form-group">
                                        <div class="mg-t-10 mg-sm-t-25 mg-xs-t-25 pd-t-4">
                                            <button type="button" class="btn btn-primary pd-x-20 tx-12" @click="validateEmail" :disabled="!isEmailModified">E-mail 확인</button>
                                        </div>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="mem_password">비밀번호</label>
                                        <input type="password" class="form-control" id="mem_password" v-model="details.mem_password" @input="onPasswordInputChange" placeholder="비밀번호" required data-parsley-required-message="비밀번호를 입력하세요.">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_username">사용자 이름</label>
                                        <input type="text" class="form-control" id="mem_username" v-model="details.mem_username" placeholder="사용자 이름" required data-parsley-required-message="사용자 이름을 입력하세요.">
                                    </div>

                                    <div class="col-8 col-lg-4 form-group">
                                        <label for="mem_nickname">닉네임</label>
                                        <input type="text" class="form-control" id="mem_nickname" v-model="details.mem_nickname" placeholder="닉네임" required data-parsley-required-message="닉네임을 입력하세요.">
                                    </div>
                                    <div class="col-4 col-lg-2 form-group">
                                        <div class="mg-t-10 mg-sm-t-25 mg-xs-t-25 pd-t-4">
                                            <button type="button" class="btn btn-primary pd-x-20 tx-12" @click="validateNickname" :disabled="!isNicknameModified">닉네임 확인</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_sex">성별</label>
                                        <select class="form-control" id="mem_sex" v-model="details.mem_sex" required data-parsley-required-message="성별을 선택하세요.">
                                            <option value="0">남성</option>
                                            <option value="1">여성</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="mem_icon">아이콘</label>
                                        <input type="text" class="form-control" id="mem_icon" v-model="details.mem_icon" placeholder="아이콘">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_photo">사진</label>
                                        <input type="text" class="form-control" id="mem_photo" v-model="details.mem_photo" placeholder="사진">
                                    </div>
                                </div>

                            </fieldset>
                            <fieldset class="form-fieldset mg-t-20">
                                <legend>연락처 정보</legend>
                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label for="mem_phone">전화번호</label>
                                        <input type="text" class="form-control" id="mem_phone" v-model="details.mem_phone" placeholder="전화번호" required data-parsley-required-message="전화번호를 입력하세요.">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_zipcode">우편번호</label>
                                        <input type="text" class="form-control" id="mem_zipcode" v-model="details.mem_zipcode" placeholder="우편번호" required data-parsley-required-message="우편번호를 입력하세요.">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="mem_address1">주소 1</label>
                                        <input type="text" class="form-control" id="mem_address1" v-model="details.mem_address1" placeholder="주소 1" required data-parsley-required-message="주소 1을 입력하세요.">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_address2">주소 2</label>
                                        <input type="text" class="form-control" id="mem_address2" v-model="details.mem_address2" placeholder="주소 2" required data-parsley-required-message="주소 2를 입력하세요.">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="mem_address3">주소 3</label>
                                        <input type="text" class="form-control" id="mem_address3" v-model="details.mem_address3" placeholder="주소 3">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_address4">주소 4</label>
                                        <input type="text" class="form-control" id="mem_address4" v-model="details.mem_address4" placeholder="주소 4">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-fieldset mg-t-20">
                                <legend>추가 정보</legend>
                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label for="mem_level">레벨</label>
                                        <input type="number" class="form-control" id="mem_level" v-model="details.mem_level" placeholder="레벨" required data-parsley-required-message="레벨을 입력하세요.">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_point">포인트</label>
                                        <input type="number" class="form-control" id="mem_point" v-model="details.mem_point" placeholder="포인트" required data-parsley-required-message="포인트를 입력하세요.">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="mem_homepage">홈페이지</label>
                                        <input type="text" class="form-control" id="mem_homepage" v-model="details.mem_homepage" placeholder="홈페이지">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_birthday">생일</label>
                                        <input type="date" class="form-control" id="mem_birthday" v-model="details.mem_birthday" placeholder="생일" required data-parsley-required-message="생일을 입력하세요.">
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label for="mem_profile_content">프로필 내용</label>
                                        <textarea class="form-control" id="mem_profile_content" v-model="details.mem_profile_content" placeholder="프로필 내용" required data-parsley-required-message="프로필 내용을 입력하세요."></textarea>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label for="mem_adminmemo">관리자 메모</label>
                                        <textarea class="form-control" id="mem_adminmemo" v-model="details.mem_adminmemo" placeholder="관리자 메모"></textarea>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-fieldset mg-t-40">
                                <legend>수신 설정</legend>
                                <div class="form-row">
                                    <div class="col-md-6 form-group">
                                        <label for="mem_open_profile">공개 여부</label>
                                        <select class="form-control" id="mem_open_profile" v-model="details.mem_open_profile" required data-parsley-required-message="공개 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_receive_email">이메일 수신</label>
                                        <select class="form-control" id="mem_receive_email" v-model="details.mem_receive_email" required data-parsley-required-message="이메일 수신 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="mem_use_note">쪽지 사용</label>
                                        <select class="form-control" id="mem_use_note" v-model="details.mem_use_note" required data-parsley-required-message="쪽지 사용 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_receive_sms">SMS 수신</label>
                                        <select class="form-control" id="mem_receive_sms" v-model="details.mem_receive_sms" required data-parsley-required-message="SMS 수신 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-fieldset mg-t-20">
                                <legend>권한설정</legend>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label class="d-block">사용자 그룹</label>
                                        <div class="d-flex flex-wrap">

                                            <div v-for="group in groups_modal" :key="group.mgr_id" class="form-check mr-3">
                                                <input class="form-check-input" type="checkbox" :id="'group_' + group.mgr_id" :value="group.mgr_id" v-model="details.group_id">
                                                <label class="form-check-label" :for="'group_' + group.mgr_id">
                                                    {{ group.mgr_title }}
                                                </label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="mem_is_admin">관리자 여부</label>
                                        <select class="form-control" id="mem_is_admin" v-model="details.mem_is_admin" required data-parsley-required-message="관리자 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="mem_is_developer">개발자 여부</label>
                                        <select class="form-control" id="mem_is_developer" v-model="details.mem_is_developer" required data-parsley-required-message="개발자 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="mem_denied">차단</label>
                                        <select class="form-control" id="mem_denied" v-model="details.mem_denied" required data-parsley-required-message="차단 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="mem_email_cert">이메일 인증</label>
                                        <select class="form-control" id="mem_email_cert" v-model="details.mem_email_cert" required data-parsley-required-message="이메일 인증 여부를 선택하세요.">
                                            <option value="0">아니오</option>
                                            <option value="1">예</option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">닫기</button>
                    <button v-if="isEditMode" type="button" class="btn btn-primary tx-13" @click="validateAndSave" :disabled="!canSave">저장</button>
                    <button v-if="isEditMode" type="button" class="btn btn-danger tx-13" @click="deleteMember">삭제</button>
                    <button v-else type="button" class="btn btn-success tx-13" @click="validateAndSave" :disabled="!canSave">생성</button>
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
                app_name: 'modalApp',
                common: {
                    modal_title: '회원 정보 수정'
                },
                selectedNo: null,
                originalUserId: '',
                details: {
                    mem_userid: '',
                    mem_email: '',
                    mem_password: '',
                    mem_username: '',
                    mem_nickname: '',
                    mem_level: 0,
                    mem_point: 0,
                    mem_homepage: '',
                    mem_phone: '',
                    mem_birthday: '',
                    mem_sex: 0,
                    mem_zipcode: '',
                    mem_address1: '',
                    mem_address2: '',
                    mem_address3: '',
                    mem_address4: '',
                    mem_receive_email: 0,
                    mem_use_note: 0,
                    mem_receive_sms: 0,
                    mem_open_profile: 0,
                    mem_denied: 0,
                    mem_email_cert: 0,
                    mem_register_datetime: '',
                    mem_register_ip: '',
                    mem_lastlogin_datetime: '',
                    mem_lastlogin_ip: '',
                    mem_is_admin: 0,
                    mem_is_developer: 0,
                    mem_profile_content: '',
                    mem_adminmemo: '',
                    mem_following: 0,
                    mem_followed: 0,
                    mem_icon: '',
                    mem_photo: '',
                    group_id: []
                },
                isEditMode: false,
                groups_modal: <?= json_encode($groups) ?>,
                isPasswordModified: false,
                isUserIdAvailable: false,
                isEmailAvailable: false,
                isNicknameAvailable: false,
            };
        },
        computed: {
            isUserIdModified() {
                return this.details.mem_userid !== this.originalUserId;
            },
            isEmailModified() {
                return this.details.mem_email !== this.originalEmail;
            },
            isNicknameModified() {
                return this.details.mem_nickname !== this.originalNickname;
            },

            canSave() {
                if (this.isUserIdModified && this.isUserIdAvailable === false) {
                    return false;
                }
                if (this.isEmailModified && this.isEmailAvailable === false) {
                    return false;
                }
                if (this.isNicknameModified && this.isNicknameAvailable === false) {
                    return false;
                }
                return true;
            }

        },
        watch: {
            'details.mem_userid'(newVal) {
                const parsleyField = $('#mem_userid').parsley();

                // 중복 체크 메시지가 이미 표시되었는지 확인하는 변수
                if (!this.userIdErrorDisplayed) {
                    if (newVal === this.originalUserId) {
                        this.isUserIdAvailable = true;
                        parsleyField.removeError('userIdTaken');
                        this.userIdErrorDisplayed = false; // 에러 메시지 초기화
                    } else {
                        parsleyField.addError('userIdTaken', {
                            message: '아이디 중복체크를 해주세요',
                            updateClass: true
                        });
                        this.isUserIdAvailable = false;
                        this.userIdErrorDisplayed = true; // 에러 메시지 표시됨
                    }
                }
            },
            'details.mem_email'(newVal) {
                const parsleyField = $('#mem_email').parsley();

                // 이메일 중복 체크 메시지가 이미 표시되었는지 확인하는 변수
                if (!this.emailErrorDisplayed) {
                    if (newVal === this.originalEmail) {
                        this.isEmailAvailable = true;
                        parsleyField.removeError('emailTaken');
                        this.emailErrorDisplayed = false; // 에러 메시지 초기화
                    } else {
                        parsleyField.addError('emailTaken', {
                            message: '이메일 중복체크를 해주세요',
                            updateClass: true
                        });
                        this.isEmailAvailable = false;
                        this.emailErrorDisplayed = true; // 에러 메시지 표시됨
                    }
                }
            },

            'details.mem_nickname'(newVal) {
                const parsleyField = $('#mem_nickname').parsley();

                // 닉네임 중복 체크 메시지가 이미 표시되었는지 확인하는 변수
                if (!this.nicknameErrorDisplayed) {
                    if (newVal === this.originalNickname) {
                        this.isNicknameAvailable = true;
                        parsleyField.removeError('nicknameTaken');
                        this.nicknameErrorDisplayed = false; // 에러 메시지 초기화
                    } else {
                        parsleyField.addError('nicknameTaken', {
                            message: '닉네임 중복체크를 해주세요',
                            updateClass: true
                        });
                        this.isNicknameAvailable = false;
                        this.nicknameErrorDisplayed = true; // 에러 메시지 표시됨
                    }
                }
            }
        },
        methods: {
            openModal(selectedNo) {
                this.selectedNo = selectedNo;
                if (selectedNo === null) {
                    this.isEditMode = false;
                    this.common.modal_title = '신규 회원 등록';
                    this.resetdetails();
                    $('#modal_save').modal('show');
                } else {
                    this.isEditMode = true;
                    this.common.modal_title = '회원 정보 수정';
                    this.fetchDetails('/api/member-api/get-member-details', selectedNo, (data) => {
                        this.details = data;
                        this.originalUserId = data.mem_userid;
                        this.originalEmail = data.mem_email;
                        this.originalNickname = data.mem_nickname; // Add this line
                        $('#modal_save').modal('show');
                    }, 'mem_id');
                }
                // Initialize Parsley validation
                $('#memberForm').parsley().reset();
            },
            validateAndSave() {
                const form = document.getElementById('memberForm');
                const parsleyForm = $(form).parsley();
                const parsleyUserIdField = $('#mem_userid').parsley();
                const parsleyEmailField = $('#mem_email').parsley();
                const parsleyNicknameField = $('#mem_nickname').parsley(); // Add this line

                // ID 중복 체크가 완료되지 않은 경우 메시지 추가
                if (this.isUserIdModified && !this.isUserIdAvailable) {
                    parsleyUserIdField.addError('mem_userid', {
                        message: '아이디 중복체크를 해주세요',
                        updateClass: true
                    });
                    return;
                } else {
                    parsleyUserIdField.removeError('mem_userid');
                }

                // 이메일 중복 체크가 완료되지 않은 경우 메시지 추가
                if (this.isEmailModified && !this.isEmailAvailable) {
                    parsleyEmailField.addError('mem_email', {
                        message: '이메일 중복체크를 해주세요',
                        updateClass: true
                    });
                    return;
                } else {
                    parsleyEmailField.removeError('mem_email');
                }

                // 닉네임 중복 체크가 완료되지 않은 경우 메시지 추가
                if (this.isNicknameModified && !this.isNicknameAvailable) {
                    parsleyNicknameField.addError('mem_nickname', {
                        message: '닉네임 중복체크를 해주세요',
                        updateClass: true
                    });
                    return;
                } else {
                    parsleyNicknameField.removeError('mem_nickname');
                }

                if (parsleyForm.validate()) {
                    if (this.isEditMode) {
                        this.saveMember();
                    } else {
                        this.createMember();
                    }
                }
            },
            validateNickname() {
                let params = new FormData();
                params.append('mem_nickname', this.details.mem_nickname);
                axios.post('/api/member-api/check-nickname', params)
                    .then(response => {
                        const parsleyField = $('#mem_nickname').parsley();
                        if (response.data.isAvailable) {
                            this.isNicknameAvailable = true;
                            parsleyField.removeError('nicknameTaken');
                            toastr.success('사용 가능한 닉네임입니다.');

                            console.log("🚀 ~ canSave ~ this.isUserIdModified:", this.isUserIdModified);
                            console.log("🚀 ~ canSave ~ this.isUserIdAvailable:", this.isUserIdAvailable);
                            console.log("🚀 ~ canSave ~ this.isEmailModified:", this.isEmailModified);
                            console.log("🚀 ~ canSave ~ this.isEmailAvailable:", this.isEmailAvailable);
                            console.log("🚀 ~ canSave ~ this.isNicknameModified:", this.isNicknameModified);
                            console.log("🚀 ~ canSave ~ this.isNicknameAvailable:", this.isNicknameAvailable);

                        } else {
                            this.isNicknameAvailable = false;
                            parsleyField.addError('nicknameTaken', {
                                message: '이미 사용중인 닉네임 입니다.',
                                updateClass: true
                            });
                            toastr.error('이미 사용 중인 닉네임입니다.');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking nickname:', error);
                        this.isNicknameAvailable = false;
                        toastr.error('Error checking nickname.');
                    });
            },
            checkUserId() {
                let params = new FormData();
                params.append('mem_userid', this.details.mem_userid);
                axios.post('/api/member-api/check-user-id', params)
                    .then(response => {
                        const parsleyField = $('#mem_userid').parsley();
                        if (response.data.isAvailable) {
                            this.isUserIdAvailable = true;
                            parsleyField.removeError('userIdTaken');
                            toastr.success('사용 가능한 ID입니다.');
                        } else {
                            this.isUserIdAvailable = false;
                            parsleyField.addError('userIdTaken', {
                                message: '이미 사용중인 아이디 입니다.',
                                updateClass: true
                            });
                            toastr.error('이미 사용 중인 ID입니다.');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking user ID:', error);
                        this.isUserIdAvailable = false;
                        toastr.error('Error checking user ID.');
                    });
            },
            validateEmail() {
                let params = new FormData();
                params.append('mem_email', this.details.mem_email);
                axios.post('/api/member-api/check-email', params)
                    .then(response => {
                        const parsleyField = $('#mem_email').parsley();
                        if (response.data.isAvailable) {
                            this.isEmailAvailable = true;
                            parsleyField.removeError('emailTaken');
                            toastr.success('사용 가능한 이메일입니다.');
                        } else {
                            this.isEmailAvailable = false;
                            parsleyField.addError('emailTaken', {
                                message: '이미 사용중인 이메일 입니다.',
                                updateClass: true
                            });
                            toastr.error('이미 사용 중인 이메일입니다.');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking email:', error);
                        this.isEmailAvailable = false;
                        toastr.error('Error checking email.');
                    });
            },
            saveMember() {
                console.log('Save button clicked, selected ID:', this.selectedNo);
                //isPasswordModified 가 true 이면 비밀번호를 전송하고 그렇지 않으면 빈값으로 전송한다.
                if (!this.isPasswordModified) {
                    this.details.mem_password = '';
                }
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/member-api/update-member', params, '회원 정보가 성공적으로 업데이트되었습니다.', 'mem_id');
            },
            createMember() {
                console.log('Create button clicked');
                let params = this.createFormData(this.details);
                this.handleAxiosRequest('/api/member-api/create-member', params, '회원이 성공적으로 생성되었습니다.', 'mem_id');
            },
            deleteMember() {
                console.log('Delete button clicked, selected ID:', this.selectedNo);

                let params = new FormData();
                params.append('mem_id', this.selectedNo);
                this.handleAxiosRequest('/api/member-api/delete-member', params, '회원이 성공적으로 삭제되었습니다.', 'mem_id');
            },

            onPasswordInputChange() {
                this.isPasswordModified = true;
            },
            resetdetails() {
                this.details = {
                    mem_userid: '',
                    mem_email: '',
                    mem_password: '',
                    mem_username: '',
                    mem_nickname: '',
                    mem_level: 0,
                    mem_point: 0,
                    mem_homepage: '',
                    mem_phone: '',
                    mem_birthday: '',
                    mem_sex: 0,
                    mem_zipcode: '',
                    mem_address1: '',
                    mem_address2: '',
                    mem_address3: '',
                    mem_address4: '',
                    mem_receive_email: 0,
                    mem_use_note: 0,
                    mem_receive_sms: 0,
                    mem_open_profile: 0,
                    mem_denied: 0,
                    mem_email_cert: 0,
                    mem_register_datetime: '',
                    mem_register_ip: '',
                    mem_lastlogin_datetime: '',
                    mem_lastlogin_ip: '',
                    mem_is_admin: 0,
                    mem_profile_content: '',
                    mem_adminmemo: '',
                    mem_following: 0,
                    mem_followed: 0,
                    mem_icon: '',
                    mem_photo: '',
                    group_id: []
                };
                this.originalUserId = '';
                this.originalEmail = '';
                this.originalNickname = ''; // Add this line
                this.isPasswordModified = false;
                this.isUserIdAvailable = false;
                this.isEmailAvailable = false;
                this.isNicknameAvailable = false; // Add this line
            }
        }
    }).mount('#modal_app');
</script>