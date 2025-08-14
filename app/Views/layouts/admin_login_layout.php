<!DOCTYPE html>
<html lang="ko">
<!-- head start-->

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Facebook -->
	<meta property="og:url" content="https://kelly.unyboard.com">
	<meta property="og:title" content="Kelly">
	<meta property="og:description" content="<?= config('App')->siteTitle ?>">

	<meta property="og:image" content="https://kelly.unyboard.com/assets/img/logo.png">
	<meta property="og:image:secure_url" content="https://kelly.unyboard.com/assets/img/logo.png">
	<meta property="og:image:type" content="image/png">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="600">

	<title> <?= config('App')->siteTitle ?></title>

	<!-- vendor css -->
	<link href="/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
	<link href="/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
	<link href="/lib/fullcalendar/fullcalendar.min.css" rel="stylesheet">
	<link href="/lib/select2/css/select2.min.css" rel="stylesheet">


	<!-- DashForge CSS -->
	<link rel="stylesheet" href="/assets/css/dashforge.css">
	<link rel="stylesheet" href="/assets/css/dashforge.auth.css">

	<link href="/lib/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

	<!-- js script start -->

	<!-- Vue.js 로컬 파일 -->
	<script src="/assets/js/vue.global.js"></script>
	<script src="/lib/axios/dist/axios.js"></script>

	<script src="/lib/fingerprintjs/fp.min.js"></script>

	<script src="/lib/jquery/jquery.min.js"></script>
	<script src="/lib/jqueryui/jquery-ui.min.js"></script>
	<script src="/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="/lib/feather-icons/feather.min.js"></script>
	<script src="/lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script src="/lib/moment/moment.js"></script>
	<script src="/lib/select2/js/select2.min.js"></script>
	<script src="/assets/js/dashforge.js"></script>
	<script src="/lib/toastr/toastr.min.js"></script>
	<!-- js script end -->



</head>
<!-- head end -->

<body>

	<header class="navbar navbar-header navbar-header-fixed">
		<a href="#" id="mainMenuOpen" class="burger-menu"><i data-feather="menu"></i></a>
		<div class="navbar-brand">
			<a href="/admin" class="df-logo">dash<span>forge</span></a>
		</div><!-- navbar-brand -->
		<div id="navbarMenu" class="navbar-menu-wrapper">
			<div class="navbar-menu-header">
				<a href="/admin" class="df-logo">dash<span>forge</span></a>
				<a id="mainMenuClose" href=""><i data-feather="x"></i></a>
			</div><!-- navbar-menu-header -->
			<ul class="nav navbar-menu">
				<li class="nav-label pd-l-20 pd-lg-l-25 d-lg-none">Main Navigation</li>

			</ul>
		</div><!-- navbar-menu-wrapper -->
		<div class="navbar-right">
			<a href="#" class="btn btn-social"><i class="fab fa-dribbble"></i></a>
			<a href="#" class="btn btn-social"><i class="fab fa-github"></i></a>
			<a href="#" class="btn btn-social"><i class="fab fa-twitter"></i></a>
			<a href="#" class="btn btn-buy"><i data-feather="shopping-bag"></i> <span>Buy Now</span></a>
		</div><!-- navbar-right -->
	</header>



	<div class="content content-fixed content-auth">
		<div class="container">
			<?= $this->renderSection('content') ?>
		</div><!-- content-body -->

	</div>

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