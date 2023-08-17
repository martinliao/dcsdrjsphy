<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save2;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6">
        <label class="control-label">年度</label>
        <input class="form-control" name="year" placeholder="" value="<?=set_value('year', $form['year']); ?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期代碼</label>
        <input class="form-control" name="class_no" placeholder="" value="<?=set_value('class_no', $form['class_no']); ?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期名稱</label>
        <input class="form-control" name="class_name" placeholder="" value="<?=set_value('class_name', $form['class_name']); ?>" disabled>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">期別</label>
        <input class="form-control" name="term" placeholder="" value="<?=set_value('term', $form['term']); ?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">可報名人數</label>
        <input class="form-control" name="no_persons" placeholder="" value="<?=set_value('no_persons', $form['no_persons']); ?>" disabled>
    </div>
    <div class="form-group col-xs-6 <?=form_error('teacher')?'has-error':'';?>">
        <label class="control-label">原來講師(或助教)</label>
        <?php
            echo form_dropdown('teacher', $choices['teacher'], set_value('teacher', ''), 'class="form-control" id="teacher"');
        ?>
        <?=form_error('teacher'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('new_teacher_id')?'has-error':'';?>">
        <label class="control-label">新講師(或助教)</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showTeach('new_teacher_id')" value="查詢">
        <input type="hidden" id="new_teacher_id" name="new_teacher_id" class="btn btn-primary" value="">
        <input class="form-control" id="new_teacher_name" name="new_teacher_name" placeholder="" value="" readonly>
        <?=form_error('new_teacher_id'); ?>
    </div>
    <div class="form-group col-xs-6 <?=form_error('new_teacher_title_id')?'has-error':'';?>">
        <label class="control-label">職稱</label>
        <input class="form-control" id="new_teacher_title_name" name="new_teacher_title_name" placeholder="" value="" readonly>
        <?=form_error('new_teacher_title_id'); ?>
    </div>
</form>

<script type="text/javascript">
    function checkSave(){
        if (document.all.teacher.value==""){
            alert("請先選擇要變更的講師");
            document.all.teacher.focus();    
            return false;    
        }

        if (document.all.new_teacher_id.value==""){
            alert("請選擇新講師");   
            return false;    
        }
          
        if(confirm('此動作將會影響現有資料的正確性,是否確定繼續執行?')){
            obj = document.getElementById("data-form");
            obj.submit();
        } else {
            return false;
        }
    }

    function showTeach(field){
      var tmp = document.getElementById('teacher').value;
      if (tmp!="")
      {
        old_id=document.getElementById("teacher").value;
        o_array = old_id.split('::');
        if(o_array[1]=='Y'){
            y = 1;
        }
        else{
            y = 2;
        }

        myW=window.open('../../../../co_course_teacher_2.php?field='+field+'&type='+y,'show_teach','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=640');
        myW.focus();
      }
      else
      {
        alert("請選擇講師");
      }
    }

    function selTeachOK(x){
        //alert(x);
      var tmpObj = document.getElementById(x).value;
      if (x=="new_teacher_id"){
        var obj1 = document.all.new_teacher_name;
        var obj2 = document.all.new_teacher_id;
        var obj3 = document.all.new_teacher_title_name;  
      }

      obj1.value = "";
      obj2.value = "";
      obj3.value = "";
      var tmpSet = tmpObj.split(",,");
      for(i=0; i<(tmpSet.length); i++){
        var ss = tmpSet[i].split("::");
        if (ss[0]!="")
        {
          if (obj1.value==""){
            obj1.value = obj1.value + ss[1];
            obj2.value = obj2.value + ss[0];
            obj3.value = obj3.value + ss[2];
          }
          else{
            obj1.value = obj1.value + "," + ss[1];
            obj2.value = obj2.value + "," + ss[0];
            obj3.value = obj3.value + "," + ss[2];
          }
        }
      }
      tmpObj = "";
    }   
</script>