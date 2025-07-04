<div class="d-sm-flex align-items-center justify-content-between mg-b-15 mg-lg-b-15">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <?php
                $maxDepth = 3; // 최대 표시 단계
                $currentDepth = 0; // 현재 깊이 초기화
                foreach ($breadcrumb as $item):
                    if ($currentDepth >= $maxDepth) break; // 최대 깊이에 도달하면 루프 종료
                    $currentDepth++;
                ?>
                    <li class="breadcrumb-item <?= $item['url'] ? '' : 'active' ?>" <?= $item['url'] ? '' : 'aria-current="page"' ?>>
                        <?php if ($item['url']): ?>
                            <a class="tx-11" href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                        <?php else: ?>
                            <span class="tx-11"><?= $item['title'] ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1 "><?= end($breadcrumb)['title'] ?></h4>
    </div>
    <?php if ($show_add_button): ?>
        <div class="d-block d-md-block mg-t-15 mg-lg-t-0">
            <button class="btn btn-sm pd-x-9 btn-primary mg-l-5" onclick="openModal(null)"><i data-feather="plus" class="wd-10 mg-r-5"></i> 추가</button>
        </div>
    <?php endif; ?>
    <?php if ($show_add_button_page): ?>
        <div class="d-block d-md-block mg-t-15 mg-lg-t-0">
            <a class="btn btn-sm pd-x-9 btn-primary mg-l-5" href="<?= $add_button_page ?>"><i data-feather="plus" class="wd-10 mg-r-5"></i> 추가하기</a>
        </div>
    <?php endif; ?>
</div>