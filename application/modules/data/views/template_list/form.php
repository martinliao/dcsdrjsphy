<link href="<?=HTTP_PLUGIN;?>bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">
<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>

<form id="data-form" role="form" method="post" action="<?=$link_save;?>"  enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="item_id" value="<?=set_value("item_id", $form['item_id']); ?>" />

    <div class="form-group col-xs-6 required <?=form_error("title")?'has-error':'';?>">
        <label class="control-label">範本名稱</label>
        <input class="form-control" name="title" placeholder="" value="<?=set_value("title", $form['title']); ?>">
        <?=form_error("title"); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('is_open')?'has-error':'';?>">
        <label class="control-label">是否開放</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="is_open_1" type="radio" value="1" name="is_open" <?=set_radio('is_open', '1', $form['is_open']==1);?>>
                    <span style="color: green;">是　</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="is_open_0" type="radio" value="0" name="is_open" <?=set_radio('is_open', '0', $form['is_open']==0);?>>
                    <span style="color: red;">否　</span>
                </label>
            </div>
            <?=form_error("is_open");?>
        </div>
    </div>

    <div class="form-group col-xs-12 required <?=form_error("content")?'has-error':'';?>">
        <label class="control-label">範本內容</label>
        <textarea class="form-control" id="content" name="content"><?=set_value("content", $form['content']); ?></textarea>
        <?=form_error("content"); ?>
    </div>


</form>

<script src="<?=HTTP_PLUGIN;?>ckeditor_4.14.0_full/ckeditor/ckeditor.js"></script>
<script>

$(function() {
    CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ',lineheight' : 'lineheight');
    CKEDITOR.replace('content', {
        language: 'zh',
        uiColor: '#AADBCB',
    });

});
function view(){
    console.log(1);
    let url = "<?=$send_email_plus?>";
    $("#data-form").attr('action', url);
    $("#data-form").attr("target", "_blank");
    $("#data-form").submit();
    url = "<?=$link_save?>";
    $("#data-form").attr('action', url);
    $("#data-form").attr("target", null);
}

</script>
