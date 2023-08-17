<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">季別</label>
                                <?php
                                    echo form_dropdown('query_season', $choices['query_season'], $filter['query_season'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">月份</label>
                                <?php
                                    echo form_dropdown('query_month_start', $choices['query_month'], $filter['query_month_start'], 'class="form-control"');
                                ?>
                                ~
                                <?php
                                    echo form_dropdown('query_month_end', $choices['query_month'], $filter['query_month_end'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">系列別</label>
                                <?php
                                    echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">次類別</label>
                                <select class="form-control" id="query_second" name='query_second' id='query_second'>
                                    <option value="">請選擇次類別</option>
                                    <?php
                                        if(isset($choices['query_second']) && !empty($choices['query_second'])){
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
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" id="query_class_no" name="query_class_no" value="<?=$filter['query_class_no'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" id="query_class_name" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">承辦人</label>
                                <input type="text" class="form-control" id="query_worker_idno" name="query_worker_idno" value="<?=$filter['query_worker_idno'];?>" readonly>
                                <input type="text" class="form-control" id="query_worker_name" name="query_worker_name" value="<?=$filter['query_worker_name'];?>" readonly>
                            </div>
                            <input type="button" value="查詢" onclick="get_worker('query_worker_idno','query_worker_name')" />
                            <a class="btn btn-info btn-sm" href="<?=$link_import_worker?>" title="import">匯入</a>
                            <a class="btn btn-info btn-sm" href="<?=$link_export_worker?>" title="export">匯出</a>
                            <br>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <a class="btn btn-info btn-sm" title="clear" onclick="doClear()">清除</a>
                        </div>
                        <div class="col-xs-12" >
                            <hr>
                        </div>
                        <div class="col-xs-12" >
                            <a class="btn btn-info btn-sm" onclick="deleteFun()" title="export">清空承辦人</a>
                            <div class="form-group">
                                <label class="control-label">承辦人</label>
                                <input type="text" class="form-control" style="width: 100px" id="set_worker_idno" name="set_worker_idno" value="" readonly>
                                <input type="text" class="form-control" id="set_worker_name" name="set_worker_name" value="" readonly>
                            </div>
                            <input type="button" value="查詢" onclick="get_worker('set_worker_idno','set_worker_name')" />
                            <div class="form-group" style="margin-left: 30px">
                                <label class="control-label">教室</label>
                                <input type="hidden" class="form-control" id="set_room_id" name="set_room_id" value="">
                                <input type="text" class="form-control" id="set_room" name="set_room" value="" size="50" readonly>
                            </div>
                            <input type="button" value="查詢" onclick="get_room('set_room_id','set_room')" />
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                            <label class="control-label">首次報名批次設定</label>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" name="all_apply_s_date" value="" id="datepick1">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepick2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" name="all_apply_e_date" value="" id="datepick3">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepick4"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label">二次報名批次設定</label>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" name="all_apply_s_date2" value="" id="datepick5">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepick6"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" name="all_apply_e_date2" value="" id="datepick7">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepick8"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <a class="btn btn-info btn-sm" onclick="confirmFun()">設定</a>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="batch_apply_s_date" id="batch_apply_s_date" value="" />
                    <input type="hidden" name="batch_apply_e_date" id="batch_apply_e_date" value="" />
                    <input type="hidden" name="batch_apply_s_date2" id="batch_apply_s_date2" value="" />
                    <input type="hidden" name="batch_apply_e_date2" id="batch_apply_e_date2" value="" />
                    <input type="hidden" name="batch_worker_idno" id="batch_worker_idno" value="" />
                    <input type="hidden" name="batch_room_id" id="batch_room_id" value="" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 2%;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='series_name asc')?'_asc':'';?><?=($filter['sort']=='series_name desc')?'_desc':'';?>" data-field="series_name" >系列別</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='second_name asc')?'_asc':'';?><?=($filter['sort']=='second_name desc')?'_desc':'';?>" data-field="second_name" >次類別</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                                <th style="width: 15%;" class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='term asc')?'_asc':'';?><?=($filter['sort']=='term desc')?'_desc':'';?>" data-field="term" >期別</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='worker asc')?'_asc':'';?><?=($filter['sort']=='worker desc')?'_desc':'';?>" data-field="worker" >承辦人姓名</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='room_name asc')?'_asc':'';?><?=($filter['sort']=='room_name desc')?'_desc':'';?>" data-field="room_name" >教室</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='range asc')?'_asc':'';?><?=($filter['sort']=='range desc')?'_desc':'';?>" data-field="range" >計畫期程(小時)</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='weights asc')?'_asc':'';?><?=($filter['sort']=='weights desc')?'_desc':'';?>" data-field="weights" >權重</th>
                                <th style="width: 3%;"> 權重後時數</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='reason asc')?'_asc':'';?><?=($filter['sort']=='reason desc')?'_desc':'';?>" data-field="reason" >系統季別</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='start_date1 asc')?'_asc':'';?><?=($filter['sort']=='start_date1 desc')?'_desc':'';?>" data-field="start_date1" >開班起日</th>
                                <th style="width: 3%;" class="sorting<?=($filter['sort']=='end_date1 asc')?'_asc':'';?><?=($filter['sort']=='end_date1 desc')?'_desc':'';?>" data-field="end_date1" >開班迄日</th>
                                <th style="width: 9%;" class="sorting<?=($filter['sort']=='start_date1 asc')?'_asc':'';?><?=($filter['sort']=='start_date1 desc')?'_desc':'';?>" data-field="start_date1" >首次報名起日</th>
                                <th style="width: 9%;" class="sorting<?=($filter['sort']=='end_date1 asc')?'_asc':'';?><?=($filter['sort']=='end_date1 desc')?'_desc':'';?>" data-field="end_date1" >首次報名迄日</th>
                                <th style="width: 9%;" class="sorting<?=($filter['sort']=='apply_s_date2 asc')?'_asc':'';?><?=($filter['sort']=='apply_s_date2 desc')?'_desc':'';?>" data-field="apply_s_date2" >二次報名起日</th>
                                <th style="width: 9%;" class="sorting<?=($filter['sort']=='apply_e_date2 asc')?'_asc':'';?><?=($filter['sort']=='apply_e_date2 desc')?'_desc':'';?>" data-field="apply_e_date2" >二次報名迄日</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $count = 0;?>
                        <?php $index = 0; ?>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['seq_no'];?>"></td>
                                <td><?=$row['series_name'];?></td>
                                <td><?=$row['second_name'];?></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['worker_name'];?></td>
                                <td><?=$row['room_name'];?></td>
                                <td><?=$row['range'];?></td>
                                <td><?=$row['weights'];?></td>
                                <td><?=($row['range']*$row['weights']);?></td>
                                <td><?=$row['reason'];?></td>
                                <td><?=date('Y-m-d',strtotime($row['start_date1']));?></td>
                                <td><?=date('Y-m-d',strtotime($row['end_date1']));?></td>
                                
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="<?php echo $index;$index++;?>" name="apply_s_date_<?=$row['seq_no'];?>" value="<?=(!empty($row['apply_s_date']))?date('Y-m-d',strtotime($row['apply_s_date'])):'';?>"/    >
                                        <span class="input-group-addon" style="cursor: pointer;" id="<?php echo $index;$index++;?>" onclick="myMsg(this)"><i
                                            class="fa fa-calendar"></i></span>
                                       
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="<?php echo $index;$index++;?>" name="apply_e_date_<?=$row['seq_no'];?>" value="<?=(!empty($row['apply_e_date']))?date('Y-m-d',strtotime($row['apply_e_date'])):'';?>"/ >
                                        <span class="input-group-addon" style="cursor: pointer;" id="<?php echo $index;$index++;?>"  onclick="myMsg(this)"><i
                                            class="fa fa-calendar"></i></span>
                                        
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">   
                                        <input type="text" class="form-control datepicker" id="<?php echo $index;$index++;?>" name="apply_s_date2_<?=$row['seq_no'];?>" value="<?=(!empty($row['apply_s_date2']))?date('Y-m-d',strtotime($row['apply_s_date2'])):'';?>"/>
                                        <span class="input-group-addon" style="cursor: pointer;" id="<?php echo $index;$index++;?>"  onclick="myMsg(this)"><i
                                            class="fa fa-calendar"></i></span>
                                        
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group"> 
                                        <input type="text" class="form-control datepicker" id="<?php echo $index;$index++;?>" name="apply_e_date2_<?=$row['seq_no'];?>" value="<?=(!empty($row['apply_e_date2']))?date('Y-m-d',strtotime($row['apply_e_date2'])):'';?>"/ >
                                        <span class="input-group-addon" style="cursor: pointer;" id="<?php echo $index;$index++;?>" onclick="myMsg(this)"><i
                                            class="fa fa-calendar"></i></span>
                                        
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
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
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

function confirmFun(){
    var obj = document.getElementById('list-form');
    document.getElementById('batch_apply_s_date').value = document.getElementById('datepick1').value;
    document.getElementById('batch_apply_e_date').value = document.getElementById('datepick3').value;
    document.getElementById('batch_apply_s_date2').value = document.getElementById('datepick5').value;
    document.getElementById('batch_apply_e_date2').value = document.getElementById('datepick7').value;
    document.getElementById('batch_worker_idno').value = document.getElementById('set_worker_idno').value;
    document.getElementById('batch_room_id').value = document.getElementById('set_room_id').value;

    obj.submit();
}

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

function doClear(){
    document.all.query_class_no.value = "";
    document.all.query_class_name.value = "";
    document.all.query_type.value = "";
    document.all.query_second.value = "";
    document.all.query_worker_idno.value = "";
    document.all.query_worker_name.value = "";
}

function get_worker(field1,field2) {
    var path = '../co_worker_popup.php?field1='+field1+'&field2='+field2;

    window.open(path,'get_worker','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
}

function get_room(field1,field2) {
    var path = '../co_room_popup.php?mode=1&field1='+field1+'&field2='+field2;

    window.open(path,'get_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
}

function deleteFun(){
    var link = "<?=$link_delete_worker;?>";
    var obj = document.getElementById('list-form');

    obj.action = link;
    obj.submit();
}


function myMsg(myObj){

    $(document).ready(function() {
    var b=Number(myObj.id);
    var a=b-1;
    //document.write(b);
    $('#'+a).datepicker();
    $('#'+b).click(function(){
    $('#'+a).focus();
    });
});
//alert("id 為: " + myObj.id);
}

$(document).ready(function() {
    $("#datepick1").datepicker();
    $('#datepick2').click(function(){
        $("#datepick1").focus();
    });
    $("#datepick3").datepicker();
    $('#datepick4').click(function(){
        $("#datepick3").focus();
    });
    $("#datepick5").datepicker();
    $('#datepick6').click(function(){
        $("#datepick5").focus();
    });

    $("#datepick7").datepicker();
    $('#datepick8').click(function(){
        $("#datepick7").focus();
    });
});
</script>
