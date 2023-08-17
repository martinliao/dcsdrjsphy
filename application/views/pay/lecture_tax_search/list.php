<!-- <?= print_r($datas)?> -->
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
                        <input hidden id='steacher' name='teacher' value="">
                        <input hidden id='suniformid' name='uniformid' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='syear' name='year' value="">
                        <input hidden id='steacherid' name='teacherid' value="">
                        <input hidden id='sremark' name='remark' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                    </form>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">講師姓名:</label>
                            <input name='teacher' id='teacher' type="text" value="<?= $sess_teacher?>" class="form-control">
                            <label class="control-label">身分證字號:</label>
                            <input name='uniformid' id='uniformid' type="text" value="<?=$sess_uniformid?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">切換週期:</label>
                            <button class="btn btn-info" onclick="fowardweek(-7);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek();">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek(7);">>></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">出單日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?= $sess_start_date?>" id="datepicker1"
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
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">備註年度:</label>
                            <select name='year' id='year'>
                            <?php foreach (array_reverse($choices['query_year']) as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出CSV</button>
                            <button id="print" class="btn btn-info btn-sm">列印</button>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div> -->
                </div>
                <!-- /.table head -->
                <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">類別</th>
                            <th class="text-center">流水號</th>
                            <th class="text-center">身分證字號</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">地址</th>
                            <th class="text-center">鐘點費</th>
                            <th class="text-center">所得稅</th>
                            <th class="text-center">實付金額</th>
                            <th class="text-center">備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php $count = 1; for($i = 1 ; $i <=4 ; $i++ ){?>
                            <?php 
                                $amt1 = 0;
                                $amt2 = 0;
                                $amt3 = 0;  
                            ?>
                            <?php if(sizeof($datas[$i]) != 0) { ?>
                                    <tr class="text-center">
                                        <td><?php if($i == 1) echo $i.".個人"; else if($i == 2) echo $i.".公司行號";
                                        else if($i == 3) echo $i.".外國人"; else if($i == 4) echo $i.".無身分證";
                                        ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                <?php foreach ($datas[$i] as $data): ?>
                            
                                    <tr class="text-center">
                                        <td></td>
                                        <td><?= $count?></td>
                                        <td><?= $data["rpno"]!=''?$data["rpno"]:$data["teacher_ID"]?></td>
                                        <td><?= $data["teacher_NAME"]?></td>
                                        <td><?= $data["address"]?></td>
                                        <td><?= $data["HOUR_FEE"]?></td>
                                        <td><?= $data["TAX"]?></td>
                                        <td><?= $data["HOUR_FEE"] - $data["TAX"] ?></td>
                                        <td><input type="button" value="編輯" onclick="openPop('<?= $sess_year?>', '<?= $data['teacher_ID']?>')"><?= $data['remark']?></td>
                                        
                                    </tr>
                                    <?php 
                                        $amt1 += $data["HOUR_FEE"];
                                        $amt2 += $data["TAX"];
                                        $amt3 += ($data["HOUR_FEE"] - $data["TAX"]);
                                    ?>
                                <?php $count++; endforeach?>

                                    <tr class="text-center">
                                        <td colspan='5' style="text-align:right">小計</td>
                                        <td><?= $amt1?></td>
                                        <td><?= $amt2?></td>
                                        <td><?= $amt3?></td>
                                        <td></td>
                                    </tr>
                            <?php }?>
                        <?php }?>
                        
                    </tbody>
                </table>
                <!-- <div class="row">
                <div class="col-lg-4">
                    Showing 10 entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div>
            </div> -->
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>

<!-- change Modal -->
<div class="modal fade bd-example-modal-lg popContent" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">講座綜合所得稅備註明細</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style='margin-top: -10px;'>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="display:flex;align-items: center;">
                    <span style='margin-right:5px;width:40px;font-size:14px'>備註</span>
                    <input id='remarkIpt' style='font-size:14px' type="text" class="form-control" value="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info btn-sm" onclick="saveRemark()">儲存</button>
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
var tempData = [-1,-1];
function openPop(year, teacher_ID) {
    console.log(year, teacher_ID);
    tempData = [year, teacher_ID];
    $(".popContent").modal('show');
}

function saveRemark() {
    $('#steacher').val($('#teacher').val());
    $('#suniformid').val($('#uniformid').val());
    $('#sstart_date').val($('#datepicker1').val());
    $('#send_date').val($('#test1').val());
    $('#syear').val(tempData[0]);
    $('#steacherid').val(tempData[1]);
    $('#sremark').val($('#remarkIpt').val());
    $('#siscsv').val(2);
    $( "#form" ).submit();
}

function check_all(obj,cName) 
{ 
    var checkboxs = document.getElementsByName(cName); 
    for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
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
    $('#Search').click(function(){
        $('#steacher').val($('#teacher').val());
        $('#suniformid').val($('#uniformid').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#syear').val($('#year').val());
        $('#siscsv').val(0);
        $( "#form" ).submit();
    });

    $('#print').click(function(){
        printData("printTable");
    });

    $('#csv').click(function(){
        $('#steacher').val($('#teacher').val());
        $('#suniformid').val($('#uniformid').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#syear').val($('#year').val());
        $('#siscsv').val(1);
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

    if($('#datepicker1').val() == "" || $('#test1').val() == "") {
        getCurrentWeek();
    }
});
</script>