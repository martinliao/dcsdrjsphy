<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('room_id')?'has-error':'';?>">
        <label class="control-label">場地代碼</label>
        <input class="form-control" name="room_id" placeholder="" value="<?=set_value('room_id', $form['room_id']); ?>">
        <?=form_error('room_id'); ?>
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">場地類別</label>
        <?php
            echo form_dropdown('room_type', $choices['room_type'], set_value('room_type', $form['room_type']), 'class="form-control"');
        ?>
        <?=form_error('room_type'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('room_name')?'has-error':'';?>">
        <label class="control-label">場地名稱</label>
        <input class="form-control" name="room_name" placeholder="" value="<?=set_value('room_name', $form['room_name']); ?>">
        <?=form_error('room_name'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('room_sname')?'has-error':'';?>">
        <label class="control-label">場地簡稱</label>
        <input class="form-control" name="room_sname" placeholder="" value="<?=set_value('room_sname', $form['room_sname']); ?>">
        <?=form_error('room_sname'); ?>
    </div>
    <div class="form-group col-xs-6 required">
        <label class="control-label">所屬單位</label>
        <?php
            echo form_dropdown('room_bel', $choices['room_bel'], set_value('room_bel', $form['room_bel']), 'class="form-control"');
        ?>
        <?=form_error('room_bel'); ?>
    </div>
    <div class="form-group col-xs-6 required">
        <label class="control-label">計價方式</label>
        <?php
            echo form_dropdown('room_countby', $choices['room_countby'], set_value('room_countby', $form['room_countby']), 'class="form-control"');
        ?>
        <?=form_error('room_countby'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('room_cap')?'has-error':'';?>">
        <label class="control-label">容納人數</label>
        <input class="form-control" name="room_cap" placeholder="" value="<?=set_value('room_cap', $form['room_cap']); ?>">
        <?=form_error('room_cap'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('room_location')?'has-error':'';?>">
        <label class="control-label">場地教室位置</label>
        <input class="form-control" name="room_location" placeholder="" value="<?=set_value('room_location', $form['room_location']); ?>">
        <?=form_error('room_location'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('room_manage')?'has-error':'';?>">
        <label class="control-label">管理單位</label>
        <input class="form-control" name="room_manage" placeholder="" value="<?=set_value('room_manage', $form['room_manage']); ?>">
        <?=form_error('room_manage'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('room_contact')?'has-error':'';?>">
        <label class="control-label">聯絡人</label>
        <input class="form-control" name="room_contact" placeholder="" value="<?=set_value('room_contact', $form['room_contact']); ?>">
        <?=form_error('room_contact'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('room_phone')?'has-error':'';?>">
        <label class="control-label">聯絡電話</label>
        <input class="form-control" name="room_phone" placeholder="" value="<?=set_value('room_phone', $form['room_phone']); ?>">
        <?=form_error('room_phone'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('room_soft')?'has-error':'';?>">
        <label class="control-label">軟體資源</label>
        <input class="form-control" name="room_soft" placeholder="" value="<?=set_value('room_soft', $form['room_soft']); ?>">
        <?=form_error('room_soft'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('room_equi')?'has-error':'';?>">
        <label class="control-label">設備</label>
        <input class="form-control" name="room_equi" placeholder="" value="<?=set_value('room_equi', $form['room_equi']); ?>">
        <?=form_error('room_equi'); ?>
    </div>

    <div class="tab-pane" id="time" >
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="50%">外借可使用時段<?=form_error("times"); ?></th>
                    <th width="10%">場地費</th>
                    <th width="10%">服務費</th>
                    <th width="10%">伙食費</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($form['item'])) { ?>
                    <?php foreach($form['item'] as $num => $row) { ?>
                    <tr>
                        <input type="hidden" name="times" value="<?=$num;?>'">
                        <td>
                        <select class="form-control" id="select_<?=$num;?>" name="item[<?=$num;?>][price_t]" >
                        <?php foreach ($choices['time_list'] as $time) { ?>
                        <option value="<?=$time['item_id'];?>" <?=$time['item_id']==$row['price_t']?'selected':'';?>><?=$time['name'];?></option>
                        <?php } ?>
                        </select>
                        </td>
                        <td><input type="text" class="form-control" id="price_a_<?=$num;?>" name="item[<?=$num;?>][price_a]" value="<?=set_value("item[{$num}][price_a]", $form['item'][$num]['price_a']);?>"></td>
                        <td><input type="text" class="form-control" id="price_b_<?=$num;?>" name="item[<?=$num;?>][price_b]" value="<?=set_value("item[{$num}][price_b]", $form['item'][$num]['price_b']);?>"></td>
                        <td><input type="text" class="form-control" id="price_c_<?=$num;?>" name="item[<?=$num;?>][price_c]" value="<?=set_value("item[{$num}][price_c]", $form['item'][$num]['price_c']);?>"></td>
                        <td align="right"><button type="button" class="btn btn-danger btn-sm" disabled="disabled" id="remove_<?=$num;?>" onclick="removeItem(this, <?=$num;?>)">刪除</button></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" align="right"><button type="button" class="btn btn-success btn-sm" onclick="addTime()">新增</button></td>
                </tr>
            </tfoot>
        </table>
    </div>

</form>

<script type="text/javascript">

var time_list = [
<?php
    foreach ($choices['time_list'] as $c) {
        echo json_encode($c).',';
    }
?>
];

$(function() {

    <?php if(isset($form['item']) && !empty($form['item'])) { ?>

    var len = $('#time table tbody tr').size();
    document.getElementById('remove_'+(len-1)+'').disabled = false;

    <?php } ?>


});


function addTime() {
    var num = $('#time table tbody tr').size();
    var html = '';
    html += '<tr>';
    html += '   <input type="hidden" name="times" value="'+ num +'">';
    html += '   <td>';
    html += '       <select class="form-control" id="select_'+ num +'" name="item['+ num +'][price_t]" >';
    for (k in time_list) {
        var row = time_list[k];
        html += '<option value="'+ row.item_id +'" >' + row.name + '</option>';
    }
    html += '       </select>';
    html += '   </td>';
    html += '   <td><input type="text" class="form-control" id="price_a_'+ num +'" name="item['+ num +'][price_a]" value="0"></td>';
    html += '   <td><input type="text" class="form-control" id="price_b_'+ num +'" name="item['+ num +'][price_b]" value="0"></td>';
    html += '   <td><input type="text" class="form-control" id="price_c_'+ num +'" name="item['+ num +'][price_c]" value="0"></td>';
    html += '   <td align="right"><button type="button" class="btn btn-danger btn-sm" disabled="disabled" id="remove_'+ num +'" onclick="removeItem(this, '+ num +')">刪除</button></td>';
    html += '</tr>';
    $('#time table tbody').append(html);


    var len = $('#time table tbody tr').size();

    for(i=0;i<len;i++){
        document.getElementById('remove_'+i+'').disabled = true;
        document.getElementById('remove_'+(len-1)+'').disabled = false;
    }

}

function removeItem(obj, num) {
    if(num != 0){
        document.getElementById('remove_'+(num-1)+'').disabled = false;
    }
    $(obj).closest('tr').remove();
}


</script>
