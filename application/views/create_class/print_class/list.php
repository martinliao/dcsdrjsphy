<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="query_class_no" value="<?=$filter['query_class_no'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">班期名稱</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($list as $row){?>
                        <tr>
                        <td><a href='#' onclick='go_schedule_Register_update("<?=$row['year']?>","<?=$row['class_no']?>","<?=$row['term']?>","<?=$row['class_name']?>")'> <?=$row['class_no']?></a></td>
                            <td class="text-center"><?=$row['term']?></td>
                            <td class="text-center"><?=$row['class_name']?></td>
                        </tr>
                    <?php }?>
                    <tbody>
                </table>
                <!-- /.table end -->
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function go_schedule_Register_update(strYEAR,strCLASS_NO,strTERM,strName){
    var myW=window.open ('../print_class_pdf.php?&year='+strYEAR+'&class_no='+strCLASS_NO+'&term='+strTERM+'&class_name='+strName+'&tmp_seq=0&ShowRetirement=1', 'newwindow', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}


$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});
</script>