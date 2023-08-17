<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $_SETTING[$this->site . '_name']; ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?= HTTP_PLUGIN; ?>bootstrap-3.4.1-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <!--link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css"-->
    <!-- Ionicons -->
    <!--link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css"-->
    <!-- fullCalendar -->
    <link rel="stylesheet" href="<?= HTTP_PLUGIN; ?>fullcalendar/dist/fullcalendar.min.css">
    <!-- <link rel="stylesheet" href="<?= HTTP_PLUGIN; ?>fullcalendar/dist/fullcalendar.print.min.css" media="print"> -->
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= HTTP_CSS; ?>AdminLTE.min.css">

    <!-- MetisMenu CSS -->
    <!-- <link href="<?= HTTP_PLUGIN; ?>metisMenu/dist/metisMenu.min.css" rel="stylesheet"> -->

    <!-- Custom CSS -->
    <link href="<?= HTTP_CSS; ?>sb-admin-2.css" rel="stylesheet">

    <!-- Self CSS -->
    <!-- <link href="<?= HTTP_CSS; ?>style.css" rel="stylesheet"> -->
    <!-- <link href="<?= HTTP_CSS; ?>calendar.css" rel="stylesheet">
    <link href="<?= HTTP_CSS; ?>sidebar_anime.css" rel="stylesheet">
    <link href="<?= HTTP_CSS; ?>drag_and_drop.css" rel="stylesheet"> -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div id="wrapper" class="toggled"> <!-- id="wrapper" -->
        <?= $__header; ?>
        <!-- Page Content -->
        <div id="page-wrapper"> <!-- id="page-wrapper" -->
            <div class="page-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-7">

                            <?php if ($this->uri->uri_string() == 'dashboard' || $this->uri->uri_string() == 'notice') { ?>
                                <h1><?= $_LOCATION['name']; ?></h1>
                            <?php } elseif ($this->uri->uri_string() == 'profile') { ?>
                                <h1>個人資料編輯</h1>
                            <?php } else { ?>
                                <?php if ($is_edap && ($_LOCATION['function']['name'] == '2D 學員基本資料' || $_LOCATION['function']['name'] == '23D 學員基本資料' || $_LOCATION['name'] == '2D 學員基本資料' || $_LOCATION['name'] == '23D 學員基本資料')) { ?>
                                    <h1><?php echo '28B 學員基本資料'; ?></h1>
                                <?php } else { ?>
                                    <h1><?= (isset($_LOCATION['function'])) ? $_LOCATION['function']['name'] : $_LOCATION['name']; ?></h1>
                                <?php } ?>
                                <ol class="breadcrumb">
                                    <?php if ($is_edap && ($_LOCATION['parent']['name'] == '2 資料管理' || $_LOCATION['parent']['name'] == '23 客服作業')) { ?>
                                        <li><?php echo '28 人事管理介面'; ?></li>
                                    <?php } else { ?>
                                        <?php if (isset($_LOCATION['parent'])) { ?>
                                            <li><?= $_LOCATION['parent']['name']; ?></li>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if ($is_edap && ($_LOCATION['function']['name'] == '2D 學員基本資料' || $_LOCATION['function']['name'] == '23D 學員基本資料' || $_LOCATION['name'] == '2D 學員基本資料' || $_LOCATION['name'] == '23D 學員基本資料')) { ?>
                                        <?php if (isset($_LOCATION['function'])) { ?>
                                            <li><a href="<?= base_url($_LOCATION['function']['link']); ?>"><?php echo '28B 學員基本資料'; ?></a></li>
                                            <!-- <li><?= $_LOCATION['name']; ?></li> -->
                                        <?php } else { ?>
                                            <li><a href="<?= base_url($_LOCATION['link']); ?>"><?php echo '28B 學員基本資料'; ?></a></li>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php if (isset($_LOCATION['function'])) { ?>
                                            <li><a href="<?= base_url($_LOCATION['function']['link']); ?>"><?= $_LOCATION['function']['name']; ?></a></li>
                                            <!-- <li><?= $_LOCATION['name']; ?></li> -->
                                        <?php } else { ?>
                                            <li><a href="<?= base_url($_LOCATION['link']); ?>"><?= $_LOCATION['name']; ?></a></li>
                                        <?php } ?>
                                    <?php } ?>
                                </ol>
                            <?php } ?>
                        </div>
                        <div class="col-lg-5 text-right">
                            <?php if (isset($link_add)) { ?>
                                <a class="btn btn-primary" href="<?= $link_add; ?>" title="Add">新增</a>
                            <?php } ?>

                            <?php if (isset($link_add_teacher)) { ?>
                                <input type="button" class="btn btn-primary" onclick="addTeacher('<?= $link_add_teacher ?>')" title="confirm" value="新增">
                            <?php } ?>

                            <?php if (isset($link_import)) { ?>
                                <a class="btn btn-primary" href="<?= $link_import; ?>" title="import">匯入</a>
                            <?php } ?>

                            <?php if (isset($link_confirm)) { ?>
                                <input type="button" class="btn btn-primary" onclick="confirmFun()" title="confirm" value="確定">
                            <?php } ?>

                            <?php if (isset($send_email)) { ?>
                                <input type="button" class="btn btn-primary" onclick="sendEmail()" title="send" value="寄出">
                                <input type="button" class="btn btn-success" onclick="view()" title="view" value="預覽">
                            <?php } ?>

                            <?php if (isset($link_save)) { ?>
                                <a class="btn btn-primary btn-save" href="#" title="Save">儲存</a>
                            <?php } ?>

                            <?php if (isset($link_save2)) { ?>
                                <input type="button" class="btn btn-primary" onclick="checkSave()" value="儲存">
                            <?php } ?>

                            <?php if (isset($link_save_delete)) { ?>
                                <input type="button" class="btn btn-primary" onclick="checkSaveDelete()" value="儲存">
                            <?php } ?>

                            <?php if (isset($link_save_cancel)) { ?>
                                <input type="button" class="btn btn-primary" onclick="checkSaveCancel()" value="儲存">
                            <?php } ?>

                            <?php if (isset($send_email_plus)) { ?>
                                <input type="button" class="btn btn-success" onclick="view()" title="view" value="預覽">
                            <?php } ?>

                            <?php if (isset($link_save_next)) { ?>
                                <input type="button" class="btn btn-primary" onclick="checkSave()" value="儲存並至下一頁">
                            <?php } ?>

                            <?php if (isset($link_save_not_next)) { ?>
                                <input type="button" class="btn btn-primary" onclick="checkSaveNotNext()" value="儲存">
                            <?php } ?>

                            <?php if (isset($link_delete)) { ?>
                                <button class="btn btn-danger" onclick="actionDelete('<?= $link_delete; ?>')" title="Delete">刪除</button>
                            <?php } ?>

                            <?php if (isset($link_delete2)) { ?>
                                <button class="btn btn-danger" onclick="actionDelete2('<?= $link_delete2; ?>')" title="Delete">刪除</button>
                            <?php } ?>

                            <?php if (isset($link_edit)) { ?>
                                <a class="btn btn-primary" href="#" onclick="editFun('<?= $link_edit; ?>')" title="edit">修改</a>
                            <?php } ?>

                            <?php if (isset($link_viewd)) { ?>
                                <button class="btn btn-success" onclick="actionSubmit('form-list')" title="Viewd"><i class="fa fa-eye"></i></button>
                            <?php } ?>

                            <?php if (isset($link_fax)) { ?>
                                <button class="btn btn-success" onclick="actionFax('<?= $link_fax; ?>')" title="Fax"><i class="fa fa-fax"></i></button>
                            <?php } ?>

                            <?php if (isset($link_exports)) { ?>
                                <a class="btn btn-success" href="<?= $link_exports; ?>" title="Export" target="_block"><i class="fa fa-file-excel-o"></i></a>
                            <?php } ?>

                            <?php if (isset($setdepno)) { ?>
                                <button class="btn btn-info" onclick="determine()">確定</button>
                            <?php } ?>

                            <?php if (isset($link_check_update)) { ?>
                                <input type="button" class="btn btn-primary" onclick="check_update()" value="儲存">
                            <?php } ?>

                            <?php if (isset($link_cancel)) { ?>
                                <?php if ($link_cancel == "history_go_back") : ?>
                                    <a class="btn btn-default" onclick="history.back()" title="Cancel">回上一頁</a>
                                <?php else : ?>
                                    <a class="btn btn-default" href="<?= $link_cancel; ?>" title="Cancel">回上一頁</a>
                                <?php endif ?>
                            <?php } ?>

                            <?php if (isset($link_refresh)) { ?>
                                <a class="btn btn-default" href="<?= $link_refresh; ?>" title="Refresh">重整</a>
                            <?php } ?>
                            <?php if (isset($link_back)) { ?>
                                <a class="btn btn-default" href="<?= $link_back; ?>" title="Refresh">重整</a>
                            <?php } ?>

                            <?php if (isset($go_back)) { ?>
                                <button class="btn btn-default" form="filter" href="<?= $go_back; ?>" onclick="back();">返回</button>
                            <?php } ?>

                            <?php if (isset($save)) { ?>
                                <button id="card_record_save" type="submit" class="btn btn-primary btn-save" form="filter" onclick="return save(2);">儲存</button>
                            <?php } ?>

                            <?php if (isset($save_19c)) { ?>
                                <button id="card_record_save" type="submit" class="btn btn-primary btn-save" form="filter" onclick="return suggestSave('<?= $list[0]['year'] ?>','<?= $list[0]['term'] ?>','<?= $list[0]['class_no'] ?>');">儲存</button>
                            <?php } ?>

                            <?php if (isset($link_printApplication)) { ?>
                                <a class="btn btn-default" href="<?= $link_printApplication; ?>">列印申請單</a>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <?= $__content; ?>
            </div>
            <div class="col-lg-12 text-right">
                <?php if (isset($link_save)) { ?>
                    <a class="btn btn-primary btn-save" title="Save">儲存</a>
                <?php } ?>
                <?php if (isset($link_delete2)) { ?>
                    <button class="btn btn-danger" onclick="actionDelete2('<?= $link_delete2; ?>')" title="Delete">刪除</button>
                <?php } ?>
                <?php if (isset($link_edit)) { ?>
                    <a class="btn btn-primary" href="#" onclick="editFun('<?= $link_edit; ?>')" title="edit">修改</a>
                <?php } ?>
                <?php if (isset($link_save2)) { ?>
                    <input type="button" class="btn btn-primary" onclick="checkSave()" value="儲存">
                <?php } ?>
                <?php if (isset($link_save_delete)) { ?>
                    <input type="button" class="btn btn-primary" onclick="checkSaveDelete()" value="儲存">
                <?php } ?>
                <?php if (isset($link_save_cancel)) { ?>
                    <input type="button" class="btn btn-primary" onclick="checkSaveCancel()" value="儲存">
                <?php } ?>
                <?php if (isset($link_save_next)) { ?>
                    <input type="button" class="btn btn-primary" onclick="checkSave()" value="儲存並至下一頁">
                <?php } ?>
                <?php if (isset($link_save_not_next)) { ?>
                    <input type="button" class="btn btn-primary" onclick="checkSaveNotNext()" value="儲存">
                <?php } ?>
                <?php if (isset($send_email)) { ?>
                    <input type="button" class="btn btn-primary" onclick="sendEmail()" title="send" value="寄出">
                    <input type="button" class="btn btn-success" onclick="view()" title="view" value="預覽">
                <?php } ?>
                <?php if (isset($send_email_plus)) { ?>
                    <input type="button" class="btn btn-success" onclick="view()" title="view" value="預覽">
                <?php } ?>

                <?php if (isset($link_check_update)) { ?>
                    <input type="button" class="btn btn-primary" onclick="check_update()" value="儲存">
                <?php } ?>

                <?php if (isset($link_cancel)) { ?>
                    <?php if ($link_cancel == "history_go_back") : ?>
                        <a class="btn btn-default" onclick="history.back()" title="Cancel">回上一頁</a>
                    <?php else : ?>
                        <a class="btn btn-default" href="<?= $link_cancel; ?>" title="Cancel">回上一頁</a>
                    <?php endif ?>
                <?php } ?>
                <?php if (isset($link_printApplication)) { ?>
                    <a class="btn btn-default" href="<?= $link_printApplication; ?>">列印申請單</a>
                <?php } ?>
            </div>

        </div>
        <!-- /#page-wrapper -->
        <?= $__footer; ?>
    </div>

<!-- jQuery 3 -->
<script src="<?= HTTP_PLUGIN; ?>jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= HTTP_PLUGIN; ?>bootstrap-3.4.1-dist/js/bootstrap.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= HTTP_PLUGIN; ?>jquery-ui/jquery-ui.min.js"></script>
<!-- Slimscroll -->
<script src="<?= HTTP_PLUGIN; ?>jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?= HTTP_PLUGIN; ?>fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?= HTTP_JS; ?>adminlte.min.js"></script>

<!-- fullCalendar -->
<script src="<?= HTTP_PLUGIN; ?>moment/moment.js"></script>
<script src="<?= HTTP_PLUGIN; ?>fullcalendar/dist/fullcalendar.min.js"></script>

    <!-- Page specific script -->

</body>

</html>