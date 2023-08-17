<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<font color="red">備註：當【交通費】設定為【-1】時，在【13A請款選取】不允許手動調整。</font>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group required col-xs-6 <?=form_error('class_type_id')?'has-error':'';?>">
        <label class="control-label">鐘點費類別</label>
        <?php
            if($page_name == 'add'){
                echo form_dropdown('class_type_id', $choices['hourlyfee_category'], set_value('class_type_id', $form['class_type_id']), 'class="form-control" ');
            }else{
                echo form_dropdown('class_type_id', $choices['hourlyfee_category'], set_value('class_type_id', $form['class_type_id']), 'class="form-control" disabled ');
            }
        ?>
        <?=form_error('class_type_id'); ?>
    </div>
    <div class="form-group required col-xs-6 <?=form_error('type')?'has-error':'';?>">
        <label class="control-label">身分別</label>
        <?php
            if($page_name == 'add'){
                echo form_dropdown('type', $choices['teacher_type'], set_value('type', $form['type']), 'class="form-control" ');
            }else{
                echo form_dropdown('type', $choices['teacher_type'], set_value('type', $form['type']), 'class="form-control" disabled ');
            }
        ?>
        <?=form_error('type'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('assistant_type_id')?'has-error':'';?>">
        <label class="control-label">助教聘請類別</label>
        <?php
            if($page_name == 'add'){
                echo form_dropdown('assistant_type_id', $choices['hire_category'], set_value('assistant_type_id', $form['assistant_type_id']), 'class="form-control" ');
            }else{
                echo form_dropdown('assistant_type_id', $choices['hire_category'], set_value('assistant_type_id', $form['assistant_type_id']), 'class="form-control" disabled ');
            }
        ?>
        <?=form_error('assistant_type_id'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('teacher_type_id')?'has-error':'';?>">
        <label class="control-label">講師聘請類別</label>
        <?php
            if($page_name == 'add'){
                echo form_dropdown('teacher_type_id', $choices['hire_category'], set_value('teacher_type_id', $form['teacher_type_id']), 'class="form-control" ');
            }else{
                echo form_dropdown('teacher_type_id', $choices['hire_category'], set_value('teacher_type_id', $form['teacher_type_id']), 'class="form-control" disabled ');
            }
        ?>
        <?=form_error('teacher_type_id'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('hour_fee')?'has-error':'';?>">
        <label class="control-label">鐘點費</label>
        <input class="form-control" name="hour_fee" placeholder="" value="<?=set_value('hour_fee', $form['hour_fee']); ?>">
        <?=form_error('hour_fee'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('traffic_fee')?'has-error':'';?>">
        <label class="control-label">交通費</label>
        <input class="form-control" name="traffic_fee" placeholder="" value="<?=set_value('traffic_fee', $form['traffic_fee']); ?>">
        <?=form_error('traffic_fee'); ?>
    </div>

</form>
