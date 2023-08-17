<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('old_item_id')?'has-error':'';?>">
        <label class="control-label">舊銀行代碼</label>
        <input class="form-control" name="old_old_item_id" placeholder="" disabled value="<?=set_value('old_item_id', $form['old_item_id']); ?>">
        <?=form_error('old_item_id'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('item_id')?'has-error':'';?>">
        <label class="control-label">銀行代碼</label>
        <input class="form-control" name="item_id" placeholder="" value="<?=set_value('item_id', $form['item_id']); ?>">
        <?=form_error('item_id'); ?>
    </div>

</form>
