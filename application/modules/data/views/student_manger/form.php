<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="row_id" value="<?=$row_id;?>" />
    
    <?php if($page_name == 'add'){ ?>
    <p style="color: red">學員個人資料-新增</p>
    <?php } else if($page_name == 'edit'){ ?>
    <p style="color: red">學員個人資料-更新</p>
    <?php } ?>
    <div class="form-group col-xs-6 required <?=form_error('name')?'has-error':'';?>">
        <label class="control-label">姓名</label>
        <input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>">
        <!-- <?=form_error('name'); ?> -->
    </div>

    <div class="form-group col-xs-6 <?=form_error('en_name')?'has-error':'';?>">
        <label class="control-label">英文姓名</label>
        <input class="form-control" name="en_name" placeholder="" value="<?=set_value('en_name', $form['en_name']); ?>">
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">性別</label>
        <?php
            echo form_dropdown('gender', $choices['gender'], set_value('gender', $form['gender']), 'class="form-control"');
        ?>
        <!-- <?=form_error('gender'); ?> -->
    </div>
    <?php //外國人學員可以修改身分證字號 --- Alex Chiou 2021-06-29 ?>
    <div class="form-group col-xs-6 <?=form_error('idno')?'has-error':'';?>">
        <label class="control-label">身分證字號</label>
        <?php if ($page_name == 'add') { ?>
        <input class="form-control" name="idno" placeholder="" value="<?=set_value('idno', $form['idno']); ?>" readonly>
        <?php } else {
                    preg_match('/[A-Z][1-2|8-9][0-9]{8}/', $form['idno'],$matches);
                    $match_count = sizeof($matches);
                    //var_dump($match_count);die();
                    if (sizeof($matches) > 0) { ?>
                   <input class="form-control" name="idno" placeholder="" value="<?=$form['idno'];?>" disabled>
            <?php } else {?>
                    <input class="form-control" name="idno" placeholder="" value="<?=$form['idno'];?>">
            <?php } }?>
        <!-- <?=form_error('idno'); ?> -->
    </div>
    <?php //外國人學員可以修改身分證字號 ---END?>
    <div class="form-group col-xs-6 required <?=form_error('birthday')?'has-error':'';?>">
        <label class="control-label">出生日期</label>
        <div class="input-daterange input-group">
            <input type="text" class="form-control datepicker"  id="datepicker1" name="birthday"  value="<?=set_value('birthday', $form['birthday']);?>"/>
            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
        </div>
        <!-- <?=form_error('birthday'); ?> -->
    </div>

    <div class="form-group col-xs-6 required <?=form_error('office_email')?'has-error':'';?>">
        <label class="control-label">公司Email</label>
        <input class="form-control" name="office_email" placeholder="office_email@example.com" value="<?=set_value('office_email', $form['office_email']); ?>">
        <!-- <?=form_error('office_email'); ?> -->
    </div>

    <div class="form-group col-xs-6 <?=form_error('email')?'has-error':'';?>">
        <label class="control-label">私人Email</label>
        <input class="form-control" name="email" placeholder="email@example.com" value="<?=set_value('email', $form['email']); ?>">
        <!-- <?=form_error('email'); ?> -->
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">局處名稱</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showBureau('<?=$page_name?>')" value="查詢">
        <input type="hidden" id="bureau_id" name="bureau_id" class="btn btn-primary" value="<?=set_value('bureau_id', $form['bureau_id']); ?>">
        <input class="form-control" id="bureau_name" name="bureau_name" placeholder="" value="<?=set_value('bureau_name', $form['bureau_name']); ?>" readonly>
        <!-- <?=form_error('bureau_name'); ?> -->
    </div>

    <div class="form-group col-xs-6 <?=form_error('out_gov_name')?'has-error':'';?>" style="margin-bottom: 17px;" >
        <label class="control-label">私立機關名稱</label>
        <input type="button" class="btn btn-xs btn-primary" value="" style="background: transparent;border-color: transparent">
        <input class="form-control" name="out_gov_name" placeholder="" value="<?=set_value('out_gov_name', $form['out_gov_name']); ?>">
        <!-- <?=form_error('out_gov_name'); ?> -->
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">學歷</label>
        <?php
            echo form_dropdown('education', $choices['education'], set_value('education', $form['education']), 'class="form-control"');
        ?>
        <!-- <?=form_error('education'); ?> -->
    </div>

    <div class="form-group col-xs-6 required <?=form_error('co_empdb_poftel')?'has-error':'';?>">
        <label class="control-label">公司電話</label>
        <input class="form-control" name="co_empdb_poftel" placeholder="" value="<?=set_value('co_empdb_poftel', $form['co_empdb_poftel']); ?>">
        <?=form_error('co_empdb_poftel'); ?>
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">主管級別</label>
        <?php
            echo form_dropdown('supervisor_id', $choices['supervisor'], set_value('supervisor_id', $form['supervisor_id']), 'class="form-control"');
        ?>
        <!-- <?=form_error('supervisor_id'); ?> -->
    </div>

    <div class="form-group col-xs-6 <?=form_error('cellphone')?'has-error':'';?>">
        <label class="control-label">手機號碼</label>
        <input class="form-control" name="cellphone" placeholder="" value="<?=set_value('cellphone', $form['cellphone']); ?>">
        <!-- <?=form_error('cellphone'); ?> -->
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">現支官職等</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showjoblevel('<?=$page_name?>')" value="查詢">
        <input type="hidden" id="job_level_id" name="job_level_id" class="btn btn-primary" value="<?=set_value('job_level_id', $form['job_level_id']); ?>">
        <input class="form-control" id="job_level_name" name="job_level_name" placeholder="" value="<?=set_value('job_level_name', $form['job_level_name']); ?>" disabled>
        <!-- <?=form_error('job_level_name'); ?> -->
    </div>

    <div class="form-group col-xs-6 <?=form_error('office_fax')?'has-error':'';?>" style="margin-bottom: 17px;">
        <label class="control-label">公司傳真</label>
        <input type="button" class="btn btn-xs btn-primary" value="" style="background: transparent;border-color: transparent">
        <input class="form-control" name="office_fax" placeholder="" value="<?=set_value('office_fax', $form['office_fax']); ?>">
        <!-- <?=form_error('office_fax'); ?> -->
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">現職區分</label>
        <?php
            echo form_dropdown('job_distinguish', $choices['job_distinguish'], set_value('job_distinguish', $form['job_distinguish']), 'class="form-control"');
        ?>
        <!-- <?=form_error('job_distinguish'); ?> -->
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">職稱</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showJobTitle('<?=$page_name?>')" value="查詢">
        <input type="hidden" id="job_title" name="job_title" class="btn btn-primary" value="<?=set_value('job_title', $form['job_title']); ?>">
        <input class="form-control" id="job_title_name" name="job_title_name" placeholder="" value="<?=set_value('job_title_name', $form['job_title_name']); ?>" readonly>

        <!-- <?=form_error('job_title_name'); ?> -->
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">離職</label>
        <?php
            echo form_dropdown('departure', $choices['departure'], set_value('departure', $form['departure']), 'class="form-control"');
        ?>
        <!-- <?=form_error('departure'); ?> -->
        (已調離本府，系統不再介接)
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">退休</label>
        <?php
            echo form_dropdown('retirement', $choices['retirement'], set_value('retirement', $form['retirement']), 'class="form-control"');
        ?>
        <!-- <?=form_error('retirement'); ?> -->
        (介接人事資訊系統已退休人員)
    </div>
    <!-- 20211111 Roger 不顯示退休狀態前的空楁start -->
        <div class="form-group col-xs-6 required">
        </div>
        <div class="form-group col-xs-6">
            <label class="control-label">不顯示退休狀態</label>
            <?php
                echo form_dropdown('showretirement', $choices['showretirement'], set_value('showretirement', $form['showretirement']), 'class="form-control"');
            ?>
            <!-- <?=form_error('showretirement'); ?> -->
        </div>
        <!-- 20211111 Roger 不顯示退休狀態前的空楁end -->

    <p style="color: red">※若該學員服務於私立機關，請輸入私立機關名稱。</p>
    <p style="color: red">※若同時輸入私立機關名稱與局處名稱，將以私立機關名稱為主。</p>
    <p style="color: red">※星號欄位資料係介接WebHR人力資源管理系統。</p>
</form>

<script type="text/javascript">
    function showBureau(page_name){
        if(page_name == 'add'){
            var path = '../../co_bureau.php?field1=bureau_id&field2=bureau_name&mode=2';
        } else if(page_name == 'edit'){
            var path = '../../../../co_bureau.php?field1=bureau_id&field2=bureau_name&mode=2';
        }

        var myW=window.open(path, 'selBureau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
        myW.focus();
    }

    function showjoblevel(page_name){
        if(page_name == 'add'){
            var path = '../../co_job_level.php?field1=job_level_id&field2=job_level_name&mode=0';
        } else if(page_name == 'edit'){
            var path = '../../../../co_job_level.php?field1=job_level_id&field2=job_level_name&mode=0';
        }

        var myW=window.open(path, 'seljoblevel','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
        myW.focus();
    }

    function showJobTitle(page_name){
        if(page_name == 'add'){
            var path = '../../co_jobtitle.php?field1=job_title&field2=job_title_name&mode=2';
        } else if(page_name == 'edit'){
            var path = '../../../../co_jobtitle.php?field1=job_title&field2=job_title_name&mode=2';
        }

        var myW=window.open(path, 'selBureau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
        myW.focus();
    }

$(document).ready(function() {
  $("#datepicker1").datepicker({
    yearRange: "-100:+1"
  });
  $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });
});
</script>