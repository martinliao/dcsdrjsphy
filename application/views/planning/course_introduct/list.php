<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" action="" target=_blank>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">年度</label>
                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                            <label class="control-label">季別</label>
                            <?php
                                echo form_dropdown('query_season', $choices['query_season'], $filter['query_season'], 'class="form-control"');
                            ?>
                            <label class="control-label">系列別代碼</label>
                            <?php
                                echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                            ?>
                            <label class="control-label">次類別代碼</label>
                            <select class="form-control" name='query_second' id='query_second'>
                                <option value="">請選擇次類別</option>
                                <?php if(isset($choices['query_second']) && !empty($choices['query_second'])){
                                    var_dump($choices['query_second']);
                                    for($i=0;$i<count($choices['query_second']);$i++){
                                    if($choices['query_second'][$i]['item_id'] == $filter['query_second']){
                                        echo '<option value="'.$choices['query_second'][$i]['item_id'].'" selected>'.$choices['query_second'][$i]['name'].'</option>';
                                    } else {
                                        echo '<option value="'.$choices['query_second'][$i]['item_id'].'">'.$choices['query_second'][$i]['name'].'</option>';
                                            }
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">班期代碼:</label>
                            <input type="text" name="query_class_no" value="<?=$filter['query_class_no']?>" class="form-control">
                            <label class="control-label">班期名稱:</label>
                            <input type="text" name="query_class_name" value="<?=$filter['query_class_name']?>" class="form-control">
                            <label class="control-label">對象:</label>
                            <input type="text" name="respondant"  value="<?=$filter['respondant']?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">開課起日:</label>
                            <div class="input-group">
                                <input type="text" name="query_start_date" class="form-control datepicker" value="<?=$filter['query_start_date']?>" id="datepicker1">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label">開課迄日:</label>
                            <div class="input-group">
                                <input type="text" name="query_end_date" class="form-control datepicker" id="test1" value="<?=$filter['query_end_date']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm" onclick="selectAction(1)">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="selectAction(2)">匯出</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">系列別</th>
                                    <th class="text-center">次類別名稱</th>
                                    <th class="text-center">班期名稱</th>
                                    <th class="text-center">研習對象</th>
                                    <th class="text-center">期數</th>
                                    <th class="text-center">每期人數</th>
                                    <th class="text-center">合計人數</th>
                                    <th class="text-center">期程(小時)</th>
                                    <th class="text-center">合計時數</th>
                                    <th class="text-center">研習目標</th>
                                    <th class="text-center">課程內容</th>
                                    <th class="text-center">開課日期</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($list as $row) {?>
                                <tr>
                                    <td><?=$row['type_name']?></td>
                                    <td><?=$row['bureau_name']?></td>
                                    <td><?=$row['class_name']?></td>
                                    <td><?=$row['respondant']?></td>
                                    <td><?=$row['max_term']?></td>
                                    <td><?=$row['no_persons']?></td>
                                    <td><?=$row['total_persons']?></td>
                                    <td><?=$row['range']?></td>
                                    <td><?=$row['total_range']?></td>
                                    <td><?=$row['obj']?></td>
                                    <td><?=$row['content']?></td>
                                    <td><?=substr($row['start_date1'],0,10)?></td>
                                </tr>
                                <?php }?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
<script>

function selectAction($number){
    if($number==1){
        document.getElementById("filter-form").action = "<?=$link_detail;?>";
        document.filter-form.submit();
        
    }
    if($number==2){
        document.getElementById("filter-form").action = "<?=$link_export;?>";
        document.filter-form.submit();
    }
}


$(document).ready(function(){
    $('#datepicker1').datepicker();
    $('#datepicker2').click(function(){
        $('#datepicker1').focus();
    });
    $('#test1').datepicker();
    $('#test2').click(function(){
        $('#test1').focus();
    });
});

function removeOptions(selectbox) {
    var i;
    for (i = selectbox.options.length - 1; i >= 0; i--) {
        selectbox.remove(i);
    }
}

function getSecond(){
    removeOptions(document.getElementById("query_second"));
    var series = document.getElementById('query_type').value;

    if(series == ''){
        return false;
    }

    var link = "<?=$link_get_second_category;?>";
  
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);

            if (result.length != 0) {
                var second = document.getElementById('query_second');
                var option_name = '請選擇次類別代碼';
                var option_value = '';
                var new_option = new Option(option_name, option_value);
                second.options.add(new_option);
                for (var i = 0; i < result.length; i++) {
                    var option_name = result[i]['name'];
                    var option_value = result[i]['item_id'];
                    var new_option = new Option(option_name, option_value);
                    second.options.add(new_option);
                }
            }
        }
    });
}
</script>