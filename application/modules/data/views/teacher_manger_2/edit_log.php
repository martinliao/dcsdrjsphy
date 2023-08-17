<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                <div class="text-center">姓名：<?=$teacher['name']?></div>
                </div>
                <div class="row">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <th class="text-center">維護日期</th>
                            <th class="text-center">維護時間</th>
                            <th class="text-center">維護狀態</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">地址</th>
                            <th class="text-center">銀行(郵局)分行</th>
                            <th class="text-center">帳號</th>
                            <th class="text-center">特殊需求</th>
                            <th class="text-center">講座介紹</th>
                            <th class="text-center">維護人員</th>
                        </thead>
                        <tbody>
                            <?php foreach($teacher_logs as $teacher_log): ?>
                            <?php 
                                $action_dt = new DateTime($teacher_log->action_dt);
                            ?>
                            <tr>
                                <td class="text-center"><?=$action_dt->format("Y-m-d")?></td>
                                <td class="text-center"><?=$action_dt->format("H:i:s")?></td>
                                <td class="text-center"><?=$teacher_log->action?></td>
                                <td><?=$teacher_log->name?></td>
                                <td><?=$teacher_log->zone.$teacher_log->city_name.$teacher_log->subcity_name.$teacher_log->addr?></td>
                                <td><?=$teacher_log->bankid?></td>
                                <td><?=$teacher_log->account?></td>
                                <td><?=$teacher_log->special_require?></td>
                                <td><?=$teacher_log->introduce?></td>
                                <td><?=$teacher_log->upd_name?></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
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
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>

    $("#clear").click(function(){
        $("input[name=idno]")[0].value="";
        $("input[name=name]")[0].value="";
        $("input[name=course_name]")[0].value="";
        $("input[name=queryFile]")[0].value="";
    });
});
function openCanteach(id){
        window.open("<?=base_url('management/lecture_notes_assignments?query_search_from=2B#')?>", "_blank", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=800,width=1500");
}
</script>
