<!-- <?php print_r($datas) ?> -->
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
                        <input hidden id='ischedule' name='nschedule' value="">
                        <input hidden id='syear' name='year' value="">                      
                        <input hidden id='stype' name='type' value="0">                      
                        <input hidden id='sseason' name='season' value="">
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='ssort' name='sort' value="">
                        <input hidden id='sact' name='act' value="">
                    </form>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">班期名稱:</label>
                            <input type="text" id="schedule" name="schedule" class="form-control"  value="<?=$sess_schedule?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style='width:90px;text-align:left;'>年度:</label>
                            <select id='year' class='form-control' style='width: 168px;'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">依季查詢:</label>
                            <select id='season' class='form-control' style='width: 168px;'>
                                <option value=""><?= $choices['query_season'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_season']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_season == $i ?"selected":"" ?> ><?= $choices['query_season'][$i];?></option>
                            <?php } ?>
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">依月查詢:</label>
                            <select id='startMonth' class='form-control' style='width: 168px;'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_startMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                            <select id='endMonth'  class='form-control' style='width: 168px;'>
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
                            <button class="btn btn-info" onclick="fowardweek(-7);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek();">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek(7);">>></button>
                            <button class="btn btn-info" onclick="setToday()">設定今天</button>
                            <button class="btn btn-info" onclick="ClearData()">清除日期</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出</button>
                            <button id="print" class="btn btn-info btn-sm">列印</button>
                        </div>
                    </div>
                    <p>本表的「應完成日」為開課起日再減十天，本表的「實際完成日」乃為帶班情形一覽表中，設定「調訓否」之日期</p>
                </div>
                <!-- /.table head -->
                <form id="list-form" method="post">
                    <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="10">臺北市政府公務人員訓練處 調訓班期一覽表</th>
                            </tr>
                            <tr>
                                <th class="text-center">編號</th>
                                <th class="text-center">班期名稱</th>
                                <th class="text-center">期別</th>
                                <th class="text-center sorting<?=($sess_sort=='FIRST_NAME+asc')?'_asc':'';?><?=($sess_sort=='FIRST_NAME+desc')?'_desc':'';?>" data-field="FIRST_NAME" onclick="sortColumn(<?=($sess_sort=='FIRST_NAME+asc')?'\'FIRST_NAME+desc\'':'\'FIRST_NAME+asc\'';?>)">承辦人(分機)</th>
                                <th class="text-center">開課起迄日</th>
                                <th class="text-center">應完成日</th>
                                <th class="text-center sorting<?=($sess_sort=='outtraydate+asc')?'_asc':'';?><?=($sess_sort=='outtraydate+desc')?'_desc':'';?>" data-field="outtraydate" onclick="sortColumn(<?=($sess_sort=='outtraydate+asc')?'\'outtraydate+desc\'':'\'outtraydate+asc\'';?>)">實際完成日(mail人事)</th>
                                <th class="text-center sorting<?=($sess_sort=='classenddate+asc')?'_asc':'';?><?=($sess_sort=='classenddate+desc')?'_desc':'';?>" data-field="classenddate" onclick="sortColumn(<?=($sess_sort=='classenddate+asc')?'\'classenddate+desc\'':'\'classenddate+asc\'';?>)">實際完成日(mail學員)</th>
                                <th class="text-center sorting<?=($sess_sort=='MAIL_DATE+asc')?'_asc':'';?><?=($sess_sort=='MAIL_DATE+desc')?'_desc':'';?>" data-field="MAIL_DATE" onclick="sortColumn(<?=($sess_sort=='MAIL_DATE+asc')?'\'MAIL_DATE+desc\'':'\'MAIL_DATE+asc\'';?>)">未錄取通知Email</th>
                                <th class="text-center">未錄取名冊</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                            <?php foreach ($datas as $key => $data): ?>
                                <tr class="text-center" <?= ($data["outtraydate"]==''||$data["classenddate"]=='')?"style='color:red;'":''?>>
                                    <td><?= ($key+1)?></td>
                                    <td><?= $data["class_name"]?></td>
                                    <td><?= $data["term"]?></td>
                                    <td><?= $data["FIRST_NAME"]?><?= $data["LAST_NAME"]?><?= $data["tel"]!=''?'(':''?><?= $data["tel"]?><?= $data["tel"]!=''?')':''?></td>
                                    <td><?= $data["start_date1"]?>-<?= $data["end_date1"]?></td>
                                    <?php $Completiondate = explode('-',$data["start_date1"]);
                                    $fmtDate = date('Y-m-d', mktime(0,0,0,$Completiondate[1],$Completiondate[2] - 10,$Completiondate[0]));?>                            
                                    <td><?= $fmtDate?></td>
                                    <td><?= $data["outtraydate"]?></td>
                                    <td><?= $data["classenddate"]?></td>
                                    <td><?= $data['tapply_count']>0?$data["MAIL_DATE"]:''?></td>
                                    <?php if($data['tapply_count']>0){?>
                                        <td><a href="<?=base_url('search_work/change_work_list/detail?type=2&year=').$data["year"]."&class=". $data["CLASS_NO"]."&term=". $data["term"] ?>">名冊</td>
                                    <?php }else{?>
                                        <td></td>
                                    <?php }?>
                                </tr>
                            
                            <?php endforeach?>
                        </tbody>
                    </table>
                </form>
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

function sortColumn(sortStr) {
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

    $('#ischedule').val($('#schedule').val());
    $('#syear').val($('#year').val());
    $('#sseason').val($('#season').val());
    $('#stype').val(type);
    $('#sstartMonth').val($('#startMonth').val());
    $('#sendMonth').val($('#endMonth').val());
    $('#sstart_date').val($('#datepicker1').val());
    $('#send_date').val($('#test1').val());
    $('#ssort').val('');
    $('#sact').val('search');
    $('#ssort').val(sortStr);
    $( "#form" ).submit();
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
        
        $('#ischedule').val($('#schedule').val());
        $('#syear').val($('#year').val());
        $('#sseason').val($('#season').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#ssort').val('');
        $('#sact').val('search');

        $( "#form" ).submit();
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
        
        $('#ischedule').val($('#schedule').val());
        $('#syear').val($('#year').val());
        $('#sseason').val($('#season').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#sact').val('csv');
        $( "#form" ).submit();
    });

    $('#print').click(function(){
        printData("printTable");
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });

});
</script>