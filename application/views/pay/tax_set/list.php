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
                        <input hidden id='imoney' name='nmoney' value="">                      
                        <input hidden id='itax' name='ntax' value="">                      
                        <input hidden id='ihealthmoney' name='nhealthmoney' value="">
                        <input hidden id='ihealthtax' name='nhealthtax' value="">
                        <input hidden id='act' name='act' value="setup">
                    </form>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">金額(必填):</label>
                            <input type="text" id="money" name="money" value="<?= $datas['TAX'];?>" class="form-control">
                            <label class="control-label">稅率(必填):</label>
                            <input type="text" id="tax" name="tax" value="<?= $datas['TAX_RATE'];?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">二代健保金額(必填):</label>
                            <input type="text" id="healthmoney" name="healthmoney" value="<?= $datas['H_TAX'];?>" class="form-control">
                            <label class="control-label">二代健保稅率(必填):</label>
                            <input type="text" id="healthtax" name="healthtax" value="<?= $datas['H_TAX_RATE'];?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">確定</button>
                            <button class="btn btn-info btn-sm" onclick="Clear()">清除</button>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>


<script type="text/javascript">

if("<?php echo ($result); ?>" != "0"){
    alert("<?php echo ($result); ?>");
}

    function check_all(obj,cName) 
{ 
    var checkboxs = document.getElementsByName(cName); 
    for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
} 

//>>清除日期
function Clear(){
    $('#money').val('');
    $('#tax').val('');
    $('#healthmoney').val('');
    $('#healthtax').val('');
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

    $('#imoney').val($('#money').val());
    $('#itax').val($('#tax').val());
    $('#ihealthmoney').val($('#healthmoney').val());
    $('#ihealthtax').val($('#healthtax').val());
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
});
</script>