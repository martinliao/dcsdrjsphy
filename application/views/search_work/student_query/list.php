<!-- <?php print_r($datas)?> -->
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
                    <input hidden id='sclass_no' name='class_no' value="">
                    <input hidden id='sterm' name='term' value="">
                    <input hidden id='sclass_name' name='class_name' value="">
                    <input hidden id='scontactor' name='contactor' value="">
                    <input hidden id='sopen_start_date' name='open_start_date' value="">
                    <input hidden id='sopen_end_date' name='open_end_date' value="">
                    <input hidden id='sapply_start_date' name='apply_start_date' value="">
                    <input hidden id='sapply_end_date' name='apply_end_date' value="">
                    <input hidden id='sclass_start_date' name='class_start_date' value="">
                    <input hidden id='sclass_end_date' name='class_end_date' value="">
                    <input hidden id='sess_mix' name='mix' value="0">
                    <input hidden id='sess_preq' name='preq' value="0">
                    <input hidden id='ssort' name='sort' value="">
                    <input hidden id='siscsv' name='iscsv' value="0">
                    <input hidden id='sact' name='act' value="setup">
                </form>
                <div id="filter-form" role="form" class="form-inline">
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label" style='width:90px;text-align:left;'>年度:</label>
                            <select id='year'>
                                <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?>><?= $year;?>
                                </option>
                                <?php endforeach?>
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">班期代碼:</label>
                            <input type="text" id='class_no' class="form-control" value='<?= $sess_class_no ?>'>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">班期名稱:</label>
                            <input type="text" id='class_name' class="form-control" value='<?= $sess_class_name ?>'>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" style='width:90px;text-align:left;'>承辦人:</label>
                            <select id='contactor'>
                            <option value='' ></option>
                            <?php foreach ($query_contactor as $contactor): ?>
                                <option value='<?= $contactor['PERSONAL_ID']?>' <?= $sess_contactor == $contactor['PERSONAL_ID'] ?"selected":"" ?> ><?= $contactor['NAME'];?></option>
                            <?php endforeach?>
                            </select>
                            <!-- <input type="text" id='contactor' class="form-control" value='<?= $sess_contactor ?>'> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">開班日期起訖:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_open_start_date?>"
                                    id="datepicker1" name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_open_end_date?>"
                                    id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <button class="btn btn-info" onclick="fowardweek1(-7,1);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek1(1);">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek1(7,1);">>></button>
                            <button class="btn btn-info" onclick="setToday1(1)">設定今天</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">報名日期起訖:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_apply_start_date?>"
                                    id="datepicker3" name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_apply_end_date?>"
                                    id="test3" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test4"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <button class="btn btn-info" onclick="fowardweek1(-7,3);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek1(3);">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek1(7,3);">>></button>
                            <button class="btn btn-info" onclick="setToday1(3)">設定今天</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">上課日期起訖:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_class_start_date?>"
                                    id="datepicker5" name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_class_end_date?>"
                                    id="test5" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test6"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <button class="btn btn-info" onclick="fowardweek1(-7,5);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek1(5);">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek1(7,5);">>></button>
                            <button class="btn btn-info" onclick="setToday1(5)">設定今天</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">只含混成班:</label>
                            <input type="checkbox" id="mix" value="1" class="form-group" <?php echo $sess_mix=='y'?"checked":""?>>
                            <label class="control-label">只含課前問卷:</label>
                            <input type="checkbox" id="preq" value="1" class="form-group" <?php echo $sess_preq=='y'?"checked":""?>>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="ClearallData()">清除所有日期</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出</button>
                            <button id="print" class="btn btn-info btn-sm">列印</button>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <div class="row">
                    <div class="col-xs-12">
                        <div id="list-form">
                            <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <?php if ($has_rid == "Y"){?>
                                            <th class="text-center" colspan="16">臺北市政府公務人員訓練處 各班期課表及研習人員名冊</th>
                                        <?php }else{ ?>
                                            <th class="text-center" colspan="15">臺北市政府公務人員訓練處 各班期課表及研習人員名冊</th>
                                        <?php }?>
                                    </tr>
                                    <tr>
                                        <th class="text-center">班期代碼</th>
                                        <th class="text-center sorting<?=($sess_sort=='class_name+asc')?'_asc':'';?><?=($sess_sort=='class_name+desc')?'_desc':'';?>" data-field="class_name" onclick="sortColumn(<?=($sess_sort=='class_name+asc')?'\'class_name+desc\'':'\'class_name+asc\'';?>)" style='min-width:140px;'>班期名稱</th>
                                        <th class="text-center">期別</th>
                                        <th class="text-center sorting<?=($sess_sort=='contactor+asc')?'_asc':'';?><?=($sess_sort=='contactor+desc')?'_desc':'';?>" data-field="contactor" onclick="sortColumn(<?=($sess_sort=='contactor+asc')?'\'contactor+desc\'':'\'contactor+asc\'';?>)" style='min-width:90px;'>承辦人(分機)</th>
                                        <th class="text-center">教室</th>
                                        <?php if ($has_rid == "Y"){?>
                                        <th class="text-center">當日教室</th>
                                        <?php } ?>
                                        <th class="text-center sorting<?=($sess_sort=='apply_s_date+asc')?'_asc':'';?><?=($sess_sort=='apply_s_date+desc')?'_desc':'';?>" data-field="apply_s_date" onclick="sortColumn(<?=($sess_sort=='apply_s_date+asc')?'\'apply_s_date+desc\'':'\'apply_s_date+asc\'';?>)" style='min-width:140px;'>報名起迄日</th>
                                        <th class="text-center">期程(小時)</th>
                                        <th class="text-center sorting<?=($sess_sort=='start_date1+asc')?'_asc':'';?><?=($sess_sort=='start_date1+desc')?'_desc':'';?>" data-field="start_date1" onclick="sortColumn(<?=($sess_sort=='start_date1+asc')?'\'start_date1+desc\'':'\'start_date1+asc\'';?>)" style='min-width:140px;'>開班日期</th>
                                        <th class="text-center">預計人數</th>
                                        <th class="text-center">報名人數</th>
                                        <th class="text-center">調訓日期</th>
                                        <th class="text-center">選+調結=研習人數</th>
                                        <th class="text-center">用餐人數</th>
                                        <?php if ($sess_preq == "y"){?>
                                        <th class="text-center">課前問卷結果</th>
                                        <?php } ?>
                                        <th class="text-center">課表</th>
                                        <th class="text-center">名冊</th>
                                    </tr>
                                </thead>
                                <tbody>


                                <?php foreach ($datas as $data): ?>
                                    <?php if ($data["is_cancel"] == '1')
                        $color = 'red';
                else
                        $color = '';?>

                                    <tr class="text-center">

                                        <td style="text-align: center;color:<?=$color?>"><?= $data["class_no"]?></td>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["class_name"]?>
                                            <?php if($data["is_cancel"] == '1'){
                                                echo '(取消開班)';
                                            } ?>
                                        </td>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["term"]?></td>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["contactor"]?></td>
                                        <td style="text-align: center;color:<?=$color?>"><?= isset($data["room_code_name"])==true?$data["room_code_name"]:""?></td>
                                        <?php if ($has_rid == 'Y'){?>
                                        <td style="text-align: center;color:blue"><?= isset($data["rid_name"])==true?$data["rid_name"]:""?></td>
                                        <?php } ?>
                                        <td style="text-align: center;color:<?=$color?>">
                                            <?= $data["apply_s_date"]."~".$data["apply_e_date"] ?>
                                            <?php if($data["apply_s_date2"]!=null&&$data["apply_e_date2"]!=null) { ?>
                                                、<br><?= $data["apply_s_date2"]."~".$data["apply_e_date2"] ?>
                                            <?php } ?>
                                        </td>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["range"]?></td>
                                        <td style="text-align: center;color:<?=$color?>">
                                            <?php
                                                if(isset($data["min_from_time"])){
                                                    echo $data["min_from_time"]['from_time']."<br>";
                                                } 
                                            ?> 
                                            <?= $data["start_date1"]."~".$data["end_date1"] ?>
                                        </td>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["no_persons"] ?></td>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["scount"] ?></td>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["mail_date"] ?></td>
                                        <td style="text-align: center;color:<?=$color?>">
                                            <?= $data["acount"]."+".$data["bcount"]."=".$data["gcount"] ?></td>

                                        <?php if ($data['ecount']-$data['gcount'] > '5' || $data['ecount']-$data['gcount'] < '-5'){?>
                                        <td style="text-align: center;color:<?=$color?>">
                                            <font color="red"><?= $data["ecount"] ?></font>
                                        </td>
                                        <?php }else{?>
                                        <td style="text-align: center;color:<?=$color?>"><?= $data["ecount"] ?></td>
                                        <?php }?>

                                        <?php if ($sess_preq == 'y') {?>
                                        <td style="text-align: center;color:<?=$color?>">
                                            <?php if ($data["preq_anscount"] == '0' ){ ?>
                                            <?= $data["preq_anscount"]?></td>
                                        <?php } else {?>
                                        <?= $data["preq_anscount"]?></td>
                                        <?php } ?>

                                        <?php } ?>

                                        <td><a title="連結至課程表"
                                                href="<?=base_url('create_class/print_schedule/print/'.$data["seq_no"].'?query_year='.$data["year"].'&query_class_no='.$data["class_no"].'&rows=10&query_class_name='.$data["class_name"])?>"
                                                onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">課程表</a>
                                        </td>
                                        <!-- <td class="text-center">
                                            <a title="連結至研習人員名冊"
                                                href="/base/admin/search_work/student_query?year=<?= $data["year"]?>&class_no=<?= $data["class_no"]?>&term=<?= $data["term"]?>&tmp_seq=0&act=dd"
                                                onclick="window.open(this.href, 'dd','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=900');return false;">名冊</a>

                                        </td> -->
                                        <td>
                                            <a title="連結至研習人員名冊"
                                                href="<?=base_url('student_list_pdf.php?uid=55&tmp_seq=0&ShowRetirement=1&year='.$data["year"].'&class_no='.$data["class_no"].'&term='.$data["term"])?>"
                                                onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">名冊</a>
                                        </td>

                                    </tr>

                                <?php endforeach?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-bordered table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="8">場地外借及教室使用一覽表</th>
                                </tr>
                                <tr>
                                    <th class="text-center">使用起日</th>
                                    <th class="text-center">使用迄日</th>
                                    <th class="text-center">使用時段</th>
                                    <th class="text-center">活動名稱</th>
                                    <th class="text-center">教室</th>
                                    <th class="text-center">申請單位</th>
                                    <th class="text-center">聯絡人</th>
                                    <th class="text-center">聯絡電話</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datas2 as $data_5c): ?>

                                <tr>
                                    
                                    <td
                                        style="text-align: center;">
                                        <!-- <?php foreach ($data_5c['app_start'] as $app_start): ?>
                                        <?= date('Y-m-d', strtotime($app_start))?>
                                        <?php endforeach?> -->
                                        <?= date('Y-m-d', strtotime($data_5c['app_start']))?>
                                    </td>
                                    <td
                                        style="text-align: center;">
                                        <!-- <?php foreach ($data_5c['app_end'] as $app_end): ?>
                                        <?= date('Y-m-d', strtotime($app_end))?>
                                        <?php endforeach?> -->
                                        <?= date('Y-m-d', strtotime($data_5c['app_end']))?>
                                    </td>
                                    <td
                                        style="text-align: center;">
                                        <!-- <?php foreach ($data_5c['period'] as $period): ?>
                                        <?= $period?>
                                        <?php endforeach?> -->
                                        <?= $data_5c['period']?>
                                    </td>
                                    <td
                                        style="text-align: center;">
                                        <?= $data_5c['app_reason']?>
                                    </td>
                                    <td
                                        style="text-align: center;color:blue">
                                        <!-- <?php foreach ($data_5c['room_name'] as $room_name): ?>
                                        <?= $room_name?>
                                        <?php endforeach?> -->
                                        <?= $data_5c['room_name']?>
                                    </td>
                                    <td
                                        style="text-align: center;">
                                        <?= $data_5c['app_name']?>
                                    </td>
                                    <td
                                        style="text-align: center;">
                                        <?= $data_5c['contact_name']?>
                                    </td>
                                    <td
                                        style="text-align: center;">
                                        <?= $data_5c['tel']?>
                                    </td>
                                    <?php endforeach?>

                            </tbody>
                        </table>
                        <?php
                            if (count($datas)==0){
                            echo '<br><font color="#FF0000">查無資料</font>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
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
    var mm = result.getMonth() + 1;
    var yy = result.getFullYear();
    result = yy + '-' + mm + '-' + dd;
    return result;
}

function sortColumn(sortStr) {
$('#syear').val($('#year').val());
        $('#sclass_no').val($('#class_no').val());
        $('#sclass_name').val($('#class_name').val());
        $('#scontactor').val($('#contactor').val());

        if ($('input#mix').is(':checked')) {
            $('#sess_mix').val('y');
        } else {
            $('#sess_mix').val(0);
        }

        if ($('input#preq').is(':checked')) {
            $('#sess_preq').val('y');

        } else {
            $('#sess_preq').val(0);
        }

        $('#sopen_start_date').val($('#datepicker1').val());
        $('#sopen_end_date').val($('#test1').val());
        $('#sapply_start_date').val($('#datepicker3').val());
        $('#sapply_end_date').val($('#test3').val());
        $('#sclass_start_date').val($('#datepicker5').val());
        $('#sclass_end_date').val($('#test5').val());
        $('#sidname').val($('#idname').val());
        $('#sname').val($('#name').val());
        $('#ssort').val(sortStr);
        $('#sact').val('search');
        $('#siscsv').val(0);
        $("#form").submit();
}

$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function() {
        $("#test1").focus();
    });
    $("#test3").datepicker();
    $('#test4').click(function() {
        $("#test3").focus();
    });
    $("#test5").datepicker();
    $('#test6').click(function() {
        $("#test5").focus();
    });

    $('#Search').click(function() {

        $('#syear').val($('#year').val());
        $('#sclass_no').val($('#class_no').val());
        $('#sclass_name').val($('#class_name').val());
        $('#scontactor').val($('#contactor').val());

        if ($('input#mix').is(':checked')) {
            $('#sess_mix').val('y');
        } else {
            $('#sess_mix').val(0);
        }

        if ($('input#preq').is(':checked')) {
            $('#sess_preq').val('y');

        } else {
            $('#sess_preq').val(0);
        }

        $('#sopen_start_date').val($('#datepicker1').val());
        $('#sopen_end_date').val($('#test1').val());
        $('#sapply_start_date').val($('#datepicker3').val());
        $('#sapply_end_date').val($('#test3').val());
        $('#sclass_start_date').val($('#datepicker5').val());
        $('#sclass_end_date').val($('#test5').val());

        $('#sidname').val($('#idname').val());
        $('#sname').val($('#name').val());
        $('#sact').val('search');

        $('#siscsv').val(0);
        $("#form").submit();
    });

    $('#print').click(function() {
        printData("printTable");
    });




    $('#csv').click(function() {
        $('#syear').val($('#year').val());
        $('#sclass_no').val($('#class_no').val());
        $('#sclass_name').val($('#class_name').val());
        $('#scontactor').val($('#contactor').val());

        if ($('input#mix').is(':checked')) {
            $('#sess_mix').val('y');
        } else {
            $('#sess_mix').val(0);
        }

        if ($('input#preq').is(':checked')) {
            $('#sess_preq').val('y');
        } else {
            $('#sess_preq').val(0);
        }

        $('#sopen_start_date').val($('#datepicker1').val());
        $('#sopen_end_date').val($('#test1').val());
        $('#sapply_start_date').val($('#datepicker3').val());
        $('#sapply_end_date').val($('#test3').val());
        $('#sclass_start_date').val($('#datepicker5').val());
        $('#sclass_end_date').val($('#test5').val());
        $('#sidname').val($('#idname').val());
        $('#sname').val($('#name').val());
        $('#ssort').val('');
        $('#sact').val('csv');
        $('#siscsv').val(1);
        $("#form").submit();
    });




    $("#datepicker1").datepicker();
    $('#datepicker2').click(function() {
        $("#datepicker1").focus();
    });
    $("#datepicker3").datepicker();
    $('#datepicker4').click(function() {
        $("#datepicker3").focus();
    });
    $("#datepicker5").datepicker();
    $('#datepicker6').click(function() {
        $("#datepicker5").focus();
    });
   /*
    if($('#datepicker5').val() == "" && $('#test5').val() == "" && $('#datepicker3').val() == "" && $('#test3').val() == "" && $('#datepicker1').val() == "" && $('#test1').val() == "") {
        setToday1(5);
        $('#Search').click();
    }*/
});

//>>清除所有日期
function ClearallData() {
    $('#datepicker1').val('')
    $('#test1').val('')
    $('#datepicker3').val('')
    $('#test3').val('')
    $('#datepicker5').val('')
    $('#test5').val('')
}
</script>