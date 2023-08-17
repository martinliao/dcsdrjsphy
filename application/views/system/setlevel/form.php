<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group required <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">名稱</label>
        <input class="form-control" name="name" value="<?=set_value('name', $form['name']);?>">
        <?=form_error('name'); ?>
    </div>
    <div class="form-group required<?=form_error('link')?'has-error':'';?>">
        <label class="control-label">Link</label>
        <input class="form-control" name="link" placeholder="" value="<?=set_value('link', $form['link']); ?>">
        <?=form_error('link'); ?>
    </div>
    <div class="form-group <?=form_error('sort_order')?'has-error':'';?>">
        <label class="control-label">排序</label>
        <input class="form-control" name="sort_order" placeholder="" value="<?=set_value('sort_order', $form['sort_order']); ?>">
        <?=form_error('sort_order'); ?>
    </div>

</form>
<script src="<?=HTTP_PLUGIN;?>jquery-validation/jquery.validate.min.js"></script>
<script src="<?=HTTP_PLUGIN;?>jquery-validation/localization/messages_zh_TW.min.js"></script>
<script>
$(function() {
    $('#tab_data a:first').tab('show');
    $('#tab_language a:first').tab('show');

    var $form = $('#data-form');

    $form.validate({
        //debug: true,
        errorElement: 'p',
        errorClass: 'has-error',
        validClass: "myValidClass",
        rules: {
            name: {required: true},
        },

        highlight: function(element, errorClass, validClass){
            $(element).parent().addClass(errorClass).removeClass(validClass)
        },
        unhighlight: function(element, errorClass, validClass){
            $(element).parent().addClass(validClass).removeClass(errorClass)
        },

    });
});
</script>
