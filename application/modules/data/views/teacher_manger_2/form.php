<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save2;?>" enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group <?=form_error('image')?'has-error':'';?>">
        <label class="control-label">照片<font style="color:red;">(大小限制 300 * 400)</font></label>
        <div class="image-block">
            <a class="close" style="<?=strlen($form['image'])==0?'display: none;':'';?>" onclick="deleteImage(this)"><i class="fa fa-times-circle"></i></a>
            <div onclick="selectImage(this)">
                <?php if (strlen($form['image']) > 0) { ?>
                <?=img(array('src'=>$form['image_thumb_src'], 'class'=>'img-rounded'));?>
                <?php } else { ?>
                <i class="fa fa-plus fa-2x"></i>
                <?php } ?>
            </div>
            <input type="hidden" name="image" value="<?=$form['image'];?>" style="width: 0px;">
            <input type="file" name="upload" style="display: none;" onchange="changeImage(this);" accept=".png,.jpg" readonly>
        </div>
        <!-- <p class="help-block">。</p> -->
        <?=form_error('image'); ?>
    </div>

    <div class="form-group required col-xs-6 <?=form_error('idno')?'has-error':'';?>">
        <label class="control-label">身分證號</label>
        <?php if ($page_name == 'add') { ?>
        <?php $readonly = (isset($filter['teacher_type'])) ? 'readonly' : '' ; ?>
        <input class="form-control" name="idno" placeholder="" value="<?=set_value('idno', $form['idno']); ?>" <?=$readonly?>>
        <?php } else { ?>
        <input class="form-control" name="idno" placeholder="" value="<?=$form['idno'];?>" >
        <?php }?>
        <?=form_error('idno'); ?>
    </div>
    
    <div class="form-group col-xs-6 <?=form_error('old_idno')?'has-error':'';?>">
        <label class="control-label">原始身分證號</label>
        <?php if ($page_name == 'add') { ?>
        <?php $readonly = 'readonly'; ?>
        <input class="form-control" name="old_idno" placeholder="" value="<?=set_value('old_idno', $form['old_idno']); ?>" <?=$readonly?>>
        <?php } else { ?>
        <input class="form-control" name="old_idno" placeholder="" value="<?=$form['old_idno'];?>" readonly>
        <?php }?>
        <?=form_error('old_idno'); ?>
    </div>


    <div class="form-group col-xs-6 <?=form_error('rpno')?'has-error':'';?>">
        <label class="control-label">居留證號</label>
        <?php if ($page_name == 'add') { ?>
        <input class="form-control" name="rpno" placeholder="" value="<?=set_value('rpno', $form['rpno']); ?>">
        <?php } else { ?>
        <input class="form-control" name="rpno" placeholder="" value="<?=$form['rpno'];?>">
        <?php }?>
        <?=form_error('rpno'); ?>
    </div>

    <div class="form-group required col-xs-6  <?=form_error('identity_type')?'has-error':'';?>">
        <label class="control-label">身分別</label>
        <?php if ($page_name == 'add') { ?>
        <?php
            echo form_dropdown('identity_type', $choices['identity_type'], set_value('identity_type', $form['identity_type']), ' class="form-control" id="identity_type" onchange="getAutoID(this.value);"');
        ?>
        <?php } else { ?>
        <?php
            echo form_dropdown('identity_type', $choices['identity_type'], set_value('identity_type', $form['identity_type']), ' class="form-control" id="identity_type" onfocus="defaultIndex=this.selectedIndex" onchange="this.selectedIndex=defaultIndex" readonly' );
        ?>
        <?php }?>
        <?=form_error('identity_type'); ?>
    </div>

    <div class="form-group required col-xs-6 <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">姓名</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>" readonly>
        <?=form_error('name'); ?>
    </div>

    <div class="form-group required col-xs-6 <?=form_error('birthday')?'has-error':'';?>">
        <label class="control-label">生日(YYYY-MM-DD)</label>
        <div class="input-daterange input-group" id="datepicker">
            <input type="text" class="form-control datepicker" name="birthday" id="test1"  value="<?=set_value('birthday', substr($form['birthday'], 0, -8));?>" readonly/>
            <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i></span>
        </div>
        <?=form_error('birthday'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('another_name')?'has-error':'';?>">
        <label class="control-label">別名</label>
        <input class="form-control" name="another_name" placeholder="" value="<?=set_value('another_name', $form['another_name']); ?>" readonly>
        <?=form_error('another_name'); ?>
    </div>

    <div class="form-group required col-xs-6 <?=form_error('institution')?'has-error':'';?>">
        <label class="control-label">任職機關</label>
        <input class="form-control" name="institution" placeholder="" value="<?=set_value('institution', $form['institution']); ?>" readonly>
        <?=form_error('institution'); ?>
    </div>

    <div class="form-group required col-xs-6 <?=form_error('job_title')?'has-error':'';?>">
        <label class="control-label">職稱</label>
        <input class="form-control" name="job_title" placeholder="" value="<?=set_value('job_title', $form['job_title']); ?>" readonly>
        <?=form_error('job_title'); ?>
    </div>

    <div class="form-group required col-xs-6 ">
        <label class="control-label">學歷</label>
        <?php
            echo form_dropdown('education', $choices['education'], set_value('education', $form['education']), 'class="form-control"');
        ?>
        <?=form_error('education'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('major')?'has-error':'';?>">
        <label class="control-label">畢業學校科系</label>
        <input class="form-control" name="major" placeholder="" value="<?=set_value('major', $form['major']); ?>" readonly>
        <?=form_error('major'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('zipcode') || form_error('route')?'has-error':'';?>">
        <label class="control-label">聯絡地址 ( 縣市 / 區域 / 郵遞區號 )</label>
        <a href="http://www.post.gov.tw/post/internet/Postal/index.jsp?ID=208" target="_blank">郵遞區號查詢</a>
        <font color="red">(限3、5或6碼)</font>
        <div id="twzipcode">
        </div>
        <input class="form-control" name="route"  value="<?=set_value('route', $form['route']); ?>" readonly>
        <?=form_error('zipcode'); ?>
        <?=form_error('route'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('c_tel')?'has-error':'';?>">
        <label class="control-label">公司電話1<font style="color:red">(範例：02-23456789#123)</font></label>
        <input class="form-control" name="c_tel" placeholder="" value="<?=set_value('c_tel', $form['c_tel']); ?>" readonly>
        <?=form_error('c_tel'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('c_tel2')?'has-error':'';?>">
        <label class="control-label">公司電話2</label>
        <input class="form-control" name="c_tel2" placeholder="" value="<?=set_value('c_tel2', $form['c_tel2']); ?>" readonly>
        <?=form_error('c_tel2'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('h_tel')?'has-error':'';?>">
        <label class="control-label">家用電話1</label>
        <input class="form-control" name="h_tel" placeholder="" value="<?=set_value('h_tel', $form['h_tel']); ?>" readonly>
        <?=form_error('h_tel'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('h_tel2')?'has-error':'';?>">
        <label class="control-label">家用電話2</label>
        <input class="form-control" name="h_tel2" placeholder="" value="<?=set_value('h_tel2', $form['h_tel2']); ?>" readonly>
        <?=form_error('h_tel2'); ?>
    </div>


    <div class="form-group col-xs-6 <?=form_error('mobile')?'has-error':'';?>">
        <label class="control-label">手機</label>
        <input class="form-control" name="mobile" placeholder="" value="<?=set_value('mobile', $form['mobile']); ?>" readonly>
        <?=form_error('mobile'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('fax')?'has-error':'';?>">
        <label class="control-label">傳真</label>
        <input class="form-control" name="fax" placeholder="" value="<?=set_value('fax', $form['fax']); ?>" readonly>
        <?=form_error('fax'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('email')?'has-error':'';?>">
        <label class="control-label">E-Mail</label>
        <input class="form-control" name="email" placeholder="email@example.com" value="<?=set_value('email', $form['email']); ?>" readonly>
        <?=form_error('email'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('email2')?'has-error':'';?>">
        <label class="control-label">E-Mail2</label>
        <input class="form-control" name="email2" placeholder="email@example.com" value="<?=set_value('email2', $form['email2']); ?>" readonly>
        <?=form_error('email2'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('bank_code')?'has-error':'';?>">
        <label class="control-label">【銀行(郵局)分行】</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="show_bank()" value="查詢" readonly>
        <input type="hidden" class="form-control" name="bank_code" id="aBank" placeholder="" value="<?=set_value('bank_code', $form['bank_code']); ?>" readonly>
        <input class="form-control" name="bank_code_name" id="aBankName" disabled value="<?=set_value('bank_code_name', $form['bank_code_name']); ?>" readonly>
        <?=form_error('bank_code'); ?>
    </div>
    <!--<?=form_error('bank_account')?'has-error':'';?>-->
    <div class="form-group required col-xs-6  <?=form_error('bank_account')?'has-error':'';?>">
        <label class="control-label">帳號</label>
        <input class="form-control" name="bank_account" id="aAccount" placeholder="" value="<?=set_value('bank_account', $form['bank_account']); ?>" readonly>
        <!--<?=form_error('bank_account','<p class="text-danger">帳號請填入數字，不可有[-]等其他字元','</p>'); ?>-->
        <?=form_error('validate_bank')?>
    </div>

    <div class="form-group required col-xs-6 <?=form_error('account_name')?'has-error':'';?>">
        <label class="control-label">帳戶名稱</label>
        <input class="form-control" name="account_name" placeholder="" value="<?=set_value('account_name', $form['account_name']); ?>" readonly>
        <?=form_error('account_name'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('contact_person')?'has-error':'';?>">
        <label class="control-label">聯絡人</label>
        <input class="form-control" name="contact_person" placeholder="" value="<?=set_value('contact_person', $form['contact_person']); ?>" readonly>
        <?=form_error('contact_person'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('contact_tel')?'has-error':'';?>">
        <label class="control-label">聯絡人電話</label>
        <input class="form-control" name="contact_tel" placeholder="" value="<?=set_value('contact_tel', $form['contact_tel']); ?>" readonly>
        <?=form_error('contact_tel'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('experience')?'has-error':'';?>">
        <label class="control-label">經歷<font style="color:red;">(300個字元)</font></label>
        <textarea class="form-control" name="experience" style="height: 110px;" readonly><?=set_value('experience', $form['experience']); ?></textarea>
        <?=form_error('experience'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('demand')?'has-error':'';?>">
        <label class="control-label">特殊需求<font style="color:red;">(300個字元)</font></label>
        <textarea class="form-control" name="demand" style="height: 110px;" readonly><?=set_value('demand', $form['demand']); ?></textarea>
        <?=form_error('demand'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('introduction')?'has-error':'';?>">
        <label class="control-label">講座介紹</label>
        <textarea class="form-control" name="introduction" style="height: 110px;" readonly><?=set_value('introduction', $form['introduction']); ?></textarea>
        <?=form_error('introduction'); ?>
    </div>

    <div class="form-group col-xs-6 required ">
        <label class="control-label">講師或助教</label>
        <?php
            // $disabled = !isset($filter['teacher_type']) ? 'disabled' : '';
            echo form_dropdown('teacher_type', $choices['teacher_type'], set_value('teacher_type', $form['teacher_type']), 'class="form-control" ');
        ?>
        <?=form_error('teacher_type'); ?>
    </div>

    <div class="form-group required col-xs-6 ">
        <label class="control-label">聘請類別</label>
        <?php
            echo form_dropdown('hire_type', $choices['hire_type'], set_value('hire_type', $form['hire_type']), 'class="form-control"');
        ?>
        <?=form_error('hire_type'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">講座可授課程</label>
        <select class="form-control" name="listCourse" id="listCourse" size="10" c>
            <?php if(!empty($form['course_lis']) && $page_name != 'add') { ?>
            <?php foreach ($form['course_lis'] as $key => $row) { ?>
                <option value="<?=$key;?>"><?=$key;?>-<?=$row;?></option>
            <?php } ?>
            <?php } ?>

        </select>
        <?=form_error('course'); ?>
    </div>
    <div id="hidden_list">
        <input type="hidden" name="course" id="addCourse" value="<?=($page_name!='add')?$form['course']:'';?>">
    </div>
</form>

<script src="<?=HTTP_PLUGIN;?>jquery-twzipcode/jquery.twzipcode.min.js"></script>
<script>

$(function() {

    $('#twzipcode').twzipcode({
        detect: false,
        zipcodeSel: '<?=set_value("zipcode", substr($form["zipcode"], 0, 3));?>',
        css: ['county', 'district', 'zipcode'],
        readonly: false,
    });

    <?php if($page_name != 'add'){?>
    $('.zipcode').val('<?=$form["zipcode"];?>');
    <?php } ?>

});

function checkSave(){
    var obj = document.getElementById('data-form');
    var zip_code = $('.zipcode').val();
    
    // if(zip_code.length != 5){
    //     alert('郵遞區號須為5碼');
    // } else {
        obj.submit();
    // }
}

function show_course(page_name, u_id){
    if(page_name == 'add'){
        var path = '../../../co_course_popup.php?type=A&u_id='+u_id;
    } else if(page_name == 'edit'){
        var path = '../../../../co_course_popup.php?type=A&u_id='+u_id;
    }
    var myW=window.open(path, 'selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
    myW.focus();
}

function selChk(objItemValue){
  objSel = document.all.listCourse;
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
  var obj = document.all.addCourse;
  var tmpSet = obj.value.split(",,");
  for(i=0; i<(tmpSet.length); i++){
    var ss = tmpSet[i].split("::");
    if (ss[0]!="")
    {
      if (selChk(ss[0])){
        var varItem = new Option(ss[0] + "-" + ss[1], ss[0]);
        var objSel = document.all.listCourse;
        objSel.options.add(varItem);
      }
    }
  }
  obj.value = "";
  getOption();
}

function delCS(){
  objSel = document.all.listCourse;
  if (objSel.selectedIndex != -1){
    objSel.options.remove(objSel.selectedIndex);
  }
  getOption();
}

function getOption(){
    var all = "";
    $("#listCourse option").each(function () {
        var val = $(this).val();
        var node = val + ",";
        all += node;
    });
    //all = all.substring(0, all.length-1);
    $("#addCourse").val(all);
}

$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });
});

function show_bank(){

  var myW=window.open('<?=$show_bank;?>','selRequire','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}

// 填寫姓名後自動帶入帳戶名稱
$("input[name=name]").blur(function(){
    if(!hasIllegalChar($("input[name=name]")[0].value)){
        if ($("input[name=account_name]")[0].value == ""){
            $("input[name=account_name]")[0].value = $("input[name=name]")[0].value;
        }
    }
});

function hasIllegalChar(str){
    return new RegExp(".*?script[^&gt;]*?.*?(&lt;\/.*?script.*?&gt;)*", "ig").test(str);
}

function getAutoID(val){

    if(val == 1){
        return false;
    }
    // alert(val);
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': val,
    }
    var url = '<?=base_url('data/teacher_manger/ajax/getAutoID');?>';

    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        $("input[name=idno]").val(response.autoid);
                    }
                }

    });
}

</script>