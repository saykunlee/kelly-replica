<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="container pd-x-0 ">

    <div id="app">
        <?= $this->include('components/breadcrumb') ?>

        <hr class="mg-t-10 mg-b-20">

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

                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">카테고리</label>
                    <select class="form-control select2_multiple" v-model="search.category_id">
                        <option v-for="category in categories" :value="category.no">{{ category.name }}</option>
                    </select>
                </div>

                <!--  <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">카테고리</label>
                    <select class="custom-select" v-model="search.category_id">
                        <option value="">전체</option>
                        <option v-for="category in categories" :value="category.no">{{ category.name }}</option>
                    </select>
                </div> -->
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput" class="d-block">상위 메뉴</label>
                    <select class="custom-select" v-model="search.parent_id">
                        <option value="">전체</option>
                        <option v-for="parentMenu in parentMenus" :value="parentMenu.no">{{ parentMenu.title }}</option>
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">제목</label>
                    <input type="text" class="form-control" v-model="search.title" placeholder="제목" @keyup.enter="ini_dt">
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="select_single1" class="d-block">노출여부</label>
                    <select id="select_single1" class="form-control select2_single" v-model="search.is_show">
                        <option value="">전체</option>
                        <option value="1">노출</option>
                        <option value="0">비노출</option>
                    </select>
                </div>
                <div class="form-group col-lg-1 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">활성화</label>
                    <select class="custom-select" v-model="search.is_active">
                        <option value="">전체</option>
                        <option value="1">활성</option>
                        <option value="0">비활성</option>
                    </select>
                </div>

                <div class="form-group col-lg-1 col-md-4 col-sm-6 col-6">
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
<?= $this->include('admin/menu/modal/sidemenu_modal') ?>

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
                    title: '',
                    is_show: ''
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