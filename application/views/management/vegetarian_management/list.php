<!-- <?php print_r($datas)?> -->
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
                            <input hidden id='sstart_date' name='start_date' value="">
                            <input hidden id='syear' name='year' value="">
                            <input hidden id='sclassno' name='classno' value="">
                            <input hidden id='sterm' name='term' value="">
                            <input hidden id='sid' name='id' value="">
                            <input hidden id='sact' name='act' value="">
                            <input hidden id='sremark' name='remark' value="">
                        </form>
                        <div class="col-xs-12">
                            <label class="control-label">上課日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?= $sess_start_date?>" id="datepicker1" name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <button id="Search" class="btn btn-info">查詢</button>
                            <?php
                                echo '<font style="color:red">用餐總人數：'.$diningTotal.'人&nbsp;&nbsp;&nbsp;素食總人數'.$vegetarianTotal.'人</font>';
                            ?>
                        </div>
                    </div>

                </div>
                <!-- /.table head -->
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-bordered table-condensed table-hover">
                            <thead>
                                <tr>
                                    <!-- <th class="text-center">年度</th>
                                    <th class="text-center">班期代碼</th> -->
                                    <th class="text-center">班期名稱</th>
                                    <th class="text-center">期別</th>
                                    <th class="text-center">承辦人</th>
                                    <th class="text-center">表定下課</th>
                                    <th class="text-center">用餐數</th>
                                    <th class="text-center">人工簽到數</th>
                                    <th class="text-center">教室</th>
                                    <th class="text-center">備註</th>
                                    <th class="text-center">素餐數</th>
                                    <th class="text-center" style="width:150px">老師素食</th>
                                    <th class="text-center">抵達</th>
                                    <th class="text-center">功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datas as $data): ?>
                        
                                <tr class="text-center">
                                    <!-- <td><?= $data["YEAR"]?></td>
                                    <td><?= $data["CLASS_NO"]?></td> -->
                                    <td><?= $data["class_name"]?></td>
                                    <td><?= $data["TERM"]?></td>
                                    <td><?= $data["name"] ?><?= '#'.$data["office_tel"] ?></td>
                                   
                                    <td><?= $data["TO_TIME"]?></td>
                                    <td><?= $data["DINING_COUNT"] ?></td>
                                    <td><?= $data["hand_people_num"] ?></td>
                                    <td><?= $data["room_id"]?></td>
                                    <!--備註欄位-->
                                    <!-- $para = $data[$i]['YEAR'].','.'\''.$data[$i]['CLASS_NO'].'\','.$data[$i]['TERM'].',\''.$data[$i]['COURSE_DATE'].'\',this.value';
                                    echo '<td align="center" bgcolor="' . $col . '"><input type="text" name="remark" id="remark" value="'.$remark.'" onchange="saveFun('.$para.')"></td>'; -->
                                    <td><input type="text" class="form-control" name="remark" id="remark" value="<?= $data["REMARK"]?>" onchange="saveFun('<?= $data['YEAR']?>','<?= $data['CLASS_NO']?>','<?= $data['TERM']?>','<?= $sess_start_date?>',this.value)"></td>
                                    <td><?= $data["totalCount"]?></td>
                                    <td id="teach_vegt_td_<?=$data['YEAR'].$data['CLASS_NO'].$data['TERM']?>" value="<?= $data["teacher_vegt"]?>" <?=$data['teacher_vegt_changed']=='1'?'style="background-color:yellow"':''?> >
                                        <input type="text" class="form-control"  style="width:50px;display: inline-table;" id="teacher_vegt_<?=$data['YEAR'].$data['CLASS_NO'].$data['TERM']?>" value="<?= $data["teacher_vegt"]?>" >
                                        <input type="button" class="btn btn-info" style="display: inline-table;" value="儲存" onclick="teachVegtFun('<?= $data['YEAR']?>','<?= $data['TERM']?>','<?= $data['CLASS_NO']?>')">
                                    </td>
                                    <td id="<?=$data['CLASS_NO'].$data['TERM']?>ARRIVAL_TIME"><?= $data["ARRIVAL_TIME"]?></td>

                                    <td id="<?=$data['CLASS_NO'].$data['TERM']?>">
                                        <?php if($data["ARRIVAL_TIME"]==""): ?>
                                            <input type="button" class="btn btn-info" value="抵達" onclick="arrivalFun('<?= $data['YEAR']?>','<?= $data['TERM']?>','<?= $data['CLASS_NO']?>')">
                                            <input type="button" class="btn btn-info" value="便當" onclick="arrivalFunBin('<?= $data['YEAR']?>','<?= $data['TERM']?>','<?= $data['CLASS_NO']?>')">
                                        <?php else: ?>
                                                <button type="button" class="btn btn-danger" onclick="resetVegetarianSearch('<?= $data['YEAR']?>','<?= $data['CLASS_NO']?>','<?= $data['TERM']?>', '<?=(new DateTime($data['COURSE_DATE']))->format('Y-m-d')?>')">重設</button>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                
                                <?php endforeach?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                if (!empty($data2)) {
                    echo '<p>學員表(素餐)</p>';
                    echo '<p style="color:red">當日申請素食人數'.$data3['fields_total'].'人，已取餐'.$data3['fields_get'].'人，未取餐'.($data3['fields_total'] - $data3['fields_get']).'人</p>';
                    echo '<table width="99%" border="0" cellspacing="0" cellpadding="0">';        
                } else {
                    echo '<table width="99%" border="0" cellspacing="0" cellpadding="0" style="display:none">';
                }
                ?>
                <!-- /.table head -->
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-bordered table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">班期名稱</th>
                                    <th class="text-center">班期期別</th>
                                    <th class="text-center">名字</th>
                                    <th class="text-center">學號</th>
                                    <th class="text-center">抵達時間</th>
                                    <th class="text-center">功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php foreach ($data2 as $data): ?>
                        
                                <tr class="text-center">
                                    <td><?= $data["class_name"]?></td>
                                    <td><?= $data["term"]?></td>
                                    <td><?= $data["name"]?></td>
                                    <td><?= $data["st_no"]?></td>
                                    <td id="V<?=$data['id']?>ARRIVAL_TIME"><?= $data["get_time"]?></td>
                                    
                                    <td id="V<?=$data['id']?>">
                                        <?php if($data["get_time"]==""): ?>
                                            <input type="button" class="btn btn-info" value="取餐" onclick="getFun('<?= $data['id']?>')">
                                        <?php else: ?>
                                            <button type="button" class="btn btn-danger" onclick="cancelFun('<?= $data['id']?>')">重設</button>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                
                                <?php endforeach?>
                            </tbody>
                        </table>
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

function check_all(obj,cName) 
{ 
    var checkboxs = document.getElementsByName(cName); 
    for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
} 

function teachVegtFun(year,term,classno){
    $('#sstart_date').val($('#datepicker1').val());
    var start_date = document.getElementById('sstart_date').value;

    var idKey = year+classno+term;
    var teach_vegt_count = document.getElementById('teacher_vegt_'+idKey).value;

    var link = "<?=$link_teachVegtFun;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'year': year,
        'classno': classno,
        'term': term,
        'start_date': start_date,
        'teach_vegt_count': teach_vegt_count
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            // alert('Ajax request error');
        },
        success: function(response) {
            document.getElementById('teach_vegt_td_'+idKey).style.backgroundColor = 'yellow';
        }
    });

    
}

function arrivalFun(year,term,classno){
    var idKey = classno+term;
    $('#'+idKey).html('');

    $('#sstart_date').val($('#datepicker1').val());
    var start_date = document.getElementById('sstart_date').value;

    var link = "<?=$link_arrivalFun;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'year': year,
        'classno': classno,
        'term': term,
        'start_date': start_date,
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            // alert('Ajax request error');
        },
        success: function(response) {
            showResetVegetarianButton(year, classno, term);
        }
    });

    
}

function arrivalFunBin(year,term,classno){
    var idKey = classno+term;
    $('#'+idKey).html('');

    $('#sstart_date').val($('#datepicker1').val());
    
    var start_date = document.getElementById('sstart_date').value;

    var link = "<?=$link_arrivalFunBin;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'year': year,
        'classno': classno,
        'term': term,
        'start_date': start_date,
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            // alert('Ajax request error');
        },
        success: function(response) {
            showResetVegetarianButton(year, classno, term);
        }
    });
    
}

function showResetVegetarianButton(year, classno, term)
{
    var idKey = classno + term;
    let html = '<button type="button" class="btn btn-danger" onclick="resetVegetarianSearch(\'' + year + '\',\'' + classno + '\',\'' + term + '\')">重設</button>';
    $('#' + idKey).html(html);
}

function showVegetarianButton(year, classno, term)
{
    var idKey = classno+term;
    let html = '<input type="button" class="btn btn-info" value="抵達" onclick="arrivalFun(\'' + year + '\',\'' + term + '\',\'' + classno + '\')">&nbsp<input type="button" class="btn btn-info" value="便當" onclick="arrivalFunBin(\'' + year + '\',\'' + term + '\',\'' + classno + '\')">';
    $('#'+idKey).html(html);
}

function resetVegetarianSearch(year, classno, term, course_date)
{
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'year': year,
        'classno': classno,
        'term': term,
        'course_date': course_date
    }
    var link = "<?=$link_reset;?>";
    if (confirm('確定要重設嗎?')){
        $.ajax({
            url: link,
            data: data,
            // dataType: 'text',
            type: "POST",
            error: function(xhr){
                
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.message == 'Success'){
                    showVegetarianButton(year, classno, term);
                    setArrivalTime(year, classno, term);
                }else if (response.message == 'Not open'){
                    alert('此功能尚未開放');
                }else{
                    alert('重設失敗');
                }
                
                console.log(response);
            }
        });        
    }

}

function setArrivalTime(year, classno, term)
{
    let idKey = classno + term + 'ARRIVAL_TIME';
    $('#'+idKey).html('');
}

function cancelFun(id)
{
    var link = "<?=$link_reset;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'id': id,
    }
    if (confirm('確定要重設嗎?')){
        $.ajax({
            url: link,
            data: data,
            dataType: 'text',
            type: "POST",
            error: function(xhr) {
                // alert('Ajax request error');
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.message == 'Success'){
                    let html = '<input type="button" class="btn btn-info" value="取餐" onclick="getFun(\'' + id + '\')">';
                    $('#V'+id).html(html);
                    $('#V'+id+'ARRIVAL_TIME').html('');
                }
                console.log(response);
            }
        });        
    }

}

function showResetGetMealButton(id){
    let html = '<button type="button" class="btn btn-danger" onclick="cancelFun(\'' + id + '\')">重設</button>';
    $('#V'+id).html(html);
}

function saveFun(year,classno,term,start_date,remark){
    $('#sstart_date').val(start_date);
    $('#syear').val(year);
    $('#sterm').val(term);
    $('#sclassno').val(classno);
    $('#sremark').val(remark);
    $('#sact').val("updateremark");
    $( "#form" ).submit();
}

function getFun(id){
    $('#V'+id).html('');

    var link = "<?=$link_getFun;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'id': id,
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            // alert('Ajax request error');
        },
        success: function(response) {
            showResetGetMealButton(id);
        }
    });
}

function getToday()
{
    var today = new Date();
    
    sdate = addDays(today, 0);

    document.getElementById("datepicker1").value = sdate;
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

// function fowardweek(days)
// {
//     var date1 = document.getElementById("datepicker1").value;
//     var date2 = document.getElementById("test1").value;
//     if(date1!="" && date2!="")
//     {
//         sdate = addDays(date1,days);
//         edate = addDays(date2,days);
//         document.getElementById("datepicker1").value = sdate; 
//         document.getElementById("test1").value = edate;
//     }
//     else
//     {
//         var today = getCurrentWeek();
//     }
// }

$(document).ready(function() {
    $('#Search').click(function(){
        if($('#datepicker1').val() == "")
        {
            alert("請選擇日期");
            return;
        }
        $('#sstart_date').val($('#datepicker1').val());
        $('#sact').val("search");
        $( "#form" ).submit();
    });
    
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();   
    });

    $("#money1").datepicker();
    $('#money2').click(function(){  
        $("#money1").focus();   
    });

    
    if($('#datepicker1').val()==""){
        getToday();
        $('#Search').click();
    }
    else {
        $("#menu-toggle").click();
    }
    
});
// if("<?php echo ($result); ?>" != "0"){
//     alert("<?php echo ($result); ?>");
//     window.location.href="/base/admin/management/vegetarian_management";
// }
</script>
<style type="text/css">
tbody tr:nth-child(2n+1):not(#test) {
    background: #ffb0f5;
}
</style>