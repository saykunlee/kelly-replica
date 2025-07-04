<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= config('App')->siteTitle ?>">
    <meta name="author" content="UNYICT">
    <title><?= config('App')->siteTitle ?>-Admin</title>

    <!-- vendor css -->
    <link href="<?= base_url('lib/@fortawesome/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/ionicons/css/ionicons.min.css') ?>" rel="stylesheet">

    <link href="<?= base_url('lib/datatables.net-dt/css/jquery.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/dataTables.buttons/css/buttons.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/select2/css/select2.min.css')  ?>" rel="stylesheet">


    <!-- DashForge CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/unyict_font.css') . '?ver=' . time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashforge.css') . '?ver=' . time() ?>">
    <!-- <link rel="stylesheet" href="<?= base_url('assets/css/dashforge.dashboard.css') ?>"> -->

    <!-- DashForge Skin -->
    <link id="dfMode" rel="stylesheet" href="<?= base_url('assets/css/skin.light.css') ?>">

    <link href="<?= base_url('lib/toastr/toastr.min.css') ?>" rel="stylesheet" type="text/css" />

    <!-- Vue.js 로컬 파일 -->
    <script src="<?= base_url('assets/js/vue.global.js') ?>"></script>
    <script src="<?= base_url('/lib/axios/dist/axios.js') ?>"></script>
    <!-- vender -->
    <script src="<?= base_url('lib/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('lib/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('lib/feather-icons/feather.min.js') ?>"></script>
    <script src="<?= base_url('lib/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('lib/prismjs/prism.js') ?>"></script>
    <script src="<?= base_url('lib/js-cookie/js.cookie.js') ?>"></script>
    <!-- <script src="<?= base_url('assets/js/dashforge.settings.js') ?>"></script> -->
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
    <script src="<?= base_url('lib/parsleyjs/parsley.min.js') ?>"></script>
    <script src="<?= base_url('lib/parsleyjs/i18n/ko.js') ?>"></script>
    <script src="<?= base_url('lib/fingerprintjs/fp.min.js') ?>"></script>
    <script src="<?= base_url('lib/toastr/toastr.min.js') ?>"></script>
    <!-- dashforge -->
    <script src="<?= base_url('assets/js/dashforge.js') ?>"></script>
    <script src="<?= base_url('assets/js/dashforge.aside.js') ?>"></script>

</head>

<body class="page-profile">

    <?= $this->include('layouts/sidebar') ?>

    <div class="content ht-100v pd-0">
        <div class="content-header">
            <div class="content-search">
                <i data-feather="search"></i>
                <input type="search" class="form-control" placeholder="Search">
            </div>
            <nav class="nav">
                <a href="" class="nav-link"><i data-feather="help-circle"></i></a>
                <a href="" class="nav-link"><i data-feather="grid"></i></a>
                <a href="" class="nav-link"><i data-feather="align-left"></i></a>
            </nav>
        </div><!-- content-header -->

        <div class="content-body">
            <?= $this->renderSection('content') ?>
        </div><!-- content-body -->
    </div><!-- content -->





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
            placeholder: '전체',
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
        //console session data
        console.log('session', <?= json_encode($_SESSION) ?>);
    </script>
    <script>
        // FingerprintJS 인스턴스 생성
        const fpPromise = FingerprintJS.load();

        fpPromise.then(fp => fp.get()).then(result => {
            const visitorId = result.visitorId; // 고유한 장치 지문
        //console.log(visitorId); // 장치 지문을 콘솔에 출력
        });
    </script>
</body>

</html>