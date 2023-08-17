<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">班期代碼:</label>
                            <input id="classno" value="<?= $sess_classno?>" type="text" class="form-control">
                            <label class="control-label">班期名稱:</label>
                            <input id="classname" value="<?= $sess_classname?>" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <form id="form" method="GET">
                            <input hidden id='sclassno' name='classno' value="">
                            <input hidden id='sclassname' name='classname' value="">
                            <input hidden id='sstart_date' name='start_date' value="">
                            <input hidden id='send_date' name='end_date' value="">
                            <input hidden id='srows' name='rows'>
                        </form>
                        <div class="col-xs-12">
                            <label class="control-label">用餐日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?= $sess_start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?= $sess_end_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>                               
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="ClearData()">清除</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </div>
                <table class="table table-horver table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">用餐日期</th>
                            <th class="text-center">用餐別</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">ID</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">身份別</th>
                            <th class="text-center">功能</th>
                        </tr>
                    </thead>
                     <tbody>
                       
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><?= $data["use_date"]?></td>
                            <td><?= $data["dining_type"]?></td>
                            <td><?= $data["year"]?></td>
                            <td><?= $data["class_no"]?></td>
                            <td><?= $data["class_name"]?></td>
                            <td><?= $data["term"]?></td>
                            <td><?= $data["id"]?></td>
                            <td><?= $data["name"]?></td>
                            <td><?= $data["term"]?></td>
                            <td><?= $data["term"]?></td>

                        </tr>
                        
                        <?php endforeach?>
                        
                        
                    </tbody>
                </table>
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.panel -->
<!-- /.col-lg-12 -->
<script>
function sendFun(){
    if($('#datepicker1').val() == "") {
        alert("起日不能為空！");
    }
    else if($('#test1').val() == "") {
        alert("訖日不能為空！");
    }
    else {
        $('#Search').click();
    }
}

$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        if($('#datepicker1').val() == "") {
            alert("起日不能為空！");
        }
        else if($('#test1').val() == "") {
            alert("訖日不能為空！");
        }
        else {
            $('#sclassno').val($('#classno').val());
            $('#sclassname').val($('#classname').val());
            $('#sstart_date').val($('#datepicker1').val());
            $('#send_date').val($('#test1').val());
            $('#srows').val($('select[name=rows]').val());

            $( "#form" ).submit();
        }
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();   
    });

});
</script>