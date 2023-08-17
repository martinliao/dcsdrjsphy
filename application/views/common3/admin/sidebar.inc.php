<aside class="navbar-default sidebar" role="navigation" style="margin-top:60px;" id="sidebar-wrapper">
    <div class="sidebar-nav  navbar-collapse" id="side-menu">
        <ul class="nav">
            <?php foreach ($_MENU as $menu) { ?>
                <li class="">
                    <?php if (count($menu['sub']) == 0) { ?>
                        <?php if ($menu['link'] == '#') { ?>
                            <a href="#" onclick="return false">
                                <?= " {$menu['name']}"; ?>
                            </a>
                        <?php } else { ?>
                            <a href="<?= base_url($menu['link']); ?>">
                                <?= " {$menu['name']}"; ?>
                            </a>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ($menu['link'] == '#') { ?>
                            <a href="#" onclick="return false">
                                <?= " {$menu['name']}"; ?> <span class="fa arrow"></span>
                            </a>
                        <?php } else { ?>
                            <a href="<?= base_url($menu['link']); ?>">
                                <?= " {$menu['name']}"; ?> <span class="fa arrow"></span>
                            </a>
                        <?php } ?>
                        <ul class="nav nav-second-level">
                            <?php foreach ($menu['sub'] as $item) { ?>
                                <li>
                                    <?php if ($item['link'] == '#') { ?>
                                        <a href="#" onclick="return false">
                                            &emsp;
                                            <?= "{$item['name']}"; ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="<?= base_url($item['link']); ?>">
                                            &emsp;
                                            <?= "{$item['name']}"; ?>
                                        </a>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
<!-- /.sidebar -->
</aside>