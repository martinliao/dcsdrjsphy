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
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='sclass_name' name='class_name' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
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
                            <label class="control-label">班期名稱:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="class_name" value="<?=$sess_class_name?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <a href="<?=$link_insideAdd?>" class="btn btn-warning">新增(計畫內)</a>
                            <a href="<?=$link_outsideAdd?>" class="btn btn-warning">新增(計畫外)</a>
                        </div>
                    </div>
                </div>
                <table  border="1" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:30%">修改</th>
                            <th class="text-center" style="width:30%">班期名稱/期別</th>
                            <th class="text-center" style="width:5%">上課<br>日期</th>
                            <th class="text-center" style="width:10%">上課教室</th>
                            <th class="text-center" style="width:5%">學員<br>人數</th>
                            <th class="text-center" style="width:5%">刷到退網址</th>
                            <th class="text-center" style="width:5%">建立者</th>
                            <th class="text-center" style="width:5%">建立<br>時間</th>
                            <th class="text-center" style="width:5%">台北通<br>QRcode</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        for($i=0;$i<count($list);$i++){
                            echo '<tr>';
                            echo '<td style="text-align:center">';
                            echo '<a href="'.$list[$i]['link_edit'].'" style="margin-right:2%"><button class="btn btn-info btn-sm">修改</button></a>';
                            echo '<a href="'.$list[$i]['link_record'].'" style="margin-right:2%"><button class="btn btn-info btn-sm">查詢刷卡紀錄</button></a>';
                            echo '<button class="btn btn-info btn-sm" style="background-color:#f0ad4e" onclick="confirmFun(\''.$list[$i]['link_delete'].'\')">刪除</button>';
                            echo '</td>';
                            echo '<td>'.$list[$i]['class_name'].'</td>';
                            echo '<td style="text-align:center">'.$list[$i]['course_date'].'</td>';
                            echo '<td style="text-align:center">'.$list[$i]['classroom'].'</td>';
                            echo '<td style="text-align:center">'.$list[$i]['student_count'].'</td>';
                            echo '<td>'.$list[$i]['url'].'</td>';
                            echo '<td style="text-align:center">'.$list[$i]['creator'].'</td>';
                            echo '<td style="text-align:center">'.$list[$i]['create_date'].'</td>';
                            
                            if(isset($list[$i]['link_show_qrcode']) && !empty($list[$i]['link_show_qrcode'])){
                                $file_name = '/base/admin/images/outsign/outsign_'.$list[$i]['id'].'.png';
                                echo '<td><a href="'.$list[$i]['link_show_qrcode'].'" target="_blank"><img style="width:100%;height:auto" id="qrpng" src="'.htmlspecialchars($file_name, ENT_HTML5|ENT_QUOTES).'?='.time().'"></a></td>';
                            } else {
                                echo '<td></td>';
                            }
                                
                            echo '</tr>';
                        }
                    ?>    
                    </tbody>
                </table>
            </div>
        </div>
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

function confirmFun(url){
    if(confirm("確認刪除本班期所有紀錄？")){
        location.href = url;
    }

    return false;
}

$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });
    $('#Search').click(function(){
        let count = 0;
        let type = 0;
        if($('#startMonth').val() !="" || $('#endMonth').val() !=""){
            count++;
            type = 2;
        }
        if($('#datepicker1').val() !="" || $('#test1').val() !=""){
            count++;
            type = 3;
        }
        if(count > 1){
            alert("請只填寫一項查詢區間:\n選了月就不能選日.\n選了日就不能選月");
            return;
        }

        $('#syear').val($('#year').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#sclass_name').val($('#class_name').val());
        $( "#form" ).submit();
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });
});
</script>