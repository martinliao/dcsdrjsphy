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
                        <input hidden id='ssearchTopic' name='searchTopic' value="">
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
                                <input type="text" class="form-control datepicker" value="" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" id="test1" name="end_date">
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
                            <label class="control-label"><input type="checkbox" id="searchTopic" value="0" class="form-control">查詢策略主題</label>
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
                            <th class="text-center" colspan="16">臺北市政府公務人員訓練處 各類班期結訓人數統計表</th>
                        </tr>
                        <tr>
                            <th class="text-center">系列</th>
                            <th class="text-center">單位類別</th>
                            <?php 
                                if ( $ssearchTopic == '1'){?>
                            
                            <!-- if ($data["searchTopic"] == '1'){?> -->
							<th style='width:15%'>策略主題</th>
						    <?php } ?>
                            <th class="text-center" text-align: center;>班期名稱/期別</th>
                            <th class="text-center" text-align: center;>班期性質</th>
                            <th class="text-center" text-align: center;>期數</th>
                            <th class="text-center" text-align: center;>結訓人數</th>
                            <th class="text-center" text-align: center;>結訓(男)</th>
                            <th class="text-center" text-align: center;>結訓(女)</th>
                            <th class="text-center" text-align: center;>訓練期程</th>
                            <th class="text-center" text-align: center;>訓練人天次</th>
                            <th class="text-center" text-align: center;>人天次(男)</th>
                            <th class="text-center" text-align: center;>人天次(女)</th>
                            <th class="text-center" text-align: center;>環教班期</th>
                            <th class="text-center" text-align: center;>政策行銷班期</th>
                            <th class="text-center" text-align: center;>退休人員數</th>
                        </tr>
                    </thead>


                    <tbody>
                      <tr>

                        <?php  if  (!empty($datas)){ ?>

                            <td></td>

                        <?php   
                            if ($ssearchTopic == '1'){

                            echo '<td colspan="5" align="right">總計：</td>';
                             } else{
                            echo '<td colspan="4" align="right">總計：</td>';}
                        ?>

                        
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["gcount"]?></td>
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["gcountm"]?></td>
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["gcountf"]?></td>
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["range"]?></td>
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["lcount"]?></td>
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["mcount"]?></td>
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["fcount"]?></td>
                            <td></td>
                            <td></td>
                            <td><?= $datas[sizeof($datas)-1]["TOTAL_COUNT"]["rcount"]?></td>
                            </tr>

                        <?php }?>

                            <?php foreach ($datas as $data): ?>
                                <tr>

                                <?php if ($data["NO2"] =='1') {?>
                                    <td rowspan="<?= $data["NO2D"] + $data["brother_count"] + 1 ?>">
                                    <?= $data["series"] ?>
                                    </td>
                                <?php } ?> 

                                <?php if ($data["NO1"] =='1') {?>
                                    <td rowspan="<?= $data["NO1D"] + 1?>">
                                    <?= $data["description"] ?></td>
                                <?php } ?> 


                                <?php 
                                if ( $ssearchTopic == '1'){
                                    if ($data["map1"] == '1')
                                        echo '<td>A營造永續環境</td>';
                                    elseif ($data["map2"] == '1')
                                        echo '<td>B健全都市發展</td>';
                                    elseif ($data["map2"] == '1')
                                        echo '<td>C發展多元文化</td>';
                                    elseif ($data["map4"] == '1')
                                        echo '<td>D優化產業勞動</td>';
                                    elseif ($data["map5"] == '1')
                                        echo '<td>E強化社會支持</td>';
                                    elseif ($data["map6"] == '1')
                                        echo '<td>F打造優質教育</td>';
                                    elseif ($data["map7"]== '1')
                                        echo '<td>G精進健康安全</td>';
                                    elseif ($data["map8"] == '1')
                                        echo '<td>H精實良善治理</td>';
                                    else 
                                        echo '<td></td>';
                                    
                                 }?>
                                    <td><?= $data["class_name"].'(第'.$data["term"].'期)'?></td>

                                    <td><?php
                                        $asses_name = "";
                                        
                                        if ('1'==$data['IS_ASSESS'] && '1'==$data['IS_MIXED']){
                                            $asses_name = "混成";
                                        }else if('1'==$data['IS_ASSESS'] ){

                                            $asses_name = "考核";
                                        }
                                        
                                        echo $asses_name;
                                    ?></td>

                                    <td>1</td>
                                    <td><?= $data["gcount"]?></td>
                                    <td><?= $data["gcountm"]?></td>
                                    <td><?= $data["gcountf"]?></td>
                                    <td><?= $data["range"]?></td>
                                    <td><?= $data["lcount"]?></td>
                                    <td><?= $data["mcount"]?></td>
                                    <td><?= $data["fcount"]?></td>

                                <?php if ($data["env_class"] == 'Y'){?>
                                    <td>☆</td>
                                <?php }else {?>
                                    <td></td>
                                <?php } ?>
                                <?php if ($data["policy_class"] == 'Y'){?>
                                    <td>☆</td>
                                <?php }else {?>
                                    <td></td>
                                <?php } ?>
                                    <td><?= $data["rcount"]?></td>
                                </tr>

                                    <?php if ($data["NO1D"] == '1'){ ?>
                                            <tr>
                                        <td></td>
                                        <?php if ($ssearchTopic == '1'){ ?>
                                            <td colspan="3" align="right">小計：</td>
                                        <?php }else{?>
                                            <td colspan="2" align="right">小計：</td>
                                        <?php }?>
                                            <td><?= $data["SUB_COUNT"]["gcount"]?></td>
                                            <td><?= $data["SUB_COUNT"]["gcountm"]?></td>
                                            <td><?= $data["SUB_COUNT"]["gcountf"]?></td>
                                            <td><?= $data["SUB_COUNT"]["range"]?></td>
                                            <td><?= $data["SUB_COUNT"]["lcount"]?></td>
                                            <td><?= $data["SUB_COUNT"]["mcount"]?></td>
                                            <td><?= $data["SUB_COUNT"]["fcount"]?></td>
                                            <td></td>
                                            <td></td>
                                            <td><?= $data["SUB_COUNT"]["rcount"]?></td>
                                        </tr>
                                        <?php }?>

                                        <?php if ($data["NO2D"] == '1'){ ?>
                                            <tr>
                                        <td></td>
                                        <?php if ($ssearchTopic == '1'){ ?>
                                            <td colspan="4" align="right">合計：</td>
                                        <?php }else{?>
                                            <td colspan="3" align="right">合計：</td>
                                        <?php }?>

                                            <td><?= $data["CLASS_COUNT"]["gcount"]?></td>
                                            <td><?= $data["CLASS_COUNT"]["gcountm"]?></td>
                                            <td><?= $data["CLASS_COUNT"]["gcountf"]?></td>
                                            <td><?= $data["CLASS_COUNT"]["range"]?></td>
                                            <td><?= $data["CLASS_COUNT"]["lcount"]?></td>
                                            <td><?= $data["CLASS_COUNT"]["mcount"]?></td>
                                            <td><?= $data["CLASS_COUNT"]["fcount"]?></td>
                                            <td></td>
                                            <td></td>
                                            <td><?= $data["CLASS_COUNT"]["rcount"]?></td>

                                        </tr>
                                            <?php } ?>   




                            
                            <?php endforeach?>


                    </tbody>





                </table>
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

        
        if ($('input#searchTopic').is(':checked')) {
            $('#ssearchTopic').val('1');
        }
        else{
            $('#ssearchTopic').val('0');
        }

       
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

        if ($('input#searchTopic').is(':checked')) {
            $('#ssearchTopic').val('1');
        }
        else{
            $('#ssearchTopic').val('0');
        }
        
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