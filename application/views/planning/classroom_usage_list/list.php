<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12" >
                            <div class="form-group required">
                                <label class="control-label">場地類別</label>
                                <?php
                                    $choices['room_type'] = array(''=>'請選擇') + $choices['room_type'];
                                    echo form_dropdown('room_type', $choices['room_type'], $filter['room_type'], 'class="form-control" id="set_room_type" onchange="get_room();"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">場地名稱</label>
                                <?php
                                    $choices['room'] = array(''=>'請選擇') + $choices['room'];
                                    echo form_dropdown('room', $choices['room'], $filter['room'], 'class="form-control" id="room" ');
                                ?>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">使用日期</label>
                                <div class="input-daterange input-group"style="width: 300px;">
                                    <input type="text" class="form-control datepicker" id="datepicker1" name="start_date" value="<?=$filter['start_date'];?>"/>
                                    <span class="input-group-addon" style="cursor: pointer;"id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>

                                        <span class="input-group-addon">to</span>

                                    <input type="text" class="form-control datepicker" id="test1" name="end_date"  value="<?=$filter['end_date'];?>"/>
                                    <span class="input-group-addon" style="cursor: pointer;"id="test2"><i
                                        class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <a class="btn btn-info btn-sm" onclick="getOtherWeek(-7,1)" ><<</a>
                            <a class="btn btn-info btn-sm" onclick="getThisWeek(1)" >本週</a>
                            <a class="btn btn-info btn-sm" onclick="getOtherWeek(7,1)" >>></a>
                            <a class="btn btn-info btn-sm" onclick="getToday()">設定今天</a>
                            
                        </div>
                        <div class="col-xs-12" >
                            <label class="control-label">教室類別</label>
                            <div class="checkbox">
                                &nbsp;<input type="checkbox" name="class_room_type_B" value="B" <?=set_checkbox('class_room_type_B', 'B', $filter['class_room_type_B']=='B');?>>
                            </div>
                            <label class="control-label">B區教室</label>
                            <div class="checkbox">
                                &nbsp;<input type="checkbox" name="class_room_type_C" value="C" <?=set_checkbox('class_room_type_C', 'C', $filter['class_room_type_C']=='C');?>>
                            </div>
                            <label class="control-label">C區教室</label>
                            <div class="checkbox">
                                &nbsp;<input type="checkbox" name="class_room_type_E" value="E" <?=set_checkbox('class_room_type_E', 'E', $filter['class_room_type_E']=='E');?>>
                            </div>
                            <label class="control-label">E區教室</label>
                            <div class="checkbox">
                                &nbsp;<input type="checkbox" name="red_class" value="Y" <?=set_checkbox('red_class', 'Y', $filter['red_class']=='Y');?>>
                            </div>
                            <label class="control-label">僅查詢已使用之紅色班期(不含預約班)</label>
                            <div class="checkbox">
                                &nbsp;<input type="checkbox" name="only_time" value="Y" <?=set_checkbox('only_time', 'Y', $filter['only_time']=='Y');?>>
                            </div>
                            <label class="control-label">僅顯示班期起訖時間</label>
                        </div>
                        <div class="col-xs-6">
                            <span style="white-space:pre; color:#000000;"><strong>藍色</strong>:表示外借已使用, <strong>紅色</strong>:表示班期已使用, </span><strong>黑色</strong>:表示已預約
                            <a class="btn btn-info btn-sm" onclick="actionSelect('<?=$select_url;?>')" >查詢</a>
                            <?php if (isset($room_export)) { ?>
                            <a class="btn btn-info btn-sm" onclick="actionExport('<?=$room_export;?>')" title="room_export">匯出</a>
                            <?php } ?>
                        </div>

                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover" style="text-align:center;" >
                        <?php if($list){ ?>
                        <thead>
                            <tr>
                                <td bgcolor="#5D7B9D"><font color="#ffffff">場地</font></td>
                                <?php $days = ((strtotime($filter['end_date'])-strtotime($filter['start_date'])) / 86400) + 1; ?>
                                <?php for($i=0; $i<$days; $i++){ ?>
                                <?php $select_day = date("Y-m-d",strtotime("+{$i} day",strtotime($filter['start_date']))); ?>
                                <td bgcolor="#5D7B9D"><font color="#ffffff"><?=$select_day;?></font></td>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $j=1;
                         foreach ($list as $row) { 
                            if($j%2==1){ ?>
                                <tr onMouseOver="this.style.backgroundColor='#FFB7DD';" onMouseOut="this.style.backgroundColor='#f1f1f1';"> 
                            <?php }else{ ?>
                                <tr onMouseOver="this.style.backgroundColor='#FFB7DD';" onMouseOut="this.style.backgroundColor='#fff';">
                            <?php } ?>
                                <td style="white-space:pre; color:#000000;"> <?=$row['room_name'];?> </td>
                                <?php for($i=0; $i<$days; $i++){ ?>
                                <?php $select_day = date("Y-m-d",strtotime("+{$i} day",strtotime($filter['start_date']))); ?>
                                <td title="<?=$row['room_name'];?>">
                                    <?php foreach ($row[$select_day] as $class) { ?>
                                        <?php if($class['BTYPE'] == '1'){?>
                                        <span style="white-space:pre; color:#000000;"> <?=substr($class['FROM_TIME'], 0, 5);?> ~ <?=substr($class['TO_TIME'], 0, 5);?> <?=$class['Year'];?>年 <?=$class['CLASS_NAME'];?>(<?=$class['TERM'];?>) <?=$class['CNAME'];?></span><br>
                                        <?php } ?>
                                        <?php if($class['BTYPE'] == '2'){?>
                                        <span style="white-space:pre; color:#1B41FF;"> <?=substr($class['FROM_TIME'], 0, 5);?> ~ <?=substr($class['TO_TIME'], 0, 5);?> <?=$class['Year'];?>年 <?=$class['CLASS_NAME'];?>(<?=$class['TERM'];?>) <?=$class['CNAME'];?></span><br>
                                        <?php } ?>
                                        <?php if($class['BTYPE'] == '3'){?>
                                        <span style="white-space:pre; color:#FF0004;"> <?=substr($class['FROM_TIME'], 0, 5);?> ~ <?=substr($class['TO_TIME'], 0, 5);?> <?=$class['Year'];?>年 <?=$class['CLASS_NAME'];?>(<?=$class['TERM'];?>) <?=$class['CNAME'];?></span><br>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php $j = $j+1; 
                        } ?>
                        </tbody>
                        <?php } ?>
                    </table>
                    </div>
                </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script>
var get_room = function() {
        var url = '<?=base_url('planning/classroom/ajax/get_room');?>';
        var room_type = $('#set_room_type').val();

        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'room_type': room_type,
        }

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            console.log(response.data);
                            setList(response.data);
                        } else {

                        }
                    }

        });

}

function Appendzero(obj){
    if(obj<10) 
        return "0" +""+ obj;
    else 
        return obj;
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    var dd = result.getDate();
    var mm = result.getMonth()+1;
    var yy = result.getFullYear();
    result = yy+'-'+Appendzero(mm)+'-'+Appendzero(dd);
    return result;
}
function setList(DataList){
  obj = document.getElementById('room');
  dataAry = DataList;
  obj.options.length = 0;
    var new_option = new Option('請選擇','');
  obj.options.add(new_option);
    for(i=0;i<dataAry.length;i++){
    strAry = dataAry[i];
    if(strAry[0]!=""){
      var new_option = new Option(strAry.room_name,strAry.room_id);
        obj.options.add(new_option);
    }
    }
}
function getToday(){ 
    setToday();
    var url = 'http://dcsdcourse.taipei.gov.tw/base/admin/planning/classroom_usage_list/?';
    actionSelect();
}
function getThisWeek(i){ 
    getCurrentWeek1(i);
    var url = 'http://dcsdcourse.taipei.gov.tw/base/admin/planning/classroom_usage_list/?';
    actionSelect();
}
function getOtherWeek(day,i){
    fowardweek1(day,i);
    var url = 'http://dcsdcourse.taipei.gov.tw/base/admin/planning/classroom_usage_list/?';
    actionSelect();
}
var actionExport = function(url) {
    var $form = $('#filter-form');
        var yesfunc = function() {
            $form.attr('action', url).submit();
        }

        var nofunc = function() {
            // bk_alert(4, 'ok', 4, 'center');
        }

        var msg = '<p>確認匯出資料?</p>';

        bk_confirm(0, msg, 'center', yesfunc, nofunc);
}

var actionSelect = function(url) {
    var $form = $('#filter-form');
    $form.attr('action', url).submit();
}

$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });
});

$(document).ready(function() {
  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });
});
</script>