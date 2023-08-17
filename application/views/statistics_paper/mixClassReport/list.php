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
                        <input hidden id='ssearchTopic' name='searchTopic' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
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
                            <select id='endMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_endMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
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
                </div>
                <!-- /.table head -->
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="19">臺北市政府公務人員訓練處 混成班期統計報表</th>
                        </tr>
                        <tr>
                            <th class="text-center">年度</th>
                            <th class="text-center">月份</th>
                            <th class="text-center">系列</th>
                            <th class="text-center">單位<br>類別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">班期<br>性質</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">線上課程內容</th>
                            <th class="text-center" style="width:10%">線上課<br>程講座</th>
                            <th class="text-center">實體課程內容</th>
                            <th class="text-center" style="width:10%">實體課程<br>講座</th>
                            <th class="text-center">報名<br>人數</th>
                            <th class="text-center">結訓<br>人數</th>
                            <th class="text-center">結訓<br>(男)</th>
                            <th class="text-center">結訓<br>(女)</th>
                            <th class="text-center">訓練<br>期程</th>
                            <th class="text-center">訓練人<br>天次</th>
                            <th class="text-center">人天次<br>(男)</th>
                            <th class="text-center">人天次<br>(女)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $tmp_count = count($datas);
                            
                            if($tmp_count > 0){
                                echo '<tr class="text-left">';
                                echo '<td colspan="11" style="text-align: right">總計：</td>';
                                echo '<td>'.$datas[$tmp_count-1]["total_scount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["gcount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["gcountm"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["gcountf"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["range"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["lcount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["mcount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["fcount"].'</td>';
                                echo '</tr>';
                            }

                            for($i=0;$i<$tmp_count;$i++){
                                echo '<tr>';
                                echo '<td>'.$datas[$i]['year'].'</td>';
                                echo '<td>'.intval($datas[$i]['month']).'</td>';
                                echo '<td>'.$datas[$i]['series'].'</td>';
                                echo '<td>'.$datas[$i]['description'].'</td>';
                                echo '<td>'.$datas[$i]['class_name'].'</td>';
                                echo '<td>考核+混成</td>';
                                echo '<td>'.$datas[$i]['term'].'</td>';
                                echo '<td>'.$datas[$i]['onlineCourse'].'</td>';
                                echo '<td>'.$datas[$i]['onlineTeacher'].'</td>';
                                echo '<td>'.$datas[$i]['phyCourse'].'</td>';
                                echo '<td>'.$datas[$i]['phyTeacher'].'</td>';
                                echo '<td>'.$datas[$i]['scount'].'</td>';
                                echo '<td>'.$datas[$i]['gcount'].'</td>';
                                echo '<td>'.$datas[$i]['gcountm'].'</td>';
                                echo '<td>'.($datas[$i]['gcount']-$datas[$i]['gcountm']).'</td>';
                                echo '<td>'.$datas[$i]['range'].'</td>';
                                echo '<td>'.$datas[$i]['lcount'].'</td>';
                                echo '<td>'.$datas[$i]['mcount'].'</td>';
                                echo '<td>'.$datas[$i]['fcount'].'</td>';
                            }

                        ?>
                    </tbody>
                </table>
                <!-- <span align="right"><p>列印時間：2019/08/30 17:06</p></span> -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>


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