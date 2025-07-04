<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<!-- ckeditor css -->
<link href="/lib/ckeditor5/ck_styles.css" rel="stylesheet">
<!-- ckeditor5 -->
<script src="/lib/ckeditor5/build/ckeditor.js"></script>


<div class="container pd-x-0 ">
    <div id="app">
        <?= $this->include('components/breadcrumb') ?>
        <!-- form area -->
        <hr class="mg-t-10 mg-b-20">
        <form id="postForm" data-parsley-validate>
            <div class="row">
                <div class="col-lg-3 d-flex">
                    <div class="form-group flex-fill">
                        <label>게시판<span class="tx-danger">*</span></label>
                        <select class="form-control" v-model="detail.brd_id" required data-parsley-required-message="게시판을 선택해주세요.">
                            <option value="">선택하세요</option>
                            <option v-for="(code,idx) in board_list" :value="code.brd_id">{{code.brd_name}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-7 d-flex">
                    <!-- 제목 post_title-->
                    <div class="form-group flex-fill">
                        <label>제목<span class="tx-danger">*</span></label>
                        <input type="text" class="form-control " placeholder="제목 입력" v-model="detail.post_title" required data-parsley-required-message="제목을 입력해주세요.">
                    </div>
                </div>
                <div class="col-lg-2 d-flex justify-content-between">
                    <!-- 노출여부 -->
                    <div class="form-group flex-fill">
                        <label>노출여부<span class="tx-danger"></span></label>
                        <select class="form-control select2_single" v-model="detail.useing">
                            <option value="0">미노출</option>
                            <option value="1">노출</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="row">

                <div class="col-lg-3 d-flex">
                    <!-- 작성자명 -->
                    <div class="form-group flex-fill">
                        <label>작성자명</label>
                        <input type="text" class="form-control " placeholder="제목 입력" v-model="detail.post_nickname">
                    </div>
                </div>
                <div class="col-lg-3 d-flex">
                    <!-- 작성일자 -->
                    <div class="form-group flex-fill">
                        <label>작성일자</label>
                        <input type="date" class="form-control " v-model="detail.insert_date_ymd"> <!-- 수정이가능한 작성일자 -->
                    </div>
                </div>
            </div>
            <!-- 에디터 -->
            <div class="row">
                <div class="col-lg-12 ">
                    <div class="form-group ">
                        <!--에디터 영역-->
                        <div class="board_write_area">

                            <div class="editor" v-html="detail.post_content"></div>
                        </div>
                        <!--! 에디터 영역-->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 d-flex">
                    <!-- 비밀번호 -->
                    <div class="form-group flex-fill d-flex align-items-center">
                        <div class="flex-fill">
                            <label>비밀번호</label>
                            <input type="text" class="form-control" placeholder="비밀번호" v-model="detail.post_password">
                            <div class="tx-13 mg-t-5 tx-left tx-danger">※ 패스워드 재설정시 '1324'로 설정됩니다. </div>
                        </div>
                        <button  class="btn btn-primary mt-1 ml-2">패스워드 재설정</button>
                    </div>
                </div>
            </div>
        </form>
        <hr class="mg-t-40 mg-b-20">

        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <!-- 왼쪽 정렬 버튼 -->
            <a class="btn btn-secondary mb-2 mb-md-0" @click="back_to_list('/admin/board/manage-posts')" href="javascript:void()"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> 뒤로가기</a>

            <!-- 오른쪽 정렬 버튼 그룹 -->
            <div class="d-flex button-group">
                <button class="btn btn-danger btn-spacing" href="#modal_delete" data-toggle="modal"><i data-feather="trash-2" class="wd-10 mg-r-5"></i> 삭제</button>
                <button class="btn btn-success btn-spacing" @click="savePost"><i data-feather="save" class="wd-10 mg-r-5"></i> 저장</button>

            </div>
        </div>

        <div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-14">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">삭제</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mg-b-0">삭제하시겠습니까?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal"><i data-feather="x" class="wd-10 mg-r-5"></i>취소</button>
                        <button type="button" class="btn btn-danger tx-13" @click="deletePost"><i data-feather="trash-2" class="wd-10 mg-r-5"></i>삭제</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<script>
    <?= $this->include('components/form_common.js') ?>
    const mainApp = Vue.createApp({
        mixins: [formCommon],
        data() {
            return {
                app_name: 'mainApp',
                board_group_list: <?= json_encode($board_group_list) ?>,
                board_list: <?= json_encode($board_list) ?>,
                isEditMode: <?= json_encode($isEditMode) ?>,
                detail: {
                    brd_id: '',
                    post_title: '',
                    post_content: '',
                    post_password: '',
                    
                    useing: '',
                    post_nickname: '',
                    insert_date_ymd: '',
                    is_deleted: 0,
                    is_temp: 0,
                    
                },



            };
        },

        computed: {
            computedPostPassword() {
                return this.detail.post_password === '' || this.detail.post_password === null ?
                    '1324' :
                    this.detail.post_password;
            }
        },

        methods: {
            
            deletePost() {
                let params = new FormData();
                params.append('post_id', this.detail.no);
                this.postData('/api/board-api/delete-post', params, false)
                    .then((response) => {
                        if (response.status == '200') {
                            // close all modal
                            $('.modal').modal('hide');
                            // go to list page after 1 second
                            setTimeout(() => {
                                window.location.href = '/admin/board/manage-posts';
                            }, 1000);
                        } else {
                            //toastr.error(response.message);
                        }
                    });
            },
            savePost() {
                this.detail.post_password = this.computedPostPassword;
                this.detail.post_datetime = new Date(this.detail.insert_date_ymd).toISOString();
                this.validateAndSave({
                    formId: 'postForm',
                    updateUrl: '/api/board-api/update-post',
                    createUrl: '/api/board-api/create-post',
                    dataObject: this.detail,
                    isEditMode: this.isEditMode,
                    callback: (response) => {
                        if (response) {
                            if (!this.isEditMode) {
                                if (response.data.id) {
                                    setTimeout(() => {
                                        window.location.href = '/admin/board/detail-post/' + response.data.id;
                                    }, 1000);
                                } else {
                                    //go to list page
                                    window.location.href = '/admin/board/manage-posts';
                                    alert('응답 데이터 또는 게시글 ID가 누락되었습니다.');
                                }
                            }
                        } else {
                            alert('응답 데이터 또는 게시글 ID가 누락되었습니다.');
                            console.error('응답 데이터 또는 게시글 ID가 누락되었습니다.');
                        }
                    }
                });
            },
        },

        mounted() {
            this.detail = <?= json_encode($post) ?>;
            //ck editor
            const watchdog = new CKSource.EditorWatchdog();

            window.watchdog = watchdog;

            // CKEditor 인스턴스 생성
            watchdog.setCreator((element, config) => {
                return CKSource.Editor.create(element, {
                    ...config,
                    removePlugins: ['Title', 'Style', 'Markdown'],

                    allowedContent: true,
                    entities: false,
                    htmlSupport: {
                        allow: [{
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }]
                    },
                    simpleUpload: {
                        // 업로드 URL 설정
                        uploadUrl: '<?= base_url('Upload_ckeditor') ?>', // 실제 업로드 엔드포인트
                        headers: {
                            'X-CSRF-TOKEN': 'CSRF-Token',
                        }
                    },

                }).then(editor => {
                    editor.model.document.on('change:data', () => {
                        this.detail.post_content = editor.getData();
                    });
                    return editor;
                });
            });

            watchdog.setDestructor(editor => {
                return editor.destroy();
            });

            watchdog.on('error', handleSampleError);

            watchdog
                .create(document.querySelector('.editor'), {
                    allowedContent: true, // 모든 HTML 콘텐츠 허용
                    // Editor configuration.
                })
                .catch(handleSampleError);

            function handleSampleError(error) {
                const issueUrl = 'https://github.com/ckeditor/ckeditor5/issues';

                const message = [
                    'Oops, something went wrong!',
                    `Please, report the following error on ${issueUrl} with the build id "h7cgchhmy4pu-yiy1nzdnn5n2" and the error stack trace:`
                ].join('\n');

                console.error(message);
                console.error(error);
            }

        },

    }).mount('#app')
</script>


<?= $this->endSection() ?>