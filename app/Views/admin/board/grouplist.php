<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="container pd-x-0 ">

    <div id="app">
        <?= $this->include('components/breadcrumb') ?>

        <hr class="mg-t-10 mg-b-20">

        <!-- search area -->
        <div class="search_area">
            <div class="row">

                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">게시판그룹명</label>
                    <input type="text" class="form-control" v-model="search.bgr_name" placeholder="게시판 그룹명">
                </div>

                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">사용여부</label>
                    <select class="custom-select" v-model="search.useing">
                        <option value="">전체</option>
                        <option value="1">사용</option>
                        <option value="0">미사용</option>
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
<?= $this->include('admin/board/modal/boardgroup_modal') ?>

<!-- modal end -->


<script>
    const mainApp = Vue.createApp({
        data() {
            return {
                list: [],
                app_name: 'mainApp',
                
                
                
                search: {
                        bgr_id: '',
                        bgr_key: '',
                        bgr_name: '',
                        bgr_nameType: 'like',
                        bg_count: '',
                        bgr_order: '',
                        update_date: '',
                        insert_date: '',
                        insert_id: '',
                        update_id: '',
                        useing: '',
                        is_deleted: 0,
                        no: '',
                        pkey: '',
                        insert_member: '',
                        update_member: '',
                        insert_date_ymd: '',
                        update_date_ymd: '',
                        useing_text: ''
                    
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
            this.ini_dt();
        }

    }).mount('#app')

    // 전역 함수로 openModal 정의
    function openModal(no) {
        modalApp.openModal(no);
    }
</script>


<?= $this->endSection() ?>