<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form class="form-inline" id="filter-form" role="form">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                            <label class="control-label">班期首日上課月份:</label>
                            <?php
                                echo form_dropdown('start_month', $choices['start_month'], $filter['start_month'], 'class="form-control"');
                            ?>
                            <label class="control-label">班期名稱:</label>
                            <input type="text" name="query_class_name" value="<?=$filter['query_class_name'];?>" class="form-control">
                            <label class="control-label">班期代碼:</label>
                            <input type="text" name="query_class_no" value="<?=$filter['query_class_no'];?>"  class="form-control">
                           
                            <input type="checkbox" class="form-control" name="checkAll" <?php if($filter['checkAll']=='on'){?> checked <?php }?>><label class="control-label">查詢所有班期</label>
                            <button class="btn btn-info">查詢</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            (發展班期有問卷且問卷已結束)
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:14px">
                        <div class="col-md-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-condensed table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">列序</th>
                                    <th class="text-center">編輯</th>
                                    <th class="text-center">無課程建議</th>
                                    <th class="text-center">年度</th>
                                    <th class="text-center">承辦人</th>
                                    <th class="text-center">期別</th>
                                    <th class="text-center">班期名稱</th>
                                    <th class="text-center">班期結束日期</th>
                                    <th class="text-center">是否有填寫</th>
                                    <th class="text-center">已開放意見回覆</th>
                                    <th class="text-center">開放者</th>
                                    <th class="text-center">開放日期</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;?>
                                <?php foreach($list as $row){?>
                                <tr class="text-center">
                                    <td><?=$i?></td>
                                    <td><a href="<?=$row['detail']?>" class="btn btn-info">編輯</a></td>
                                    <?php 
                                        $username='"'.$this->flags->user['username'].'"';
                                        $param='"'. $row['year']  . '","' . $row['class_no']  . '","' . $row['term']  . '"';

                                        $ans_str = "無填寫";
                                        if ($row['A1_BY']!=''||$row['A2_BY']!=''||$row['A3_BY']!=''||$row['A4_BY']!=''||$row['A5_BY']!=''||$row['A6_BY']!=''||$row['A7_BY']!=''||$row['A8_BY']!=''){
                                                $ans_str = "<font color='blue'>有填寫</font>";
                                        }
                                        if($row['suggest_status']=='1'){
                                            $ans_str="<font color='blue'>教務無意見</font>";
                                            $checked = 'checked';
                                        }else{
                                            $checked = '';
                                        }

                                        if(!empty($row['s1']) || !empty($row['s2']) || !empty($row['s3']) || !empty($row['s4']) ){
                                            echo '<td><input type="checkbox" name="nosug" id="nosug" value="1" disabled>設定</td>';
                                        }else{
                                            echo '<td><input type="checkbox" name="nosug" id="nosug" value="1"'.$checked.' onclick=sugFun(this,'.$param.','.$username.');>設定</td>';
                                            //echo '<td><input type="checkbox" name="nosug" id="nosug" value="1">設定</td>';
                                        }
                                    ?>
                                    <td><?=$row['year']?></td>
                                    <td><?=$row['worker_name']?></td>
                                    <td><?=$row['term']?></td>
                                    <td><?=$row['class_name']?></td>
                                    <td><?=substr($row['end_date'],0,10)?></td>
                                    <td><?=$ans_str?></td>
                                    <td><?=$row['is_annouce']?></td>
                                    <td><?=$row['worker_name']?></td>
                                    <td><?=$row['annouce_date']?></td>
                                </tr>
                                <?php $i++; }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to
                        <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?>
                        of <?=$filter['total'];?> entries
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
<script type="text/javascript">
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
function sugFun(obj,year,class_no,term,username)
{
    if(obj.checked == true){
        var status = '1';
    } else {
        var status = '0';
    }
    var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            year: year,
            class_no: class_no,
            term:term,
            status:status,
            username:username,
        }

    jQuery.ajax({
        url: '<?=base_url('customer_service/opinion_response/ajax');?>',
        dataType: 'text',
        async: false,
        data: data,
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            if(response == 'OK'){
              alert('設定成功');
              location.reload();
            } else {
              alert('設定失敗');
            } 
        }
    });

}
    
</script>