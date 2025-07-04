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
                    <label for="bgr_id">게시판그룹</label>
                    <select class="form-control" id="bgr_id" v-model="search.bgr_id" placeholder="게시판그룹">
                        <option value="">전체</option>
                        <option v-for="item in board_group_list" :value="item.bgr_id">{{ item.bgr_name }}</option>
                    </select>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-6">
                    <label for="formGroupExampleInput2" class="d-block">게시판명</label>
                    <input type="text" class="form-control" v-model="search.brd_name" placeholder="게시판명">
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
<?= $this->include('admin/board/modal/board_modal') ?>

<!-- modal end -->


<script>
    const mainApp = Vue.createApp({
        data() {
            return {
                list: [],
                app_name: 'mainApp',


                board_group_list: <?= json_encode($board_group_list) ?>,
                search: {
                    brd_id: '',
                    bgr_id: '',
                    brd_key: '',
                    brd_type: '',
                    brd_form: '',
                    brd_name: '',
                    brd_nameType: 'like',
                    brd_mobile_name: '',
                    brd_order: '',
                    brd_search: '',
                    update_date: '',
                    insert_date: '',
                    insert_id: '',
                    update_id: '',
                    useing: '',
                    is_deleted: '',
                    no: '',
                    pkey: '',
                    tags: '',
                    all_tags: '',
                    post_cnt: '',
                    bgr_name: '',
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
        console.log("🚀 ~ openModal ~ this.board_group_list:", <?= json_encode($board_group_list) ?>)
        
        modalApp.openModal(no,<?= json_encode($board_group_list) ?>);
    }
</script>


<?= $this->endSection() ?>