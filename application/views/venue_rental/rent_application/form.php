<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>" enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="app_id" id="appId" value="<?=set_value('app_id', $form['app_id']); ?>" />

    <?php if($page_name == 'edit') { ?>
    <input type="hidden" name="appi_id" id="appi_id" value="<?=set_value('appi_id', $form['appi_id']); ?>" />
    <div class="form-group col-xs-12 <?=form_error('billno')?'has-error':'';?>">
        <label class="control-label">收據號碼</label>
        <input class="form-control" name="billno" value="<?=set_value('billno', $form['billno']); ?>">
    </div>
    <div class="form-group col-xs-4 <?=form_error('people')?'has-error':'';?>">
        <label class="control-label">請輸入人數</label>
        <input class="form-control" name="people" value="<?=set_value('people', $form['people']); ?>">
    </div>
    <div class="form-group col-xs-4 <?=form_error('days')?'has-error':'';?>">
        <label class="control-label">請輸入天數</label>
        <input class="form-control" name="days" value="<?=set_value('days', $form['days']); ?>">
    </div>
    <div class="form-group col-xs-4 <?=form_error('billno')?'has-error':'';?>">
        <label class="control-label">合計人天次</label>
        <input class="form-control" name="appDayP" readonly="readonly" value="<?=($form['days']*$form['people']);?>">
    </div>
    <?php } ?>
    <?php if($page_name == 'add') { ?>
    <!-- <div class="col-xs-12" >

    </div> -->
    <?php } ?>
    <div class="form-group required col-xs-6 <?=form_error('app_name')?'has-error':'';?>">
        <label class="control-label">申請單位</label> <input type="button" class="btn btn-primary" onclick="show_course('<?=$page_name?>')" value="選取">
        <input class="form-control" name="app_name" id="appName" placeholder="" readonly="readonly" value="<?=set_value('app_name', $form['app_name']); ?>">
    </div>

    <div class="form-group col-xs-6 "  >
        <label class="control-label">是否為市府單位</label><br>
        <input type="checkbox" style="padding-bottom: 9px;" name="is_public" id="appIspub" value="Y" onclick="return false" style="cursor: not-allowed;" <?=set_checkbox('is_public', 'Y', $form['is_public']=='Y');?> >
    </div>


    <div class="form-group col-xs-6 ">
        <label class="control-label">聯絡人姓名</label>
        <input class="form-control" name="contact_name" id="appContact" placeholder="" readonly="readonly" value="<?=set_value('contact_name', $form['contact_name']); ?>">
    </div>

    <div class="form-group col-xs-6 ">
        <label class="control-label">電話</label>
        <input class="form-control" name="tel" id="appTel" placeholder="" readonly="readonly" value="<?=set_value('tel', $form['tel']); ?>">
    </div>

    <div class="form-group col-xs-6 ">
        <label class="control-label">傳真</label>
        <input class="form-control" name="fax" id="appFax" placeholder="" readonly="readonly" value="<?=set_value('fax', $form['fax']); ?>">
    </div>

    <div class="form-group col-xs-6 ">
        <label class="control-label">E-Mail</label>
        <input class="form-control" name="email" id="appEmail" placeholder="email@example.com" readonly="readonly" value="<?=set_value('email', $form['email']); ?>">
    </div>

    <div class="form-group col-xs-12">
        <label class="control-label">地址</label>
        <input class="form-control" name="addr" id="appAddr" readonly="readonly" value="<?=set_value('addr', $form['zone'].$form['addr']); ?>">
    </div>

    <div class="form-group required col-xs-12 <?=form_error('app_reason')?'has-error':'';?>">
        <label class="control-label">活動名稱暨內容說明</label>
        <input class="form-control" name="app_reason" value="<?=set_value('app_reason', $form['app_reason']); ?>">
        <?=form_error('app_reason'); ?>
    </div>

    <div class="form-group col-xs-12 <?=form_error('memo')?'has-error':'';?>">
        <label class="control-label">其它代辦事項</label>
        <textarea class="form-control" name="memo"><?=set_value('memo', $form['memo']); ?></textarea>
        <?=form_error('memo'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('other_expense')?'has-error':'';?>">
        <label class="control-label">代辦事項費用</label>
        <input class="form-control" name="other_expense" value="<?=set_value('other_expense', $form['other_expense']); ?>">
        <?=form_error('other_expense'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('total_expense')?'has-error':'';?>">
        <label class="control-label">金額總計</label>
        <input class="form-control" name="total_expense" value="<?=set_value('total_expense', $form['total_expense']); ?>">
        <?=form_error('total_expense'); ?>
    </div>

    <div class="form-group col-xs-6 " style="padding-bottom: 9px;" >
        <label class="control-label">是否公告至電視牆</label><br>
        <input type="checkbox" name="tv_wall" value="Y" <?=set_checkbox('tv_wall', 'Y', $form['tv_wall']=='Y');?>>
    </div>

    <?php if($page_name == 'edit') { ?>
    <div class="tab-pane col-xs-12" id="booking" >
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="12%">使用起日</th>
                    <th width="12%">使用迄日</th>
                    <th >使用類別</th>
                    <th >使用名稱</th>
                    <th >使用時段</th>
                    <th width="6%">單位</th>
                    <th width="5%">數量</th>
                    <th width="10%">折扣</th>
                    <th width="10%">備註</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="input-group" id="start_date">
                            <input type="text" class="form-control <?=form_error('start_date')?'has-error':'';?> datepicker" id="set_start_date" name="start_date" value="<?=set_value('start_date', $form['start_date']); ?>"/>
                            <span class="input-group-addon" style="cursor: pointer;" ><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group" id="end_date">
                            <input type="text" class="form-control <?=form_error('end_date')?'has-error':'';?> datepicker" id="set_end_date" name="end_date" value="<?=set_value('end_date', $form['end_date']); ?>"/>
                            <span class="input-group-addon" style="cursor: pointer;" ><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td>
                        <?php
                            $choices['room_type'] = array(''=>'請選擇') + $choices['room_type'];
                            echo form_dropdown('room_type', $choices['room_type'], set_value('room_type', ''), 'class="form-control" id="set_room_type" onchange="get_room();"');
                        ?>
                    </td>
                    <td class="<?=form_error('addRoom')?'has-error':'';?>" >
                        <select class="form-control" id="addRoom" name="addRoom" onchange="get_room_time();" >
                        <option value="" >請選擇</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" id="set_room_time" name="room_time" >
                        <option value="" >請選擇</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control <?=form_error('addCountby')?'has-error':'';?> " id="addCountby" name="addCountby" value="<?=set_value('addCountby', $form['addCountby']); ?>"/>
                    </td>
                    <td>
                        <input type="text" style="width:150px" class="form-control <?=form_error('addCount')?'has-error':'';?> " id="addCount" name="addCount" value="<?=set_value('addCount', $form['addCount']); ?>"/>
                    </td>
                    <td>
                        <input type="text" class="form-control <?=form_error('addDiscount')?'has-error':'';?> " id="addDiscount" name="addDiscount" value="<?=set_value('addDiscount', $form['addDiscount']); ?>"/>
                    </td>
                    <td>
                        <input type="text" class="form-control <?=form_error('addNote')?'has-error':'';?> " id="addNote" name="addNote" value="<?=set_value('addNote', $form['addNote']); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="9">
                        <input type="button" class="btn btn-primary" id="addBtn1" onclick="add()" value="新增">
                        <input type="button" class="btn btn-primary" id="addBtn2" style="display: none;" onclick="addOt()" value="新增宿舍">
                    </td>
                </tr>
            </tbody>

        </table>
    </div>

    <div class="tab-pane col-xs-12">
        <div style="color:red;"><b>市府單位:外借費用(場地費+服務費) 不收費, 週六假日:外借費用(場地費+服務費)加2成 (以上不含宿舍及餐廳, 但自行折扣則無此限)</b></div>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th>功能</th>
                    <th>使用起日</th>
                    <th>使用迄日</th>
                    <th>使用類別</th>
                    <th>使用名稱</th>
                    <th>使用時段</th>
                    <th>單位</th>
                    <th>數量</th>
                    <th>場地費</th>
                    <th>服務費</th>
                    <th>伙食費</th>
                    <th>週六日天數</th>
                    <th>是否為市府單位</th>
                    <th>折扣</th>
                    <th>預估金額</th>
                    <th>備註</th>
                </tr>
            </thead>
            <tbody id="bookinged">
                <?php $all_expense = '0'; ?>
                <?php if(isset($room_use_list)) { ?>
                <?php foreach( $room_use_list as $row){ ?>
                <tr>
                    <td>
                        <button type="button" class="btn btn-outline btn-danger btn-xs btn-toggle" title="刪除" onclick="del_room_use(<?=$row['appi_id'];?>, <?=$row['groupnum'];?>);">
                            刪除
                        </button>
                    </td>
                    <td><?=substr($row['start_date'], 0, 10);?></td>
                    <td><?=substr($row['end_date'], 0, 10);?></td>
                    <td><?=$choices['room_type'][$row['cat_id']];?></td>
                    <td><?=$row['room_name'];?></td>
                    <td><?=$choices['time_list'][$row['use_period']];?></td>
                    <td><?=!empty($room_countby[$row['unit']])?$room_countby[$row['unit']]:''?></td>
                    <td><?=$row['num'];?></td>
                    <td><?=$row['price_a'];?></td>
                    <td><?=$row['price_b'];?></td>
                    <td><?=$row['price_c'];?></td>
                    <td><?=$row['weekend'];?></td>
                    <td><?=$form['is_public'];?></td>
                    <td><?=$row['discount'];?></td>
                    <td><?=$row['expense'];?></td>
                    <td><?=$row['groupnote'];?></td>
                </tr>
                <?php $all_expense += $row['expense']; ?>
                <?php } ?>
                <?php } ?>
                <tr>
                    <td colspan="16">
                    <div class="text-right" >小計:<?=$all_expense;?>元</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php } ?>
</form>

<script>

<?php if($page_name == 'edit') { ?>
$(document).ready(function() {
    $( "#start_date" ).click(function() {
        $("input#set_start_date").trigger("focus");
    });

    $( "#end_date" ).click(function() {
        $("input#set_end_date").trigger("focus");
    });

});
var room_use_add2 = function(room_use, addDiscount, addNote) {

    var url = '<?=base_url('venue_rental/rent_application/ajax/room_use_add2');?>';
    var set_start_date = $('#set_start_date').val();
    var set_end_date = $('#set_end_date').val();
    var set_room_type = $('#set_room_type').val();
    var appi_id = $('#appi_id').val();

    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'set_start_date': set_start_date,
        'set_end_date': set_end_date,
        'set_room_type': set_room_type,
        'room_use': room_use,
        'addDiscount': addDiscount,
        'addNote': addNote,
        'appi_id': appi_id,

    }

    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        // console.log(response.data);
                        location.reload();
                    } else {
                        location.reload();
                    }
                }

    });
}


function addOt(){
  if (document.all.set_start_date.value==""){
    alert("請輸使用起日");
    document.all.set_start_date.focus();
    return false;
  }
  if (document.all.set_end_date.value==""){
    alert("請輸使用迄日");
    document.all.set_end_date.focus();
    return false;
  }

  obj1 = document.all.set_start_date;
  obj2 = document.all.set_end_date;
  var seqno = $('#appi_id').val();
  var path = '../../../../csrmrent_combo_roomOt.php?seqno=' + seqno + '&s1=' + obj1.value + '&s2=' + obj2.value;
  var myW=window.open(path ,'popAddRoom','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}

function add(){
    if (document.all.set_start_date.value==""){
        alert("請輸使用起日");
        document.all.set_start_date.focus();
        return false;
    }
    if (document.all.set_end_date.value==""){
        alert("請輸使用迄日");
        document.all.set_end_date.focus();
        return false;
    }
    if (document.all.set_room_type.value==""){
        alert("請輸使用類別");
        document.all.set_room_type.focus();
        return false;
    }
    if (document.all.addRoom.value==""){
        alert("請輸使用名稱");
        document.all.addRoom.focus();
        return false;
    }
    if (document.all.set_room_time.value==""){
        alert("請輸使用時段");
        document.all.set_room_time.focus();
        return false;
    }
    if (document.all.addCount.value==""){
        alert("請輸數量");
        document.all.addCount.focus();
        return false;
    }

    var url = '<?=base_url('venue_rental/rent_application/ajax/room_use_add');?>';

    var set_start_date = $('#set_start_date').val();
    var set_end_date = $('#set_end_date').val();
    var set_room_type = $('#set_room_type').val();
    var room_id = $('#addRoom').val();
    var set_room_time = $('#set_room_time').val();
    var addCount = $('#addCount').val();
    var addDiscount = $('#addDiscount').val();
    var addNote = $('#addNote').val();
    var appi_id = $('#appi_id').val();

    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'set_start_date': set_start_date,
        'set_end_date': set_end_date,
        'set_room_type': set_room_type,
        'room_id': room_id,
        'set_room_time': set_room_time,
        'addCount': addCount,
        'addDiscount': addDiscount,
        'addNote': addNote,
        'appi_id': appi_id,
    }

    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        // console.log(response.data);
                        location.reload();
                    } else {
                        location.reload();
                    }
                }

    });

}

var del_room_use = function(appi_id, groupnum) {
    var url = '<?=base_url('venue_rental/rent_application/ajax/del_room_use');?>';
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'appi_id': appi_id,
        'groupnum': groupnum,

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

var get_room = function() {
    var url = '<?=base_url('venue_rental/rent_application/ajax/get_room');?>';

    var room_type = $('#set_room_type').val();

    if (room_type=="02"){
    document.all.addRoom.disabled = true;
    document.all.set_room_time.disabled = true;
    document.all.addCountby.disabled = true;
    document.all.addCount.disabled = true;
    document.all.addDiscount.disabled = true;
    document.all.addNote.disabled = true;
    document.all.addBtn1.style.display = "none";
    document.all.addBtn2.style.display = "";
    return false;
  }
  else{
    document.all.addRoom.disabled = false;
    document.all.set_room_time.disabled = false;
    document.all.addCountby.disabled = false;
    document.all.addCount.disabled = false;
    document.all.addDiscount.disabled = false;
    document.all.addNote.disabled = false;
    document.all.addBtn1.style.display = "";
    document.all.addBtn2.style.display = "none";
  }

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
                        // console.log(response.data);
                        setList(response.data);
                    } else {
                        setList(response.data);
                    }
                }

    });

}

var get_room_time = function() {
    var url = '<?=base_url('venue_rental/rent_application/ajax/get_room_time');?>';

    var room_id = $('#addRoom').val();
    var tmp = '';
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'room_id': room_id,
    }

    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        // console.log(response.data);
                        setTimeList(response.data);

                        if (response.room_countby=="1"){
                            tmp = "人";
                        }
                        if (response.room_countby=="2"){
                            tmp = "桌";
                        }
                        if (response.room_countby=="3"){
                            tmp = "場地";
                        }
                        $('#addCountby').val(tmp);
                    } else {
                        setTimeList(response.data);
                        $('#addCountby').val('');
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
      var new_option = new Option(strAry.room_name,strAry.room_id);
        obj.options.add(new_option);
    }

    }

}

function setTimeList(DataList){
  obj = document.getElementById('set_room_time');
  dataAry = DataList;
  obj.options.length = 0;
    var new_option = new Option('請選擇','');
  obj.options.add(new_option);
    for(i=0;i<dataAry.length;i++){
    strAry = dataAry[i];
    if(strAry[0]!=""){
      var new_option = new Option(strAry.name,strAry.price_t);
        obj.options.add(new_option);
    }
    }

}
<?php } ?>

<?php if($page_name == 'add') { ?>
function show_course(page_name){

        var path = '../../../pop_appinfo.php';

    var myW=window.open(path, 'selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
    myW.focus();
}
<?php } ?>

</script>