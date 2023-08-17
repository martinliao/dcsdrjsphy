    <nav class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin-bottom: 0">
        <?= $navbar ?>
        <!-- Main Sidebar Container -->
        <?= $sidebar ?>
        <!-- Main Sidebar Container --> <!-- Ignore it. May2023 -->
    </nav>
    <!-- Page Content -->
    <div id="page-wrapper" > <!-- id="page-wrapper" -->
        <div class="page-header" >
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-7">
                        <h1 class="m-0"><?= $_LOCATION['name']; ?></h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>#">Home</a></li>
                            <?php if (isset($_LOCATION['parent'])) { ?>
                            <li class="breadcrumb-item"><?= $_LOCATION['parent']['name']; ?></li>
                            <?php } ?>
                            <li class="breadcrumb-item active"><?= $_LOCATION['name']; ?></li>
                        </ol>
                    </div><!-- /.col -->
                    <div class="col-sm-5 text-right">
                        <?php if (isset($link_save2)) { ?>
                            <input type="button" class="btn btn-primary" onclick="checkSave()" value="儲存">
                        <?php } ?>
                        <a class="btn btn-default" href="<?=$link_refresh;?>" title="Refresh">重整</a>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!--section class="content"-->
        <div class="container-fluid">
            <?= $__content ?>
        </div><!-- /.container-fluid -->
        <!--/section-->
        <div class="col-lg-12 text-right">
            <?php if (isset($link_save2)) { ?>
                <input type="button" class="btn btn-primary" onclick="checkSave()" value="儲存">
            <?php } ?>
            <?php if (isset($link_cancel)) { ?>
                <?php if($link_cancel == "history_go_back"): ?>
                    <a class="btn btn-default" onclick="history.back()" title="Cancel">回上一頁</a>
                <?php else:?>
                    <a class="btn btn-default" href="<?=$link_cancel;?>" title="Cancel">回上一頁</a>
                <?php endif ?>
            <?php } ?>
        </div>
    </div>
    <!-- /#page-wrapper -->
