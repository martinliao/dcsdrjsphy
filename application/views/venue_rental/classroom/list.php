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
                                <div class="input-daterange input-group"  style="width: 240px;">
                                    <input type="text" style="width:100px" class="form-control datepicker" id="test1"name="start_date" value="<?=$filter['start_date'];?>"/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" style="width:100px"class="form-control datepicker"  id="test3"name="end_date"   value="<?=$filter['end_date'];?>"/>
                                    <span class="input-group-addon" style="cursor: pointer;" id="test4"><i
                                        class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                        <div class="col-xs-6">
                            <span style="white-space:pre; color:#000000;">黑色:表示已預約, 藍色:表示外借已使用, 紅色:表示班期已使用 (本功能只能對於是預約的修改)</span>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover" ">
                        <?php if($list){ ?>
                        <thead>
                            <tr>
                                <th>場地</th>
                                <?php $days = ((strtotime($filter['end_date'])-strtotime($filter['start_date'])) / 86400) + 1; ?>
                                <?php for($i=0; $i<$days; $i++){ ?>
                                <?php $select_day = date("Y-m-d",strtotime("+{$i} day",strtotime($filter['start_date']))); ?>
                                <th><?=$select_day;?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td style="white-space:pre; color:#000000;"> <?=$row['room_name'];?> </td>
                                <?php for($i=0; $i<$days; $i++){ ?>
                                <?php $select_day = date("Y-m-d",strtotime("+{$i} day",strtotime($filter['start_date']))); ?>
                                <td>
                                    <?php foreach ($row[$select_day] as $class) { ?>
                                        <?php if($class['BTYPE'] == '1'){?>
                                        <span style="white-space:pre; color:#000000;"> <?=substr($class['FROM_TIME'], 0, 5);?> ~ <?=substr($class['TO_TIME'], 0, 5);?> <?=$class['Year'];?>年 <?=$class['CLASS_NAME'];?> <?=(!empty($class['TERM']))? "(" .$class['TERM'] .")" :'';?> <button type="button" class="btn btn-outline btn-danger btn-xs btn-toggle" title="刪除" onclick="del_list_booking('<?=$class['id'];?>', '<?=$class['BOOKING_DATE'];?>');">刪除</button> </span><br>
                                        <?php } ?>
                                        <?php if($class['BTYPE'] == '2'){?>
                                        <span style="white-space:pre; color:#1B41FF;"> <?=substr($class['FROM_TIME'], 0, 5);?> ~ <?=substr($class['TO_TIME'], 0, 5);?> <?=$class['Year'];?>年 <?=$class['CLASS_NAME'];?> <?=(!empty($class['TERM']))? "(" .$class['TERM'] .")" :'';?> </span><br>
                                        <?php } ?>
                                        <?php if($class['BTYPE'] == '3'){?>
                                        <span style="white-space:pre; color:#FF0004;"> <?=substr($class['FROM_TIME'], 0, 5);?> ~ <?=substr($class['TO_TIME'], 0, 5);?> <?=$class['Year'];?>年 <?=$class['CLASS_NAME'];?> <?=(!empty($class['TERM']))? "(" .$class['TERM'] .")" :'';?> </span><br>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
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
        var url = '<?=base_url('venue_rental/classroom/ajax/get_room');?>';
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

var del_list_booking = function(booking_id, booking_date) {
        var url = '<?=base_url('venue_rental/classroom/ajax/del_list_booking');?>';
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'booking_id': booking_id,
            'booking_date': booking_date,

        }

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            location.reload();
                        } else {

                        }
                    }

        });

}
$(document).ready(function() {

$("#test1").datepicker();

$("#test3").datepicker();
$('#test4').click(function(){
    $("#test3").focus();
});
});

</script>