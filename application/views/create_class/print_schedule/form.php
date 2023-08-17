<link href="<?=HTTP_PLUGIN;?>bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">
<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post"   enctype="multipart/form-data">
    <div class="form-group required <?=form_error("answers")?'has-error':'';?>">
        <textarea class="form-control" id="answers" name="answers"><?=set_value("answers", $form['answers']); ?></textarea>
        <?=form_error("answers"); ?>
    </div>
</form>

<script src="<?=HTTP_PLUGIN;?>ckeditor_4.14.0_full/ckeditor/ckeditor.js"></script>
<script>
$(function() {
    CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ',lineheight' : 'lineheight');
    CKEDITOR.replace('answers', {
        language: 'zh',
        uiColor: '#AADBCB',
    });

});
</script>
