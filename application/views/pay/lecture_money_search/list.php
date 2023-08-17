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
                    <form id="form" method="GET">
                        <input hidden id='iteacher' name='nteacher' value="">                      
                        <input hidden id='iid' name='nid' value="">                      
                        <input hidden id='istart' name='nstart' value="">
                        <input hidden id='iend' name='nend' value="">
                        <input hidden id='iperpage' name='nperpage' value="">
                        <input hidden id='irows' name='rows' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">講師姓名:</label>
                            <input type="text" id="teacher" value="<?=$sess_nteacher?>" name="teacher" class="form-control">
                            <label class="control-label">身分證字號:</label>
                            <input type="text" id="id" value="<?=$sess_nid?>" name="id" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">上課日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_nstart?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <span>至</span>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_nend?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="ClearData()">清除</button>
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
                <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                        <tr>
                            <th class="text-center" width='30%'>班期名稱</th>
                            <th class="text-center" width='10%'>上課日期</th>
                            <th class="text-center" width='10%'>講座姓名</th>
                            <th class="text-center" width='10%'>身分證字號</th>
                            <th class="text-center" width='5%'>鐘點費</th>
                            <th class="text-center" width='5%'>交通費</th>
                            <th class="text-center" width='5%'>合計</th>
                            <th class="text-center" width='5%'>實支</th>
                            <th class="text-center" width='10%'>出單日期</th>
                            <th class="text-center" width='10%'>入帳日期</th>
                        </tr>
                        <?php
                            $page_hour_fee = 0;
                            $page_traffic_fee = 0;
                            $page_subtotal = 0;
                            $page_aftertax = 0;
                        ?>
                        <?php if(sizeof($datas) > 0) { ?>
                            <?php foreach ($datas as $data): ?>
                            
                                <tr class="text-center">
                                    <td><?= $data["class_name"]?>(第<?= $data["term"]?>期)</td>
                                    <td><?= substr($data["use_date"],0,-8)?></td>
                                    <td><a href="<?=base_url('pay/lecture_money_search/detail?teacher_id=').$data["teacher_id"] ?>"><?= $data["teacher_name"]?></td>
                                    <td><?= $data["rpno"]!=''?$data["rpno"]:$data["teacher_id"]?></td>
                                    <td><?= number_format($data["hour_fee"])?></td>
                                    <?php
                                        if($data['traffic_fee']<0){
                                            $data['traffic_fee']=0;
                                        }
                                    ?>
                                    <td><?= number_format($data["traffic_fee"])?></td>
                                    <td><?= number_format($data["subtotal"])?></td>
                                    <td><?= number_format($data['subtotal']-$data['tax'])?></td>
                                    <td><?= substr($data["bill_date"],0,-8)?></td>
                                    <td><?= substr($data["entry_date"],0,-8)?></td>
                                </tr>

                                <?php
                                    $page_hour_fee += $data['hour_fee'];
                                    $page_traffic_fee += $data['traffic_fee'];
                                    $page_subtotal += $data['subtotal'];
                                    $page_aftertax += $data['subtotal']-$data['tax'];
                                ?>
                            
                            <?php endforeach?>
                        <?php } ?>

                        <?php if(sizeof($datas) > 0) { ?>
                            <tr class="text-right">
                                <td colspan="4">小計</td>
                                <td><?= number_format($page_hour_fee)?></td>
                                <td><?= number_format($page_traffic_fee)?></td>
                                <td><?= number_format($page_subtotal)?></td>
                                <td><?= number_format($page_aftertax)?></td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="text-right">
                                <td colspan="4">總計</td>
                                <td><?= number_format($datas[0]["TT_HOUR_FEE"])?></td>
                                <td><?= number_format($datas[0]["TT_TRAFFIC_FEE"])?></td>
                                <td><?= number_format($datas[0]["TT_HOUTT_SUBTOTALR_FEE"])?></td>
                                <td><?= number_format($datas[0]["TT_AFTERTAX"])?></td>
                                <td colspan="2"></td>
                            </tr>
                        <?php } ?>
                </table>
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div><br>
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>


<script type="text/javascript">
function sendFun(){
    if($('#datepicker1').val() == "" || $('#test1').val() == ""){
        alert("請選擇日期區間")
        return;
    }
    
    $('#Search').click();
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
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        if($('#datepicker1').val() == "" || $('#test1').val() == ""){
            alert("請選擇日期區間")
            return;
        }

        $('#iteacher').val($('#teacher').val());
        $('#iid').val($('#id').val());
        $('#istart').val($('#datepicker1').val());
        $('#iend').val($('#test1').val());
        $('#iperpage').val($('#perpage').val());
        $('#irows').val($('select[name=rows]').val());

        $( "#form" ).submit();

    });

    $('#print').click(function(){
        printData("printTable");
    });


    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });

    $("#money1").datepicker();
    $('#money2').click(function(){  
    $("#money1").focus();   
  });
});
</script>