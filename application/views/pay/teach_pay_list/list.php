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
                        <input hidden id='sworkname' name='workname' value="">
                        <input hidden id='sappno' name='appno' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='smtlist' name='mtlist' value="">
                        <input hidden id='sact' name='act' value="">
                        <input hidden id='soutdt' name='outdt' value="">
                        <input hidden id='soutimd' name='outimd' value="">
                        <input hidden id='sappseq' name='appseq' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">承辦人:</label>
                            <input id="workname" value="<?= $sess_workname?>" type="text" class="form-control">
                            <label class="control-label">申請編號:</label>
                            <input id="appno" value="<?= $sess_appno?>" type="text" class="form-control">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">上課日期區間:</label>
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
                            <div class="input-group">
                              <button class="btn btn-info" onclick="fowarweek(-7);">
                              <<</button> <button class="btn btn-info" onclick="getCurrenWeek();">本週
                              </button>
                              <button class="btn btn-info" onclick="fowarweek(7);">>></button>
                            </div>
                            <div class="input-group">
                              <button class="btn btn-info" onclick="ClearData()">清除日期</button>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="ClearData()">清除</button>
                        </div>              
                    </div>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">選取<input type="checkbox" onclick="toggle(this);"></th>
                            <th class="text-center">入帳選取<input type="checkbox" onclick="toggle2(this);"></th>
                            <th class="text-center">領現金</th>
                            <th class="text-center">申請編號</th>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">班期數</th>
                            <th class="text-center">講師/助教人數</th>
                            <th class="text-center">出單日期</th>
                            <th class="text-center">入帳日期</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td>
                                <input type="checkbox" value="<?=$data['APP_SEQ']?>" id="sel" name="sel">
                            </td>
                            <td>
                                <input type="checkbox" value="<?=$data['APP_SEQ']?>" id="sel_1" name="sel_1">
                            </td>
                            <td>
                                <input type="checkbox" value="<?=$data['APP_SEQ']?>" id="cash" name="cash" onclick="enableFun(this)">
                            </td>
                            <td>
                                <span onclick="detail('<?=$data['APP_SEQ']?>')"><a href="teach_pay_list/detail?appno=<?= $data['APP_SEQ']?>" ><?= $data['APP_SEQ']?></a></span>
                            </td>
                            <td><?= $data["WORKER_NAME"]?></td>
                            <td><?= $data["CLASS_CNT"]?></td>
                            <td><?= $data["TEACH_CNT"]?></td>
                            <td><?= substr($data["BILL_DATE"], 0, 10)?></td>
                            <td><?= substr($data["ENTRY_DATE"], 0, 10)?></td>
                        </tr>
                        
                        <?php endforeach?>
                    </tbody>
                </table>

                <?php
                if (count($datas)==0){
                  echo '<br><font color="#FF0000">查無資料</font>';
                }
                ?>
                <br><br>
                <table>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" style="margin-right: 5px; margin-bottom: 5px;" onclick="updDt()">設定出單日期</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" style="margin-right: 5px; margin-bottom: 5px;" onclick="delDt()">取消出單日期</button>
                        </td>
                        <td>
                            出單日期
                        </td>
                        <td>
                            <div class="input-group" id="bill_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_outdt?>" id="test3" name="bill_date" style="width:150px;">
                                <span class="input-group-addon" style="cursor: pointer;" id="test4"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" onclick="updImD()">設定入帳日期</button>
                        </td>
                        <td>
                            <button type="button"class="btn btn-info btn-sm" onclick="delImD()">取消入帳日期</button>
                        </td>
                        <td>
                            入帳日期
                        </td>
                        <td>
                            <div class="input-group" id="account_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_outimd?>" id="test5" name="account_date" style="width:150px;">
                                <span class="input-group-addon" style="cursor: pointer;" id="test6"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div> 
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->




<script type="text/javascript">
if("<?php echo ($result); ?>" != "0"){
    alert("<?php echo ($result); ?>");
    var link = "<?=$link_refresh;?>";
    window.location.href=link+"?workname=<?php echo ($sess_workname); ?>&appno=<?php echo ($sess_appno); ?>&start_date=<?php echo ($sess_start_date); ?>&end_date=<?php echo ($sess_end_date); ?>&mtlist=&act=search&outdt=&outimd=&appseq=";
}


function toggle(source) {
    checkboxes = document.getElementsByName("sel");
    console.log(checkboxes);
    for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
    }
}

function toggle2(source) {
    checkboxes = document.getElementsByName("sel_1");
    console.log(checkboxes);
    for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
    }
}

$(document).ready(function() {
    $('#Search').click(function(){
        // if($('#datepicker1').val() == "" || $('#test1').val() == ""){
        //   alert("請選擇日期區間")
        //   return;
        // }

        $('#sappno').val($('#appno').val());
        $('#sworkname').val($('#workname').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#sact').val('search');

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

    $("#test3").datepicker();
    $('#test4').click(function(){
        $("#test3").focus();
    });

    $("#test5").datepicker();
    $('#test6').click(function(){
        $("#test5").focus();
    });

    $("#money1").datepicker();
    $('#money2').click(function(){  
    $("#money1").focus();   
  });
    $("#money3").datepicker();
    $('#money4').click(function(){  
    $("#money3").focus();   
  });
});
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
function updDt(){
  var obj1 = document.all.sel;
  var ml = '';
  if (typeof(obj1.value) != "undefined"){
      if (obj1.checked)
      {
        ml += obj1.value + ',,';
      }    
  }
  else{
    for(i=0; i<(obj1.length); i++)
    {
      if (obj1[i].checked)
      {
        ml += obj1[i].value + ',,';
      }
    }
  }
  if (ml==""){
    alert("尚未選取");   
    return false;    
  }
  
  if ($('#test3').val()=="")
  {
    alert("出單日期未設定!");   
    return false;    
  }   
  
  if(confirm("確定設定出單日期("+$('#test3').val()+")?")) {

      $('#soutdt').val($('#test3').val());
      $('#sappno').val($('#appno').val());
      $('#sworkname').val($('#workname').val());
      $('#sstart_date').val($('#datepicker1').val());
      $('#send_date').val($('#test1').val());
      $('#smtlist').val(ml);
      $('#sact').val('setdt');

      $( "#form" ).submit();
  } else {
      return false;
  }
}
function delDt(){
  var obj1 = document.all.sel;
  var ml = '';
  if (typeof(obj1.value) != "undefined"){
      if (obj1.checked)
      {
        ml += obj1.value + ',,';
      }    
  }
  else{
    for(i=0; i<(obj1.length); i++)
    {
      if (obj1[i].checked)
      {
        ml += obj1[i].value + ',,';
      }
    }
  }
  if (ml==""){
    alert("尚未選取");   
    return false;    
  }
  if(confirm("確定取消出單日期?")) {
    
      $('#smtlist').val(ml);
      $('#sappno').val($('#appno').val());
      $('#sworkname').val($('#workname').val());
      $('#sstart_date').val($('#datepicker1').val());
      $('#send_date').val($('#test1').val());
      $('#sact').val('canceldt');

      $( "#form" ).submit();

  } else {
      return false;
  }   
}


function updImD(){
  var obj1 = document.all.sel_1;
  var ml = '';
  if (typeof(obj1.value) != "undefined"){
      if (obj1.checked)
      {
        ml += obj1.value + ',,';
      }    
  }
  else{
    for(i=0; i<(obj1.length); i++)
    {
      if (obj1[i].checked)
      {
        ml += obj1[i].value + ',,';
      }
    }
  }
  if (ml==""){
    alert("尚未選取");   
    return false;    
  }
  
  if ($('#test5').val()=="")
  {
    alert("入帳日期未設定!");   
    return false;    
  }   
  
  if(confirm("確定設定入帳日期("+$('#test5').val()+")?")) {
      $('#soutimd').val($('#test5').val());
      $('#sappno').val($('#appno').val());
      $('#sworkname').val($('#workname').val());
      $('#sstart_date').val($('#datepicker1').val());
      $('#send_date').val($('#test1').val());
      $('#smtlist').val(ml);
      $('#sact').val('setimd');

      $( "#form" ).submit();  
 
  } else {
      return false;
  }   
}
function delImD(){
  var obj1 = document.all.sel_1;
  var ml = '';
  if (typeof(obj1.value) != "undefined"){
      if (obj1.checked)
      {
        ml += obj1.value + ',,';
      }    
  }
  else{
    for(i=0; i<(obj1.length); i++)
    {
      if (obj1[i].checked)
      {
        ml += obj1[i].value + ',,';
      }
    }
  }
  if (ml==""){
    alert("尚未選取");   
    return false;    
  }
  if(confirm("確定取消入帳日期?")) {
      $('#soutimd').val($('#test5').val());
      $('#sappno').val($('#appno').val());
      $('#sworkname').val($('#workname').val());
      $('#sstart_date').val($('#datepicker1').val());
      $('#send_date').val($('#test1').val());
      $('#smtlist').val(ml);
      $('#sact').val('cancelimd');

      $( "#form" ).submit();  
  
  } else {
      return false;
  }  
}

function enableFun(obj){
    // var status;
    // if(obj.checked){
    //   status = '1';
    // } else {
    //   status = '0';
    // }
  if(confirm("確認設定為領現金後，13D將不再顯示此筆資料，是否確認修改")){
    $('#sact').val('getcash');
    $('#sappno').val($('#appno').val());
    $('#sworkname').val($('#workname').val());
    $('#sstart_date').val($('#datepicker1').val());
    $('#send_date').val($('#test1').val());
    $('#sappseq').val(obj.value);

    $("#form").submit();
  }
  else {
    obj.checked = false;
  }
}
</script>