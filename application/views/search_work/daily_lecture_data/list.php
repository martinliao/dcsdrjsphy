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
                        <form id="form" method="GET">
                            <input hidden id='syear' name='year' value="">                      
                            <input hidden id='stype' name='type' value="0">                      
                            <input hidden id='sseason' name='season' value="">
                            <input hidden id='sstartMonth' name='startMonth' value="">
                            <input hidden id='sendMonth' name='endMonth' value="">
                            <input hidden id='sstart_date' name='start_date' value="">
                            <input hidden id='send_date' name='end_date' value="">
                            <input hidden id='sd' name='sd' value="0">
                            <input hidden id='st' name='st' value="0">
                            <input hidden id='siscsv' name='iscsv' value="0">
                            <input hidden id='srows' name='rows' value="0">
                            <input hidden id='ssite_B' name='site_B' value="0">
                            <input hidden id='ssite_C' name='site_C' value="0">
                            <input hidden id='ssite_E' name='site_E' value="0">
                        </form> 
                    </div>
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
                            <label class="control-label"> - </label>
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
                            <input type="checkbox" id="d" <?= ($sess_d == 'Y') ? "checked":"" ?> value ="Y" name="d" class="form-group" style="width: 20px; height: 20px;">
                            <label class="control-label">僅顯示處內課程</label>
                            
                            <!-- <input type="checkbox" id="t" <?= ($sess_t == 'Y') ? "checked":"" ?> value ="Y"  name="t" class="form-group" style="width: 20px; height: 20px;">
                            <label class="control-label">不顯示教師資料</label> -->
                            <input type="radio" name="T_radio" value="T_N"  <?= ($sess_t == 'T_N') ? "checked":"" ?>> 不顯示教師資料
                            <input type="radio" name="T_radio" value="T_M"  <?= ($sess_t == 'T_M') ? "checked":"" ?>> 合併同班期、同講座
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">教室區別:</label>
                            <input type="checkbox" id="site_B" <?= ($sess_site_B == 'Y') ? "checked":"" ?> value ="Y" name="site_B" class="form-group" style="width: 20px; height: 20px;">
                            <label class="control-label">B區教室</label>
                            <input type="checkbox" id="site_C" <?= ($sess_site_C == 'Y') ? "checked":"" ?> value ="Y" name="site_C" class="form-group" style="width: 20px; height: 20px;">
                            <label class="control-label">C區教室</label>
                            <input type="checkbox" id="site_E" <?= ($sess_site_E == 'Y') ? "checked":"" ?> value ="Y" name="site_E" class="form-group" style="width: 20px; height: 20px;">
                            <label class="control-label">E區教室</label>
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
                <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="14">臺北市政府公務人員訓練處   每日研習班次講座資料</th>
                        <tr>
                            <?php if(($sess_t == 'Y')){?>
                        <tr>
                            <th class="text-center">上課時間</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">局處</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">調訓人數</th>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">教室代碼</th>
                        </tr>
                            <?php }else{?>
                        <tr>
                            <th class="text-center">當日開班</th>
                            <th class="text-center" style="width: 340px;">班期名稱</th>
                            <th class="text-center">局處</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">調訓人數</th>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">教室代碼</th>
                            <th class="text-center">課程名稱</th>
                            <th class="text-center" style="width: 140px;">時間</th>
                            <th class="text-center">講師</th>
                            <th class="text-center">講師背景</th>
                            <th class="text-center" style="width: 120px;">上課日期</th>
                            <th class="text-center">用餐否</th>
                            <th class="text-center">下午上課</th>
                        </tr>
                            <?php }?>
                    </thead>
                    <tbody>
                    <!-- <?php print_r($datas);?> -->
                        <?php foreach ($datas as $data): ?>
                        <?php if(($sess_t == 'Y')){?>
                        <tr class="text-center">
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= substr($data["use_date"], 0, 10)?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>>
                                <a title="課程表"
                                    href="<?=base_url('create_class/print_schedule/print/'.$data["seq_no"].'?query_year='.$data["year"].'&query_class_no='.$data["class_id"].'&rows=10&query_class_name='.$data["class_name"])?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">
                                    <?= $data["class_name"]?>
                                </a>
                            </td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["DEV_NAME"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["term"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>>
                                <a title="名冊"
                                    href="<?=base_url('student_list_pdf.php?uid=55&tmp_seq=0&ShowRetirement=1&year='.$data["year"].'&class_no='.$data["class_id"].'&term='.$data["term"])?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">
                                    <?= $data["pcount"]?>
                                </a>
                            </td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["workername"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["room_id"]?></td>
                        </tr>
                        <?php }else{?>
                        <tr class="text-center">
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["OPENED"]?"Y":"" ?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>>
                                <a title="課程表"
                                    href="<?=base_url('create_class/print_schedule/print/'.$data["seq_no"].'?query_year='.$data["year"].'&query_class_no='.$data["class_id"].'&rows=10&query_class_name='.$data["class_name"])?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">
                                    <?= $data["class_name"]?>
                                </a>
                            </td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["DEV_NAME"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["term"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>>
                                <a title="名冊"
                                    href="<?=base_url('student_list_pdf.php?uid=55&tmp_seq=0&ShowRetirement=1&year='.$data["year"].'&class_no='.$data["class_id"].'&term='.$data["term"])?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">
                                    <?= $data["pcount"]?>
                                </a>
                            </td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["workername"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["room_id"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["description"]?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["from_time"]." ~ ".  $data["to_time"] ?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["name"]?></td>

                            <!-- <{if $row.CORP!=""}>
                  	  			<{if $row.ISTEACHER eq 'N'}>(助)<{/if}><{$row.CORP}>-<{$row.POSITION}>
                  			<{else}>
                  	  			&nbsp;
                  			<{/if}> -->
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>>
                                <?= ($data["corp"] !="")? (($data["isteacher"] == 'N')?"(助)".$data["corp"]."-".$data["position"]:$data["corp"]."-".$data["position"]):"&nbsp;" ?>
                            </td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= substr($data["use_date"],0,10)?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["dining_count"] != 0 ? "Y":"" ?></td>
                            <td <?php echo (substr($data["room_id"],0,1) == 'B'||substr($data["room_id"],0,1) == 'C'||substr($data["room_id"],0,1) == 'E')?"":"style='background-color:yellow;'" ?>><?= $data["after_count"] !=0 ?"Y":"" ?></td>
                        </tr>
                        <?php }?>
                        
                        <?php endforeach?>
                        
                        
                    </tbody>
                </table>
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>


<script type="text/javascript">
function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    var dd = result.getDate();
    var mm = result.getMonth()+1;
    var yy = result.getFullYear();
    result = yy+'-'+mm+'-'+dd;
    return result;
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
        else if(count == 0){
            alert("請輸入查詢條件!");
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
        $('#srows').val($('select[name=rows]').val());
        if ($('input#d').is(':checked')) {
            $('#sd').val($('#d:checked').val());
        }
        else{
            $('#sd').val(0);
        }
        if ($('input#site_B').is(':checked')) {
            $('#ssite_B').val($('#site_B:checked').val());
        }else{
            $('#ssite_B').val(0);
        }
        if ($('input#site_C').is(':checked')) {
            $('#ssite_C').val($('#site_C:checked').val());
        }else{
            $('#ssite_C').val(0);
        }
        if ($('input#site_E').is(':checked')) {
            $('#ssite_E').val($('#site_E:checked').val());
        }else{
            $('#ssite_E').val(0);
        }
        $('#st').val($('input[name=T_radio]:checked').val());

        // if ($('input#t').is(':checked')) {
        //     $('#st').val($('#t:checked').val());
        // }
        // else{
        //     $('#st').val(0);
        // }
        
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
        else if(count == 0){
            alert("請輸入查詢條件!");
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
        var ssd = "";
        var sst = "";
        if ($('input#d').is(':checked')) {
            $('#sd').val($('#d:checked').val());
            ssd = $('#d:checked').val();
        }
        else{
            $('#sd').val(0);
            ssd = 0;
        }
        var site_B ="";
        if ($('input#site_B').is(':checked')) {
            $('#ssite_B').val($('#site_B:checked').val());
            site_B = $('#site_B:checked').val();
        }
        var site_C ="";
        if ($('input#site_C').is(':checked')) {
            $('#ssite_C').val($('#site_C:checked').val());
            site_C = $('#site_C:checked').val();
        }
        var site_E ="";
        if ($('input#site_E').is(':checked')) {
            $('#ssite_E').val($('#site_E:checked').val());
            site_E = $('#site_E:checked').val();
        }
        // if ($('input#t').is(':checked')) {
        //     $('#st').val($('#t:checked').val());
        //     sst = $('#t:checked').val();
        // }
        // else{
        //     $('#st').val(0);
        //     sst = 0;
        // }
        // $('#st').val($('input[name=T_radio]:checked').val());
        sst = $('input[name=T_radio]:checked').val();
        var link = "<?=$link_refresh;?>";
        window.open(link+"?year="+$('#year').val()+"&type="+type+"&season="+$('#season').val()+"&startMonth="+$('#startMonth').val()+"&endMonth="+$('#endMonth').val()+"&start_date="+$('#datepicker1').val()+"&end_date="+$('#test1').val()+"&sd="+ssd+"&st="+sst+"&iscsv=1&site_B="+site_B+"&site_C="+site_C+"&site_E="+site_E, "_blank");
        // $( "#form" ).submit();
    });

    

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();   
    });
    if($('#datepicker1').val() == "" && $('#test1').val() == "" && $("#season").val() == "" && $("#startMonth").val() == "" && $("#endMonth").val() == "") {
        setToday();
        $('#Search').click();
    }
});
</script>