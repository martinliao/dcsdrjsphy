<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">班期代碼:</label>
                            <input type="text" class="form-control" id="query_class_no" name="query_class_no" value="<?=$filter['query_class_no'];?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">班期名稱:</label>
                            <input type="text" class="form-control" id="query_class_name" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info">查詢</button>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-hover table-condensed table-bordered">
                            <tr>
                                <th class="text-center">年度</th>
                                <th class="text-center">班期代碼</th>
                                <th class="text-center">期別</th>
                                <th class="text-center">班期名稱</th>
                                <th class="text-center">收件者</th>
                                <th class="text-center">內文</th>
                                <th class="text-center">發信時間</th>
                            </tr>
                            <?php
                                for($i=0;$i<count($list);$i++){
                                    echo '<tr>';
                                    echo '<td class="text-center">'.$list[$i]['year'].'</td>';
                                    echo '<td>'.$list[$i]['class_no'].'</td>';
                                    echo '<td class="text-center">'.$list[$i]['term'].'</td>';
                                    echo '<td>'.$list[$i]['class_name'].'</td>';
                                    echo '<td>'.$list[$i]['mailto'].'</td>';
                                    echo '<td>'.$list[$i]['content'].'</td>';
                                    echo '<td>'.date('Y-m-d',strtotime($list[$i]['sendtime'])).'</td>';
                                    echo '</tr>';
                                }
                            ?>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>

            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function sendFun(){
    if($('#query_class_no').val()=="" && $('#query_class_no').val()=="") {
        alert("請輸入代碼或名稱");
        return false;
    } else {
        var obj = document.getElementById('filter-form');
        obj.submit();
    }
}
</script>