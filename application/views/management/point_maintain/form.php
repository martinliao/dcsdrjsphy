<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" name='editForm' role="form" method="post" action="<?=$link_save_file;?>" enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="year" id="year" value="<?=$detail_data['year'];?>">
    <input type="hidden" name="class_no" id="class_no" value="<?=$detail_data['class_no'];?>">
    <input type="hidden" name="class_name" id="class_name" value="<?=$detail_data['class_name'];?>">
    <input type="hidden" name="term" id="term" value="<?=$detail_data['term'];?>">

    <div class="form-group col-xs-6 <?=form_error('grade_type')?'has-error':'';?>">
        <label class="control-label">類別</label>
        <?php
            echo form_dropdown('grade_type', $choices['grade_type'], set_value('grade_type', $form['grade_type']), 'class="form-control" id="grade_type" ');
        ?>
        <?=form_error('grade_type'); ?>
    </div>

    <div class="form-group required col-xs-6 <?=form_error('proportion')?'has-error':'';?>">
        <label class="control-label">百分比</label>
        <div class="input-daterange input-group">
            <input class="form-control" name="proportion" id="proportion" value="<?=set_value('proportion', $form['proportion']); ?>">
            <span class="input-group-addon" >%</span>
        </div>
        <?=form_error('proportion'); ?>
    </div>

    <div class="form-group col-xs-6">

    <input type='button' name="btnSave" id="btnSave" value='儲存' onclick="btn_submit('<?=$page_name;?>');"  class='button'/>
    <input type='button' name='btnCancel' id='btnCancel' value='取消' onclick='go_to_detail()' class='button' />
    </div>
</form>

<form id="detail-form" role="form" method="post" >
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="year" id="year" value="<?=$detail_data['year'];?>">
    <input type="hidden" name="class_no" id="class_no" value="<?=$detail_data['class_no'];?>">
    <input type="hidden" name="class_name" id="class_name" value="<?=$detail_data['class_name'];?>">
    <input type="hidden" name="term" id="term" value="<?=$detail_data['term'];?>">
</form>

<script>
function go_to_detail() {
    obj=document.getElementById("detail-form");
    obj.action='<?=base_url("management/point_maintain/detail/{$class['seq_no']}")?>';
    obj.submit();
}

function btn_submit(mod){

    obj =document.getElementById('data-form');
    if(document.getElementById('grade_type').value==''){
        alert("請輸入類別!");
        return;
    }

    if(document.getElementById('proportion').value==''){
        alert("請輸入百分比!");
        return;
    }

    obj.submit();
}

</script>