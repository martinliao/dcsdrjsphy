<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('limit_year')?'has-error':'';?>">
        <label class="control-label">限制年限</label>
        <input class="form-control" name="limit_year" placeholder="" value="<?=set_value('limit_year', $form['limit_year']); ?>">
        <?=form_error('limit_year'); ?>
    </div>
</form>
