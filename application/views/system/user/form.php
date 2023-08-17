<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />


    <div class="form-group required <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">稱呼</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>">
        <?=form_error('name'); ?>
    </div>
    <div class="form-group required <?=form_error('username')?'has-error':'';?>">
        <label class="control-label">帳號</label>
        <?php if ($page_name == 'add') { ?>
        <input class="form-control" name="username" placeholder="" value="<?=set_value('username', $form['username']); ?>">
        <?php } else { ?>
        <input class="form-control" name="username" placeholder="" value="<?=$form['username'];?>" disabled>
        <?php }?>
        <?=form_error('username'); ?>
    </div>
    <div class="form-group <?=($page_name=='add')?'required':'';?> <?=form_error('password')?'has-error':'';?>">
        <label class="control-label">密碼</label>
        <input class="form-control" type="password" name="password" placeholder="" value="<?=set_value('password'); ?>">
        <?=form_error('password'); ?>
    </div>
    <div class="form-group <?=($page_name=='add')?'required':'';?> <?=form_error('passconf')?'has-error':'';?>">
        <label class="control-label">確認密碼</label>
        <input class="form-control" type="password" name="passconf" placeholder="" value="<?=set_value('passconf'); ?>">
        <?=form_error('passconf'); ?>
    </div>
    <?php if($page_name == 'add') {?>
    <div class="form-group <?=($page_name=='add')?'required':'';?> <?=form_error('idno')?'has-error':'';?>">
        <label class="control-label">身分證字號</label>
        <input class="form-control" type="text" name="idno" placeholder="" value="<?=set_value('idno'); ?>">
        <?=form_error('idno'); ?>
    </div>
    <?php } else if($page_name == 'edit') {?>
    <div class="form-group">
        <label class="control-label">身分證字號</label>
        <input class="form-control" type="text" name="idno" placeholder="" value="<?=$form['idno']; ?>" disabled>
    </div>
    <?php } ?>
    <div class="form-group required <?=form_error('email')?'has-error':'';?>">
        <label class="control-label">E-Mail</label>
        <input class="form-control" name="email" placeholder="email@example.com" value="<?=set_value('email', $form['email']); ?>">
        <?=form_error('email'); ?>
    </div>
    <div class="form-group <?=form_error('telephone')?'has-error':'';?>">
        <label class="control-label">聯絡電話</label>
        <input class="form-control" name="telephone" placeholder="0987654321" value="<?=set_value('telephone', $form['telephone']); ?>">
        <?=form_error('telephone');?>
    </div>
    <div class="form-group required <?=form_error('')?'has-error':'';?>">
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
