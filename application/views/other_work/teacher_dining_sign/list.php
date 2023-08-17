
<style>
    #printTable2{
        border: 1px solid #FFF;
    }

    #printTable2 td{
        border: 1px solid #FFF;
    }

    #printTable2 th{
        border: 1px solid #FFF;
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
                <div id="filter-form" role="form" class="form-inline">
                    <form id="form" method="GET">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                    </form>
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
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" style="font-size: 16px">
                            <p style="color: blue;font-weight: bold">1、先搜尋用餐日期。</p>
                            <p style="color: blue;font-weight: bold">2、勾選登記便當欄。</p>
                            <p style="color: blue;font-weight: bold">3、點選藍色區塊之資料後按【設定】。</p>
                            <p style="color: blue;font-weight: bold">4、學員、工作人員請按【新增】鍵入資料。</p>
                        </div>
                    </div>
                    <form id="form-list" method="POST">
                    <input type="hidden" name="mode" id="mode" value=""></input>
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-6">
                            <table border="1" class="table table-bordered table-condensed table-hover">
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;width: 30%">*放置或用餐地點：</td>
                                    <td>
                                        <select id="place" name="place">
                                            <option value="">請選擇</option>
                                            <option value="B">B區</option>
                                            <option value="C">C區</option>
                                            <option value="E">E區</option>
                                            <option value="R">餐廳</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*用餐方式：</td>
                                    <td>
                                        <select id="way" name="way">
                                            <option value="">請選擇</option>
                                            <option value="1">紙盒</option>
                                            <option value="2">鐵盒</option>
                                            <option value="3">餐盤</option>
                                        </select>
                                        (餐盤外送須自取)
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*葷／素：</td>
                                    <td>
                                        <select id="food_type" name="food_type">
                                            <option value="">請選擇</option>
                                            <option value="1">葷</option>
                                            <option value="2">素</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*數量：</td>
                                    <td>
                                        <input type="text" class="form-control" value="1" id="num" name="num">
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">備註：</td>
                                    <td>
                                        <input type="text" class="form-control" style="width: 100%" maxlength="50" value="" id="remark" name="remark">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='setup' class="btn btn-info btn-sm" style="font-weight: bold;background-color: #ffc107">設定</button>
                            <button type="button" id="add" class="btn btn-info btn-sm" style="font-weight: bold;background-color: green" onclick="addFun('<?=$link_data_add?>')">新增(非講座)</button>
                            <button type="button" id="clearDining" class="btn btn-info btn-sm" style="font-weight: bold;background-color: green">刪除登記</button>
                        </div>
                    </div>
                </div>
                <br>
                <!-- /.table head -->
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="13">臺北市政府公務人員訓練處 用餐登記</th>
                        <tr>
                        <tr>
                            <th class="text-center">登記便當</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center" style="width: 10%">申請人</th>
                            <th class="text-center">教室</th>
                            <th class="text-center">用餐人員<br>姓名</th>
                            <th class="text-center">用餐人員<br>類別</th>
                            <th class="text-center" style="background-color: #c9eeff">放置或<br>用餐地點</th>
                            <th class="text-center" style="background-color: #c9eeff">用餐方式</th>
                            <th class="text-center" style="background-color: #c9eeff">葷/素</th>
                            <th class="text-center" style="background-color: #c9eeff">數量</th>
                            <th class="text-center">用餐日期</th>
                            <th class="text-center">備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for($i=0;$i<count($list);$i++){
                                echo '<tr>';

                                if($this->flags->user['idno'] != $list[$i]['worker'] || strtotime(date('Y-m-d')) > strtotime($list[$i]['course_date'])){
                                    echo '<td class="text-center"></td>';
                                } else if(!empty($list[$i]['year']) && !empty($list[$i]['class_no']) && !empty($list[$i]['term']) && !empty($list[$i]['course_date']) && !empty($list[$i]['worker']) && empty($list[$i]['id'])){
                                    $key = $list[$i]['year'].'_'.$list[$i]['class_no'].'_'.$list[$i]['term'].'_'.$list[$i]['course_date'].'_'.$list[$i]['teacher_id'];

                                    echo '<td class="text-center"><input type="checkbox" name="auto[]" value="'.$key.'"></td>';
                                } else if(empty($list[$i]['year']) && $list[$i]['id'] > 0){
                                    echo '<td class="text-center"><input type="checkbox" name="manual[]" value="'.$list[$i]['id'].'"></td>';
                                } else {
                                    echo '<td class="text-center"></td>';
                                }
                                
                                echo '<td class="text-center">'.$list[$i]['class_name'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['term'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['worker_name'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['room_sname'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['teacher_name'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['type'].'</td>';

                                if($i%2 == 0){
                                    echo '<td class="text-center" style="background-color:#E5F6FF">'.$list[$i]['place'].'</td>';
                                    echo '<td class="text-center" style="background-color:#E5F6FF">'.$list[$i]['way'].'</td>';
                                    echo '<td class="text-center" style="background-color:#E5F6FF">'.$list[$i]['food_type'].'</td>';
                                    echo '<td class="text-center" style="background-color:#E5F6FF">'.$list[$i]['num'].'</td>';
                                } else {
                                    echo '<td class="text-center" style="background-color:#FFFFFF">'.$list[$i]['place'].'</td>';
                                    echo '<td class="text-center" style="background-color:#FFFFFF">'.$list[$i]['way'].'</td>';
                                    echo '<td class="text-center" style="background-color:#FFFFFF">'.$list[$i]['food_type'].'</td>';
                                    echo '<td class="text-center" style="background-color:#FFFFFF">'.$list[$i]['num'].'</td>';
                                }
                               
                                echo '<td class="text-center">'.$list[$i]['course_date'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['remark'].'</td>';
                                echo '</tr>'; 
                            }
                        ?>
                    </tbody>
                </table>
                </form>
                <table  border="1" id="printTable2" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="background-color: #FED966;font-size: 26px">B區</th>
                            <th class="text-center" style="background-color: #FED966;font-size: 26px">C區</th>
                            <th class="text-center" style="background-color: #FED966;font-size: 26px">E區</th>
                            <th class="text-center" style="background-color: #c9eeff;color: black;font-size: 26px;width: 20%">餐廳</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="background-color: #FFF2CD">
                                紙盒：<font style="color: red"><?=isset($report['B'][1][1])?$report['B'][1][1]:0?></font>葷、<font style="color: red"><?=isset($report['B'][1][2])?$report['B'][1][2]:0?></font>素
                                <br>
                                鐵盒：<font style="color: red"><?=isset($report['B'][2][1])?$report['B'][2][1]:0?></font>葷、<font style="color: red"><?=isset($report['B'][2][2])?$report['B'][2][2]:0?></font>素
                                <br>
                                餐盤：<font style="color: red"><?=isset($report['B'][3][1])?$report['B'][3][1]:0?></font>葷、<font style="color: red"><?=isset($report['B'][3][2])?$report['B'][3][2]:0?></font>素
                                <br>
                            </td>
                            <td style="background-color: #FFF2CD">
                                紙盒：<font style="color: red"><?=isset($report['C'][1][1])?$report['C'][1][1]:0?></font>葷、<font style="color: red"><?=isset($report['C'][1][2])?$report['C'][1][2]:0?></font>素
                                <br>
                                鐵盒：<font style="color: red"><?=isset($report['C'][2][1])?$report['C'][2][1]:0?></font>葷、<font style="color: red"><?=isset($report['C'][2][2])?$report['C'][2][2]:0?></font>素
                                <br>
                                餐盤：<font style="color: red"><?=isset($report['C'][3][1])?$report['C'][3][1]:0?></font>葷、<font style="color: red"><?=isset($report['C'][3][2])?$report['C'][3][2]:0?></font>素
                                <br>
                            </td>
                            <td style="background-color: #FFF2CD">
                                紙盒：<font style="color: red"><?=isset($report['E'][1][1])?$report['E'][1][1]:0?></font>葷、<font style="color: red"><?=isset($report['E'][1][2])?$report['E'][1][2]:0?></font>素
                                <br>
                                鐵盒：<font style="color: red"><?=isset($report['E'][2][1])?$report['E'][2][1]:0?></font>葷、<font style="color: red"><?=isset($report['E'][2][2])?$report['E'][2][2]:0?></font>素
                                <br>
                                餐盤：<font style="color: red"><?=isset($report['E'][3][1])?$report['E'][3][1]:0?></font>葷、<font style="color: red"><?=isset($report['E'][3][2])?$report['E'][3][2]:0?></font>素
                                <br>
                            </td>
                            <td style="font-size: 12px;background-color: #E5F6FF">
                                紙盒：<font style="color: red"><?=isset($report['R'][1][1])?$report['R'][1][1]:0?></font>葷、<font style="color: red"><?=isset($report['R'][1][2])?$report['R'][1][2]:0?></font>素
                                <br>
                                鐵盒：<font style="color: red"><?=isset($report['R'][2][1])?$report['R'][2][1]:0?></font>葷、<font style="color: red"><?=isset($report['R'][2][2])?$report['R'][2][2]:0?></font>素
                                <br>
                                餐盤：<font style="color: red"><?=isset($report['R'][3][1])?$report['R'][3][1]:0?></font>葷、<font style="color: red"><?=isset($report['R'][3][2])?$report['R'][3][2]:0?></font>素
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="background-color: #FFE699;font-weight: bold">
                                合計：【紙盒<?=$report['total_bce'][1][1]?>葷、<?=$report['total_bce'][1][2]?>素】【鐵盒<?=$report['total_bce'][2][1]?>葷、<?=$report['total_bce'][2][2]?>素】【餐盤<?=$report['total_bce'][3][1]?>葷、<?=$report['total_bce'][3][2]?>素】
                            </td>
                            <td style="background-color: #EAEEF7;font-size: 12px">
                                合計：【紙盒<?=$report['total_r'][1][1]?>葷、<?=$report['total_r'][1][2]?>素】【鐵盒<?=$report['total_r'][2][1]?>葷、<?=$report['total_r'][2][2]?>素】【餐盤<?=$report['total_r'][3][1]?>葷、<?=$report['total_r'][3][2]?>素】
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
function addFun(url)
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

        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(0);
        $( "#form" ).submit();
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

    $('#setup').click(function(){
        if($('input[name="auto[]"]:checked').length == 0 && $('input[name="manual[]"]:checked').length == 0){
            alert('請先勾選一筆登記便當講座');
            return false;
        }

        if($('#place').val() == ''){
            alert('請選擇放置或用餐地點');
            return false;
        }

        if($('#way').val() == ''){
            alert('請選擇用餐方式');
            return false;
        }

        if($('#food_type').val() == ''){
            alert('請選擇葷/素');
            return false;
        }

        if($('#num').val() == ''){
            alert('請輸入數量');
            return false;
        }

        $('#mode').val('setup');
        $("#form-list").submit();
    });

    $('#clearDining').click(function(){
        if($('input[name="auto[]"]:checked').length == 0 && $('input[name="manual[]"]:checked').length == 0){
            alert('請先勾選一筆登記便當講座');
            return false;
        }

        $('#mode').val('clearDining');
        $("#form-list").submit();
    });    
</script>