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
                <h4 class="mg-b-0 tx-spacing--1 ">로그인기록</h4>
            </div>
            <div class="d-block d-md-block mg-t-15 mg-lg-t-0">
              <!--   <button class="btn btn-sm pd-x-9 btn-primary mg-l-5" onclick="openModal(null)"><i data-feather="plus" class="wd-10 mg-r-5"></i> 사용자추가</button> -->
            </div>
        </div>

        <!-- search area -->
        <div class="search_area">
            <div class="row">
              
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">아이디</label>
                    <input type="text" class="form-control" v-model="search.mll_userid" placeholder="아이디" @keyup.enter="ini_dt">
                </div>

                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">상태</label>
                    <select id="formGroupExampleInput2" class="form-control select2_single" v-model="search.mll_success" @keyup.enter="ini_dt">
                        <option value="">전체</option>
                        <option value="1">성공</option>
                        <option value="0">실패</option>
                    </select>
                </div>
               
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput5" class="d-block">&nbsp;</label>
                    <button id="formGroupExampleInput5" class="btn btn-block pd-x-9 btn-primary" @click="ini_dt"><i data-feather="search" class="wd-10 mg-r-5"></i> 조회</button>
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
<?= $this->include('admin/login/modal/loginlog_modal') ?>
<!-- modal end -->

<script>
    const mainApp = Vue.createApp({
        data() {
            return {
                app_name: 'mainApp',
                list: [],
                selected_groups: [],
                search: {
                    mll_userid: '',
                    mll_success: '',
                    mll_ip: ''
                },
              
                categories: <?= json_encode($sidebar_menu['categories']) ?>,
                groups: <?= json_encode($groups) ?>
            };
        },
        methods: {
            ini_dt() {
                // Add your datatable initialization logic here
                datatableApp.ini_dt('list', this.search, null); // Pass selectedNo to DataTable initialization
               // console.log("🚀 ~ ini_dt ~ this.search:", this.search)
            }
        },
        mounted() {
            this.ini_dt();
        }

    }).mount('#app')
    // 전역 함수로 openModal 정의
    function openModal(no) {
        console.log("🚀 ~ openModal ~ no:", no)
        modalApp.openModal(no);
    }
</script>

<?= $this->endSection() ?>