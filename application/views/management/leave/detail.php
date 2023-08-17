<style type="text/css">
    #rows input, #rows select {
        border-radius: 3px;
        font-size: 12px;
        height: 30px;
        line-height: 1.5;
        padding: 5px 10px;
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
                <form id="filter-form" role="form" class="form-inline" method="POST" action="">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="action" value="recount">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" value="<?=$class_info->year?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$class_info->class_no?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$class_info->class_name?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$class_info->term?>" disabled>
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">實際下課日期:</label>
                                <select name="real_end_date">
                                    <?php foreach($room_uses as $room_use): ?>
                                    <option value="<?=$room_use->use_date?>"><?=$room_use->use_date?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">實際上課時間(0800):</label>
                                <input type="text" name="real_start_time" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label">實際下課時間(1620):</label>
                                <input type="text" name="real_end_time" class="form-control">
                            </div>                            
                        </div>
                    </div>

                </form>
                
                <div class="row"> 
                    <div class="col-xs-12">
                        <button class="btn btn-info" onclick="recount()">重新計算</button>
                    </div>
                </div>
                <hr>
                <form class="form-inline" method="POST" action="<?=base_url("management/leave/add")?>">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" >
                    <input type="hidden" name="year" id="year" value="<?=$class_info->year?>">
                    <input type="hidden" name="term" id="term" value="<?=$class_info->term?>">
                    <input type="hidden" name="class_no" id="class_no" value="<?=$class_info->class_no?>">

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">學號(必填)</label>
                                <input type="text" class="form-control" style="width: 60px" id="st_no" onchange="getStudent('<?=$class_info->year?>', '<?=$class_info->class_no?>', '<?=$class_info->term?>')">
                                <input type="hidden" name="id" id="idno" value="">
                            </div>
                            <div class="form-group">
                                <label class="control-label">學員姓名:</label>
                                <input type="text" id="student_name" class="form-control" style="width: 120px" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">假別:</label>
                                <select name="va_code">
                                    <option value="01">請假</option>
                                    <option value="02">未請假</option>
                                    <option value="03">未留宿</option>
                                <select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">請假日期(必填):</label>
                                <select name="vacation_date" id="vacation_date">
                                    <option value=""></option>
                                    <?php foreach($room_uses as $room_use): ?>
                                    <option value="<?=$room_use->use_date?>"><?=$room_use->use_date?></option>
                                    <?php endforeach ?>
                                <select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">請假開始時間(必填)(0830):</label>
                                <input type="text" class="form-control" style="width: 65px" name="from_time" id="from_time">
                            </div>
                            <div class="form-group">
                                <label class="control-label">請假結束時間(必填)(1750):</label>
                                <input type="text" class="form-control" style="width: 65px" name="to_time" id="to_time">
                            </div>
                             <div class="form-group">
                                <label class="control-label">請假時數:</label>
                                <input type="text" class="form-control" style="width: 45px" name="hours" id="hours">
                            </div>
                        </div>
                    </div>
                    
                    <input type="submit" class="btn btn-info" value="新增請假人員" onclick="return check()">
                </form>

                <form id="rows" action="" class="form-inline">    
                    <input type="hidden" name="year" value="<?=$class_info->year?>">
                    <input type="hidden" name="term" value="<?=$class_info->term?>">
                    <input type="hidden" name="class_no" value="<?=$class_info->class_no?>">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <form id="data-form" role="form" class="form-inline" method="POST" action="">  
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr style="background: #8CBBFF;">
                            <th>全選<input type="checkbox" id="chkall" onclick="checkAll(this,'chk[]')"></th>
                            <th>學號</th>
                            <th>學員姓名</th>
                            <th>假別</th>
                            <th>請假日期</th>
                            <th>請假開始時間</th>
                            <th>請假結束時間</th>
                            <th>請假時數</th>
                            <th>流水號</th>
                            <th>線上請假<br>起時</th>
                            <th>線上請假<br>迄時</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                           
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" >
                        <input type="hidden" name="action" value="update">
                        <?php foreach($leaves as $leave): ?>
                        <tr>
                            <?php if(isset($leave->seq_no)){?>
                            <td><input type="checkbox" name="chk[]" value="<?=$leave->seq_no?>"></td>
                            <td><?=$leave->st_no?></td>
                            <td><?=$leave->name?></td>
                            <td>
                                <select name="leaves[<?=$leave->seq_no?>][va_code]">
                                    <option value="00"></option>
                                    <option value="01" <?=($leave->va_code == "01") ? 'selected' : ''?>>請假</option>
                                    <option value="02" <?=($leave->va_code == "02") ? 'selected' : ''?>>未請假</option>
                                    <option value="03" <?=($leave->va_code == "03") ? 'selected' : ''?>>未留宿</option>
                                </select>                                
                            </td>
                            <td>
                                <select name="leaves[<?=$leave->seq_no?>][vacation_date]">
                                    <option value=""></option>
                                    <?php foreach($room_uses as $room_use): ?>
                                    <option value="<?=$room_use->use_date?>" <?=($room_use->use_date == $leave->vacation_date) ? 'selected' : ''?> ><?=$room_use->use_date?></option>
                                    <?php endforeach ?>
                                <select>
                            </td>
                            <td><input type="text" name="leaves[<?=$leave->seq_no?>][from_time]" value="<?=$leave->from_time?>"></td>
                            <td><input type="text" name="leaves[<?=$leave->seq_no?>][to_time]" value="<?=$leave->to_time?>"></td>
                            <td><input type="text" name="leaves[<?=$leave->seq_no?>][hours]" value="<?=$leave->hours?>"></td>
                            <td><?=$leave->seq_no?></td>
                            <td><?=isset($leave->online_from_time)?$leave->online_from_time:''?></td>
                            <td><?=isset($leave->online_to_time)?$leave->online_to_time:''?></td>
                            <?php } else { ?>
                            <td></td>
                            <td><?=$leave['st_no']?></td>
                            <td><?=$leave['name']?></td>
                            <td></td>
                            <td><?=$leave['vacation_date']?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?=$leave['from_time']?></td>
                            <td><?=$leave['to_time']?></td>
                            <?php } ?> 
                            
                        </tr>
                        <?php endforeach ?>
                        
                    </tbody>
                    
                </table>
                </form>
                <div class="row ">
                    <div class="col-lg-4">
                        Showing <?=count($leaves)?> / <?=$paginate_config['total_rows']?> entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>
$(document).ready(function () {
    //console.log(path);

    $('#to_time').blur(function () {

        var year = $('#year').val();
        var classNo = $('#class_no').val();
        var term = $('#term').val();
        var courseDate = $('#vacation_date').val();
        var s_hour = document.getElementById('from_time').value;
        var e_hour = document.getElementById('to_time').value;

        if(s_hour =='' || s_hour.length!=4 )
        {
            alert("請輸入請假開始時分(例如:0830=8點半開始)!");
            return false;
        }
        if (isNaN(s_hour)|| parseInt(s_hour.substring(0,2))>23 || parseInt(s_hour.substring(2,4))>59)
        {
            alert("請假開始時分格式錯誤!");
            return false;
        }

        if(e_hour =='' || e_hour.length!=4 )
        {
            alert("請輸入請假結束時分(例如:1230=12點半結束)!");
            return false;
        }

        if (isNaN(e_hour)|| parseInt(e_hour.substring(0,2))>24 || parseInt(e_hour.substring(2,4))>59)
        {
            alert("請假結束時分格式錯誤!");
            return false;
        }

        if (s_hour =='' || isNaN(s_hour) || e_hour =='' || isNaN(e_hour)) 
        {
          return false;
            }
        else
            {
            
            hour = calc_times(s_hour, e_hour);
            document.getElementById('hours').value = hour;                   
        }
    });
    
    //中午1200-1300不算版
    function calc_times(s_hour,e_hour)
    {
        var start_time =  "0830"; //請假開始時間(0830) = 8*60+30 510min 1
        var end_time = "1730";//請假結束時間(1730) = 17*60+30 1050min
        var hour = 0;
        
        //alert("1:"+s_hour+" "+e_hour+" "+hour);
        //alert(s_hour);
        if (s_hour.substring(0,1)=='0')
            s_hour = (parseInt(s_hour.substring(1,2))*60)+(parseInt(s_hour.substring(2,4)));
        else
            s_hour = (parseInt(s_hour.substring(0,2))*60)+(parseInt(s_hour.substring(2,4)));
        if (e_hour.substring(0,1)=='0')
            e_hour = (parseInt(e_hour.substring(1,2))*60)+(parseInt(e_hour.substring(2,4)));
        else
            e_hour = (parseInt(e_hour.substring(0,2))*60)+(parseInt(e_hour.substring(2,4)));
        //alert("2:"+s_hour+" "+e_hour+" "+hour);
        if(s_hour < 510 )
            s_hour = 510;
        if(e_hour < 510 )
            e_hour = 510;
        /*  
        if(s_hour > 1050 )
            s_hour = 1050;
        if(e_hour > 1050 )
            e_hour = 1050;
        */  
        //alert(s_hour+" "+e_hour+" "+hour);
        if((s_hour >= 510 && s_hour <= 780) && (e_hour >= 510 && e_hour <= 780) )//同區間 早上
        {
            hour = e_hour - s_hour;
            if(hour>240)
                hour = 240;
        }           
        else if ((s_hour >= 780 && s_hour <= 1050) && (e_hour >= 780 && e_hour <= 1050))//同區間 下午
        {
            hour = e_hour - s_hour;
        }   
        else if (s_hour >= 1050 && e_hour >= 1050) //同區間 晚上
        {
            hour = e_hour - s_hour;
        }
        else
        {
            s_hour = 720 - s_hour;
            e_hour = e_hour - 780;
            hour = s_hour + e_hour;
    }
        //alert(s_hour+" "+e_hour+" "+hour);
        if((hour%60)>0){
            return Math.floor((hour/60)+1);
        }
        else{
            return Math.floor((hour/60));
            
        }
    
    }
});

function checkAll(id,name)
{
    var checkboxs = document.getElementsByName(name);
    for(var i=0;i<checkboxs.length;i++)
        {
            checkboxs[i].checked = id.checked;
        } 
}

function recount(){
    var form = $("#filter-form");
    var real_end_time = $("input[name='real_end_time']")[0];
    var real_start_time = $("input[name='real_start_time']")[0];

    var re = /^[0-9]{4}$/; // 最多4個字，且都是數字 0-9

    var re_st_test = re.test(real_start_time.value);
    var re_en_test = re.test(real_end_time.value);

    console.log(re_en_test);

    if (re_st_test || re_en_test){
        form.submit();
    }else{
        bk_alert(3, '未輸入或者格式錯誤，時間格式為4個數字，例如 1620', 4, 'center');
    }
    
    return false;
}

function sendFun(){
    $("#rows").submit();
}    

function getStudent(year, class_no, term){
    var st_no = document.getElementById("st_no").value;
    var link = "<?=$link_get_stud;?>";
    $.ajax({
        url: link+"?year=" + year + "&class_no=" + class_no  + "&term=" + term + "&st_no=" + st_no
    }).done(function(response) {
        response = JSON.parse(response);
        // console.log(response);
        document.getElementById("student_name").value = "";
        document.getElementById("idno").value = "";
        if (response.status == 1){
            alert("找不到該學員");
        }else{
            if (response.student.yn_sel === '4' || response.student.yn_sel === '5' ){
                alert("無此學號(可能未報到或退訓)");
            }
            document.getElementById("student_name").value = response.student.name;
            document.getElementById("idno").value = response.student.id;
        }
    });
}

function checktime(value){
    var re = /^[0-9]{4}$/; // 最多4個字，且都是數字 0-9
    var test = re.test(value);
    
    if (test == false){
        alert("未輸入或者格式錯誤，時間格式為4個數字，例如 1620");
        return false;
    }else{
        return true;
    }
    
}

function check(){
    var from_time = document.getElementsByName("from_time")[0].value;
    var to_time = document.getElementsByName("to_time")[0].value;
    var id = document.getElementsByName("id")[0].value;
    var va_code = document.getElementsByName("va_code")[0].value;
    var vacation_date = document.getElementsByName("vacation_date")[0].value;
    var hours = document.getElementsByName("hours")[0].value;

    if (id == ""){
        alert("請輸入學號");
        return false;        
    }

    if (va_code == ""){
        alert("請選擇假別");
        return false;        
    }

    if (vacation_date == ""){
        alert("請選擇請假日期");
        return false;        
    }

    // if (hours == ""){
    //     alert("請輸入時數");
    //     return false;        
    // }

    if (checktime(from_time) == false){
        return false;
    }
    
    if (checktime(to_time) == false){
        return false;
    }

    return true;
}

function actionDelete2(){
    document.getElementsByName("action")[1].value = "delete";
    $("#data-form").submit();
}


</script>