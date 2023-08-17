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
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">依日期區間查詢<font style="color:red">(至多設定一週)</font>:</label>
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
                            <button class="btn btn-info btn-sm" style="background-color: #FFD966;font-weight: bold;" onclick="keepFun('<?=$link_keep?>')">保留休息室</button>
                        </div>
                    </div>
                </div>
                <br>
                <form id="form-list" method="POST">
                <input type="hidden" name="mode" id="mode" value=""></input>
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                           <label class="control-label">請輸入講座姓名<font style="color:red">(不可空白):</font></label>
                           <input type="text" class="form-control" value="" id="teacher_name" name="teacher_name">
                           <label class="control-label">請輸入班期名稱<font style="color:red">(不可空白):</font></label>
                           <input type="text" class="form-control" value="" id="class_name" name="class_name">
                           <button id="reserve" class="btn btn-info btn-sm" style="background-color: #C55A11">預約</button>
                           <a href="<?=$link_lounge_edit?>"><button type="button" class="btn btn-info btn-sm" style="background-color: #C55A11">修改</button></a>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="background-color: #858484">日期</th>
                            <th class="text-center" style="background-color: #858484">星期</th>
                            <th class="text-center" style="background-color: #858484">休息室</th>
                            <th class="text-center" style="background-color: #858484">08:00-12:00</th>
                            <th class="text-center" style="background-color: #face4a;color:red;font-size: 28px">12:00-13:40</th>
                            <th class="text-center" style="background-color: #858484">13:40-17:30</th>
                            <th class="text-center" style="background-color: #858484">17:30-</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for($i=0;$i<$days;$i++){
                                $today = date('Y-m-d');
                                $thisday = date('Y-m-d',strtotime($sess_start_date ."+ $i days"));
                                $disabled = '';

                                if(strtotime($today)>strtotime($thisday)){
                                    $disabled = 'disabled';
                                }

                                $current_date = date('m/d',strtotime($sess_start_date ."+ $i days"));
                                $current_day = date('w',strtotime($sess_start_date ."+ $i days"));

                                switch ($current_day) {
                                    case '0':
                                        $current_day = '日';
                                        break;
                                    case '1':
                                        $current_day = '一';
                                        break;
                                    case '2':
                                        $current_day = '二';
                                        break;
                                    case '3':
                                        $current_day = '三';
                                        break;
                                    case '4':
                                        $current_day = '四';
                                        break;
                                    case '5':
                                        $current_day = '五';
                                        break;
                                    case '6':
                                        $current_day = '六';
                                        break;
                                    default:
                                        break;
                                }

                                echo '<tr>';
                                echo '<td rowspan="5" class="text-center" style="border-bottom:3px solid #000000">'.$current_date.'</td>';
                                echo '<td rowspan="5" class="text-center" style="border-bottom:3px solid #000000">'.$current_day.'</td>';
                                echo '<td class="text-center">C301</td>';

                                
                                $keep_key = 'C301_'.$thisday;
                                if(isset($keep_list[$keep_key])){
                                    echo '<td><input type="checkbox" name="lounge[]" value="A_C301_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="B_C301_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="C_C301_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="D_C301_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                } else {
                                    $key = 'A_C301_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="A_C301_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="A_C301_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'B_C301_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="B_C301_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="B_C301_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'C_C301_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="C_C301_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="C_C301_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'D_C301_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="D_C301_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="D_C301_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }
                                }
                                echo '</tr>';

                                echo '<tr>';
                                echo '<td class="text-center">C302</td>';

                                $keep_key = 'C302_'.$thisday;
                                if(isset($keep_list[$keep_key])){
                                    echo '<td><input type="checkbox" name="lounge[]" value="A_C302_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="B_C302_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="C_C302_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="D_C302_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                } else {
                                    $key = 'A_C302_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="A_C302_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="A_C302_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'B_C302_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="B_C302_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="B_C302_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'C_C302_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="C_C302_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="C_C302_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'D_C302_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="D_C302_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="D_C302_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }
                                }
                                echo '</tr>';

                                echo '<tr>';
                                echo '<td class="text-center">C303</td>';

                                $keep_key = 'C303_'.$thisday;
                                if(isset($keep_list[$keep_key])){
                                    echo '<td><input type="checkbox" name="lounge[]" value="A_C303_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="B_C303_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="C_C303_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="D_C303_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                } else {
                                    $key = 'A_C303_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="A_C303_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="A_C303_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'B_C303_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="B_C303_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="B_C303_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'C_C303_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="C_C303_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="C_C303_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'D_C303_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="D_C303_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="D_C303_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }
                                }
                                echo '</tr>';

                                echo '<tr>';
                                echo '<td class="text-center">C304</td>';

                                $keep_key = 'C304_'.$thisday;
                                if(isset($keep_list[$keep_key])){
                                    echo '<td><input type="checkbox" name="lounge[]" value="A_C304_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="B_C304_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="C_C304_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td><input type="checkbox" name="lounge[]" value="D_C304_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                } else {
                                    $key = 'A_C304_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="A_C304_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="A_C304_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'B_C304_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="B_C304_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="B_C304_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'C_C304_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="C_C304_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="C_C304_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'D_C304_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td>';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="D_C304_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td><input type="checkbox" name="lounge[]" value="D_C304_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }
                                }
                                echo '</tr>';

                                echo '<tr>';
                                echo '<td class="text-center" style="border-bottom:3px solid #000000">C305</td>';

                                $keep_key = 'C305_'.$thisday;
                                if(isset($keep_list[$keep_key])){
                                    echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="A_C305_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="B_C305_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="C_C305_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                    echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="D_C305_'.$thisday.'" disabled >'.$keep_list[$keep_key].'</td>';
                                } else {
                                    $key = 'A_C305_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td style="border-bottom:3px solid #000000">';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="A_C305_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="A_C305_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'B_C305_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td style="border-bottom:3px solid #000000">';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="B_C305_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="B_C305_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'C_C305_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td style="border-bottom:3px solid #000000">';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="C_C305_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="C_C305_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }

                                    $key = 'D_C305_'.$thisday;
                                    if(isset($data_list[$key])){
                                        echo '<td style="border-bottom:3px solid #000000">';
                                        echo $data_list[$key]['description1'];
                                        echo '<input type="checkbox" name="lounge[]" value="D_C305_'.$thisday.'" disabled checked>';
                                        echo $data_list[$key]['description2'];
                                        echo $data_list[$key]['description3'];
                                        echo '</td>';
                                    } else {
                                        echo '<td style="border-bottom:3px solid #000000"><input type="checkbox" name="lounge[]" value="D_C305_'.$thisday.'" '.$disabled.'>空休息室</td>';
                                    }
                                }
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                </table>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
function keepFun(url)
{
    window.open(url, "keep", "width=1000,height=500");
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

    if(mm > 0 && mm < 10){
        mm = '0' + mm;
    }

    if(dd > 0 && dd < 10){
        dd = '0' + dd;
    }

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

function dateDiff(sDate1, sDate2){    
   var aDate, oDate1, oDate2, iDays;
   
   oDate1 = new Date(sDate1);    
   oDate2 = new Date(sDate2); 
   iDays = (oDate2-oDate1)/86400000;;   

   return  iDays; 
}

function dateCompare(sDate1, sDate2){
   var aDate, oDate1, oDate2, iDays;
   
   oDate1 = new Date(sDate1);    
   oDate2 = new Date(sDate2); 
   if(oDate1 > oDate2){
        return true;
   }

   return  false; 
}

$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });
    $('#Search').click(function(){
        if(dateCompare($('#datepicker1').val(),$('#test1').val())){
            alert('起日不可大於迄日');
            return false;
        }

        var diff = dateDiff($('#datepicker1').val(),$('#test1').val());
        if(diff > 6){
            alert('至多設定一週');
            return false;
        }

        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(0);
        $( "#form" ).submit();
    });

    $('#print').click(function(){
        printData("printTable");
    });

    $('#csv').click(function(){
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

    $('#reserve').click(function(){
        if($('#teacher_name').val().trim() == ''){
            alert('請輸入講座姓名');
            return false;
        }

        if($('#class_name').val().trim() == ''){
            alert('請輸入班期名稱');
            return false;
        }

        $('#mode').val('reserve');
        $("#form-list").submit();
    });
</script>