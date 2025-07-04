<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 4 Dashboard Template">
    <meta name="author" content="ThemePixels">
    <title>Kelly Project -Admin</title>

    <!-- vendor css -->
    <link href="<?= base_url('lib/@fortawesome/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/ionicons/css/ionicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/select2/css/select2.min.css')  ?>" rel="stylesheet">
    <link href="<?= base_url('lib/datatables.net-dt/css/jquery.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/dataTables.buttons/css/buttons.dataTables.min.css') ?>" rel="stylesheet">

    <!-- DashForge CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/unyict_font.css') . '?ver=' . time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashforge.css') . '?ver=' . time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashforge.dashboard.css') . '?ver=' . time() ?>">

    <link id="dfMode" rel="stylesheet" href="<?= base_url('assets/css/skin.light.css') ?>">
    <link href="<?= base_url('lib/toastr/toastr.min.css') ?>" rel="stylesheet" type="text/css" />

    <!-- Vue.js 로컬 파일 -->
    <script src="<?= base_url('assets/js/vue.global.js') ?>"></script>
    <script src="<?= base_url('/lib/axios/dist/axios.js') ?>"></script>
    <!-- vendor -->
    <script src="<?= base_url('lib/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('lib/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('lib/feather-icons/feather.min.js') ?>"></script>
    <script src="<?= base_url('lib/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('lib/prismjs/prism.js') ?>"></script>
    <script src="<?= base_url('lib/js-cookie/js.cookie.js') ?>"></script>
    <script src="<?= base_url('lib/select2/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('lib/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('lib/datatables.net-dt/js/dataTables.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('lib/datatables.net-responsive/js/dataTables.responsive.min.js') ?>"></script>
    <script src="<?= base_url('lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('lib/dataTables.buttons/js/dataTables.buttons.min.js') ?>"></script>
    <script src="<?= base_url('lib/jszip/jszip.min.js') ?>"></script>
    <script src="<?= base_url('lib/pdfmake/pdfmake.min.js') ?>"></script>
    <script src="<?= base_url('lib/pdfmake/vfs_fonts.js') ?>"></script>
    <script src="<?= base_url('lib/buttons.html5/buttons.html5.min.js') ?>"></script>
    <script src="<?= base_url('lib/buttons.print/buttons.print.min.js') ?>"></script>
    <script src="<?= base_url('lib/toastr/toastr.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/dashforge.js') ?>"></script>
    <script src="<?= base_url('assets/js/dashforge.aside.js') ?>"></script>
</head>

<body>
    <?= $this->include('layouts/topmenu') ?>

    <div class="content content-fixed">
        <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
            <?= $this->renderSection('content') ?>
        </div><!-- content-body -->

    </div>

    <footer class="footer pd-40 tx-11 ">
        <div>
            <nav class="nav tx-12 mg-b-20">
                <a href="https://themeforest.net/licenses/standard" class="nav-link">공지사항</a>
                <a href="../../change-log.html" class="nav-link">이용약관</a>
                <a href="https://discordapp.com/invite/RYqkVuw" class="nav-link tx-bold">개인정보처리방침</a>
                <a href="https://discordapp.com/invite/RYqkVuw" class="nav-link">해외여행자보험</a>
            </nav>

            <p>사업자등록번호 : 128-36-66269 | 관광사업자번호 : 2014-000007호 | 여행보증보험 : 제 100-000-2023 0101 9819호 | 통신판매신고 : 2011-경기고양-2221호 | 대표 : 정민석
                <br>주소 : 경기도 고양시 덕양구 행신동 952번지 세신훼밀리타운 402호
                <br>필리핀사무소 : Lot 7 Block 21 Amazon St, Brgy. Anunas, Riverside Friendship Angeles City, Philippines 2009
                <br>개인정보관리책임자 : 임대호 | FAX : 031-938-3320
                <br>메일주소 : 2013clark@naver.com | 고객센터 : 070-7732-1169
            </p>
            <span class="">Copyright&copy; 인앤인 All Rights Reserved </span>
        </div>
        <div>
            <nav class="nav">
                <a href="https://themeforest.net/licenses/standard" class="nav-link">카카오톡</a>
                <a href="../../change-log.html" class="nav-link">네이버밴드</a>
                <a href="https://discordapp.com/invite/RYqkVuw" class="nav-link">youtube</a>
            </nav>
        </div>
    </footer>

    <?= $this->renderSection('scripts') ?>
    <script>
        $(document).ready(function() {
            // 여기에 다른 초기화 .
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            //on load 

            //console all data from controller
            console.info('view_data', <?= json_encode($view_data) ?>);

            // PerfectScrollbar 초기화
            var aside = document.querySelector('.aside');
            if (aside) {
                new PerfectScrollbar(aside);
            } else {
                //console.error('No element found to initialize PerfectScrollbar');
            }
        });

        var select2_multiple = $('.select2_multiple').select2({
            placeholder: '전체',
            allowClear: true,
            multiple: "multiple",
        });
        select2_multiple.on('select2:select', function(e) {
            let event = new Event('change');
            e.target.dispatchEvent(event);
        });

        select2_multiple.on('select2:unselect', function(e) {
            let event = new Event('change');
            e.target.dispatchEvent(event);
        });

        var select2_single = $('.select2_single').select2({
            placeholder: '선택하세요',
            allowClear: true,
        });
        select2_single.on('select2:select', function(e) {
            let event = new Event('change');
            e.target.dispatchEvent(event);
        });

        select2_single.on('select2:unselect', function(e) {
            let event = new Event('change');
            e.target.dispatchEvent(event);
        });
    </script>

</body>

</html>