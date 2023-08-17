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
                                <i>～</i>
                                <?php
                                    echo form_dropdown('query_month_end', $choices['query_month'], $filter['query_month_end'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">開課日期</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input type="text" class="form-control datepicker" id="pick5"name="query_start_date" value="<?=$filter['query_start_date'];?>"/>
                                    <span class="input-group-addon" style="cursor: pointer;"id="pick6"><i
                                            class="fa fa-calendar"></i></span>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control datepicker" id="pick7" name="query_end_date"  value="<?=$filter['query_end_date'];?>"/>
                                    <span class="input-group-addon" style="cursor: pointer;" id="pick8"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">系列別</label>
                                <?php
                                    echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">次類別</label>
                                <select class="form-control" name='query_second' id='query_second'>
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
                                <input type="text" class="form-control" name="query_class_no" value="<?=$filter['query_class_no'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">僅顯示最小期數</label>
                                <input type="checkbox" class="form-control" name="query_min_term" value="1" <?= isset($filter['query_min_term']) && $filter['query_min_term']=='1'?'checked':'';?>>
                            </div>
                            <div class="form-group">
                                <label class="control-label">僅顯示起始日期未設定</label>
                                <input type="checkbox" class="form-control" name="query_startdate_setup" value="1" <?= isset($filter['query_startdate_setup']) && $filter['query_startdate_setup']=='1'?'checked':'';?>>
                            </div>

                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                        <div class="col-xs-7">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                        </div>
                        <!--div class="col-xs-6 text-right"-->
                        <div class="col-xs-5">    
                            <label class="control-label">日期批次設定</label>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" name="all_start_date" id="pick1" value=""/>
                                <span class="input-group-addon" style="cursor: pointer;" id="pick2"><i
                                            class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" name="all_end_date"  id="pick3" value=""/>
                                <span class="input-group-addon" style="cursor: pointer;" id="pick4"><i
                                            class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="batch_start_date" id="batch_start_date" value="" />
                    <input type="hidden" name="batch_end_date" id="batch_end_date" value="" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 3%;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th style="width: 3%;"></th>
                                <th style="width: 7%;" class="sorting<?=($filter['sort']=='year asc')?'_asc':'';?><?=($filter['sort']=='year desc')?'_desc':'';?>" data-field="year" >年度</th>
                                <th style="width: 8%;" class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                                <th style="width: 25%;" class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                                <th style="width: 8%;" class="sorting<?=($filter['sort']=='term asc')?'_asc':'';?><?=($filter['sort']=='term desc')?'_desc':'';?>" data-field="term" >期別</th>
                                <th style="width: 20%;" class="sorting<?=($filter['sort']=='start_date1 asc')?'_asc':'';?><?=($filter['sort']=='start_date1 desc')?'_desc':'';?>" data-field="start_date1" >開課起日</th>
                                <th style="width: 20%;" class="sorting<?=($filter['sort']=='end_date1 asc')?'_asc':'';?><?=($filter['sort']=='end_date1 desc')?'_desc':'';?>" data-field="end_date1" >開課迄日</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $count = 0;?>
                        <?php $index = 0;?>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['seq_no'];?>"></td>
                                <td><?=++$count;?></td>
                                <td><?=$row['year'];?></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['term'];?></td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="<?php echo $index;$index++;?>" name="start_date_<?=$row['seq_no'];?>" value="<?=!empty($row['start_date1'])?date('Y-m-d',strtotime($row['start_date1'])):'';?>"/ >
                                        <span class="input-group-addon" style="cursor: pointer;" id="<?php echo $index;$index++;?>" onclick="myMsg(this)"><i
                                            class="fa fa-calendar"></i></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="<?php echo $index;$index++;?>" name="end_date_<?=$row['seq_no'];?>" value="<?=!empty($row['end_date1'])?date('Y-m-d',strtotime($row['end_date1'])):'';?>"/>
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
// $(document).ready(function() {
//     $('#rows select').change(function(){
//         $('#filter-form').submit();
//     });

//     // <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
//     // $('#list-form').highlight('<?=$filter['q'];?>');
//     // <?php } ?>
// });


function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

function confirmFun(){
    var obj = document.getElementById('list-form');
    document.getElementById('batch_start_date').value = document.getElementById('pick1').value;
    document.getElementById('batch_end_date').value = document.getElementById('pick3').value;

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

function myMsg(myObj)
{
    $(document).ready(function() {
    var b=Number(myObj.id);
    var a=b-1;
    //document.write(b);
    $('#'+a).datepicker();
    $('#'+b).click(function(){
    $('#'+a).focus();
    });
    });
}
$(document).ready(function() {
    $("#pick1").datepicker();
    $('#pick2').click(function(){
        $("#pick1").focus();
    });
    $("#pick3").datepicker();
    $('#pick4').click(function(){
        $("#pick3").focus();
    });
    $("#pick5").datepicker();
    $('#pick6').click(function(){
        $("#pick5").focus();
    });
    $("#pick7").datepicker();
    $('#pick8').click(function(){
        $("#pick7").focus();
    });
});
</script>
