<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('item_id')?'has-error':'';?>">
        <label class="control-label">代碼</label>
        <input class="form-control" name="item_id" placeholder="" value="<?=set_value('item_id', $form['item_id']); ?>">
        <?=form_error('item_id'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=($page_name=='add')?'required':'';?> <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">名稱</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>">
        <?=form_error('name'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('price')?'has-error':'';?>">
        <label class="control-label">價錢</label>
        <input class="form-control" name="price" placeholder="" value="<?=set_value('price', $form['price']); ?>">
        <?=form_error('price'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('food_time')?'has-error':'';?>">
        <label class="control-label">時間</label>
        <input class="form-control" name="food_time" placeholder="" value="<?=set_value('food_time', $form['food_time']); ?>">
        <?=form_error('food_time'); ?>
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
