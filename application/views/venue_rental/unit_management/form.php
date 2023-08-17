<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>" enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group required col-xs-6 <?=form_error('app_name')?'has-error':'';?>">
        <label class="control-label">單位名稱</label>
        <input class="form-control" name="app_name" placeholder="" value="<?=set_value('app_name', $form['app_name']); ?>">
    </div>

    <div class="form-group required col-xs-6 <?=form_error('contact_name')?'has-error':'';?>">
        <label class="control-label">聯絡人姓名</label>
        <input class="form-control" name="contact_name" placeholder="" value="<?=set_value('contact_name', $form['contact_name']); ?>">
    </div>

    <div class="form-group required col-xs-6 <?=form_error('tel')?'has-error':'';?>">
        <label class="control-label">電話</label>
        <input class="form-control" name="tel" placeholder="" value="<?=set_value('tel', $form['tel']); ?>">
    </div>

    <div class="form-group col-xs-6 <?=form_error('fax')?'has-error':'';?>">
        <label class="control-label">傳真</label>
        <input class="form-control" name="fax" placeholder="" value="<?=set_value('fax', $form['fax']); ?>">
    </div>

    <div class="form-group col-xs-6 <?=form_error('email')?'has-error':'';?>">
        <label class="control-label">E-Mail</label>
        <input class="form-control" name="email" placeholder="email@example.com" value="<?=set_value('email', $form['email']); ?>">
    </div>

 
    <div class="form-group col-xs-6 <?=form_error('is_public')?'has-error':'';?>">
            <label class="control-label">是否為市府單位</label><br>
            <input type="checkbox" name="is_public" value="Y"  <?=set_checkbox('is_public', 'Y', $form['is_public']=='Y');?> >
    </div>


    <div class="form-group col-xs-12 <?=form_error('memo')?'has-error':'';?>">
    <div class="form-group col-xs-2 <?=form_error('zone')?'has-error':'';?>" >
            <label class="control-label">郵遞區號</label>
            <input class="form-control" name="zone"  value="<?=set_value('zone', $form['zone']); ?>">
    </div>
    <div class="form-group col-xs-10 <?=form_error('addr')?'has-error':'';?>" >
            <label class="control-label">地址</label>
            <input class="form-control" name="addr"  value="<?=set_value('addr', $form['addr']); ?>">
    </div>
    </div>

    <div class="form-group col-xs-12 <?=form_error('memo')?'has-error':'';?>">
        <label class="control-label">備註</label>
        <textarea class="form-control" name="memo"><?=set_value('memo', $form['memo']); ?></textarea>
    </div>

</form>

<script src="<?=HTTP_PLUGIN;?>jquery-twzipcode/jquery.twzipcode.min.js"></script>
<script>

$(function() {

    $('#twzipcode').twzipcode({
        detect: false,
        zipcodeSel: '<?=set_value("zone", $form["zone"]);?>',
        css: ['county', 'district', 'zipcode'],
        readonly: false,
    });

});


</script>