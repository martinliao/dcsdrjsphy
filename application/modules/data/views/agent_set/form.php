<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('item_id')?'has-error':'';?>">
        <label class="control-label">承辦人</label>
        <?php if($page_name=='add'){ ?>
        <input type="button" class="btn btn-xs btn-primary" onclick="showBureau('<?=$link_bureau?>')" value="查詢">
        <input type='hidden' name='addClass' id='addClass' value='' disabled="disabled">
        <?php } ?>
        <input type="hidden" id="item_id" name="item_id" value="<?=set_value('item_id', $form['item_id']); ?>">
        <input class="form-control" id="undertaker" name="undertaker" disabled="disabled" value="<?=set_value('undertaker', $form['undertaker']); ?>">
        <?=form_error('undertaker'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=($page_name=='add')?'required':'';?> <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">代理人</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>">
        <?=form_error('name'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('ext1')?'has-error':'';?>">
        <label class="control-label">承辦人分機</label>
        <input class="form-control" name="ext1" placeholder="" value="<?=set_value('ext1', $form['ext1']); ?>">
        <?=form_error('ext1'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('ext2')?'has-error':'';?>">
        <label class="control-label">代理人分機</label>
        <input class="form-control" name="ext2" placeholder="" value="<?=set_value('ext2', $form['ext2']); ?>">
        <?=form_error('ext2'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('remark')?'has-error':'';?>">
        <label class="control-label">備註</label>
        <input class="form-control" name="remark" placeholder="" value="<?=set_value('remark', $form['remark']); ?>">
        <?=form_error('remark'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('')?'has-error':'';?>">
        <label class="control-label">是否啟用</label>
        <div>
            <?php
                $enable_1 = $form['enable']==1? TRUE : FALSE;
                $enable_2 = $form['enable']==0? TRUE : FALSE;
            ?>
            <div class="radio-inline">
                <label>
                    <input id="enable_1" type="radio" value="1" name="enable" <?=set_radio('enable', '1', $enable_1);?>>
                    <span style="color: green;">是　</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="enable_0" type="radio" value="0" name="enable" <?=set_radio('enable', '0', $enable_2);?>>
                    <span style="color: red;">否　</span>
                </label>
            </div>
            <?=form_error("enable");?>
        </div>
    </div>

</form>

<script>
function showBureau(url){
  window.open(url,'selbeaurau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
}

function selWorkerOK(){
  var obj = document.all.addClass;
  var tmp = obj.value.split("::");
  document.all.item_id.value = tmp[0];
  document.all.undertaker.value = tmp[1];
  obj.value = "";
}


</script>