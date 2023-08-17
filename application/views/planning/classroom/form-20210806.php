<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" id="seq_no" name="seq_no" value="<?=set_value('seq_no', $form['seq_no']); ?>">
    <div class="col-xs-12" >
        <input type="button" class="btn btn-primary" onclick="show_course('<?=$page_name?>')" value="選取">
    </div>
    <div class="form-group col-xs-6 required <?=form_error('year')?'has-error':'';?>">
        <label class="control-label">年度</label>
        <input class="form-control" name="year" id="year" readonly="" placeholder="" value="<?=set_value('year', $form['year']); ?>">
        <?=form_error('year'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('class_no')?'has-error':'';?>">
        <label class="control-label">班期代碼</label>
        <input class="form-control" name="class_no" id="class_no" readonly="" placeholder="" value="<?=set_value('class_no', $form['class_no']); ?>">
        <?=form_error('class_no'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('class_name')?'has-error':'';?>">
        <label class="control-label">班期名稱</label>
        <input class="form-control" name="class_name" id="class_name" readonly="" placeholder="" value="<?=set_value('class_name', $form['class_name']); ?>">
        <?=form_error('class_name'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('term')?'has-error':'';?>">
        <label class="control-label">期別</label>
        <input class="form-control" name="term" id="term" readonly="" placeholder="" value="<?=set_value('term', $form['term']); ?>">
        <?=form_error('term'); ?>
    </div>

    <div class="tab-pane col-xs-12" id="booking" >
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="20%">使用起日<?=form_error("times"); ?></th>
                    <th width="20%">使用迄日</th>
                    <th width="20%">使用類別</th>
                    <th width="20%">使用時段</th>
                    <th width="20%">使用名稱</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><div class="input-group">
                        <input type="text" class="form-control <?=form_error('start_date')?'has-error':'';?> datepicker" id="set_start_date" name="start_date" value="<?=substr(set_value('start_date', $form['start_date']),0,10); ?>" onchange="check_year(this)"/>
                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span></div></td>
                    <td><div class="input-group">
                        <input type="text" class="form-control <?=form_error('end_date')?'has-error':'';?> datepicker" id="set_end_date" name="end_date" value="<?=substr(set_value('end_date', $form['end_date']),0,10); ?>" onchange="check_year(this)"/>
                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i
                                        class="fa fa-calendar"></i></span></div></td>
                    <td>
                    	<?php
				            echo form_dropdown('room_type', $choices['room_type'], set_value('room_type', ''), 'class="form-control" id="set_room_type"');
				        ?>
                    </td>
                    <td>
                    	<select class="form-control" id="set_room_time" name="room_time" onchange="get_place();" >
                    	<option value="" >請選擇</option>
                        <?php foreach ($choices['time_list'] as $key => $time) { ?>
                        <option value="<?=$key;?>" ><?=$time;?></option>
                        <?php } ?>
                        </select>
                    </td>
                    <td class="<?=form_error('addRoom')?'has-error':'';?>" >
                    	<select class="form-control" id="addRoom" name="addRoom" >
                        <option value="" >請選擇</option>
                        </select>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>

    <div class="tab-pane col-xs-12">
    	<div style="color:red;FONT-SIZE:16px;"><b>預約紀錄</b></div>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th>功能</th>
                    <th>使用起日</th>
                    <th>使用迄日</th>
                    <th>使用類別</th>
                    <th>使用名稱</th>
                    <th>使用時段</th>
                </tr>
            </thead>
            <tbody id="bookinged">
            	<?php if(isset($booking)) { ?>
            	<?php foreach( $booking as $row){ ?>
                <tr>
                    <td>
                        <button type="button" class="btn btn-outline btn-danger btn-xs btn-toggle" title="刪除" onclick="del_booking('<?=$row['room_id'];?>', '<?=$row['cat_id'];?>', '<?=$row['booking_period'];?>', '<?=$row['start_date'];?>', '<?=$row['end_date'];?>');">
                            刪除
                        </button>
                    </td>
                    <td><?=$row['start_date'];?></td>
                    <td><?=$row['end_date'];?></td>
                    <td><?=$choices['room_type'][$row['cat_id']];?></td>
                    <td><?=$row['room_name'];?></td>
                    <td><?=$choices['time_list'][$row['booking_period']];?></td>
                </tr>
                <?php } ?>
                <?php } ?>
            </tbody>

        </table>
    </div>


	<input type="hidden" id="doAction" name="doAction" value="">
	</div>
</form>

<script>

var del_booking = function(room_id, cat_id, booking_period, start_date, end_date) {
        var url = '<?=base_url('planning/classroom/ajax/del_booking');?>';
        var seq_no = $('#seq_no').val();
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'seq_no': seq_no,
            'room_id': room_id,
            'cat_id': cat_id,
            'booking_period': booking_period,
            'start_date': start_date,
            'end_date': end_date,

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

var get_booking = function() {
        var url = '<?=base_url('planning/classroom/ajax/get_booking');?>';
        var seq_no = $('#seq_no').val();


        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'seq_no': seq_no,

        }

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            // console.log(response.data);
                            show_booking(response.data);
                        } else {

                        }
                    }

        });

}

var show_booking = function(booking_data) {
    $("#bookinged").empty();
    var html = '';
    for (k in booking_data) {
        var row = booking_data[k];
        html += '<tr>';
        html += '   <td>';
        html += '   <button type="button" class="btn btn-outline btn-danger btn-xs btn-toggle" title="刪除" onclick="del_booking('+ row.room_id +');">刪除</button>';
        html += '   </td>';
        html += '   <td>'+ row.start_date +'</td>';
        html += '   <td>'+ row.end_date +'</td>';
        html += '   <td>'+ row.cat_name +'</td>';
        html += '   <td>'+ row.room_name +'</td>';
        html += '   <td>'+ row.booking_period_name +'</td>';
        html += '</tr>';
    }
    $('#bookinged').append(html);
    // console.log(booking_data);
}

var get_place = function() {
        var url = '<?=base_url('planning/classroom/ajax/get_place');?>';
        var start_date = $('#set_start_date').val();
        var end_date = $('#set_end_date').val();
        var room_type = $('#set_room_type').val();
        var room_time = $('#set_room_time').val();

        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'start_date': start_date,
            'end_date': end_date,
            'room_type': room_type,
            'room_time': room_time,
        }

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            // console.log(response.data);
                            setList(response.data);
                        } else {

                        }
                    }

        });

}

function setList(DataList){
  obj = document.getElementById('addRoom');
  dataAry = DataList;
  obj.options.length = 0;
    var new_option = new Option('請選擇','');
  obj.options.add(new_option);
    for(i=0;i<dataAry.length;i++){
    strAry = dataAry[i];
    if(strAry[0]!=""){
        if(strAry.room_id =='E507' || strAry.room_id =='E501' ||strAry.room_id =='C209' ){
            continue;
        }
        var new_option = new Option(strAry.room_name,strAry.room_id);
        obj.options.add(new_option);
    }
    }

}


function show_course(page_name){

        var path = '../../../pop_require.php';

    var myW=window.open(path, 'selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
    myW.focus();
}

$(document).ready(function() {
  $("#set_start_date").datepicker();
  $('#datepicker2').click(function(){
    $("#set_start_date").focus();
  });

  $("#set_end_date").datepicker();
  $('#datepicker4').click(function(){
    $("#set_end_date").focus();
  });
});

function check_year(e) {
    console.log(e);
    console.log(e.value);
    var yaer = $("input[name=year]").val();
    if(year==''){
        alert('請先選取班期');
        e.value='';
        return false;
    }
    var newyear = parseInt(yaer)+1911;
    var checkyear = e.value.substring(0,4);
    if(newyear != checkyear){
        alert('所選班期年度和預約教室日期起迄年度需相同。');
        e.value='';
        return false;
    }

}
</script>
