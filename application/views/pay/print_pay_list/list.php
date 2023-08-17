<!-- <?php echo print_r($datas); ?> -->

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
            <form id="form" method="GET">
                    <input hidden id='sstart_date' name='start_date' value="">
                    <input hidden id='send_date' name='end_date' value="">
                    <input hidden id='scount' name='count' value="">
                    <input hidden id='sact' name='act' value="">
                    <input hidden id='schklist' name='chklist' value="">
                    <input hidden id='srows' name='rows' value="0">
                </form> 
                <div class="row ">
                    <div class="col-xs-12">
                        <label class="control-label">切換週期:</label>
                        <button class="btn btn-info" onclick="fowarweek(-7);">
                            <<</button> <button class="btn btn-info" onclick="getCurrenWeek();">本週
                        </button>
                        <button class="btn btn-info" onclick="fowarweek(7);">>></button>
                    </div>
                </div>
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">日期區間:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?= $sess_start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?= $sess_end_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <!-- <a href="/upload_require/auth_agree.doc">公務人員訓練處講座教材著作授權使用同意書</a> -->
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
                        <tr style="background-color:#8CBBFF">
                            <th class="text-center">產生流水號</th>
                            <th class="text-center">講座</th>
                            <th class="text-center">刪除流水號</th>
                            <th class="text-center">選取列印</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">名稱</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($datas as $key=>$data): ?>
                            
                            <tr class="text-center">
                                <?php if($data["app_seq"] == ''){?>
                                <td><input type="checkbox" name="add" value="<?=$data["YEAR"]?>::<?=$data["class_no"]?>::<?=$data["term"]?>"></td>
                                <?php }else{?>
                                <td><font style="<?=(isset($taxIsFinish[$data["app_seq"]])) ? 'color:blue' : '' ?>" ><?=$data["app_seq"]?></font></td>
                                <?php } ?>

                                <td class="text-center">
                                    <?php if (isset($hour_traffic_taxs[$data["app_seq"]])): ?>
                                        <?php foreach($hour_traffic_taxs[$data["app_seq"]] as $hour_traffic_tax): ?>
                                            <a class="<?=($hour_traffic_tax->ischeck == 'Y') ? 'ischeck' : 'uncheck'?>" onclick="return <?=($hour_traffic_tax->ischeck == 'Y') ? 'false' : "openCheckPage('$hour_traffic_tax->seq', '{$sess_start_date}', '{$sess_end_date}')" ?>" href=""><?=$hour_traffic_tax->teacher_name?></a><br>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </td>

                                <?php if($data["app_seq"] != '' && $data["getStatus"] == '待確認'){?>
                                <td><input type="checkbox" name="del" value="<?= $data["app_seq"]?>::<?=$data["YEAR"]?>::<?=$data["class_no"]?>::<?=$data["term"]?>"></td>
                                <?php }else{?>
                                <td></td>
                                <?php } ?>
                                <td><input type="checkbox" name="single" value="<?=$data["YEAR"]?>::<?=$data["class_no"]?>::<?=$data["term"]?>::<?= $data["app_seq"]?>::<?= $data["isStatusOK"]?>"></td>
                                <td><?= $data["YEAR"]?></td>
                                <td><?= $data["class_no"]?></td>
                                <td><?= $data["term"]?></td>
                                <td><?= $data["class_name"]?></td>
                            </tr>
                        
                        <?php endforeach?>

                        <tr class="text-center">
                            <td>
                                <button id="insert" class="btn btn-info btn-sm">產生流水號</button>
                            </td>
                                <td></td>
                            <td>
                                <button id="delete" class="btn btn-info btn-sm">刪除流水號</button>
                            </td>
                            <td>
                            <button class="btn btn-info btn-sm" onclick="mlPrintPaper()">清冊</button>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
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

$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        if($('#datepicker1').val() == "" || $('#test1').val() == ""){
            alert("請輸入日期間");
            return;
        }
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#scount').val($('#count').val());
        $('#srows').val($('select[name=rows]').val());
        $( "#form" ).submit();

    });

    $('#insert').click(function(){
        var array = "";
        $("input:checkbox[name=add]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+",,"+this.value;
            }
        });
        if (array==""){
            alert("尚未選取");
            return false;
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
        $("input:checkbox[name=del]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+",,"+this.value;
            }
        });
        if (array==""){
            alert("尚未選取");
            return false;
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
        getCurrenWeek();
    }
});

function sendFun(){
    if($('#datepicker1').val() == "" || $('#test1').val() == ""){
        alert("請輸入日期間");
        return;
    }
    
    $('#Search').click();

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

function fowarweek(days)
{
    var date1 = document.getElementById("datepicker1").value;
    var date2 = document.getElementById("test1").value;
    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        document.getElementById("datepicker1").value = sdate; 
        document.getElementById("test1").value = edate;

        $('#Search').click();
    }
    else
    {
        var today = getCurrenWeek();
    }
}

// 列印黏貼憑證+
function mlPaper(){
    var array = "";
        $("input:checkbox[name=single]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+",,"+this.value;
            }
        });

  if (array==""){
    alert("尚未選取");
    return false;
  }

  // 檢查是否選擇相同的流水號
  var each_class = array.split(',,');
  var tmp = '';
  var tmp1 = '';
  var diff_app_seq = true;
  for(ec = 0; ec < each_class.length; ec++){
	var each_str = each_class[ec];
	if( each_str !='' ){
		var app_seq_array = each_str.split('::');
		if(tmp == '')
			tmp = app_seq_array[3];
		tmp1 = app_seq_array[3];
		tmp2 = app_seq_array[4];

		if(tmp != tmp1)
			diff_app_seq = false;
	}
  }
  if(diff_app_seq == false){
	alert('你所選取的流水號不一致，請重新選取。');
	return false;
  }


//   document.all.s1.value = document.all.d1.value;
//   document.all.s2.value = document.all.d2.value;
//   document.all.mtList.value = ml;
//   document.all.paper_app_seq.value = tmp1;
//   document.all.is_status_ok.value = tmp2;

  // custom (b) by chiahua 點選合併列印時，將單獨要傳送的year,class_no,term清空
//   document.all.year.value = '';
//   document.all.class_no.value = '';
//   document.all.term.value = '';
  // custom (b) by chiahua 點選合併列印時，將單獨要傳送的year,class_no,term清空
  var link = "<?=$link_refresh;?>";
  window.open(link+"?start_date="+$('#datepicker1').val()
  +"&end_date="+$('#test1').val()
  +"&is_status_ok="+tmp2
  +"&mtList="+array+"&paper_app_seq="+tmp1+"&act=pdf","dwPDF","width=800,height=600,toolbar=0, scrollbars=yes, resizable=yes, location=no, status=no");


}

// 列印黏貼憑證+清冊
function mlPrintPaper(){
    var array = "";
        $("input:checkbox[name=single]:checked").each(function () {
            if(array==""){
                array=this.value;
            }
            else{
                array=array+",,"+this.value;
            }
        });

  if (array==""){
    alert("尚未選取");
    return false;
  }
  // 檢查是否選擇相同的流水號
  var each_class = array.split(',,');
  var tmp = '';
  var tmp1 = '';
  var diff_app_seq = true;
  for(ec = 0; ec < each_class.length; ec++){
	var each_str = each_class[ec];
	if( each_str !='' ){
		var app_seq_array = each_str.split('::');
		if(tmp == '')
			tmp = app_seq_array[3];
		tmp1 = app_seq_array[3];
		tmp2 = app_seq_array[4];

		if(tmp != tmp1)
			diff_app_seq = false;
	}
  }
  if(diff_app_seq == false){
	alert('你所選取的流水號不一致，請重新選取。');
	return false;
  }


//   document.all.s1.value = document.all.d1.value;
//   document.all.s2.value = document.all.d2.value;
//   document.all.mtList.value = ml;
//   document.all.paper_app_seq.value = tmp1;
//   document.all.is_status_ok.value = tmp2;

  // custom (b) by chiahua 點選合併列印時，將單獨要傳送的year,class_no,term清空
//   document.all.year.value = '';
//   document.all.class_no.value = '';
//   document.all.term.value = '';
  // custom (b) by chiahua 點選合併列印時，將單獨要傳送的year,class_no,term清空

//   obj = document.getElementById("actQuery2");
//   obj.action = 'pay03_paperprn.php';
var link = "<?=$link_refresh;?>";
  window.open(link+"?start_date="+$('#datepicker1').val()
  +"&end_date="+$('#test1').val()
  +"&is_status_ok="+tmp2
  +"&mtList="+array+"&paper_app_seq="+tmp1+"&act=pdf2","dwPDF","width=800,height=800,toolbar=0, scrollbars=yes, resizable=yes, location=no, status=no");
//   myW.focus();
//   obj.submit();

}

function openCheckPage(seq, sdate, edate)
{
    var link = "<?=$link_checkpage;?>";
    window.open(link+"?seq=" + seq + "&sdate=" + sdate + "&edate=" + edate,"check","width=800,height=800,toolbar=0, scrollbars=yes, resizable=yes, location=no, status=no");
    return false;
}


</script>