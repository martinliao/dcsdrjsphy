<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">姓名</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>">
        <?=form_error('name'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('co_usrnick')?'has-error':'';?>">
        <label class="control-label">暱稱</label>
        <input class="form-control" name="co_usrnick" placeholder="" value="<?=set_value('co_usrnick', $form['co_usrnick']); ?>">
        <?=form_error('co_usrnick'); ?>
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">性別</label>
        <?php
            echo form_dropdown('gender', $choices['gender'], set_value('gender', $form['gender']), 'class="form-control"');
        ?>
        <?=form_error('gender'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('idno')?'has-error':'';?>">
        <label class="control-label">身分證字號</label>
        <?php if ($page_name == 'add') { ?>
        <input class="form-control" name="idno" placeholder="" value="<?=set_value('idno', $form['idno']); ?>">
        <?php } else { ?>
        <input class="form-control" name="idno" placeholder="" value="<?=$form['idno'];?>" disabled>
        <?php }?>
        <?=form_error('idno'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('birthday')?'has-error':'';?>">
        <label class="control-label">生日</label>
        <div class="input-daterange input-group">
            <input type="text" class="form-control datepicker"  id="datepicker1" name="birthday"  value="<?=set_value('birthday', $form['birthday']);?>"/>
            <span class="input-group-addon" style="cursor: pointer;"  id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
        </div>
        <?=form_error('birthday'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('cellphone')?'has-error':'';?>">
        <label class="control-label">手機號碼</label>
        <input class="form-control" name="cellphone" placeholder="" value="<?=set_value('cellphone', $form['cellphone']); ?>">
        <?=form_error('cellphone'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('email')?'has-error':'';?>">
        <label class="control-label">E-Mail</label>
        <input class="form-control" name="email" placeholder="email@example.com" value="<?=set_value('email', $form['email']); ?>">
        <?=form_error('email'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('office_email')?'has-error':'';?>">
        <label class="control-label">公司E-Mail</label>
        <input class="form-control" name="office_email" placeholder="office_email@example.com" value="<?=set_value('office_email', $form['office_email']); ?>">
        <?=form_error('office_email'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('office_tel')?'has-error':'';?>">
        <label class="control-label">公司電話</label>
        <input class="form-control" name="office_tel" placeholder="" value="<?=set_value('office_tel', $form['office_tel']); ?>">
        <?=form_error('office_tel'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('office_fax')?'has-error':'';?>">
        <label class="control-label">公司傳真</label>
        <input class="form-control" name="office_fax" placeholder="" value="<?=set_value('office_fax', $form['office_fax']); ?>">
        <?=form_error('office_fax'); ?>
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">局處名稱</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showBureau('<?=$page_name?>')" value="查詢">
        <input type="hidden" id="bureau_id" name="bureau_id" class="btn btn-primary" value="<?=set_value('bureau_id', $form['bureau_id']); ?>">
        <input class="form-control" id="bureau_name" name="bureau_name" placeholder="" value="<?=set_value('bureau_name', $form['bureau_name']); ?>" readonly>
        <?=form_error('bureau_name'); ?>
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">職稱</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showJobTitle('<?=$page_name?>')" value="查詢">
        <input type="hidden" id="job_title" name="job_title" class="btn btn-primary" value="<?=set_value('job_title', $form['job_title']); ?>">
        <input class="form-control" id="job_title_name" name="job_title_name" placeholder="" value="<?=set_value('job_title_name', $form['job_title_name']); ?>" readonly>

        <?=form_error('job_title_name'); ?>
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

<script type="text/javascript">
    function showBureau(page_name){
        if(page_name == 'add'){
            var path = '../../../co_bureau.php?field1=bureau_id&field2=bureau_name&mode=2';
        } else if(page_name == 'edit'){
            var path = '../../../../co_bureau.php?field1=bureau_id&field2=bureau_name&mode=2';
        }

        var myW=window.open(path, 'selBureau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
        myW.focus();
    }

    function showJobTitle(page_name){
        if(page_name == 'add'){
            var path = '../../../co_jobtitle.php?field1=job_title&field2=job_title_name&mode=2';
        } else if(page_name == 'edit'){
            var path = '../../../../co_jobtitle.php?field1=job_title&field2=job_title_name&mode=2';
        }

        var myW=window.open(path, 'selBureau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
        myW.focus();
    }


$(document).ready(function() {
    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
        $("#datepicker1").focus();
    });
});
</script>