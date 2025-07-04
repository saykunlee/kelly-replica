<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="container pd-x-0 ">

    <div id="app">

        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">메뉴관리</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1 ">사이드카테고리 관리</h4>
            </div>
            <div class="d-block d-md-block mg-t-15 mg-lg-t-0">
                <!--                 <button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
                <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="share-2" class="wd-10 mg-r-5"></i> Share</button> -->
                <button class="btn btn-sm pd-x-9 btn-primary mg-l-5  " onclick="openModal(null)"><i data-feather="plus" class="wd-10 mg-r-5"></i> 신규 추가</button>
            </div>
        </div>

        <!-- search area -->
        <div class="search_area">
            <div class="row">
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">타입</label>
                    <select class="custom-select" v-model="search.type">
                        <option value="">전체</option>
                        <option value="admin">관리자</option>
                        <option value="user">사용자</option>
                    </select>
                </div>
                <!-- <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">카테고리</label>
                    <select class="custom-select" v-model="search.category_id">
                        <option value="">전체</option>
                        <option v-for="category in categories" :value="category.no">{{ category.name }}</option>
                    </select>
                </div> -->
                <!-- <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">상위 메뉴</label>
                    <select class="custom-select" v-model="search.parent_id">
                        <option value="">전체</option>
                        <option v-for="parentMenu in parentMenus" :value="parentMenu.no">{{ parentMenu.title }}</option>
                    </select>
                </div> -->
               
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">카테고리명</label>
                    <input type="text" class="form-control" v-model="search.title" placeholder="카테고리명" @keyup.enter="ini_dt">
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">활성화</label>
                    <select class="custom-select" v-model="search.is_active">
                        <option value="">전체</option>
                        <option value="1">활성</option>
                        <option value="0">비활성</option>
                    </select>
                </div>
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
<?= $this->include('admin/menu/modal/sidecate_modal') ?>

<!-- modal end -->


<script>
    const mainApp = Vue.createApp({
        data() {
            return {
                list: [],
                app_name: 'mainApp',
                search: {
                    category_id: '',
                    parent_id: '',
                    is_active: '',
                    type: '',
                    title: ''
                },


                categories: <?= json_encode($sidebar_menu['categories']) ?>,
                parentMenus: <?= json_encode($sidebar_menu['parentMenus']) ?>
            };
        },
        methods: {
            ini_dt() {
                // Add your datatable initialization logic here
                datatableApp.ini_dt('list', this.search);
            }
        },
        mounted() {
            this.ini_dt();
        }

    }).mount('#app')
    // 전역 함수로 openModal 정의
    function openModal(no) {
        modalApp.openModal(no);
    }
</script>


<?= $this->endSection() ?>