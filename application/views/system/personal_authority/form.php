<link href="<?=HTTP_PLUGIN;?>lou-multi-select/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
<style>
.ms-container {
    width: 100%;
}
.ms-container .ms-list  {
    height: 300px;
}
</style>
<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group required <?=form_error('user_id')?'has-error':'';?>">
        <label class="control-label">帳號</label>
        <?php if ($page_name == 'add') { ?>
        <?php
            $choices['user_id'] = array(''=>'-- None --') + $choices['user_id'];
            echo form_dropdown('user_id', $choices['user_id'], set_value('user_id', $form['user_id']), 'class="form-control msDropDown"');
        ?>
        <?php } else { ?>
        <?php
            $choices['user_id'] = array(''=>'-- None --') + $choices['user_id'];
            echo form_dropdown('user_id', $choices['user_id'], set_value('user_id', $form['user_id']), 'class="form-control msDropDown" disabled');
        ?>
        <?php }?>

        <?=form_error('user_id'); ?>
    </div>

    <div class="form-group <?=form_error('auth')?'has-error':'';?>">
        <label class="control-label">權限</label>
        <p>
            <button type="button" class="btn btn-link" id="select-all">選擇全部</button> /
            <button type="button" class="btn btn-link" id="deselect-all">移除全部</button>
        </p>
        <?php
            echo form_dropdown('auth[]', $choices['menu'], $form['auth'], 'style="height: 200px;" multiple');
        ?>
        <?=form_error('auth[]');?>
    </div>

</form>

<script src="<?=HTTP_PLUGIN;?>lou-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script>
$(function() {
    $('select[name="auth[]"]').multiSelect({
        selectableHeader: "<div class='custom-header'>可選權限</div>",
        selectionHeader: "<div class='custom-header'>選定權限</div>",
    });
    $('#select-all').click(function(){
        $('select[name="auth[]"]').multiSelect('select_all');
        return false;
    });
    $('#deselect-all').click(function(){
        $('select[name="auth[]"]').multiSelect('deselect_all');
        return false;
    });
});
</script>
