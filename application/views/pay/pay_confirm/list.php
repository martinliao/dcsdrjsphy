<!-- <?php print_r($datas)?> -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="form" method="GET">
                    <input hidden id='sstart_date' name='start_date' value="">
                    <input hidden id='send_date' name='end_date' value="">
                    <input hidden id='scount' name='count' value="">
                    <input hidden id='sact' name='act' value="">
                    <input hidden id='schklist' name='chklist' value="">
                    <input hidden id='srows' name='rows' value="">
                </form>        
                <div class="row ">
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
                            <label class="control-label">日期區間:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?= $s1?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label">~</label>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?= $s2?>" id="test1" name="end_date">
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
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr style="background-color: #8CBBFF;">
                            <th class="text-center">選取</th>
                            <th class="text-center">清冊</th>
                            <th class="text-center">產生流水號</th>
                            <!-- <th class="text-center">刪除流水號</th> -->
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">名稱</th>
                            <th class="text-center">明細筆數</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr class="text-center">
                            <td><input type="checkbox" name="single" value="0"></td>
                            <td><a href="#">清冊</a></td>
                            <td>201907152019</td>
                            <td></td>
                            <td>108</td>
                            <td>B00139</td>
                            <td>1</td>
                            <td>夜間英文中級班</td>
                            <td>1</td>
                        </tr> -->
                        <?php foreach ($datas as $key=>$data): ?>
                        
                            <tr class="text-center">
                                <td><input type="checkbox" name="single" value="<?= $data["app_seq"]?>"></td>
                                <td><a class="btn btn-info btn-sm" onclick="show_pdf('<?=$data["YEAR"]?>::<?=$data["class_no"]?>::<?=$data["term"]?>::<?= $data["app_seq"]?>')">清冊</a></td>
                                <td><?= $data["app_seq"]?></td>
                                <!-- <td></td> -->
                                <td><?= $data["YEAR"]?></td>
                                <td><?= $data["class_no"]?></td>
                                <td><?= $data["term"]?></td>
                                <td><?= $data["class_name"]?></td>
                                <td><?= $data["cnt"]?></td>
                            </tr>
                        
                        <?php endforeach?>
                    </tbody>
                </table>
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div><br>
                <div>
                    <button id="comfirm" class="btn btn-info">請款確認</button>
                    <!-- <button id="insert" class="btn btn-info">產生流水號</button>
                    <button id="delete" class="btn btn-info">刪除流水號</button> -->
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
    
if("<?php echo ($result); ?>" != "0"){
    alert("<?php echo ($result); ?>");
}

function show_pdf(array){
    console.log(array)
    let paper_app_seq = array.split('::')[3];
    var link = "<?=$link_showpdf;?>";
    window.open(link+"?start_date="+$('#datepicker1').val()
        +"&end_date="+$('#test1').val()
        +"&is_status_ok=N"
        +"&mtList="+array+"&paper_app_seq="+paper_app_seq+"&act=pdf2","dwPDF","width=800,height=800,toolbar=0, scrollbars=yes, resizable=yes, location=no, status=no"); 
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

        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#scount').val($('#count').val());
        $('#sact').val("search");
        $('#srows').val($('select[name=rows]').val());

        $( "#form" ).submit();

    });

    $('#comfirm').click(function(){
        var array = "";
        $("input:checkbox[name=single]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+","+this.value;
            }
        });
        if(array==""){
            alert("請選擇資料");
            return;
        }
        $('#schklist').val(array);
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#scount').val($('#count').val());
        $('#sact').val("comfirm");

        $( "#form" ).submit();

    });
    $('#insert').click(function(){
        var array = "";
        $("input:checkbox[name=single]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+","+this.value;
            }
        });
        if(array==""){
            alert("請選擇資料");
            return;
        }
        $('#schklist').val(array);
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#scount').val($('#count').val());
        $('#sact').val("insert");

        $( "#form" ).submit();

    });
    $('#delete').click(function(){
        var array = "";
        $("input:checkbox[name=single]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+","+this.value;
            }
        });
        if(array==""){
            alert("請選擇資料");
            return;
        }
        $('#schklist').val(array);
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#scount').val($('#count').val());
        $('#sact').val("delete");

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