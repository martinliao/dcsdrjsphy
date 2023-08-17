<!-- <?php print_r($datas)?> -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <div class="form-group row">
                        <form id="form" method="GET">
                            <input hidden id='sclassno' name='classno' value="">
                            <input hidden id='sclassname' name='classname' value="">
                            <input hidden id='sstart_date' name='start_date' value="">
                            <input hidden id='send_date' name='end_date' value="">
                            <input hidden id='srows' name='rows' value="">
                        </form>
                        <div class="col-xs-12">
                            <label class="control-label">班期代碼:<label>
                            <input id="classno" value="<?= $sess_classno?>" type="text" class="form-control">
                            <label class="control-label">班期名稱:<label>
                            <input id="classname" value="<?= $sess_classname?>" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">用餐日期:</label>
                            <div class="input-group" id="start_date" >
                                <input type="text" class="form-control datepicker" value="<?= $sess_start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            至
                            <div class="input-group" id="end_date" >
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
                    <span style="color:green">學員人數含：選員、調訓及結訓。</span>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">功能</th>
                            <th class="text-center" rowspan="2">用餐日期</th>
                            <th class="text-center" rowspan="2">年度</th>
                            <th class="text-center" rowspan="2">班期代碼</th>
                            <th class="text-center" rowspan="2">班期名稱</th>
                            <th class="text-center" rowspan="2">期別</th>
                            <th class="text-center" rowspan="2">承辦人</th>
                            <th class="text-center" colspan="4">早餐</th>
                            <th class="text-center" colspan="4">午餐</th>
                            <th class="text-center" colspan="4">晚餐</th>
                            <th class="text-center" rowspan="2">總金額</th>
                            <th class="text-center" rowspan="2">備註</th>
                        </tr>
                        <tr>
                            <th class="text-center">講師</th>
                            <th class="text-center">人數</th>
                            <th class="text-center">追加</th>
                            <th class="text-center">金額</th>
                            <th class="text-center">講師</th>
                            <th class="text-center">人數</th>
                            <th class="text-center">追加</th>
                            <th class="text-center">金額</th>
                            <th class="text-center">講師</th>
                            <th class="text-center">人數</th>
                            <th class="text-center">追加</th>
                            <th class="text-center">金額</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><button class="btn btn-info" onclick='deletefun(<?= $data["id"]?>)'>刪除</button> <button class="btn btn-info" onclick='editfun(<?= $data["id"]?>)'>修改</button>   </td>
                            <td><?= substr($data["use_date"],0,-8)?></td>
                            <td><?= $data["year"]?></td>
                            <td><?= $data["class_no"]?></td>
                            <td><?= $data["class_name"]?></td>
                            <td><?= $data["term"]?></td>
                            <td><?= $data["WORKER_NAME"]?></td>
                            <td><?= $data["TCNT1"]?></td>
                            <td><?= $data["persons_1"]?></td>
                            <td><?= $data["add_persons_1"]?></td>
                            <td><?= $data["amount_1"]?></td>
                            <td><?= $data["TCNT2"]?></td>
                            <td><?= $data["persons_2"]?></td>
                            <td><?= $data["add_persons_2"]?></td>
                            <td><?= $data["amount_2"]?></td>
                            <td><?= $data["TCNT3"]?></td>
                            <td><?= $data["persons_3"]?></td>
                            <td><?= $data["add_persons_3"]?></td>
                            <td><?= $data["amount_3"]?></td>
                            <td><?= $data["total_amount"]?></td>
                            <td><?= $data["memo"]?></td>
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
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
<script>
function sendFun(){
    if($('#datepicker1').val() != "" && $('#test1').val() != "") {
        $('#Search').click();
    }
}

function editfun(id){
    document.location.href="./eat_management?action=edit&id="+id;
}

function deletefun(id){
    var r=confirm("確定刪除嗎?")
    if (r==true){
        ApiGet("eat_management?action=delete&id="+id,"delete")
    }
}

function ApiGet(url,name){
    $.ajax({
        async: false,
        url: url,
        type: "GET",
        dataType: "json",
        success: function (Jdata) {
            console.log(Jdata);
            if(name == "delete"){
                if(Jdata[0])
                {
                    alert("刪除成功")
                    location.reload();
                }
                else{
                    alert("刪除失敗")
                }
            }
        }
    });
}

$(document).ready(function() {
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
            $('#form').submit();
        }
    });

    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });
    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
        $("#datepicker1").focus();
    });
});
</script>