<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">

                    <form id="form" method="GET">
                        <input hidden id='syear' name='year' value="">
                        <input hidden id='sclass_no' name='class_no' value="">
                        <input hidden id='ischedule' name='schedule' value="">
                        <input hidden id='scontactor' name='contactor' value="">
                        <input hidden id='stype' name='type' value="0">                      
                        <input hidden id='sseason' name='season' value="">
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">                      
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                        <input hidden id='srows' name='rows' value="0">
                    </form>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">班期名稱:</label>
                            <input type="text" id="schedule" name="schedule" class="form-control" value="<?= $sess_schedule?>">
                            <label class="control-label">班期代碼:</label>
                            <input type="text" id="class_no" name="class_no" class="form-control" value="<?= $sess_class_no?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">依季查詢:</label>
                            <select id='season'>
                                <option value=""><?= $choices['query_season'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_season']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_season == $i ?"selected":"" ?> ><?= $choices['query_season'][$i];?></option>
                            <?php } ?>
                            </select>
                            <label class="control-label">依月查詢:</label>
                            <select id='startMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_startMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                            <label class="control-label"> - </label>
                            <select id='endMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_endMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                            <label class="control-label">承辦人:</label>
                            <input type="text" id="contactor" name="contactor" class="form-control" value="<?= $sess_contactor?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">依日期區間查詢:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" id="datepicker1"
                                    name="start_date" value="<?= $sess_start_date?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" id="test1" name="end_date" value="<?= $sess_end_date?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <button class="btn btn-info" onclick="fowardweek(-7);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek();">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek(7);">>></button>
                            <button class="btn btn-info" onclick="setToday();">設定今天</button>
                            <button class="btn btn-info" onclick="ClearData();">清除日期</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出</button>
                            <button id="print" class="btn btn-info btn-sm">列印</button>
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
                <!-- /.table head -->
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="10">臺北市政府公務人員訓練處 班期局處承辦人一覽表</th>
                        </tr>
                        <tr>
                            <th class="text-center">系列</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">局處名稱</th>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">局處電話</th>
                            <th class="text-center">開班起日</th>
                            <th class="text-center">開班迄日</th>
                            <th class="text-center">期程(小時)</th>
                            <th class="text-center">教室代碼</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($datas as $data): ?>

                        <tr class="text-center">
                            <td><?= $data["TYPE"]?></td>
                            <td><?= $data["class_name"]?></td>
                            <td><?= $data["term"]?></td>
                            <td><?= $data["DESCRIPTION"]?></td>
                            <td><?= $data["UNAME"]?></td>
                            <td><?= $data["TEL"]?></td>
                            <td><?= date('Y-m-d', strtotime($data["start_date1"]))?></td>
                            <td><?= date('Y-m-d', strtotime($data["end_date1"]))?></td>
                            <td><?= $data["Range"]?></td>
                            <td><?= $data["Room_Code"]?></td>

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
</div>
</div>


<script type="text/javascript">
function sendFun(){
    let count = 0;
    let type = 0;
    if($('#season').val() !=""){
        count++;
        type = 1;
    }
    if($('#startMonth').val() !="" || $('#endMonth').val() !=""){
        count++;
        type = 2;
    }
    if($('#datepicker1').val() !="" || $('#test1').val() !=""){
        count++;
        type = 3;
    }
    if(count > 1){
        alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
        return;
    }
    
    $('#Search').click();
}

function getCurrentWeek()
{
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,-diff);
    edate = addDays(sdate,6);
    document.getElementById("datepicker1").value = sdate;
    document.getElementById("test1").value = edate;
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    var dd = result.getDate();
    var mm = result.getMonth()+1;
    var yy = result.getFullYear();
    result = yy+'-'+mm+'-'+dd;
    return result;
}

function fowardweek(days)
{
    var date1 = document.getElementById("datepicker1").value;
    var date2 = document.getElementById("test1").value;
    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        document.getElementById("datepicker1").value = sdate; 
        document.getElementById("test1").value = edate;
    }
    else
    {
        var today = getCurrentWeek();
    }
}


$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        let count = 0;
        let type = 0;
        if($('#season').val() !=""){
            count++;
            type = 1;
        }
        if($('#startMonth').val() !="" || $('#endMonth').val() !=""){
            count++;
            type = 2;
        }
        if($('#datepicker1').val() !="" || $('#test1').val() !=""){
            count++;
            type = 3;
        }
        if(count > 1){
            alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
            return;
        }

        $('#syear').val($('#year').val());
        $('#sseason').val($('#season').val());
        $('#stype').val(type);
        $('#scontactor').val($('#contactor').val());
        $('#sclass_no').val($('#class_no').val());
        $('#ischedule').val($('#schedule').val());
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(0);
        $('#srows').val($('select[name=rows]').val());
        $( "#form" ).submit();
    });

    $('#print').click(function(){
        printData("printTable");
    });

    $('#csv').click(function(){
        let count = 0;
        let type = 0;
        if($('#season').val() !=""){
            count++;
            type = 1;
        }
        if($('#startMonth').val() !="" || $('#endMonth').val() !=""){
            count++;
            type = 2;
        }
        if($('#datepicker1').val() !="" || $('#test1').val() !=""){
            count++;
            type = 3;
        }
        if(count > 1){
            alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
            return;
        }

        $('#syear').val($('#year').val());
        $('#sseason').val($('#season').val());
        $('#scontactor').val($('#contactor').val());
        $('#stype').val(type);
        $('#sclass_no').val($('#class_no').val());
        $('#ischedule').val($('#schedule').val());
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(1);
        $( "#form" ).submit();
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });

});

</script>