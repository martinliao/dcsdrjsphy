<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline" >
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12" >
                            <label class="control-label">查詢日期</label>
                            <div class="input-group" id="start_date" >
                                <input type="text" class="form-control datepicker" id="test1" name="start_date" value="<?=$filter['start_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;" id="test2" ><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label"> 至</label>
                            <div class="input-group" id="end_date" >
                                <input type="text" class="form-control datepicker" id="test3"name="end_date" value="<?=$filter['end_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;"  id="test4"><i class="fa fa-calendar"></i></span>
                            </div>
                            <a class="btn btn-info btn-sm" onclick="actionSelect('<?=$select_url;?>')" >查詢</a>
                            <?php if (isset($link_export)) { ?>
                            <a class="btn btn-info btn-sm" onclick="actionExport('<?=$link_export;?>')" title="export">匯出</a>
                            <?php } ?>
                        </div>

                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover" ">
                        <thead>
                            <tr>
                                <th>申請單位</th>
                                <th>場地名稱</th>
                                <th>容納人數</th>
                                <th>統計人數</th>
                                <th>單價</th>
                                <th>統計金額</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$row['APP_NAME'];?></td>
                                <td><?=$row['room_name'];?></td>
                                <td><?=$row['room_cap'];?></td>
                                <td><?=$row['TOTCNT'];?></td>
                                <td><?=$row['UNITAMT'];?></td>
                                <td><?=number_format($row['TOTAMT']);?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    </div>
                </form>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script>
$(document).ready(function() {

$("#test1").datepicker();
$('#test2').click(function(){
    $("#test1").focus();
});
$("#test3").datepicker();
$('#test4').click(function(){
    $("#test3").focus();
});
});

var actionExport = function(url) {
    var $form = $('#filter-form');
        var yesfunc = function() {

            window.open(url,'newWin','width=400,height=500,scrollbars=yes');
            // $form.attr('action', url).submit();
        }

        var nofunc = function() {
            // bk_alert(4, 'ok', 4, 'center');
        }

        var msg = '<p>確認匯出資料?</p>';

        bk_confirm(0, msg, 'center', yesfunc, nofunc);
}

var actionSelect = function(url) {
    var $form = $('#filter-form');

    $form.attr('action', url).submit();

}
</script>