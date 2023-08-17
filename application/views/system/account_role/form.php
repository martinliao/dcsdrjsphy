<link href="<?=HTTP_PLUGIN;?>lou-multi-select/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type='hidden' name='worker' id="worker" value="" disabled >
    <?php if($page_name == 'add'){ ?>
    <div class="form-group col-xs-12">
        <input type="button" class="btn btn-xs btn-primary" onclick="showWorker()" value="選取">
    </div>
    <?php } ?>
    <div class="form-group col-xs-6">
        <label class="control-label">局處名稱</label>
        <input class="form-control" name="b_name" id="b_name" readonly="readonly" value="<?=set_value('b_name', $form['b_name']); ?>">
    </div>
    <div class="form-group col-xs-6 required <?=form_error('username')?'has-error':'';?>">
        <label class="control-label">帳號</label>
        <input class="form-control" name="username" id="username" readonly="readonly" value="<?=set_value('username', $form['username']); ?>">
        <?=form_error('username'); ?>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">身分證</label>
        <input class="form-control" name="idno" id="idno" readonly="readonly" value="<?=set_value('idno', $form['idno']); ?>">
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">姓名</label>
        <input class="form-control" name="name" id="name" readonly="readonly" value="<?=set_value('username', $form['username']); ?>">
    </div>
    <div class="form-group col-xs-6 <?=form_error('group_id')?'has-error':'';?>">
        <label class="control-label">角色</label>
        <?php
            $choices['group'] = array(''=>'請選擇角色') + $choices['group'];
            echo form_dropdown('group_id', $choices['group'], $form['group_id'], 'class="form-control"');
        ?>
        <?=form_error('group_id');?>
    </div>
</form>

<script>

$(document).ready(function() {
    var yesfunc = function() {
    };

    <?php if(isset($error_msg)){ ?>
    bk_confirm_2(3, "<?=$error_msg;?>", 'center', yesfunc);
    <?php } ?>
});

function showWorker(){
  var myW=window.open('<?=$co_worker;?>', 'selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
  myW.focus();
}

function selWorkerOK(){
  x="worker";
  var tmpObj = document.getElementById(x);
  var tmpObjVal = document.getElementById(x).value;

  if (x=="worker"){
    var obj1 = document.all.b_name ;
    var obj2 = document.all.username;
    var obj4 = document.all.idno;
    var obj5 = document.all.name;
  }

  obj1.value = "";
  obj2.value = "";
  obj4.value = "";
  obj5.value = "";

    var ss = tmpObjVal.split("::");



        obj1.value = obj1.value + ss[0];
        obj2.value = obj2.value + ss[1];
        obj4.value = obj4.value + ss[2];
        obj5.value = obj5.value + ss[3];

  return true;
}
</script>
