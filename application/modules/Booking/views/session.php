<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">所有班期</h4>
                        <!-- <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div> -->
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked" id='all_class'>
                            <li class="active"><a href="#"><i class="fa fa-inbox"></i> Inbox
                                <span class="label label-primary pull-right">12</span></a></li>
                            <li><a href="#"><i class="fa fa-envelope-o"></i> Sent</a></li>
                            <li><a href="#"><i class="fa fa-file-text-o"></i> Drafts</a></li>
                            <li><a href="#"><i class="fa fa-filter"></i> Junk <span class="label label-warning pull-right">65</span></a>
                            </li>
                            <li><a href="#"><i class="fa fa-trash-o"></i> Trash</a></li>
                        </ul>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-10">
                <div class="box box-primary" id='session_detail'>
                </div>
                <!-- /. box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
    <div class="card-body pad">
        <table class="table table-bordered table-sm" id="booking_table" width="100%">
            <thead>
                <tr>
                    <th></th>
                    <th>期別</th>
                    <th></th>
                    <th>開課起日</th>
                    <th>開課迄日</th>
                    <th>教室名稱</th>
                    <th>預約時段</th>
                    <th>功能</th>
                </tr>
            </thead>
            <tbody id="classroom_data">
            </tbody>
        </table>
    </div>
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">
    require(['jquery', "core/log", "mod_Booking/js", 'mod_bootstrapbase/bootstrap'], function($, log, booking) {
        //log.debug('index.php loading...');
        $('#all_class').empty();
        <?php foreach ($all_class as $c) {
            $seqNo = $c['seq_no'];
            $active = ($seqNo == $current_seq_no) ? 'class="active"' : '';
            //$term = $c['year'] . ' - ' . $c['term'];
            //$term = "{$c['year']} - {$c['term']} ({$c['range']})";
            //$term = sprintf("  %4d年 - %3d期 (range: %4d)", $c['year'], $c['term'], $c['range']);
            $term = sprintf("  %4d年 - %3d期", $c['year'], $c['term'], $c['range']);
        ?>
            $('#all_class').append(`<li <?=$active?>>
                <a href="#" data-seq_no="<?= $seqNo ?>"><?= $term; ?>
                    <span class="label label-primary pull-right"><?=$c['range'];?></span>
                </a></li>`
            );
        <?php } ?>
        booking.init();
    });
</script>