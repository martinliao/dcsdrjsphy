<style>
    @media screen and (min-width: 768px) {
        .searchDate {
            width: 90px !important;
        }
    }
</style>

<!-- <?= json_encode($datas)?> -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row ">
                    <form id="form" method="GET">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='srows' name='rows' value="0">
                        <input hidden id='sallQry' name='allQry' value="0">
                    </form>
                    <form id="setForm" method="GET">
                        <input hidden id='syear' name='year' value="">
                        <input hidden id='sclass_no' name='class_no' value="">
                        <input hidden id='sterm' name='term' value="">
                        <input hidden id='srs' name='rs' value="">
                        <input hidden id='sre' name='re' value="">
                        <input hidden id='stype' name='type' value="">
                    </form>
                    <form id="deleteform" method="GET">
                        <input hidden id='syear' name='year' value="">
                        <input hidden id='sclass_no' name='class_no' value="">
                        <input hidden id='sterm' name='term' value="">
                        <input hidden id='srs' name='rs' value="">
                        <input hidden id='sre' name='re' value="">
                        <input hidden id='stype' name='type' value="">
                    </form>
                    <div class="col-xs-12">
                        <label class="control-label">切換週期:</label>
                        <button class="btn btn-info" onclick="fowardweek(-7);">
                            <<</button> <button class="btn btn-info" onclick="getCurrentWeek();">本週
                        </button>
                        <button class="btn btn-info" onclick="fowardweek(7);">>></button>
                    </div>
                </div>
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">開始日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker searchDate" value="<?=$sess_start_date?>" id="datepicker1"
                                    >
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label">結束日期:</label>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker searchDate" value="<?=$sess_end_date?>" id="test1" >
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <input type="checkbox" class="form-control" name="allQryCheck" <?= $sess_allQry==1? 'checked':''?>>(含確認請款已完成)
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr style="background-color: #8CBBFF;">
                            <th style="min-width:52px;text-align:center;">明細</th>
                            <th style="min-width:52px;text-align:center;">年度</th>
                            <th style="min-width:95px;text-align:center;">班期代碼</th>
                            <th style="min-width:52px;text-align:center;">期別</th>
                            <th style="min-width:180px;text-align:center;">名稱</th>
                            <th style="min-width:240px;text-align:center;">日期區間</th>
                            <th style="min-width:105px;text-align:center;">請款狀態變更</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $key => $data): ?>
                            
                            <?php if($sess_allQry == 1) { ?>
                                <?php if($data["count"] == 0){?>
                                    <tr style='background-color:#ffffff;'>
                                        <td style="text-align:center;"><a class="btn btn-info" onclick="routerDetail('<?= $key?>','<?= $data['YEAR']?>','<?= $data['term'] ?>','<?= $data['class_no'] ?>','<?= $data['class_name'] ?>','<?= $data['rs']?>','<?= $data['re']?>')">明細</a></td>
                                        <td style="text-align:center;"><?= $data["YEAR"] ?></td>
                                        <td style="text-align:center;"><?= $data["class_no"] ?></td>
                                        <td style="text-align:center;"><?= $data["term"] ?></td>
                                        <td style="text-align:center;"><?= $data["class_name"] ?></td>
                                        <td style="text-align:center;"><?= $data["rs"]." ~ ". $data["re"] ?></td>
                                        <td style="text-align:center;"><button class="btn btn-info" onclick="setPay('<?= $data["YEAR"] ?>','<?= $data["class_no"] ?>','<?= $data["term"] ?>','<?= $data["rs"] ?>','<?= $data["re"] ?>')">確認請款已完成</button></td>
                                    </tr>
                                <?php } else {?>
                                    <tr style='background-color:#FFB6C1;'>
                                        <td style="text-align:center;"><a class="btn btn-info" onclick="routerDetail('<?= $key?>','<?= $data['YEAR']?>','<?= $data['term'] ?>','<?= $data['class_no'] ?>','<?= $data['class_name'] ?>','<?= $data['rs']?>','<?= $data['re']?>')">明細</a></td>
                                        <td style="text-align:center;"><?= $data["YEAR"] ?></td>
                                        <td style="text-align:center;"><?= $data["class_no"] ?></td>
                                        <td style="text-align:center;"><?= $data["term"] ?></td>
                                        <td style="text-align:center;"><?= $data["class_name"] ?></td>
                                        <td style="text-align:center;"><?= $data["rs"]." ~ ". $data["re"] ?></td>
                                        <td style="text-align:center;"><button class="btn btn-info" onclick="deletePay('<?= $data["YEAR"] ?>','<?= $data["class_no"] ?>','<?= $data["term"] ?>','<?= $data["rs"] ?>','<?= $data["re"] ?>')">變更請款未完成</button></td>
                                    </tr>
                                <?php }?>
                            <?php } else if($sess_allQry != 1 && $data["count"] == 0) {?>
                                <tr style='background-color:#ffffff;'>
                                    <td style="text-align:center;"><a class="btn btn-info" onclick="routerDetail('<?= $key?>','<?= $data['YEAR']?>','<?= $data['term'] ?>','<?= $data['class_no'] ?>','<?= $data['class_name'] ?>','<?= $data['rs']?>','<?= $data['re']?>')">明細</a></td>
                                    <td style="text-align:center;"><?= $data["YEAR"] ?></td>
                                    <td style="text-align:center;"><?= $data["class_no"] ?></td>
                                    <td style="text-align:center;"><?= $data["term"] ?></td>
                                    <td style="text-align:center;"><?= $data["class_name"] ?></td>
                                    <td style="text-align:center;"><?= $data["rs"]." ~ ". $data["re"] ?></td>
                                    <td style="text-align:center;"><button class="btn btn-info" onclick="setPay('<?= $data["YEAR"] ?>','<?= $data["class_no"] ?>','<?= $data["term"] ?>','<?= $data["rs"] ?>','<?= $data["re"] ?>')">確認請款已完成</button></td>
                                </tr>
                            <?php }?>
                            
                        <?php endforeach?>
                    </tbody>
                </table>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>


<script>
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

function setPay(year,class_no,term,rs,re){

    if ( confirm ("確認要將("+class_no+")這一筆設定為請款已完成嗎?")  ) {	
        
        
        ApiGet("pay?year="+year
        +"&class_no="+class_no
        +"&term="+term
        +"&rs="+rs
        +"&re="+re
        +"&type=update","update")
        
	}
}

function deletePay(year,class_no,term,rs,re){
    if ( confirm ("確認要將("+class_no+")這一筆設定為回請款未完成嗎?")  ) {	
        ApiGet("pay?year="+year
        +"&class_no="+class_no
        +"&term="+term
        +"&rs="+rs
        +"&re="+re
        +"&type=noupdate","noupdate")
	}
}

function ApiGet(url,name){
    $.ajax({
        async: false,
        url: url,
        type: "GET",
        dataType: "json",
        success: function (Jdata) {
            console.log(Jdata);
            if(name == "update"){
                if(Jdata.status == 1){
                    location.reload();
                }
                else{
                    alert("更新失敗");
                }
            }
            else if(name == "noupdate"){
                // document.getElementById("formBody").innerHTML  = "";
                // createForm(Jdata)
                if(Jdata.status == 1){
                    location.reload();
                }
                else{
                    alert("更新失敗");
                }
            }
           
        }
    });
}

function routerDetail(index,year,term,class_no,class_name,start_date,end_date) {
    window.location.href = "<?=base_url('pay/pay/detail')?>?year="+year+"&term="+term+"&class="+class_no+"&classname="+class_name+"&startdate="+start_date+"&enddate="+end_date+"&act=search";
}


$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#srows').val($('select[name=rows]').val());
        if($("input[name=allQryCheck]")[0].checked) {
            $('#sallQry').val(1);
        }
        else {
            $('#sallQry').val(0);
        }
        
        $( "#form" ).submit();
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();
    });

    if($('#datepicker1').val() == "" || $('#test1').val() == "") {
        getCurrentWeek();
        $('#Search').click();
    }
});
</script>