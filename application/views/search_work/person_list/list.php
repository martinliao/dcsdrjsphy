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
                        <input hidden id='ischedule' name='nschedule' value="">
                        <input hidden id='scontactor' name='contactor' value="">
                        <input hidden id='stype' name='type' value="0">                      
                        <input hidden id='sseason' name='season' value="">
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">
                        <input hidden id='sfirstSeries' name='firstSeries' value="">
                        <input hidden id='ssecondSeries' name='secondSeries' value="">                      
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                        <input hidden id='srows' name='rows' value="">
                    </form>

                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label" style='min-width:90px;'>承辦人:</label>
                            <select class='form-control' id='contactor' style='min-width:170px;'>
                                <option value='' ></option>
                                <?php foreach ($query_contactor as $contactor): ?>
                                    <option value='<?= $contactor['PERSONAL_ID']?>' <?= $sess_contactor == $contactor['PERSONAL_ID'] ?"selected":"" ?> ><?= $contactor['NAME'];?></option>
                                <?php endforeach?>
                            </select>
                            <!-- <input type="text" id="contactor" name="contactor" class="form-control" value='<?= $sess_contactor?>'> -->
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" style='min-width:90px;'>系列別:</label>
                            <?php
                                    echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'style="min-width:170px;" class="form-control" id="query_type" onchange="getSecond()"');
                                ?>
                            <label class="control-label" style='min-width:90px;'>次類別:</label>
                            <select class="form-control" name='query_second' id='query_second' style='min-width:170px;'>
                                    <option value="">請選擇次類別</option>
                                    <?php if(isset($choices['query_second']) && !empty($choices['query_second'])){
                                        for($i=0;$i<count($choices['query_second']);$i++){
                                        if($choices['query_second'][$i]['item_id'] == $filter['query_second']){
                                        echo '<option value="'.$choices['query_second'][$i]['item_id'].'" selected>'.$choices['query_second'][$i]['name'].'</option>';
                                        } else {
                                            echo '<option value="'.$choices['query_second'][$i]['item_id'].'">'.$choices['query_second'][$i]['name'].'</option>';
                                                }
                                            }
                                        }
                                    ?>
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" style='min-width:90px;'>班期代碼:</label>
                            <input type="text" id="class_no" name="class_no" class="form-control" style='min-width:170px;' value='<?= $sess_class_no?>'>
                            <label class="control-label" style='min-width:90px;'>班期名稱:</label>
                            <input type="text" id="schedule" name="schedule" class="form-control" style='min-width:170px;' value='<?= $sess_schedule?>'>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" style='min-width:90px;'>年度:</label>
                            <select class='form-control' id='year' style='min-width:170px;'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label" style='min-width:90px;'>依季查詢:</label>
                            <select class='form-control' id='season' style='min-width:170px;'>
                                <option value=""><?= $choices['query_season'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_season']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_season == $i ?"selected":"" ?> ><?= $choices['query_season'][$i];?></option>
                            <?php } ?>
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" style='min-width:90px;'>依月查詢:</label>
                            <select class='form-control' id='startMonth' style='min-width:170px;'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_startMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                            <label class="control-label"> - </label>
                            <select class='form-control' id='endMonth' style='min-width:170px;'>
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
                        <div class="col-xs-12">
                            <button class="btn btn-info" onclick="fowardweek(-7,1);"><<</button>
                            <button class="btn btn-info" onclick="getCurrentWeek(1);">本週</button>
                            <button class="btn btn-info" onclick="fowardweek(7,1);">>></button>
                            <button class="btn btn-info" onclick="setToday(1)">設定今天</button>
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
                            <th class="text-center" colspan="13">臺北市政府公務人員訓練處 承辦人帶班一覽表</th>
                        </tr>
                        <tr>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">班期性質</th>
                            <th class="text-center">系列別</th>
                            <th class="text-center">次類別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">教室</th>
                            <th class="text-center">實招人數</th>
                            <th class="text-center">開班起日</th>
                            <th class="text-center">開班迄日</th>
                            <th class="text-center">實際期程(小時)</th>
                            <th class="text-center">計畫期程(小時)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><?= $data["UNAME"]?></td>
                            <td><?= $data["class_no"]?></td>
                            

                            <td><?php
                             if ('1'==$data['IS_ASSESS'] && '1'==$data['IS_MIXED']){
                                $asses_name = "混成";
                            }else if('1'==$data['IS_ASSESS'] ){

                                $asses_name = "考核";
                            }else{
                                $asses_name='';
                            }
                            
                            echo $asses_name;
                            ?></td>
                            <td><?= $data["TYPE_NAME"]?></td>
                            <td><?= $data["BU_NAME"]?></td>
                            <td>
                                <a title="連結至課程表"
                                    href="<?=base_url('create_class/print_schedule/print/'.$data["seq_no"].'?query_year='.$data["year"].'&query_class_no='.$data["class_no"].'&rows=10&query_class_name='.$data["class_name"])?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;"><?= $data["class_name"]?></a>
                            </td>
                            <td><?= $data["term"]?></td>
                            <td><?= $data["room_name"]?></td>
                            <td>
                                <a title="連結至研習人員名"
                                href="<?=base_url('student_list_pdf.php?uid=55&tmp_seq=0&ShowRetirement=1&year='.$data["year"].'&class_no='.$data["class_no"].'&term='.$data["term"])?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;"><?= $data["student_count"]?></a>
                            </td>
                            <td><?= date('Y-m-d', strtotime($data["start_date1"]))?></td>
                            <td><?= date('Y-m-d', strtotime($data["end_date1"]))?></td>
                            <td><?= $data["Range_real"]?></td>
                            <td><?= $data["Range"]?></td>
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
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</div>
</div>

<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
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

function removeOptions(selectbox) {
    var i;
    for (i = selectbox.options.length - 1; i >= 0; i--) {
        selectbox.remove(i);
    }
}

function getSecond(){
    removeOptions(document.getElementById("query_second"));
    var series = document.getElementById('query_type').value;

    if(series == ''){
        return false;
    }

    var link = "<?=$link_get_second_category;?>";
  
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);

            if (result.length != 0) {
                var second = document.getElementById('query_second');
                var option_name = '請選擇次類別代碼';
                var option_value = '';
                var new_option = new Option(option_name, option_value);
                second.options.add(new_option);
                for (var i = 0; i < result.length; i++) {
                    var option_name = result[i]['name'];
                    var option_value = result[i]['item_id'];
                    var new_option = new Option(option_name, option_value);
                    second.options.add(new_option);
                }
            }
        }
    });
}
</script>

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
        $('#sclass_no').val($('#class_no').val());
        $('#scontactor').val($('#contactor').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        
        $('#sfirstSeries').val($('#query_type').val());
        $('#ssecondSeries').val($('#query_second').val());

        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#srows').val($('select[name=rows]').val());
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
        $('#ischedule').val($('#schedule').val());
        $('#sclass_no').val($('#class_no').val());
        $('#scontactor').val($('#contactor').val());
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#sfirstSeries').val($('#query_type').val());
        $('#ssecondSeries').val($('#query_second').val());
        $('#siscsv').val(1);
        var link = "<?=$link_refresh;?>";
        window.open(link+"?year="+$('#year').val()+"&class_no="+$('#class_no').val()+"&nschedule="+$('#schedule').val()+"&contactor="+$('#contactor').val()+"&type="+type+"&season="+$('#season').val()+"&startMonth="+$('#startMonth').val()+"&endMonth="+$('#endMonth').val()+"&firstSeries="+$('#query_type').val()+"&secondSeries="+$('#query_second').val()+"&start_date="+$('#datepicker1').val()+"&end_date="+$('#test1').val()+"&iscsv=1", "_blank");
        
        // $( "#form" ).submit();
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();   
    });

    // if(window.location.href.indexOf("?") == -1) {
    //     $('#Search').click();
    // }
});
</script>