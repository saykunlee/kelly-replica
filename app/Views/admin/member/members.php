<?= $this->extend($layout) ?>

<?= $this->section('content') ?>

<div class="container pd-x-0 ">

    <div id="app">

        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">사용자</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1 ">사용자 관리</h4>
            </div>
            <div class="d-block d-md-block mg-t-15 mg-lg-t-0">
                <button class="btn btn-sm pd-x-9 btn-primary mg-l-5" onclick="openModal(null)"><i data-feather="plus" class="wd-10 mg-r-5"></i> 사용자추가</button>
            </div>
        </div>

        <!-- search area -->
        <div class="search_area">
            <div class="row">
                <div class="form-group col-lg-3 col-md-4 col-sm-12 col-12 ">
                    <label for="formGroupExampleInput" class="d-block">그룹</label>
                    <select class="form-control select2_multiple" v-model="search.group_id">
                        <option v-for="group in groups" :value="group.mgr_id">{{ group.mgr_title }}</option>
                    </select>
                </div>

                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">아이디</label>
                    <input type="text" class="form-control" v-model="search.mem_userid" placeholder="아이디">
                </div>

                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">이름</label>
                    <input type="text" class="form-control" v-model="search.mem_username" placeholder="이름">
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput3" class="d-block">최고관리자</label>
                    <select id="formGroupExampleInput3" class="form-control select2_single" v-model="search.mem_is_admin">
                        <option value="">전체</option>
                        <option value="1">예</option>
                        <option value="0">아니오</option>
                    </select>
                </div>
                <div class="form-group col-lg-1 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput4" class="d-block">상태</label>
                    <select id="formGroupExampleInput4" class="form-control select2_single" v-model="search.mem_denied">
                        <option value="">전체</option>
                        <option value="0">승인</option>
                        <option value="1">차단</option>
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput5" class="d-block">&nbsp;</label>
                    <button id="formGroupExampleInput5" class="btn btn-block pd-x-9 btn-primary" @click="ini_dt()"><i data-feather="search" class="wd-10 mg-r-5"></i> 조회</button>
                </div>

            </div>
        </div>
        <!-- search area -->
    </div>


    <?= $this->include('components/datatable_list') ?>

    <div class="<?= $currentMenu['dt_mode'] == 1 ? 'd-block' : 'd-none' ?>">
        <?= $this->include('components/datatable_settings') ?>
    </div>
</div>

<!-- Include modalApp component -->
<?= $this->include('admin/member/modal/member_modal') ?>
<!-- modal end -->

<script>
    const mainApp = Vue.createApp({
        data() {
            return {
                app_name: 'mainApp',
                list: [],
                selected_groups: [],
                search: {
                    mem_userid: '',
                    group_id: [], // Initialize as an array
                    mem_denied: '',
                    mem_is_admin: '',
                    mem_username: '',
                    mem_nickname: ''
                },
                useCheckboxColumn: true, // Flag to use checkbox column
                selectedItems: [], // Array to store selected items
                categories: <?= json_encode($sidebar_menu['categories']) ?>,
                groups: <?= json_encode($groups) ?>
            };
        },
        methods: {
            ini_dt() {
                datatableApp.ini_dt('list', this.search); // Pass the flag to DataTable initialization
                //  console.log("🚀 ~ ini_dt ~ this.search:", this.search)
            },
            handleCheckboxChange(item) {
                const index = this.selectedItems.indexOf(item);
                if (index > -1) {
                    this.selectedItems.splice(index, 1);
                } else {
                    this.selectedItems.push(item);
                }
            },
            handleSelectAll(event) {
                const isChecked = event.target.checked;
                this.selectedItems = isChecked ? this.list.map(item => item.no) : [];
                this.$nextTick(() => {
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                });
            }
        },
        mounted() {
            // URL 파라미터에서 'b' 값을 확인
            const urlParams = new URLSearchParams(window.location.search);
            const backFromDetail = urlParams.get('b');

            const placeholderText = <?php
                                    if (isset($view_data['member_residence_country'])) {
                                        switch ($view_data['member_residence_country']) {
                                            case 803:
                                                echo "'전체'";
                                                break;
                                            case 708:
                                                echo "'全て'";
                                                break;
                                            case 832:
                                                echo "'ALL'";
                                                break;
                                            default:
                                                echo "''";
                                                break;
                                        }
                                    } else {
                                        echo "''";
                                    }
                                    ?>;
            if (backFromDetail) {
                // 'b' 값이 존재하면 세션 스토리지 값을 유지
                const storedSearch = sessionStorage.getItem('search');
                if (storedSearch) {
                    this.search = JSON.parse(storedSearch);
                    var select2_multiple = $('.select2_multiple').select2({
                        placeholder: <?php
                                        if (isset($view_data['member_residence_country'])) {
                                            switch ($view_data['member_residence_country']) {
                                                case 803:
                                                    echo "'전체'";
                                                    break;
                                                case 708:
                                                    echo "'全て'";
                                                    break;
                                                case 832:
                                                    echo "'ALL'";
                                                    break;
                                                default:
                                                    echo "''";
                                                    break;
                                            }
                                        } else {
                                            echo "''";
                                        }
                                        ?>,
                        allowClear: true,
                        multiple: "multiple",
                    });
                }
                //pageLength
                datatableApp.pageLength = sessionStorage.getItem('pageLength');
                //currentPage
                datatableApp.startPage = sessionStorage.getItem('startPage');
            } else {
                // 'b' 값이 없으면 세션 스토리지 값을 리셋
                sessionStorage.removeItem('search');
                sessionStorage.removeItem('pageLength');
                sessionStorage.removeItem('startPage');
                datatableApp.pageLength = null;
                datatableApp.startPage = null;
            }

            this.ini_dt();
        }
    }).mount('#app')
    // 전역 함수로 openModal 정의
    function openModal(no) {
        modalApp.openModal(no);
    }
</script>

<?= $this->endSection() ?>