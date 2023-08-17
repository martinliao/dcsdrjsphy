<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" name='editForm' role="form" method="post" action="<?=$link_save_file;?>" enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="all_set2Term" value="">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="year" id="year" value="<?=$detail_data['year'];?>">
    <input type="hidden" name="class_no" id="class_no" value="<?=$detail_data['class_no'];?>">
    <input type="hidden" name="class_name" id="class_name" value="<?=$detail_data['class_name'];?>">
    <input type="hidden" name="course_code" id="course_code" value="<?=$detail_data['course_code'];?>">
    <input type="hidden" name="id" id="id" value="<?=$detail_data['id'];?>">
    <input type="hidden" name="path" id="path" value="<?=$form['file_path'];?>">
    <div class="form-group required col-xs-6 <?=form_error('title')?'has-error':'';?>">
        <label class="control-label">講義名稱</label>
        <input class="form-control" name="title" id="title" <?=($page_name == 'edit')?'readonly':'';?> value="<?=set_value('title', $form['title']); ?>">
        <?=form_error('title'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('term')?'has-error':'';?>">
        <label class="control-label">期別</label>
        <?php
            if ($page_name == 'add') {
                echo form_dropdown('term', $choices['term'], set_value('term', $form['term']), 'class="form-control" ');
            } else {
                echo form_dropdown('term', $choices['term'], set_value('term', $form['term']), 'class="form-control" disabled');
            }
        ?>
        <?=form_error('term'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('start_date')?'has-error':'';?>">
        <label class="control-label">開放日期</label>
        <div class="input-daterange input-group" id="datepicker">
            <input type="text" class="form-control datepicker" name="start_date" id="test1"  value="<?=set_value('start_date', $form['start_date']);?>"/>
            <span class="input-group-addon" style="cursor: pointer;" id="test2"><i class="fa fa-calendar"></i></span>
        </div>
        <?=form_error('start_date'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('end_date')?'has-error':'';?>">
        <label class="control-label">結束日期</label>
        <div class="input-daterange input-group" id="datepicker">
            <input type="text" class="form-control datepicker" name="end_date" id="datepicker1"  value="<?=set_value('end_date', $form['end_date']);?>"/>
            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
        </div>
        <?=form_error('end_date'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('is_open')?'has-error':'';?>">
        <label class="control-label">開放下載</label>
        <div>
            <?php
                $is_open_1 = $form['is_open']=='Y'? TRUE : FALSE;
                $is_open_2 = $form['is_open']=='N'? TRUE : FALSE;
            ?>
            <div class="radio-inline">
                <label>
                    <input id="is_open_1" type="radio" value="Y" name="is_open" <?=($form['is_open']=='Y')? 'checked' : '';?> >
                    <span >是　</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="is_open_0" type="radio" value="N" name="is_open" <?=($form['is_open']=='N')? 'checked' : '';?> >
                    <span >否　</span>
                </label>
            </div>
            <?=form_error("is_open");?>
        </div>
    </div>

    <div class="form-group col-xs-6 <?=form_error('open_to_all')?'has-error':'';?>">
        <label class="control-label">開放給本班所有期別的人下載</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="open_to_all_1" type="radio" value="Y" name="open_to_all" <?=($form['open_to_all']=='Y')? 'checked' : '';?>>
                    <span >是　</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="open_to_all_0" type="radio" value="N" name="open_to_all" <?=($form['open_to_all']=='N')? 'checked' : '';?>>
                    <span >否　</span>
                </label>
            </div>
            <?=form_error("open_to_all");?>
        </div>
    </div>

    <div class="form-group col-xs-6 ">
        <label class="control-label">上傳檔案</label>
        <?php  if ($form['file_path']=="") { ?>
        <input type="file" name="userfile" id="userfile" style='width:300px' accept=".odt,.ods,.odp,.docx,.xlsx,.pptx,.doc,.xls,.ppt,.zip,.rar,.jpg,.png,.gif,.pdf"/>
        <?php } else{ ?>
            <input type="hidden" name="ori_file_path" id="ori_file_path" value='<?php  echo $form['file_path'];?>'>
            <div id='upload' >

                &nbsp;<a style="cursor: pointer;" onclick="go_download('<?=$form['file_path'];?>')" ><?=preg_replace('/^.+[\\\\\\/]/', '', $form['file_path']);?></a>

            </div>
        <?php } ?>
        <p class="help-block">
            <font color="red">
            ※檔名：不允許－符號或空白<br>
            ※大小：限制64MB以下
            </font>
        </p>
    </div>

    <div class="form-group col-xs-6 <?=form_error('open_to_all')?'has-error':'';?>">
        <label class="control-label">指定給哪一期學員下載</label>
        <div>
            <?php
                $termsArr = ($set_to_terms != '' ? @explode(',',$set_to_terms) : array());
                foreach($choices['term'] as $row2){
                    echo "<input type='checkbox' name='set2Term' value='{$row2}' ".(in_array($row2, $termsArr) ? ' checked' : '').">&nbsp;" . $row2."&nbsp;" ;
                }
            ?>
        </div>
    </div>

    <div class="form-group col-xs-6 <?=form_error('approve')?'has-error':'';?>">
        <label class="control-label">爾後課程皆同意授權</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="approve_1" type="radio" value="1" name="approve" <?=($form['approve']=='1')? 'checked' : '';?>>
                    <span >YES　</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="approve_0" type="radio" value="0" name="approve" <?=($form['approve']=='0')? 'checked' : '';?>>
                    <span >NO　</span>
                </label>
            </div>
            <?=form_error("approve");?>
        </div>
    </div>
    <div class="form-group col-xs-6">

    <input type='button' name="btnSave" id="btnSave" value='儲存' onclick="btn_submit('<?=$page_name;?>');"  class='button'/>
    <input type='button' name='btnCancel' id='btnCancel' value='取消' onclick='go_to_detail()' class='button' />
    </div>
</form>

<form id="detail-form" role="form" method="post" >
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="year" id="year" value="<?=$detail_data['year'];?>">
    <input type="hidden" name="class_no" id="class_no" value="<?=$detail_data['class_no'];?>">
    <input type="hidden" name="class_name" id="class_name" value="<?=$detail_data['class_name'];?>">
    <input type="hidden" name="course_code" id="course_code" value="<?=$detail_data['course_code'];?>">
    <input type="hidden" name="id" id="id" value="<?=$detail_data['id'];?>">
    <input type="hidden" name="path" id="path" value="">
</form>

<script>
function go_to_detail() {
    obj=document.getElementById("detail-form");
    obj.action='<?=base_url('management/lecture_notes_assignments/detail')?>';
    obj.submit();
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
});

function btn_submit(mod){

    obj =document.getElementById('data-form');
    if(document.getElementById('title').value==''){
        alert("請輸入講義名稱!");
        return;
    }

    if(document.getElementById('userfile')){
        if(document.getElementById('userfile').value==''){
            alert("請上傳講義!");
            return;
        }
  }
  if (mod == 'add' && IsFileSizeOk()===false)
  {
    alert('檔案大小超過限制(64MB)，請先分割!!');
    return false;
  }

    // custom (b) by chiahua
    var s2t = '';
    obj2 = document.getElementsByName('set2Term');
    if(obj2.length > 0){
        for(i=0; i< obj2.length; i++){
            if(obj2[i].checked){
                s2t += obj2[i].value +',';
            }
        }
    }
    document.editForm.all_set2Term.value = (s2t.substring(0,s2t.length-1));
    // custom (e) by chiahua
    obj.submit();
}

function IsFileSizeOk() {
    var fileSize = document.getElementById("userfile").files.item(0).size;

    if (fileSize > 64000000) //size max is 64MB
    {
        return false;
    }
    else
    {
        return true;
    }
}

function go_download(url){
    obj =document.getElementById('detail-form');
    obj.path.value = url;
    obj.action='<?=base_url('management/lecture_notes_assignments/download')?>';
    obj.submit();
}

</script>