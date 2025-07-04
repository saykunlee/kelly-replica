<aside class="aside aside-fixed">
    <div class="aside-header">
        <a href="<?= base_url('/') ?>" class="aside-logo"> <i style="font-family: 'IBM Plex Sans';">KELLY</i><span style="font-family: 'IBM Plex Sans';font-weight:700 !important;">Replica</span></a>
        <a href="" class="aside-menu-link">
            <i data-feather="menu"></i>
            <i data-feather="x"></i>
        </a>
    </div>
    </div>
    <div class="aside-body">
        <div class="aside-loggedin">
            <div class="d-flex align-items-center justify-content-start">
                <a href="" class="avatar">
                    <img src="/uploads/<?php echo !empty($_SESSION['mem_photo']) ? $_SESSION['mem_photo'] : 'noimage.jpg'; ?>" class="rounded-circle" alt="">
                </a>

                <div class="aside-alert-link">
                    <!-- <a href="" class="new" data-toggle="tooltip" title="You have 2 unread messages"><i data-feather="message-square"></i></a>
                    <a href="" class="new" data-toggle="tooltip" title="You have 4 new notifications"><i data-feather="bell"></i></a> -->
                    <a href="/logout" data-toggle="tooltip" title="Sign out"><i data-feather="log-out"></i></a>
                </div>
            </div>
            <div class="aside-loggedin-user">

                <a href="#loggedinMenu" class="d-flex align-items-center justify-content-between mg-b-2" data-toggle="collapse">
                    <h6 class="tx-semibold mg-b-0"><?php echo $_SESSION['mem_username']; ?></h6>
                    <i hidden data-feather="chevron-down"></i>
                </a>
                <?php if ($_SESSION['mem_is_developer']) { ?>
                    <p class="tx-color-03 tx-12 mg-b-0">
                        개발자
                    </p>
                <?php } elseif ($_SESSION['mem_is_admin']) { ?>
                    <p class="tx-color-03 tx-12 mg-b-0">
                        운영관리자
                    </p>
                <?php } else { ?>
                    <p class="tx-color-03 tx-12 mg-b-0">
                        일반회원
                    </p>
                <?php } ?>

            </div>
            <div class="collapse" id="loggedinMenu">
                <ul class="nav nav-aside mg-b-0">
                    <!--                     <li class="nav-item"><a href="" class="nav-link"><i data-feather="edit"></i> <span>Edit Profile</span></a></li>
                    <li class="nav-item"><a href="" class="nav-link"><i data-feather="user"></i> <span>View Profile</span></a></li>
                    <li class="nav-item"><a href="" class="nav-link"><i data-feather="settings"></i> <span>Account Settings</span></a></li>
                    <li class="nav-item"><a href="" class="nav-link"><i data-feather="help-circle"></i> <span>Help Center</span></a></li> -->
                    <li class="nav-item"><a href="/logout" class="nav-link"><i data-feather="log-out"></i> <span>Sign Out</span></a></li>
                </ul>
            </div>
        </div><!-- aside-loggedin -->
        <ul class="nav nav-aside">
            <?php foreach ($sidebar_menu['menuTree'] as $category) : ?>
                <li class="nav-label mg-t-25 "><?= $category['name'] ?></li>
                <?php foreach ($category['menus'] as $menu) : ?>
                    <?php
                    // Determine if this menu or any of its submenus is active
                    $isActive = current_url() == base_url($menu['url']);
                    $hasActiveSub = false;
                    // Check if the menu should be shown
                    if ($menu['is_show'] == 1) { // {{ edit_1 }}
                        if (!empty($menu['sub_menus'])) {
                            foreach ($menu['sub_menus'] as $sub_menu) {
                                if (current_url() == base_url($sub_menu['url'])) {
                                    $hasActiveSub = true;
                                    break;
                                }
                            }
                        }
                    } else {
                        continue; // Skip this menu if is_show is not 1
                    }
                    ?>
                    <li class="nav-item <?= !empty($menu['sub_menus']) ? 'with-sub' : '' ?> <?= $isActive || $hasActiveSub ? 'active show' : '' ?>">
                        <a href="<?= base_url($menu['url']) ?>" class="nav-link">
                            <i data-feather="<?= $menu['icon'] ?>"></i>
                            <span><?= $menu['title'] ?></span>
                        </a>
                        <?php if (!empty($menu['sub_menus'])) : ?>
                            <ul>
                                <?php foreach ($menu['sub_menus'] as $sub_menu) : ?>
                                    <?php if ($sub_menu['is_show'] == 1) : // {{ edit_2 }}
                                    ?>
                                        <li class="<?= current_url() == base_url($sub_menu['url']) ? 'active' : '' ?>">
                                            <a href="<?= base_url($sub_menu['url']) ?>"><?= $sub_menu['title'] ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>

    </div>
</aside>
