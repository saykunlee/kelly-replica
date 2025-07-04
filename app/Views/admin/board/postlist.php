<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="container pd-x-0 ">

    <div id="app">
        <?= $this->include('components/breadcrumb') ?>

        <hr class="mg-t-10 mg-b-20">

        <!-- search area -->
        <div class="search_area">
            <div class="row">
                <div class="form-group col-lg-3 col-md-3 col-sm-6 col-6">
                    <label for="bgr_id">게시판</label>
                    <select class="form-control" id="bgr_id" v-model="search.brd_id" placeholder="게시판">
                        <option value="">전체</option>
                        <option v-for="item in board_list" :value="item.brd_id">{{ item.brd_name }}</option>
                    </select>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">글제목</label>
                    <input type="text" class="form-control" v-model="search.post_title" placeholder="글제목">
                </div>


               <!--  <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">사용여부</label>
                    <select class="custom-select" v-model="search.useing">
                        <option value="">전체</option>
                        <option value="1">사용</option>
                        <option value="0">미사용</option>
                    </select>
                </div> -->
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">&nbsp;</label>
                    <button class="btn btn-block pd-x-9 btn-primary" @click="ini_dt()"><i data-feather="search" class="wd-10 mg-r-5"></i> 조회</button>
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
<?= $this->include('admin/board/modal/board_modal') ?>

<!-- modal end -->


<script>
    const mainApp = Vue.createApp({
        data() {
            return {
                list: [],
                app_name: 'mainApp',


                board_group_list: <?= json_encode($board_group_list) ?>,
                board_list: <?= json_encode($board_list) ?>,
                search: {
                    brd_id: '',
                    post_title: '',
                    post_titleType: 'like',

                },
                categories: <?= json_encode($top_menu['categories']) ?>,
                parentMenus: <?= json_encode($top_menu['parentMenus']) ?>
            };
        },
        methods: {
            ini_dt() {
                // Add your datatable initialization logic here
                datatableApp.ini_dt('list', this.search);
            },

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
        console.log("🚀 ~ openModal ~ this.board_group_list:", <?= json_encode($board_group_list) ?>)
        
        modalApp.openModal(no,<?= json_encode($board_group_list) ?>);
    }
</script>


<?= $this->endSection() ?>