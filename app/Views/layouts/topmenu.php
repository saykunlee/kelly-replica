<header class="navbar navbar-header navbar-header-fixed">
    <a href="" id="mainMenuOpen" class="burger-menu"><i data-feather="menu"></i></a>
    <div class="navbar-brand">
        <a href="<?= base_url('/') ?>" class="df-logo">hight<span>class</span></a>
    </div><!-- navbar-brand -->
    <div id="navbarMenu" class="navbar-menu-wrapper">
        <div class="navbar-menu-header">
            <a href="<?= base_url('/') ?>" class="df-logo">hight<span>class</span></a>
            <a id="mainMenuClose" href=""><i data-feather="x"></i></a>
        </div><!-- navbar-menu-header -->
        <ul class="nav navbar-menu">
            <li class="nav-label pd-l-20 pd-lg-l-25 d-lg-none">Main Navigation</li>
            <?php foreach ($top_menu['menuTree'] as $category): ?>
                <!-- if name is SingleMenus -->
                <?php if ($category['name'] == 'SingleMenus'): ?>
                    <?php foreach ($category['menus'] as $menu): ?>
                        <?php if ($menu['is_visible'] == 1): ?>
                        <li class="nav-item">
                            <a href="<?= esc($menu['url']) ?>" class="nav-link">

                                <?= esc($menu['title']) ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>

                    <li class="nav-item with-sub">
                        <!-- 카테고리 -->
                        <a href="#" class="nav-link">
                            <i data-feather="<?= esc($category['icon'] ?? 'layers') ?>"></i> <?= esc($category['name']) ?>
                        </a>
                        <!-- /카테고리 -->
                        <?php if (!empty($category['menus']) && count($category['menus']) > 0 && count($category['menus']) <= 1): ?>
                            <?php foreach ($category['menus'] as $menu): ?>
                                <ul class="navbar-menu-sub">
                                    <?php if (!empty($menu['sub_menus'])): ?>
                                        <?php foreach ($menu['sub_menus'] as $subMenu): ?>
                                            <?php if ($subMenu['is_visible'] == 1): ?>
                                            <li class="nav-sub-item">
                                                <a href="<?= esc($subMenu['url']) ?>" class="nav-sub-link">
                                                    <i data-feather="<?= esc($subMenu['icon'] ?? 'file-text') ?>"></i>
                                                    <?= esc($subMenu['title']) ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- 서브카테고리 형식 -->
                            <div class="navbar-menu-sub">
                                <div class="d-lg-flex">
                                    <ul>
                                        <?php $index = 0; ?>
                                        <?php foreach ($category['menus'] as $menu): ?>
                                            <li class="<?= $index > 0 ? 'nav-label mg-t-20' : 'nav-label' ?>"><?= esc($menu['title']) ?></li>
                                            <?php if (!empty($menu['sub_menus'])): ?>
                                                <?php foreach ($menu['sub_menus'] as $subMenu): ?>
                                                    <?php if ($subMenu['is_visible'] == 1): ?>
                                                    <li class="nav-sub-item">
                                                        <a href="<?= esc($subMenu['url']) ?>" class="nav-sub-link">
                                                            <i data-feather="<?= esc($subMenu['icon'] ?? 'file-text') ?>"></i>
                                                            <?= esc($subMenu['title']) ?>
                                                        </a>
                                                    </li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <?php $index++; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- /서브카테고리 형식 -->
                        <?php endif; ?>

                    </li>
                <?php endif; ?>


            <?php endforeach; ?>

        </ul>
    </div><!-- navbar-menu-wrapper -->
</header><!-- navbar -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
   // console.log('menuTree',<?php echo json_encode($menu); ?>);
</script>