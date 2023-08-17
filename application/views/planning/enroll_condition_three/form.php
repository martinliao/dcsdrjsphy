<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 <?=form_error('limit_name')?'has-error':'';?>">
        <label class="control-label">名稱</label>
        <input class="form-control" name="limit_name" placeholder="" value="<?=set_value('limit_name', $form['limit_name']); ?>">
        <?=form_error('limit_name'); ?>
    </div>
    <div class="form-group col-xs-6 ">
        <label class="control-label">條件</label>
        <?php
            echo form_dropdown('condition', $choices['condition'], set_value('condition', $form['condition']), 'class="form-control"');
        ?>
        <?=form_error('condition'); ?>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">限制</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="show_class('<?=$page_name?>')" value="選擇班期">
        <input type="button" class="btn btn-xs btn-danger" onclick="delCS()" value="刪除班期">
        <select class="form-control" name="listClass" id="listClass" size="10">
            <?php 
            	if($page_name == 'edit' && isset($form['class_list']) && !empty($form['class_list'])) { 
            		foreach ($form['class_list'] as $key => $value) {
            			echo '<option value="'.$value['class_no'].'">'.$value['class_no'].'-'.$value['class_name'].'</option>';
            		}
            	}
            ?>
        </select>
        <?=form_error('class'); ?>
    </div>
    
    <div id="hidden_list">
        <input type="hidden" name="class" id="addClass" value="<?=$form['class'];?>">
    </div>
</form>

<script type="text/javascript">
function show_class(page_name, u_id){
	if(page_name == 'add'){
	    var path = '../../../co_class_popup.php?field=addClass&mode=2';
	} else if(page_name == 'edit'){
	    var path = '../../../../co_class_popup.php?field=addClass&mode=2';
	}
	var myW=window.open(path, 'selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
	myW.focus();
}

function selChk(objItemValue){
  objSel = document.all.listClass;
  var isExit = true;
  for (var i=0; i<objSel.options.length; i++) {
    if (objSel.options[i].value == objItemValue) {
      isExit = false;
      break;
    }
  }
  return isExit;
}

function selOK(){
  var obj = document.all.addClass;
  var tmpSet = obj.value.split(",,");
  for(i=0; i<(tmpSet.length); i++){
    var ss = tmpSet[i].split("::");
    if (ss[0]!="")
    {
      if (selChk(ss[0])){
        var varItem = new Option(ss[0] + "-" + ss[1], ss[0]);
        var objSel = document.all.listClass;
        objSel.options.add(varItem);
      }
    }
  }
  obj.value = "";
  getOption();
}

function delCS(){
  objSel = document.all.listClass;
  if (objSel.selectedIndex != -1){
    objSel.options.remove(objSel.selectedIndex);
  }
  getOption();
}

function getOption(){
    var all = "";
    $("#listClass option").each(function () {
        var val = $(this).val();
        var node = val + ",";
        all += node;
    });
    //all = all.substring(0, all.length-1);
    $("#addClass").val(all);
}
</script>