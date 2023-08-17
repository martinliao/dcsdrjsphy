<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6 required <?=form_error('group_id')?'has-error':'';?>">
        <label class="control-label">群組代碼</label>
        <input class="form-control" name="group_id" placeholder="" value="<?=set_value('group_id', $form['group_id']); ?>">
        <?=form_error('group_id'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('group_name')?'has-error':'';?>">
        <label class="control-label">群組名稱</label>
        <input class="form-control" name="group_name" placeholder="" value="<?=set_value('group_name', $form['group_name']); ?>">
        <?=form_error('group_name'); ?>
    </div>
    <div class="form-group col-xs-6 required <?=form_error('limited')?'has-error':'';?>">
        <label class="control-label">限制參訓數</label>
        <input class="form-control" name="limited" placeholder="" value="<?=set_value('limited', $form['limited']); ?>">
        <?=form_error('limited'); ?>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期</label>
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
	    var path = '../../../co_class_popup.php?field=addClass&mode=1';
	} else if(page_name == 'edit'){
	    var path = '../../../../co_class_popup.php?field=addClass&mode=1';
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