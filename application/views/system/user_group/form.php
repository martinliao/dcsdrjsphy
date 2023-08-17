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

    <div class="form-group required <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">群組名稱</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>">
        <?=form_error('name'); ?>
    </div>
    <div class="form-group <?=form_error('telephone')?'has-error':'';?>">
        <label class="control-label">描述</label>
        <textarea class="form-control" rows="3" name="description"><?=set_value('description', $form['description']); ?></textarea>
        <?=form_error('description');?>
    </div>
    <div class="form-group <?=form_error('auth')?'has-error':'';?>">
        <label class="control-label">群組權限</label>
        <p>
            <button type="button" class="btn btn-link" id="select-all">選擇全部</button> /
            <button type="button" class="btn btn-link" id="deselect-all">移除全部</button>
        </p>
        <?php
            echo form_dropdown('auth[]', $choices['menu'], $form['auth'], 'style="height: 200px;" multiple');
        ?>
        <?=form_error('auth[]');?>
    </div>
    <div class="form-group required <?=form_error('enable')?'has-error':'';?>">
        <label class="control-label">是否啟用</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input type="radio" value="1" name="enable" <?=set_radio('enable', '1', $form['enable']==1);?>>
                    <span style="color: green;">是　</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input type="radio" value="0" name="enable" <?=set_radio('enable', '0', $form['enable']==0);?>>
                    <span style="color: red;">否　</span>
                </label>
            </div>
            <?=form_error("enable");?>
        </div>
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
