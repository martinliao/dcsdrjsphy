

<style>
    .ischeck{
        color: blue;
    }

    .uncheck{
        color: block;
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
                <form id="form" method="GET" action="<?=base_url('pay/printHourTax')?>">
                    
                    <div class="row ">
                        <div class="col-xs-12">
                            <label class="control-label">切換週期:</label>
                            <button type="button" class="btn btn-info" onclick="fowarweek(-7);">
                                <<</button> <button class="btn btn-info" onclick="getCurrenWeek();">本週
                            </button>
                            <button type="button" class="btn btn-info" onclick="fowarweek(7);">>></button>
                        </div>
                    </div>
                    <div id="filter-form" role="form" class="form-inline">
                        <div class="row">
                            <div class="col-xs-12">
                                <label class="control-label">日期區間:</label>
                                <div class="input-group" id="start_date">
                                    <input type="text" class="form-control datepicker" value="<?=$filter['sdate']?>" id="datepicker1"
                                        name="sdate" autocomplete="off">
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                            class="fa fa-calendar"></i></span>
                                </div>
                                <div class="input-group" id="end_date">
                                    <input type="text" class="form-control datepicker" value="<?=$filter['edate']?>" id="test1" name="edate" autocomplete="off">
                                    <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                            class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <button id='Search' class="btn btn-info btn-sm">查詢</button>
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
                
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />  
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr style="background-color:#8CBBFF">
                                <th class="text-center"><input type="checkbox" name="all" id="all" onclick="chk_all(this.checked)">選取列印</th>
                                <th class="text-center">產生流水號</th>
                                <th class="text-center">年度</th>
                                <th class="text-center">班期代碼</th>
                                <th class="text-center">期別</th>
                                <th class="text-center">名稱</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($list as $class): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if(isset($taxIsFinish[$class->app_seq])): ?>
                                        <input type="checkbox" name="app_seq[]" value="<?=$class->app_seq?>">
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center"><?=$class->app_seq?></td>
                                    <td class="text-center"><?=$class->year?></td>
                                    <td class="text-center"><?=$class->class_no?></td>
                                    <td class="text-center"><?=$class->term?></td>
                                    <td class="text-center"><?=$class->class_name?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                
                    <div class="row">
                        <div class="col-lg-8  text-left">
                            <button class="btn btn-primary" name="export" value="checkCsv">產製鐘點費核銷清冊</button>
                            <button class="btn btn-primary" name="export" value="hourPdf">產製鐘點費確認清冊及課表</button>
                        </div>
                    </div>
                </form>                
                <div class="row">
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

$(document).ready(function() {
    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
        $("#datepicker1").focus();
    });

    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });
});


function openCheckPage(seq)
{
    window.open("/base/admin/pay/print_pay_list/check?seq=" + seq,"check","width=800,height=800,toolbar=0, scrollbars=yes, resizable=yes, location=no, status=no");
    return false;
}

function fowarweek(days)
{
    var date1 = document.getElementById("datepicker1").value;
    var date2 = document.getElementById("test1").value;
    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        console.log(date1);
        document.getElementById("datepicker1").value = sdate; 
        document.getElementById("test1").value = edate;

        // $('#Search').click();
    }
    else
    {
        var today = getCurrenWeek();
    }
}


function getCurrenWeek()
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

    $('#Search').click();
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

function chk_all(argtype){
    var obj=document.getElementsByName("app_seq[]");
    var len = obj.length;

    for (i = 0; i < len; i++)
    {
        obj[i].checked = argtype;
    } 
}
</script>