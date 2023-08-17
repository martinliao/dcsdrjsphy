<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('bureau_id')?'has-error':'';?>">
        <label class="control-label">局處代碼</label>
        <input class="form-control" name="bureau_id" placeholder="" value="<?=set_value('bureau_id', $form['bureau_id']); ?>" <?=($page_name=='edit' || $page_name=='transfer')?'readonly':'';?>>
        <?=form_error('bureau_id'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">局處名稱</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>" <?=($page_name=='transfer')?'disabled':'';?>>
        <?=form_error('name'); ?>
    </div>
    <div class="form-group col-xs-6 required">
        <label class="control-label">機關層級</label>
        <?php
            $bureau_level_status = ($page_name=='transfer')?'disabled':'';
            echo form_dropdown('bureau_level', $choices['bureau_level'], set_value('bureau_level', $form['bureau_level']), "class='form-control' id='bureau_level' $bureau_level_status");
        ?>
        <?=form_error('bureau_level'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('parent_id')?'has-error':'';?>">
        <label class="control-label">主管機關代碼</label>
        <input class="form-control" id="parent_id" name="parent_id" placeholder="" value="<?=set_value('parent_id', $form['parent_id']); ?>" <?=($page_name=='transfer')?'disabled':'readonly';?>>
        <?=form_error('parent_id'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('parent_name')?'has-error':'';?>">
        <label class="control-label">主管機關名稱</label>
        <?php if($page_name != 'transfer'){ ?>
        <input type="button" class="btn btn-xs btn-primary" onclick="showBureau('<?=$page_name?>')" value="查詢">
        <?php } ?>
        <input class="form-control" id="parent_name" name="parent_name" placeholder="" value="<?=set_value('parent_name', $form['parent_name']); ?>" disabled>
        <?=form_error('parent_name'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('effective_date')?'has-error':'';?>">
        <label class="control-label">機關生效日期</label>
        <div class="input-daterange input-group">
            <input type="text" class="form-control datepicker"  id="datepicker1" name="effective_date"  value="<?=set_value('effective_date', $form['effective_date']);?>" <?=($page_name=='transfer')?'disabled':'';?> />
            <span class="input-group-addon" style="cursor: pointer;"  id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
        </div>
        <?=form_error('effective_date'); ?>
    </div>
    <div class="form-group col-xs-6 <?=($page_name=='transfer')?'required':'';?> <?=form_error('abolish_date')?'has-error':'';?>">
        <label class="control-label">機關裁撤日期</label>
        <div class="input-daterange input-group" >
            <input type="text" class="form-control datepicker" id="test1" name="abolish_date"  value="<?=set_value('abolish_date', $form['abolish_date']);?>"/>
            <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i></span>
        </div>
        <?=form_error('abolish_date'); ?>
    </div>
    <?php if($page_name == 'edit' || $page_name == 'transfer'){ ?>
    <div class="form-group col-xs-6 <?=($page_name=='transfer')?'required':'';?>">
        <label class="control-label">裁撤註記</label>
        <?php
            echo form_dropdown('del_flag', $choices['del_flag'], set_value('del_flag', $form['del_flag']), 'class="form-control"');
        ?>
        <?=form_error('del_flag'); ?>
    </div>
    <?php } ?>
    <div class="form-group col-xs-6 required">
        <label class="control-label">機關身分</label>
        <?php
            $position_status = ($page_name == 'transfer')?'readonly':'';
            echo form_dropdown('position', $choices['position'], set_value('position', $form['position']), "class='form-control' $position_status");
        ?>
        <?=form_error('position'); ?>
    </div>

    <?php if($page_name == 'transfer'){ ?>
    <div class="form-group col-xs-6 required <?=form_error('new_bureau_id')?'has-error':'';?>">
        <label class="control-label">新局處代碼</label>
        <input class="form-control" name="new_bureau_id" placeholder="" value="<?=set_value('new_bureau_id', $form['new_bureau_id']); ?>">
        <?=form_error('new_bureau_id'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('new_name')?'has-error':'';?>">
        <label class="control-label">新局處名稱</label>
        <input class="form-control" name="new_name" placeholder="" value="<?=set_value('new_name', $form['new_name']); ?>">
        <?=form_error('new_name'); ?>
    </div>
    <div class="form-group col-xs-6 required">
        <label class="control-label">新機關層級</label>
        <?php
            echo form_dropdown('new_bureau_level', $choices['new_bureau_level'], set_value('new_bureau_level', $form['new_bureau_level']), "class='form-control' id='new_bureau_level'");
        ?>
        <?=form_error('new_bureau_level'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('new_parent_id')?'has-error':'';?>">
        <label class="control-label">新主管機關代碼</label>
        <input class="form-control" id="new_parent_id" name="new_parent_id" placeholder="" value="<?=set_value('new_parent_id', $form['new_parent_id']); ?>" readonly>
        <?=form_error('new_parent_id'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('new_parent_name')?'has-error':'';?>">
        <label class="control-label">新主管機關名稱</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showBureau('<?=$page_name?>')" value="查詢">
        <input class="form-control" id="new_parent_name" name="new_parent_name" placeholder="" value="<?=set_value('new_parent_name', $form['new_parent_name']); ?>" disabled>
        <?=form_error('new_parent_name'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('new_effective_date')?'has-error':'';?>">
        <label class="control-label">新機關生效日期</label>
        <div class="input-daterange input-group" >
            <input type="text" class="form-control datepicker" id="datepicker3" name="new_effective_date"  value="<?=set_value('new_effective_date', $form['new_effective_date']);?>"/>
            <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i
                                        class="fa fa-calendar"></i></span>
        </div>
        <?=form_error('new_effective_date'); ?>
    </div>
    <?php } ?>
</form>

<script type="text/javascript">
function showBureau(page_name){
    if(page_name == 'add'){
        if (document.getElementById("bureau_level").value==""){
            alert("請選機關層級");
            document.getElementById("bureau_level").focus();
            return false;
        }
        var path = '../../../co_bureau.php?field1=parent_id&field2=parent_name&level=';
        y=parseInt(document.getElementById("bureau_level").value)-1;
    } else if(page_name == 'edit') {
        if (document.getElementById("bureau_level").value==""){
            alert("請選機關層級");
            document.getElementById("bureau_level").focus();
            return false;
        }
        var path = '../../../../co_bureau.php?field1=parent_id&field2=parent_name&level=';
        y=parseInt(document.getElementById("bureau_level").value)-1;
    } else if(page_name == 'transfer') {
        if (document.getElementById("new_bureau_level").value==""){
            alert("請選新機關層級");
            document.getElementById("new_bureau_level").focus();
            return false;
        }
        var path = '../../../../co_bureau.php?field1=new_parent_id&field2=new_parent_name&level=';
        y=parseInt(document.getElementById("new_bureau_level").value)-1;
    }

    var myW=window.open(path+y, 'selBureau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
    myW.focus();
}

$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });
  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });
  $("#datepicker3").datepicker();
  $('#datepicker4').click(function(){
    $("#datepicker3").focus();
  });
});
</script>