<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/dashforge.profile.css') . '?ver=' . time() ?>">

<div class="container pd-x-0 tx-13">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1 mg-b-5">
            <li class="breadcrumb-item"><a href="#">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">환경설정</li>
        </ol>
    </nav>
    <h4 class="mg-b-25">기본환경설정</h4>

    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <ul class="nav nav-line nav-line-profile mg-b-30">
                <li class="nav-item">
                    <a href="" class="nav-link d-flex align-items-center active">Followers <span class="badge">340</span></a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Following <span class="badge">1,563</span></a>
                </li>
                <li class="nav-item d-none d-sm-block">
                    <a href="" class="nav-link">Request <span class="badge">19</span></a>
                </li>
            </ul>

            <!-- Profile List -->
            <div id="profile-component" data-props='<?= $componentData ?>'></div>
            <div id="Following-component" data-props='<?= $componentData ?>'></div>
            <div id="Request-component" data-props='<?= $componentData ?>'></div>



            <button class="btn btn-block btn-sm btn-white">Load more</button>
        </div><!-- col -->

        <div class="col-lg-4 col-xl-3 mg-t-40 mg-lg-t-0">
            <div id="right-bar" data-props='<?= $componentData ?>'></div>
        </div><!-- col -->
    </div>
</div>
<script src="<?= base_url('build/admin/bundle.js') ?>"></script><!-- row -->


<?= $this->endSection() ?>