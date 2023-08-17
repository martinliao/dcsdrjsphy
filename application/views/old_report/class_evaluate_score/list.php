<!-- "<?= json_encode($choices['query_season']['']);?>" -->
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
                        <input hidden id='stype' name='type' value="0">                      
                        <input hidden id='sseason' name='season' value="">
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                        <input hidden id='query_class_no' name="query_class_no" value="">
                        <input hidden id='query_class_name' name="query_class_name" value="">
                        <input hidden id='rows' name="rows" value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach (array_reverse($choices['query_year']) as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?=htmlspecialchars($year,ENT_HTML5|ENT_QUOTES);?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">依季查詢:</label>
                            <select id='season'>
                                <option value=""><?= $choices['query_season'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_season']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_season == $i ?"selected":"" ?> ><?=htmlspecialchars($choices['query_season'][$i],ENT_HTML5|ENT_QUOTES);?></option>
                            <?php } ?>
                            </select>
                            <label class="control-label">依月查詢:</label>
                            <select id='startMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_startMonth == $i ?"selected":"" ?> ><?=htmlspecialchars($choices['query_month'][$i],ENT_HTML5|ENT_QUOTES);?></option>
                            <?php } ?>
                            </select>
                            <select id='endMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_endMonth == $i ?"selected":"" ?> ><?=htmlspecialchars($choices['query_month'][$i],ENT_HTML5|ENT_QUOTES);?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">班期代碼:</label>
                            <input type="text" class="form-control" value="<?=htmlspecialchars($sess_class_no,ENT_HTML5|ENT_QUOTES);?>" id="class_no">
                            <label class="control-label">班期名稱:</label>
                            <input type="text" class="form-control" value="<?=htmlspecialchars($sess_class_name,ENT_HTML5|ENT_QUOTES);?>" id="class_name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">依日期區間查詢:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_end_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <div class="input-group">
                                <button class="btn btn-info" onclick="fowardweek(-7);">
                                    <<</button> <button class="btn btn-info" onclick="getCurrentWeek();">本週
                                </button>
                                <button class="btn btn-info" onclick="fowardweek(7);">>></button>
                            </div>
                            <div class="input-group">
                                <button class="btn btn-info btn-sm" onclick="setToday()">設定今天</button>
                            </div>
                            <div class="input-group">
                            <button class="btn btn-info btn-sm" onclick="ClearData()">清除日期</button>
                            </div>
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
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" id="query_rows" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                            </div>
                <!-- /.table head -->
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="12">臺北市政府公務人員訓練處 班期評估分數查詢</th>
                        </tr>
                        <tr>
                            <th class="text-center">班期類別</th>
                            <th class="text-center">次類別</th>
                            <th class="text-center">局處</th>
                            <th class="text-center">策略主題</th>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">開班起日</th>
                            <th class="text-center">開班迄日</th>
                            <th class="text-center">平均分數</th>
                            <th class="text-center">平均分數<br>(男)</th>
                            <th class="text-center">平均分數<br>(女)</th>
                            <th class="text-center">開放性<br>意見表<br>下載</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        <tr class="text-center">
                            <td><?=htmlspecialchars($data["master_cate"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["sub_cate"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["bname"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["topic"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["worker_name"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["full_class_name"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars(date('Y-m-d',strtotime($data["start_date1"])),ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars(date('Y-m-d',strtotime($data["end_date1"])),ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["score"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["male"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["female"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td>
                                <?php if(!empty($data['export_url'])){ ?>
                                    <a href="<?=$data['export_url']?>"><button type="button" class="btn btn-info btn-sm">開放性<br>意見表</button></a>
                                <?php } ?>
                            </td>
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
                <!-- <span align="right"><p>列印時間：2019/08/30 17:06</p></span> -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>
function sendFun(){
    $('#Search').click();
}
</script>

<script type="text/javascript">
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
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(0);
        $('#query_class_no').val($('#class_no').val());
        $('#query_class_name').val($('#class_name').val());
        $('#rows').val($('#query_rows').val());
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
        $('#stype').val(type);
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