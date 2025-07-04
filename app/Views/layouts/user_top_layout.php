<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 4 Dashboard Template">
    <meta name="author" content="ThemePixels">
    <title>User Dashboard</title>

    <!-- vendor css -->
    <link href="<?= base_url('lib/@fortawesome/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('lib/ionicons/css/ionicons.min.css') ?>" rel="stylesheet">

    <!-- DashForge CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/unyict_font.css') . '?ver=' . time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashforge.css') . '?ver=' . time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashforge.dashboard.css') . '?ver=' . time() ?>">
    
    <link id="dfMode" rel="stylesheet" href="<?= base_url('assets/css/skin.light.css') ?>">
    
</head>

<body class="page-profile">
    <?= $this->include('layouts/topmenu') ?>


    <?= $this->renderSection('content') ?>




    <footer class="footer">
        <div>
            <span>&copy; 2019 DashForge v1.0.0. </span>
            <span>Created by <a href="http://themepixels.me">ThemePixels</a></span>
        </div>
        <div>
            <nav class="nav">
                <a href="https://themeforest.net/licenses/standard" class="nav-link">Licenses</a>
                <a href="../../change-log.html" class="nav-link">Change Log</a>
                <a href="https://discordapp.com/invite/RYqkVuw" class="nav-link">Get Help</a>
            </nav>
        </div>
    </footer>

    <?= $this->renderSection('scripts') ?>
    <script src="<?= base_url('lib/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('lib/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('lib/feather-icons/feather.min.js') ?>"></script>
    <script src="<?= base_url('lib/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('lib/chart.js/Chart.bundle.min.js') ?>"></script>
    <script src="<?= base_url('lib//jquery.flot/jquery.flot.js') ?>"></script>
    <script src="<?= base_url('lib/jquery.flot/jquery.flot.stack.js') ?>"></script>
    <script src="<?= base_url('lib/jquery.flot/jquery.flot.resize.js') ?>"></script>

    <script src="<?= base_url('assets/js/dashforge.js') ?>"></script>
    <script src="<?= base_url('assets/js/dashforge.sampledata.js') ?>"></script>
    <script src="<?= base_url('assets/js/dashboard-two.js') ?>"></script>
    <script src="<?= base_url('lib/js-cookie/js.cookie.js') ?>"></script>
    

    <!-- append theme customizer -->

    
</body>

</html>