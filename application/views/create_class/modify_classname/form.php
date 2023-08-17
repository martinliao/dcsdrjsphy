<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <div class="form-group col-xs-6  <?=form_error('year')?'has-error':'';?>">
        <label class="control-label">年度</label>
        <input class="form-control" name="year" placeholder="" value="<?=set_value('year', $form['year']); ?>" readonly>
        <?=form_error('year'); ?>
    </div>
    <div class="form-group col-xs-6  <?=form_error('class_no')?'has-error':'';?>">
        <label class="control-label">班期代碼</label>
        <input class="form-control" name="class_no" placeholder="" value="<?=set_value('class_no', $form['class_no']); ?>" readonly>
        <?=form_error('class_no'); ?>
    </div>
    <div class="form-group col-xs-6  <?=form_error('class_name')?'has-error':'';?>">
        <label class="control-label">原班期名稱</label>
        <input class="form-control" name="class_name" placeholder="" value="<?=set_value('class_name', $form['class_name']); ?>" disabled>
        <?=form_error('class_name'); ?>
    </div>
    <div class="form-group col-xs-6  <?=form_error('new_class_name')?'has-error':'';?>">
        <label class="control-label">新班期名稱</label>
        <input class="form-control" name="new_class_name" placeholder="" value="<?=set_value('new_class_name', $form['class_name']); ?>">
        <?=form_error('new_class_name'); ?>
    </div>
    <div class="form-group col-xs-6  <?=form_error('class_name_shot')?'has-error':'';?>">
        <label class="control-label">簡稱</label>
        <input class="form-control" name="class_name_shot" placeholder="" value="<?=set_value('class_name_shot', $form['class_name_shot']); ?>">
        <?=form_error('class_name_shot'); ?>
    </div>
</form>
