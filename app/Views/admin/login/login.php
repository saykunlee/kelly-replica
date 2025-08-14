<?= $this->extend($layout) ?>

<?= $this->section('content') ?>

<div id="app">

    <div class="media align-items-stretch justify-content-center ht-100p pos-relative">
        <div class="media-body align-items-center d-none d-lg-flex">
            <div class="mx-wd-600">
                <img src="/assets/img/login/graphic3.svg" class="img-fluid" alt="">
            </div>
            <div class="pos-absolute b-0 l-0 tx-12 tx-center">

            </div>
        </div><!-- media-body -->
        <div class="sign-wrapper mg-lg-l-50 mg-xl-l-60">
            <div class="wd-100p">
                <h3 class="tx-color-01 mg-b-5">Sign In</h3>
                <p class="tx-color-03 tx-16 mg-b-40">Welcome back! Please sign in to continue to Kelly Admin.</p>

                <?php if (isset($validationResponse) && $validationResponse['status'] !== 'ok'): ?>
                    <div class="alert alert-danger">
                        <?php if (isset($validationResponse['error_message'])): ?>
                            <?= $validationResponse['error_message'] ?>

                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?= form_open('/login') ?>
                <div class="form-group">
                    <label>User ID</label>
                    <input type="hidden" name="url" class="form-control" v-model="search.url">
                    <input type="hidden" name="device_fingerprint" class="form-control" v-model="search.device_fingerprint">
                    <input type="text" name="mem_userid" class="form-control" placeholder="Enter your User ID" value="<?= set_value('mem_userid') ?>" autocomplete="username">
                </div>
                <div class="form-group position-relative">
                    <label class="mg-b-0-f">Password</label>
                    <input type="password" id="password" name="mem_password" class="form-control" placeholder="Enter your password" autocomplete="current-password">
                    <span id="togglePassword" class="position-absolute tx-color-03" style="right: 10px; top: 30px; cursor: pointer;">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <button type="submit" class="btn btn-brand-02 btn-block">Sign In</button>
                <?= form_close() ?>
                <div class="divider-text">or</div>
                <!-- <button class="btn btn-outline-facebook btn-block">Sign In With Facebook</button>
                <button class="btn btn-outline-twitter btn-block">Sign In With Twitter</button> -->
                <!-- apple -->
                <button class="btn btn-outline-secondary btn-block">Sign In With Apple</button>
                <!-- google -->
                <button class="btn btn-outline-primary btn-block">Sign In With Google</button>
                <div class="tx-13 mg-t-20 tx-center">Don't have an account? <a href="mailto:sunvalley-admin@sunvalley-clark.com">Request an account</a></div>
            </div>
        </div><!-- sign-wrapper -->
    </div>
</div>

<script>
    const mainApp = Vue.createApp({
        data() {
            return {
                app_name: 'mainApp',
                list: [],
                selected_groups: [],
                search: {
                    mem_userid: '',
                    group_id: [], // Initialize as an array
                    mem_denied: '',
                    mem_is_admin: '',
                    mem_username: '',
                    mem_nickname: '',
                    url: '',
                    device_fingerprint: '',
                },
            };
        },
        methods: {},
        mounted() {

            //get url parameter
            const urlParams = new URLSearchParams(window.location.search);
            this.search.url = urlParams.get('url');
            
            // FingerprintJS 인스턴스 생성
            const fpPromise_vue = FingerprintJS.load();

            fpPromise_vue.then(fp => fp.get()).then(result => {
                this.search.device_fingerprint = result.visitorId; // 고유한 장치 지문
                console.log(this.search.device_fingerprint); // 장치 지문을 콘솔에 출력
            });

            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.querySelector('#togglePassword');
                const password = document.querySelector('#password');
                const icon = togglePassword.querySelector('i');

                togglePassword.addEventListener('click', function() {
                    // Toggle the type attribute
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // Toggle the icon class
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            });
        }
    }).mount('#app')
</script>

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?= $this->endSection() ?>