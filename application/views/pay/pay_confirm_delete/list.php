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
                        <input hidden id='itaker' name='ntaker' value="">                      
                        <input hidden id='iapplyid' name='napplyid' value="">
                        <input hidden id='istart' name='nstart' value="">                      
                        <input hidden id='iend' name='nend' value="">  
                        <input hidden id='icount' name='ncount' value="">
                        <input hidden id='iact' name='nact' value="">
                        <input hidden id='schklist' name='chklist' value="">
                        <input hidden id='irows' name='rows' value="">              
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">承辦人:</label>
                            <input type="text" id='taker' name='taker' value="<?=$taker?>" class="form-control">
                            <label class="control-label">申請編號:</label>
                            <input type="text" id='applyid' name='applyid' value="<?=$applyid?>" class="form-control">
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
                            <label class="control-label">日期區間:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$start?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$end?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id='Delete' class="btn btn-info btn-sm">刪除請款確認</button>
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
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">選取</th>
                            <th class="text-center">明細</th>
                            <th class="text-center">申請編號</th>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">申請日期</th>
                            <th class="text-center">資料筆數</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                            
                            <tr class="text-center">
                                <td><input type="checkbox" name="single" value="<?= $data["APP_SEQ"]?>"></td>
                                <td><a href="<?=base_url('pay/pay_confirm_delete/detail?appseq='.$data["APP_SEQ"].'') ?>">明細</td>
                                <td><?= $data["APP_SEQ"]?></td>
                                <td><?= $data["NAME"]?></td>
                                <td><?= $data["USE_DATE"]?></td>
                                <td><?= $data["CNT"]?></td>
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
        $('#itaker').val($('#taker').val());
        $('#iapplyid').val($('#applyid').val());
        $('#istart').val($('#datepicker1').val());
        $('#iend').val($('#test1').val());
        $('#icount').val($('#count').val());
        $('#iact').val('search');
        $('#irows').val($('select[name=rows]').val());

        $( "#form" ).submit();

    });
    $('#Delete').click(function(){
        var array = "";
        $("input:checkbox[name=single]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+",,"+this.value;
            }
        });
        if(array==""){
            alert("請選擇資料");
            return;
        }
        $('#schklist').val(array);
        $('#itaker').val($('#taker').val());
        $('#iapplyid').val($('#applyid').val());
        $('#istart').val($('#datepicker1').val());
        $('#iend').val($('#test1').val());
        $('#icount').val($('#count').val());
        $('#iact').val('delete');

        $( "#form" ).submit();

    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });

    $("#money1").datepicker();
    $('#money2').click(function(){  
        $("#money1").focus();   
    });
    if("<?php echo ($result); ?>" != "0"){
        alert("<?php echo ($result); ?>");
        $('#Search').click();
    }
});
</script>