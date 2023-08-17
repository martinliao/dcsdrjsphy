<style>
    .columnStyle1 {
        width:32px;
        text-align:center;
    }
    .columnStyle2 {
        width:32px;
        vertical-align:top;
        text-align:center;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="form" method="GET">
                    <input hidden id='syear' name='year' value="">                      
                        <input hidden id='stype' name='type' value="0">                      
                        <input hidden id='sseason' name='season' value="">
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">

                    </form>
                <div id="filter-form" role="form" class="form-inline">
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
                                <input type="text" class="form-control datepicker" id="datepicker1"
                                    name="start_date" value="<?=$sess_start_date?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" id="test1" name="end_date" value="<?=$sess_end_date?>">
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
            <?php if (sizeof($datas) != 0){ ?>

            <table width="100%" border="1" id="printTable" class="table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th class="text-center" colspan="31">臺北市政府公務人員訓練處 各類班期結訓人員屬性統計表</th>
                    </tr>
                    <tr>
						<th rowspan="3" class="columnStyle1">類別</th>
						<th rowspan="3" class="columnStyle1">次類別</th>
						<th rowspan="3" style="width:200px;">班期名稱</th>
						<th rowspan="3" class="columnStyle1"><span style=" width: 22px; display: inline-block;">結訓人數</span></th>
						<th rowspan="3" class="columnStyle1"><span style=" width: 22px; display: inline-block;">訓練期程</span></th>
						<th rowspan="3" class="columnStyle1"><span style=" width: 22px; display: inline-block; ">訓練人天次</span></th>
						<th style="text-align:center" colspan='<?= count($datas['degree']) + 8 + count($datas['job']) ; ?>' >人員資料分析</th>
					</tr>
					<tr>
						<th class="columnStyle1" colspan="2" >性別</th>
						<th class="columnStyle1" colspan='<?= count($datas['degree']) ;?>'>學歷區分</th>
						<th class="columnStyle1" colspan="6">年齡區分</th>
						<th class="columnStyle1" colspan='<?= count($datas['job']) ;?>'>現職區分</th>
					</tr>
					<tr>
						<td class="columnStyle2">男</td>
						<td class="columnStyle2">女</td>
						<?php foreach ($datas["degree"] as $datadd): ?>
							<td class="columnStyle2"><?= $datadd["description"] ?></td>
                        <?php endforeach?>
						<td class="columnStyle2">20以下</td>
						<td class="columnStyle2">21-30</td>
						<td class="columnStyle2">31-40</td>
						<td class="columnStyle2">41-50</td>
						<td class="columnStyle2">51-60</td>
						<td class="columnStyle2">60以上</td>
						<?php foreach ($datas["job"] as $datajd): ?>
							<td class="columnStyle2"><?= $datajd["description"] ?></td>
                        <?php endforeach?>
					</tr>
                </thead>

            <?php if (count($datas['jobInfo']) != 0) {?>
				<tbody>  
                    <?php foreach ($datas["jobInfo"] as $data): ?>
						<tr>
							<?php if ($data["NO2"] == '1') {?>
								<td class="columnStyle1" rowspan="<?= $data['NO2D'] + $data["brother_count"] + 1 ?>">
                                <?= $data["series"] ?>
								</td>
                                <?php } ?> 

                                <?php if ($data["NO1"] =='1') {?>
                                    <td class="columnStyle1" rowspan="<?= $data["NO1D"] + 1?>">
                                    <?= $data["description"] ?></td>
                                <?php } ?> 

							<td><?= $data["class_name"] ?> (第<?= $data["term"] ?>期)</td>
							<td class="columnStyle1"><?= $data["gcount"] ?></td>
							<td class="columnStyle1"><?= $data["range"] ?></td>
							<td class="columnStyle1"><?= $data["lcount"] ?></td>
							<td class="columnStyle1"><?= $data["reg_mcount"] ?></td>
                            <td class="columnStyle1"><?= $data["reg_fcount"] ?></td>
                            
							<?php foreach ($data["degree"] as $datajid): ?>
								<td class="columnStyle1"><?= $datajid ?></td>
                            <?php endforeach?>
							<td class="columnStyle1"><?= $data["ycount_0_20"] ?></td>
							<td class="columnStyle1"><?= $data["ycount_21_30"] ?></td>
							<td class="columnStyle1"><?= $data["ycount_31_40"] ?></td>
							<td class="columnStyle1"><?= $data["ycount_41_50"] ?></td>
							<td class="columnStyle1"><?= $data["ycount_51_60"] ?></td>
                            <td class="columnStyle1"><?= $data["ycount_60"] ?></td>

							<?php foreach ($data["job"] as $datajij): ?>
								<td class="columnStyle1"><?= $datajij ?></td>
                            <?php endforeach?>
                        </tr>

                        <?php if ($data["NO1D"] == '1') {?>
							<tr>
								<td colspan="1" align="right">小計：</td>
								<td class="columnStyle1"><?= $data["SUB_COUNT"]["gcount"] ?></td>
                                <td class="columnStyle1"><?= $data["SUB_COUNT"]["range"] ?></td>
                                <td class="columnStyle1"><?= $data["SUB_COUNT"]["lcount"] ?></td>
                                <td class="columnStyle1"><?= $data["SUB_COUNT"]["gcountm"] ?></td>
                                <td class="columnStyle1"><?= $data["SUB_COUNT"]["gcountf"] ?></td>

                                <?php foreach ($data["SUB_COUNT"]["degreeCounts"] as $datascd): ?>
								    <td class="columnStyle1"><?= $datascd ?></td>
                                <?php endforeach?>

                                <td class="columnStyle1"><?= $data["SUB_COUNT"]["ycount_0_20"] ?></td>
								<td class="columnStyle1"><?= $data["SUB_COUNT"]["ycount_21_30"] ?></td>
								<td class="columnStyle1"><?= $data["SUB_COUNT"]["ycount_31_40"] ?></td>
								<td class="columnStyle1"><?= $data["SUB_COUNT"]["ycount_41_50"] ?></td>
								<td class="columnStyle1"><?= $data["SUB_COUNT"]["ycount_51_60"] ?></td>
                                <td class="columnStyle1"><?= $data["SUB_COUNT"]["ycount_60"] ?></td>
                                
                                <?php foreach ($data["SUB_COUNT"]["jobCounts"] as $datascj): ?>
								    <td class="columnStyle1"><?= $datascj ?></td>
                                <?php endforeach?>
								
							</tr>
                            <?php } ?> 
                        
                        <?php if ($data["NO2D"] == '1') {?>
						
							<tr>
								<td colspan="2" align="right">合計：</td>
								<td class="columnStyle1"><?= $data["CLASS_COUNT"]["gcount"] ?></td>
                                <td class="columnStyle1"><?= $data["CLASS_COUNT"]["range"] ?></td>
                                <td class="columnStyle1"><?= $data["CLASS_COUNT"]["lcount"] ?></td>
                                <td class="columnStyle1"><?= $data["CLASS_COUNT"]["gcountm"] ?></td>
                                <td class="columnStyle1"><?= $data["CLASS_COUNT"]["gcountf"] ?></td>

								<?php foreach ($data["CLASS_COUNT"]["degreeCounts"] as $dataccd): ?>
								    <td class="columnStyle1"><?= $dataccd ?></td>
                                <?php endforeach?>
                                
								<td class="columnStyle1"><?= $data["CLASS_COUNT"]["ycount_0_20"] ?></td>
								<td class="columnStyle1"><?= $data["CLASS_COUNT"]["ycount_21_30"] ?></td>
								<td class="columnStyle1"><?= $data["CLASS_COUNT"]["ycount_31_40"] ?></td>
								<td class="columnStyle1"><?= $data["CLASS_COUNT"]["ycount_41_50"] ?></td>
								<td class="columnStyle1"><?= $data["CLASS_COUNT"]["ycount_51_60"] ?></td>
                                <td class="columnStyle1"><?= $data["CLASS_COUNT"]["ycount_60"] ?></td>

								<?php foreach ($data["CLASS_COUNT"]["jobCounts"] as $dataccj): ?>
								    <td class="columnStyle1"><?= $dataccj ?></td>
                                <?php endforeach?>
							</tr>
                        <?php } ?> 
                    <?php endforeach?>
					<tr>
                        
						<td colspan="3" align="right">總計：</td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["gcount"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["range"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["lcount"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["gcountm"] ?></td>
                        <td><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["gcountf"] ?></td>

                            <?php foreach ($datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["degreeCounts"] as $datatcd): ?>
								<td class="columnStyle1"><?= $datatcd ?></td>
                            <?php endforeach?>
						                                               
						<td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["ycount_0_20"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["ycount_21_30"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["ycount_31_40"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["ycount_41_50"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["ycount_51_60"] ?></td>
                        <td class="columnStyle1"><?= $datas['jobInfo'][sizeof($datas['jobInfo'])-1]["TOTAL_COUNT"]["ycount_60"] ?></td>

                        <?php foreach ($datas['jobInfo'][sizeof($datas['jobInfo'])-1]["CLASS_COUNT"]["jobCounts"] as $datatcj): ?>
                                <td class="columnStyle1"><?= $datatcj ?></td>
                        <?php endforeach?>

					</tr>
				</tbody>
            <?php } ?>
            </table>
                        <?php }?>
                <?php
                    if (count($datas)==0){
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