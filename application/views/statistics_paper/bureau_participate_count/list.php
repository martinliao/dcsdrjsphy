<!-- <?php print_r($datas); ?> -->
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
                        <input hidden id='sact' name='act' value="">
                        <input hidden id='scs' name='cs' value="">
                        <input hidden id='scp' name='cp' value="">
                        <input hidden id='sct' name='ct' value="">
                        <input hidden id='srm' name='rm' value="">
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
                                <input type="text" class="form-control datepicker" value="<?=htmlspecialchars($sess_start_date, ENT_HTML5|ENT_QUOTES)?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=htmlspecialchars($sess_end_date, ENT_HTML5|ENT_QUOTES)?>" id="test1" name="end_date">
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
                            <label class="control-label" style="display:inline-flex;align-items:center;"><input type="checkbox" id="cs" class="form-control" style="width: 20px;" <?php echo $sess_cs==''?'':'checked';?>>互調</label>
                            <label class="control-label" style="display:inline-flex;align-items:center;"><input type="checkbox" id="cp" class="form-control" style="width: 20px;" <?php echo $sess_cp==''?'':'checked';?>>換員</label>
                            <label class="control-label" style="display:inline-flex;align-items:center;"><input type="checkbox" id="ct" class="form-control" style="width: 20px;" <?php echo $sess_ct==''?'':'checked';?>>換期</label>
                            <label class="control-label" style="display:inline-flex;align-items:center;"><input type="checkbox" id="rm" class="form-control" style="width: 20px;" <?php echo $sess_rm==''?'':'checked';?>>取消</label>
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出</button>
                            <button id="print" class="btn btn-info btn-sm">列印</button>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="100">臺北市政府公務人員訓練處 各類班期局處參訓人員異動統計表</th>
                        </tr>
                        <tr>
                            <th class="text-center" rowspan="2">系列</th>
                            <th class="text-center" rowspan="2">班期名稱</th>
                            <th class="text-center" rowspan="2">期別</th>
                            <th class="text-center" rowspan="2">開班日期</th>
                            <th class="text-center" rowspan="2">承辦人</th>
                            <?php if($datas['hasdata']!=0){?>
                            <?php if(!isset($datas)){?>
                            <?php }else{?>
                            <?php foreach ($datas['bureau'] as $row): ?>
                            <th colspan="<?=$datas['cnt']?>" valign="top">
                            <?php if($row["NAME"]){?>
                                <?=$row["NAME"]?>
                            <?php }else{?>
                                <?=$row["BUREAU_ID"]?>
                            <?php }?>
                            </th>
                            <?php endforeach?>
                            <?php }}?>
                        </tr>
                        <tr>
                            <?php if($datas['hasdata']!=0){?>
                                <?php for($i=0;$i<$datas['bcnt'];$i++){?>
                                    <?php if(isset($datas['cs']) && $datas['cs']=='1'){?>
                                        <th>互調</th>
                                    <?php }?>
                                    <?php if(isset($datas['cp']) && $datas['cp']=='1'){?>
                                        <th>換員</th>
                                    <?php }?>
                                    <?php if(isset($datas['ct']) && $datas['ct']=='1'){?>
                                        <th>換期</th>
                                    <?php }?>
                                    <?php if(isset($datas['rm']) && $datas['rm']=='1'){?>
                                        <th>取消</th>
                                    <?php }?>
                                <?php }?>
                            <?php }?>
					    </tr>
                    </thead>
                    <tbody>
                        <?php if($datas['hasdata']!=0){?>
                            <?php foreach ($datas['rows'] as $data): ?>
                                <tr>
                                    <?php if($data['NO1']==1){?>
                                        <td rowspan="<?= $data["NO1D"]+1?>"><?= $data["series"]?></td>
                                    <?php }?>
                                    <td><?= $data["class_name"]?></td>
                                    <td><?= $data["term"]?></td>
                                    <td><?= $data["start_date1"]?></td>
                                    <td><?= $data["worker_name"]?></td>
                                    <?php foreach ($data['bureausInfo'] as $subdata): ?>
                                        <td style="text-align: center;"><?= $subdata?></td>
                                    <?php endforeach?>
                                </tr>
                                <?php if($data['NO1D']==1){?>
                                    <tr>
                                        <td colspan="4" align="right"><?= $data["series"]?>小計：</td>
                                        <?php foreach ($data['totalInfos'] as $totalCount): ?>
                                            <td style="text-align: center;"><?= $totalCount?></td>
                                        <?php endforeach?>
                                    </tr>
                                <?php }?>
                            <?php endforeach?>
                            <tr>
                                <td colspan="5" align="right">總計：</td>
                                <?php if(isset($datas['sumTotalCount'])) {?>
                                    <?php foreach ($datas['sumTotalCount'] as $sunTotalCount): ?>
                                        <td style="text-align: center;"><?= $sunTotalCount?></td>
                                    <?php endforeach?>
                                <?php }?>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
                <?php
                    if (count($datas['rows'])==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
                <!-- <span align="right"><p>列印時間：2019/08/30 17:06</p></span> -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>


<script type="text/javascript">
if("<?php echo ($result); ?>" != "0"){
    alert("<?php echo ($result); ?>");
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
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        if ($('#cs').is(':checked')) {
            $('#scs').val('1');
        }
        if ($('#cp').is(':checked')) {
            $('#scp').val('1');
        }
        if ($('#ct').is(':checked')) {
            $('#sct').val('1');
        }
        if ($('#rm').is(':checked')) {
            $('#srm').val('1');
        }
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
        $('#syear').val($('#year').val());        
        $('#sseason').val($('#season').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        if ($('#cs').is(':checked')) {
            $('#scs').val('1');
        }
        if ($('#cp').is(':checked')) {
            $('#scp').val('1');
        }
        if ($('#ct').is(':checked')) {
            $('#sct').val('1');
        }
        if ($('#rm').is(':checked')) {
            $('#srm').val('1');
        }
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