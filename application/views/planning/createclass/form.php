<?php
    $user_bureau_status = $user_bureau!='379680000A'?'disabled':'';

    if(!empty($form['map1']) || !empty($form['map2']) || !empty($form['map3']) || !empty($form['map4']) || !empty($form['map5']) || !empty($form['map6']) || !empty($form['map7']) || !empty($form['map8'])
    || !empty($form['map9']) || !empty($form['map10']) || !empty($form['map11'])){
        $fmap = 'Y';
    } else {
        $fmap = 'N';
    }
?>
<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>

<style type="text/css">
    .checkbox-inline input[type=checkbox], .radio-inline input[type=radio] {
        position: absolute;
        margin-top: 4px \9;
        margin-left: -14px;
    }
    /*.radio_margin{
        margin-left: -13px;
    }*/
</style>

<form id="data-form" role="form" method="post" action="<?=$link_save2;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <?php if($user_bureau != '379680000A'){ ?>
    <input type="hidden" id="type" value="A" disabled />
    <?php } ?>
    <?php if($page_name == 'add'){ ?>
    <p style="color: red">目前為新增模式</p>
    <?php } else if($page_name == 'edit'){ ?>
    <p style="color: red">目前為修改模式</p>
    <?php } ?>
    <div class="form-group col-xs-6">
        <label class="control-label">年度</label>
        <?php
            echo form_dropdown('year', $choices['year'], set_value('year', $form['year']), 'class="form-control" id="year" onchange="getDefaultYear()"');
        ?>
        <?=form_error('year'); ?>
    </div>

    <?php if($user_bureau == '379680000A'){ ?>
    <div class="form-group col-xs-6 required <?=form_error('type')?'has-error':'';?>">
        <label class="control-label">系列別代碼</label>
        <?php
            echo form_dropdown('type', $choices['type'], set_value('type', $form['type']), 'class="form-control" id="type" onchange="getSecond()"');
        ?>
        <?=form_error('type'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">次類別代碼</label>
        <select class="form-control" name='beaurau_id' id='beaurau_id'>
        <?php
            if(isset($beaurau_id) && !empty($beaurau_id)){
                for($i=0;$i<count($beaurau_id);$i++){
                    if($beaurau_id[$i]['item_id'] == $form['beaurau_id']){
                        echo '<option value="'.$beaurau_id[$i]['item_id'].'" selected="selected">'.$beaurau_id[$i]['name'].'</option>';
                    } else {
                        echo '<option value="'.$beaurau_id[$i]['item_id'].'">'.$beaurau_id[$i]['name'].'</option>';
                    }
                }
            }
        ?>
        </select>
    </div>
    <?php } else { ?>
    <?php if(preg_match("/^211.79.136.20[2,3,4,5,6]$/", $_SERVER["REMOTE_ADDR"]) || preg_match("/^163.29.35.[0-9]*[0-9]*[0-9]*$/", $_SERVER["REMOTE_ADDR"])) { ?> 
        <div class="form-group col-xs-6 required <?=form_error('type')?'has-error':'';?>">
            <label class="control-label">系列別代碼</label>
            <select class="form-control" id="type" name="type">
                <option value="A">
                    行政系列
                </option>
            </select>
            <?=form_error('type'); ?>
        </div>
        <?php if($page_name == 'add' && $is_edat){ ?>
            <div class="form-group col-xs-6">
                <label class="control-label">次類別代碼</label>
                <select class="form-control" name='beaurau_id' id='beaurau_id' readonly>
                <?php
                    if(isset($beaurau_id) && !empty($beaurau_id)){
                        $is_get = false;
                        
                        for($i=0;$i<count($beaurau_id);$i++){
                            if($beaurau_id[$i]['item_id'] == $user_bureau){
                                echo '<option value="'.$beaurau_id[$i]['item_id'].'" selected>'.$beaurau_id[$i]['name'].'</option>';
                                $is_get = true;
                            } 
                        }

                        if(!$is_get){
                            echo '<option value=""></option>';
                        }
                    }
                ?>
                </select>
            </div>
        <?php } else { ?>
            <div class="form-group col-xs-6">
                <label class="control-label">次類別代碼</label>
                <select class="form-control" name='beaurau_id' id='beaurau_id'>
                <?php
                    if(isset($beaurau_id) && !empty($beaurau_id)){
                        for($i=0;$i<count($beaurau_id);$i++){
                            if($beaurau_id[$i]['item_id'] == $form['beaurau_id']){
                                echo '<option value="'.$beaurau_id[$i]['item_id'].'" selected>'.$beaurau_id[$i]['name'].'</option>';
                            } 
                        }
                    }
                ?>
                </select>
            </div>
        <?php } ?>
    <?php } else { ?>
    <div class="form-group col-xs-6 required <?=form_error('type')?'has-error':'';?>">
        <label class="control-label">系列別代碼</label>
        <select class="form-control" id="type" name="type">
            <option value="A">
                行政系列
            </option>
        </select>
        <?=form_error('type'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">次類別代碼</label>
        <select class="form-control" name='beaurau_id' id='beaurau_id'>
        <?php
            if(isset($beaurau_id) && !empty($beaurau_id)){
                for($i=0;$i<count($beaurau_id);$i++){
                    if($beaurau_id[$i]['item_id'] == $form['beaurau_id']){
                        echo '<option value="'.$beaurau_id[$i]['item_id'].'" selected>'.$beaurau_id[$i]['name'].'</option>';
                    } 
                }
            }
        ?>
        </select>
    </div>
    <?php } ?>
    <?php } ?>

    <div class="form-group col-xs-6 <?=form_error('dev_type_name')?'has-error':'';?>">
        <label class="control-label">所屬局處名稱</label>
        <?php if($user_bureau == '379680000A'){ ?>
        <input type="button" class="btn btn-xs btn-primary" onclick="showBureau('dev_type','<?=$page_name?>')" value="查詢">
        <?php } ?>
        <input type="hidden" id="dev_type" name="dev_type" class="btn btn-primary" value="<?=set_value('dev_type', $form['dev_type']); ?>">
        <input class="form-control" id="dev_type_name" name="dev_type_name" placeholder="" value="<?=set_value('dev_type_name', $form['dev_type_name']); ?>" readonly>
        <?=form_error('dev_type_name'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('class_no')?'has-error':'';?>">
        <label class="control-label">班期代碼<font style="color: red">(班期名稱輸入後，請滑鼠在其他地方點一下，讓系統自動產生代碼)</font></label>
        <input class="form-control" id="class_no" name="class_no" placeholder="" value="<?=set_value('class_no', $form['class_no']); ?>" readonly>
        <?=form_error('class_no'); ?>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('ecpa_class_id')?'has-error':'';?>">
        <label class="control-label">終身學習類別代碼<a href="/base/admin/files/example_files/planning/ecpa.pdf" target="_blank">(查詢終身學習代碼表)</a></label>
        <input class="form-control" id="ecpa_class_id" name="ecpa_class_id" placeholder="" value="<?=set_value('ecpa_class_id', $form['ecpa_class_id']); ?>" onblur="showEcpaClassName(this.value)">
        <?=form_error('ecpa_class_id'); ?>
    </div>

    <?php //if($user_bureau != '379680000A' && ($page_name == 'edit' || isset($transfer))){ ?>
    <!--div class="form-group col-xs-6 required <?=form_error('class_name')?'has-error':'';?>">
        <label class="control-label">班期名稱</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="query_classname('<?=$page_name?>')" value="查詢已建立班期名稱">
        <input class="form-control" id="class_name" name="class_name" placeholder="" value="<?=set_value('class_name', $form['class_name']); ?>" readonly>
        <?=form_error('class_name'); ?>
    </div -->
    <?php //} else { ?>
    
    <div class="form-group col-xs-6 required <?=form_error('class_name')?'has-error':'';?>">
        <label class="control-label">班期名稱</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="query_classname('<?=$page_name?>')" value="查詢已建立班期名稱">
        <input class="form-control" id="class_name" name="class_name" placeholder="" onblur="getClassNO()" value="<?=set_value('class_name', $form['class_name']); ?>">
        <?=form_error('class_name'); ?>
    </div>
    <?php //} ?>

    <div class="form-group col-xs-6">
        <label class="control-label">課程類別名稱</label>
        <input type="button" class="btn btn-xs btn-primary" value="" style="background: transparent;border-color: transparent">
        <input class="form-control" id="ecpa_class_name" name="ecpa_class_name" placeholder="" value="<?=set_value('ecpa_class_name', $form['ecpa_class_name']); ?>" readonly>
    </div>

    <input id="class_name_shot" name="class_name_shot" type="hidden" value="<?=set_value('class_name_shot', $form['class_name_shot']); ?>">

    <div class="form-group col-xs-6 required">
        <label class="control-label">目標</label>
        <input class="form-control" id="obj" name="obj" placeholder="" value="<?=set_value('obj', $form['obj']); ?>">
        <?=form_error('obj'); ?>
    </div>

    <div class="form-group col-xs-6 required">
        <label class="control-label">對象</label>
        <input class="form-control" id="respondant" name="respondant" placeholder="" value="<?=set_value('respondant', $form['respondant']); ?>">
        <?=form_error('respondant'); ?>
    </div>
    
    <div class="form-group col-xs-6 <?=form_error('term')?'has-error':'';?>">
        <label class="control-label">期別<?=$page_name=='add'?'(為累計增加)':'(為單筆增加)';?></label>
        <input class="form-control" id="term" name="term" placeholder="" value="<?=set_value('term', $form['term']); ?>" <?=$page_name=='edit'?'readonly':'';?>>
        <?=form_error('term'); ?>
    </div>

    <!-- <div class="form-group col-xs-6 <?=form_error('base_term')?'has-error':'';?>">
        <label class="control-label">計畫初始期數</label>
        <input class="form-control" id="base_term" name="base_term" placeholder="" value="<?=set_value('base_term', $form['base_term']); ?>" <?=$form['class_status']=='2'?'disabled':'';?>>
        <?=form_error('base_term'); ?>
    </div> -->

    <?php if($is_edat) { ?>
        <input id="ht_class_type" name="ht_class_type" type="hidden" value="<?=set_value('ht_class_type', $form['ht_class_type']); ?>">
    <?php } else {?>
    <div class="form-group col-xs-6 required <?=form_error('ht_class_type')?'has-error':'';?>">
        <label class="control-label">鐘點費類別</label>
        <?php
            echo form_dropdown('ht_class_type', $choices['ht_class_type'], set_value('ht_class_type', $form['ht_class_type']), "class='form-control' $user_bureau_status");
        ?>
        <?=form_error('ht_class_type'); ?>
    </div>
    <?php } ?>

    <div class="form-group col-xs-6 required <?=form_error('no_persons')?'has-error':'';?>">
        <label class="control-label">本期人數</label>
        <input class="form-control" id="no_persons" name="no_persons" placeholder="" value="<?=set_value('no_persons', $form['no_persons']); ?>">
        <?=form_error('no_persons'); ?>
    </div>

    <input id="min_no_persons" name="min_no_persons" type="hidden" value="<?=set_value('min_no_persons', $form['min_no_persons']); ?>">
    <input id="max_no_persons" name="max_no_persons" type="hidden" value="<?=set_value('max_no_persons', $form['max_no_persons']); ?>">    

    <?php if($is_edat) { ?>
        <input id="classify" name="classify" type="hidden" value="<?=set_value('classify', $form['classify']); ?>">
    <?php } else {?>
    <div class="form-group col-xs-6">
        <label class="control-label">班期屬性</label>
        <?php
            echo form_dropdown('classify', $choices['classify'], set_value('classify', $form['classify']), "class='form-control' $user_bureau_status");
        ?>
        <?=form_error('classify'); ?>
    </div>
    <?php } ?>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練方式(一)住班或通勤</label>
        <?php
            echo form_dropdown('class_cate', $choices['class_cate'], set_value('class_cate', $form['class_cate']), 'class="form-control"');
        ?>
        <?=form_error('class_cate'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練方式(二)全天或半天</label>
        <?php
            echo form_dropdown('class_cate1', $choices['class_cate1'], set_value('class_cate1', $form['class_cate1']), 'class="form-control"');
        ?>
        <?=form_error('class_cate1'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練方式(三)連續或間斷</label>
        <?php
            echo form_dropdown('class_cate2', $choices['class_cate2'], set_value('class_cate2', $form['class_cate2']), 'class="form-control"');
        ?>
        <?=form_error('class_cate2'); ?>
    </div>

    <input id="range_week" name="range_week" type="hidden" value="<?=set_value('range_week', $form['range_week']); ?>">

    <div class="form-group col-xs-6 required <?=form_error('range')?'has-error':'';?>">
        <label class="control-label">訓練期程(小時)</label>
        <input class="form-control" id="range" name="range" placeholder="" value="<?=set_value('range', $form['range']); ?>">
        <?=form_error('range'); ?>
    </div>

    <?php if($is_edat) { ?>
        <input id="weights" name="weights" type="hidden" value="<?=set_value('weights', ($form['weights']>0)?$form['weights']:'1'); ?>" <?=($user_bureau!='379680000A')?'readonly':''?>">
    <?php } else {?>
    <div class="form-group col-xs-6 required <?=form_error('weights')?'has-error':'';?>">
        <label class="control-label">權重</label>
        <input class="form-control" id="weights" name="weights" placeholder="" value="<?=set_value('weights', ($form['weights']>0)?$form['weights']:'1'); ?>" <?=($user_bureau!='379680000A')?'readonly':''?>>
        <?=form_error('weights'); ?>
    </div>
    <?php } ?>

    <div class="form-group col-xs-6">
        <label class="control-label">同班不同期可重複受訓否</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="isappsameclass" type="radio" value="1" style="zoom:1.5;" name="isappsameclass" <?=$form['isappsameclass']=='1'?'checked':''?> <?=$user_bureau_status?>>
                    <span>YES</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="isappsameclass" type="radio" value="2" style="zoom:1.5;" name="isappsameclass" <?=(empty($form['isappsameclass']) || $form['isappsameclass']=='2')?'checked':''?> <?=$user_bureau_status?>>
                    <span>NO</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('req_beaurau_name')?'has-error':'';?>">
        <label class="control-label">承辦單位代碼</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showBureau('req_beaurau','<?=$page_name?>')" value="查詢">
        <input type="hidden" id="req_beaurau" name="req_beaurau" class="btn btn-primary" value="<?=set_value('req_beaurau', $form['req_beaurau']); ?>">
        <input class="form-control" id="req_beaurau_name" name="req_beaurau_name" placeholder="" value="<?=set_value('req_beaurau_name', $form['req_beaurau_name']); ?>" readonly>
        <?=form_error('req_beaurau_name'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('contactor')?'has-error':'';?>">
        <label class="control-label">承辦單位聯絡人</label>
        <input class="form-control" id="contactor" name="contactor" placeholder="" value="<?=set_value('contactor', $form['contactor']); ?>">
        <?=form_error('contactor'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('tel')?'has-error':'';?>">
        <label class="control-label">承辦單位聯絡電話</label>
        <input class="form-control" id="tel" name="tel" placeholder="" value="<?=set_value('tel', $form['tel']); ?>">
        <?=form_error('tel'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('contactor_email')?'has-error':'';?>">
        <label class="control-label">承辦單位聯絡人_EMAIL</label>
        <input class="form-control" id="contactor_email" name="contactor_email" placeholder="" value="<?=set_value('contactor_email', $form['contactor_email']); ?>">
        <?=form_error('contactor_email'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('room_name')?'has-error':'';?>">
        <label class="control-label">預約教室<font style="color:red">(選非公訓處上課者，處外場地須自行洽外機關預約)</font></label>
        <?php if($page_name == 'edit'){ ?>
        <input type="button" class="btn btn-xs btn-primary" onclick="bookingFun(<?=$form['seq_no']?>)" value="預約">
        <input type="button" class="btn btn-xs btn-primary" onclick="updateBookingFun(<?=$form['seq_no']?>)" value="更新">
        <?php } ?>
        <?php if(empty($form['room_code']) && $form['room_name'] == '非公訓處上課'){ ?>
        <input type="button" class="btn btn-xs btn-primary" id="enableRoom" onclick="notAtLocalFun(this.value)" value="本處上課">
        <?php } else if(empty($form['room_code'])){ ?>
        <input type="button" class="btn btn-xs btn-primary" id="enableRoom" onclick="notAtLocalFun(this.value)" value="非公訓處上課">
        <?php } ?>
        <input class="form-control" id="room_name" name="room_name" placeholder="" value="<?=set_value('room_name', $form['room_name']); ?>" readonly>
        <?=form_error('room_name'); ?>
    </div>

    <div class="form-group col-xs-6"><!-- mark 2021-06-04 加入unlock條件-->
        <label class="control-label">開課起日</label>
        <div class="input-group" id="start_date1">
            <input type="text" class="form-control <?=form_error('start_date1')?'has-error':'';?> datepicker" id="set_start_date1" name="start_date1" value="<?=set_value('start_date1', !empty($form['start_date1'])?date('Y-m-d',strtotime($form['start_date1'])):''); ?>" <?=($unlock_start_date1=='true')?'':'disabled'?> />
            <span class="input-group-addon" style="cursor: pointer;"><i class="fa fa-calendar"></i></span>
        </div>
    </div>

    <div class="form-group col-xs-6"><!-- mark 2021-06-04 加入unlock條件-->
        <label class="control-label">開課迄日</label>
        <div class="input-group" id="end_date1">
            <input type="text" class="form-control <?=form_error('end_date1')?'has-error':'';?> datepicker" id="set_end_date1" name="end_date1" value="<?=set_value('end_date1', !empty($form['end_date1'])?date('Y-m-d',strtotime($form['end_date1'])):''); ?>" <?=($unlock_end_day1=='true')?'':'disabled'?> />
            <span class="input-group-addon" style="cursor: pointer;" ><i class="fa fa-calendar"></i></span>
        </div>
    </div>
   
    <?php if($user_bureau == '379680000A'){ 
            if($is_edat) { ?>
    <div class="form-group col-xs-6">
        <label class="control-label">選員方式</label>
        <?php
            echo form_dropdown('app_type', $choices['app_type'], set_value('app_type', $form['app_type']), 'class="form-control"');
        ?>
        <?=form_error('app_type'); ?>
    </div>
    <?php } else {?>
        <input id="app_type" name="app_type" type="hidden" value=0>
    <?php 
    }
    } else {
        if(!$is_edat) { ?>
    <div class="form-group col-xs-6">
        <label class="control-label">報名方式</label>
        <input class="form-control" placeholder="" value="各單位報名" disabled>
    </div>
    <?php } 
    } ?>

    <div class="form-group col-xs-6">
        <label class="control-label">草案、確定計畫、新增計畫</label>
        <?php if($user_bureau == '379680000A'){ ?>
        <?php
            echo form_dropdown('class_status', $choices['class_status'], set_value('class_status', $form['class_status']), "class='form-control' $user_bureau_status");
        ?>
        <?php } else { ?>
            <select class='form-control' name="class_status">
                <option value="1">草案</option>
            </select>
        <?php } ?>
        <?=form_error('class_status'); ?>
    </div>

    <?php if($user_bureau == '379680000A'){?>
        <div class="form-group col-xs-6">
        <label class="control-label">季別</label>
        <?php
            echo form_dropdown('reason', $choices['reason'], set_value('reason', $form['reason']), "class='form-control' disabled");
        ?>
        <?=form_error('reason'); ?>
    </div>
    <?php } 
    else { ?>
        <input id="reason" type="hidden" name="reason" value="<?=set_value('reason', $form['reason']); ?>">
    <?php } ?>

    <?php if($user_bureau == '379680000A'){ 
            if($is_edat) { ?>
    <div class="form-group col-xs-6">
        <label class="control-label">參訓限制條件權限下放</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="is_start" type="radio" value="Y" name="is_start" style="zoom:1.5;" <?=$form['is_start']=='Y'?'checked':''?>>
                    <span>YES</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="is_start" type="radio" value="N" name="is_start" style="zoom:1.5;" <?=(empty($form['is_start']) || $form['is_start']=='N')?'checked':''?>>
                    <span>NO</span>
                </label>
            </div>
        </div>
        <?=form_error('is_start'); ?>
    </div>
    <?php } else {
            $startvaule = 'N';
            if(isset($form['is_start']) && $form['is_start']=='Y'){
                $startvaule = 'Y'; 
            }
    ?>
    <input id="is_start" type="hidden" value="Y" name="is_start" value="<?=$startvaule?>">
    <?php } 
    } ?>

    <div class="form-group col-xs-6 <?=form_error('segmemo')?'has-error':'';?>">
        <label class="control-label">辦班時段<font style="color: red">(最多200個中文字)</font></label>
        <textarea class="form-control" id="segmemo" name="segmemo" maxlength='200' cols='100' rows='2'><?=set_value('segmemo', $form['segmemo']); ?></textarea>
        <?=form_error('segmemo'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">教學方式</label>
        <div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way1" type="checkbox" value="Y" name="way1" style="zoom:1.5;" <?=set_checkbox('way2', 'Y', $form['way1']=='Y'?TRUE:FALSE);?>>
                    <span>1.講授</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way2" type="checkbox" value="Y" name="way2" style="zoom:1.5;" <?=set_checkbox('way2', 'Y', $form['way2']=='Y'?TRUE:FALSE);?>>
                    <span>2.實習</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way3" type="checkbox" value="Y" name="way3" style="zoom:1.5;" <?=set_checkbox('way3', 'Y', $form['way3']=='Y'?TRUE:FALSE);?>>
                    <span>3.研討</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way4" type="checkbox" value="Y" name="way4" style="zoom:1.5;" <?=set_checkbox('way4', 'Y', $form['way4']=='Y'?TRUE:FALSE);?>>
                    <span>4.習作</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way5" type="checkbox" value="Y" name="way5" style="zoom:1.5;" <?=set_checkbox('way5', 'Y', $form['way5']=='Y'?TRUE:FALSE);?>>
                    <span>5.討論</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way6" type="checkbox" value="Y" name="way6" style="zoom:1.5;" <?=set_checkbox('way6', 'Y', $form['way6']=='Y'?TRUE:FALSE);?>>
                    <span>6.座談</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way7" type="checkbox" value="Y" name="way7" style="zoom:1.5;" <?=set_checkbox('way7', 'Y', $form['way7']=='Y'?TRUE:FALSE);?>>
                    <span>7.演練</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way8" type="checkbox" value="Y" name="way8" style="zoom:1.5;" <?=set_checkbox('way8', 'Y', $form['way8']=='Y'?TRUE:FALSE);?>>
                    <span>8.說唱</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way9" type="checkbox" value="Y" name="way9" style="zoom:1.5;" <?=set_checkbox('way9', 'Y', $form['way9']=='Y'?TRUE:FALSE);?>>
                    <span>9.表演</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way10" type="checkbox" value="Y" name="way10" style="zoom:1.5;" <?=set_checkbox('way10', 'Y', $form['way10']=='Y'?TRUE:FALSE);?>>
                    <span>10.參觀活動</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way11" type="checkbox" value="Y" name="way11" style="zoom:1.5;" <?=set_checkbox('way11', 'Y', $form['way11']=='Y'?TRUE:FALSE);?>>
                    <span>11.案例討論</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way12" type="checkbox" value="Y" name="way12" style="zoom:1.5;" <?=set_checkbox('way12', 'Y', $form['way12']=='Y'?TRUE:FALSE);?>>
                    <span>12.角色扮演</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way13" type="checkbox" value="Y" name="way13" style="zoom:1.5;" <?=set_checkbox('way13', 'Y', $form['way13']=='Y'?TRUE:FALSE);?>>
                    <span>13.實地參觀</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way14" type="checkbox" value="Y" name="way14" style="zoom:1.5;" <?=set_checkbox('way14', 'Y', $form['way14']=='Y'?TRUE:FALSE);?>>
                    <span>14.模擬演練</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way15" type="checkbox" value="Y" name="way15" style="zoom:1.5;" <?=set_checkbox('way15', 'Y', $form['way15']=='Y'?TRUE:FALSE);?>>
                    <span>15.電腦實機</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="way16" type="checkbox" value="Y" name="way16" style="zoom:1.5;" <?=set_checkbox('way16', 'Y', $form['way16']=='Y'?TRUE:FALSE);?>>
                    <span>16.視聽教材</span>
                </label>
            </div>
            <div>
                <label class="control-label">17.其他</label>
                <input class="form-control" id="way17" name="way17" placeholder="" value="<?=set_value('way17', $form['way17']); ?>">
                <?=form_error('way17'); ?>
            </div>
        </div>
    </div>
    <?php if($user_bureau == '379680000A'){?>
        <div class="form-group col-xs-6">
            <label class="control-label">課程內容(舊資料)(僅供參考)</label>
            <textarea class="form-control" id="content" name="content" maxlength='400' cols='100' rows='4' disabled><?=set_value('content', $form['content']); ?></textarea>
            <?=form_error('content'); ?>
        </div>
    <?php } 
    else { ?>
        <input id="content" type="hidden" name="content" value="<?=set_value('content', $form['content']); ?>">
    <?php } ?>

    <div class="tab-pane col-xs-12" id="course_content">
        <label class="control-label">課程內容(非必填)<button type="button" class="btn btn-success btn-sm" onclick="addCourse()">新增課程</button></label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="90%">課目</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
            <?php
                if(isset($course_name) && !empty($course_name)){
                    for($i=0;$i<count($course_name);$i++){
                        echo '<tr>';
                        echo '<td>';
                        echo '<input class="form-control" name="course_name[]" placeholder="" value="'.$course_name[$i]['course_name'].'">';
                        echo '</td>';
                        /*
                        echo '<td>';
                        echo '<select class="form-control" name="material[]" id="material">';
                        echo '<option value="4" '.($course_name[$i]['material']=='4'?'selected':'').'>無</option>';
                        echo '<option value="0" '.($course_name[$i]['material']=='0'?'selected':'').'>實境錄製教材(單一主題)</option>';
                        echo '<option value="1" '.($course_name[$i]['material']=='1'?'selected':'').'>實境錄製教材(系列性主題)</option>';
                        echo '<option value="2" '.($course_name[$i]['material']=='2'?'selected':'').'>全動畫教材(貴局處無經費)</option>';
                        echo '<option value="3" '.($course_name[$i]['material']=='3'?'selected':'').'>全動畫教材(貴局處有經費)</option>';
                        echo '</select>';
                        echo '</td>';
                        */
                        echo '<td align="right">';
                        if(isset($course_name[$i]['material'])){
                            echo '<input name="material[]" type="hidden" value="'.$course_name[$i]['material'].'">';
                        }
                        echo '<button type="button" class="btn btn-danger btn-sm" id="remove_'.$i.'" onclick="removeItem(this, '.$i.')">刪除</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
            ?>
            </tbody>
        </table>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('is_assess')?'has-error':'';?>">
        <label class="control-label">考核班期</label>
        <?php
            echo form_dropdown('is_assess', $choices['is_assess'], set_value('is_assess', $form['is_assess']), "class='form-control' id='is_assess' $user_bureau_status");
        ?>
        <?=form_error('is_assess'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">考核方式</label>
        <div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type1" type="checkbox" value="1" name="type1" style="zoom:1.5;" <?=set_checkbox('type2', '1', $form['type1']=='1'?TRUE:FALSE);?>>
                    <span>測驗</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type2" type="checkbox" value="1" name="type2" style="zoom:1.5;" <?=set_checkbox('type2', '1', $form['type2']=='1'?TRUE:FALSE);?>>
                    <span>書面報告</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type3" type="checkbox" value="1" name="type3" style="zoom:1.5;" <?=set_checkbox('type3', '1', $form['type3']=='1'?TRUE:FALSE);?>>
                    <span>成果發表</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type4" type="checkbox" value="1" name="type4" style="zoom:1.5;" <?=set_checkbox('type4', '1', $form['type4']=='1'?TRUE:FALSE);?>>
                    <span>實作演練</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type5" type="checkbox" value="1" name="type5" style="zoom:1.5;" <?=set_checkbox('type5', '1', $form['type5']=='1'?TRUE:FALSE);?>>
                    <span>心得分享</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type6" type="checkbox" value="1" name="type6" style="zoom:1.5;" <?=set_checkbox('type6', '1', $form['type6']=='1'?TRUE:FALSE);?>>
                    <span>案例研討</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type7" type="checkbox" value="1" name="type7" style="zoom:1.5;" <?=set_checkbox('type7', '1', $form['type7']=='1'?TRUE:FALSE);?>>
                    <span>意見交流</span>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label">其他<font style="color: red">(請輸入其他考核方式)</font></label>
                <input class="form-control" id="type8" name="type8" placeholder="" value="<?=set_value('type8', $form['type8']); ?>">
                <?=form_error('type8'); ?>
            </div>
        </div>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('is_mixed')?'has-error':'';?>">
        <label class="control-label">混成班期<font style="color: red">選擇{是}，請新增線上課程</font></label>
        <?php
            echo form_dropdown('is_mixed', $choices['is_mixed'], set_value('is_mixed', $form['is_mixed']), 'class="form-control" id="is_mixed"');
        ?>
        <?=form_error('is_mixed'); ?>
    </div>

    <div class="tab-pane col-xs-12" id="online_course" style="display: none">
        <input type="hidden" name="hidStr" id="hidStr" value="" disabled />
        <label class="control-label">線上課程</label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="85%">課程</th>
                    <th width="10%">時數</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <tbody>
            <?php 
                if(isset($form['online_course']) && !empty($form['online_course'])){
                    for ($i=0;$i<count($form['online_course']);$i++) { 
                        $rows = $i+1;
                        echo '<tr>';
                        echo '<td><input class="form-control" type="text" name="online_course_name[]" id="online_course_name[]" value="'.$form['online_course'][$i]['class_name'].'"></td>';
                        echo '<td><input class="form-control" type="text" name="hours[]" id="hours[]" value="'.$form['online_course'][$i]['hours'].'"><input type="hidden" value="'.$form['online_course'][$i]['elearn_id'].'" name="elrid[]" id="elrid[]"></td>';
                        echo '<td align="right"><button type="button" class="btn btn-danger btn-sm" id="remove_'.$rows.'" onclick="removeItem(this, '.$rows.')">刪除</button></td>';
                        echo '</tr>';
                    }
                }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" align="right"><button type="button" class="btn btn-success btn-sm" onclick="openCourSeltor()">新增</button></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">重大政策</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input type="radio" value="Y" name="fmap" style="zoom:1.5;" onclick="fmap_on();" <?=$fmap=='Y'?'checked':''?>>
                    <span>是</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input type="radio" value="N" name="fmap" style="zoom:1.5;" onclick="fmap_off();" <?=($fmap=='N')?'checked':''?>>
                    <span>否</span>
                </label>
            </div>
        </div>
        <div>
            <!--div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map1" type="checkbox" value="1" name="map1" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map1']=='1'?'checked':''?>>
                    <span>A營造永續環境</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map2" type="checkbox" value="1" name="map2" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map2']=='1'?'checked':''?>>
                    <span>B健全都市發展</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map3" type="checkbox" value="1" name="map3" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map3']=='1'?'checked':''?>>
                    <span>C發展多元文化</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map4" type="checkbox" value="1" name="map4" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map4']=='1'?'checked':''?>>
                    <span>D優化產業勞動</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map5" type="checkbox" value="1" name="map5" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map5']=='1'?'checked':''?>>
                    <span>E強化社會支持</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map6" type="checkbox" value="1" name="map6" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map6']=='1'?'checked':''?>>
                    <span>F打造優質教育</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map7" type="checkbox" value="1" name="map7" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map7']=='1'?'checked':''?>>
                    <span>G精進健康安全</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map8" type="checkbox" value="1" name="map8" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map8']=='1'?'checked':''?>>
                    <span>H精實良善治理</span>
                </label>
            </div-->
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map9" type="checkbox" value="1" name="map9" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map9']=='1'?'checked':''?>>
                    <span>樂活宜居(45項)</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map10" type="checkbox" value="1" name="map10" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map10']=='1'?'checked':''?>>
                    <span>友善共融(31項)</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map11" type="checkbox" value="1" name="map11" style="zoom:1.5;" onclick="chooseOne(this);" <?=$form['map11']=='1'?'checked':''?>>
                    <span>創新活力(37項)</span>
                </label>
            </div>
        </div>
        <?=form_error('fmap'); ?>
    </div>

    <div class="form-group col-xs-6">
    </div>

    <div class="form-group col-xs-6 required <?=form_error('env_class')?'has-error':'';?>">
        <label class="control-label">環境教育班期</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="env_class" type="radio" value="Y" name="env_class" style="zoom:1.5;" <?=set_radio('env_class', 'Y', $form['env_class']=='Y'?TRUE:FALSE);?> >
                    <span>是(結訓學員可取得環境教育研習時數)</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="env_class" type="radio" value="N" name="env_class" style="zoom:1.5;" <?=set_radio('env_class', 'N', $form['env_class']=='N'?TRUE:FALSE);?>>
                    <span>否</span>
                </label>
            </div>
        </div>
        <?=form_error('env_class'); ?>
    </div>


    <div class="form-group col-xs-6 required">
        <label class="control-label">政策行銷班期</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="policy_class" type="radio" value="Y" name="policy_class" style="zoom:1.5;" <?=set_radio('policy_class', 'Y', $form['policy_class']=='Y'?TRUE:FALSE);?>>
                    <span>是</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="policy_class" type="radio" value="N" name="policy_class" style="zoom:1.5;" <?=set_radio('policy_class', 'N', $form['policy_class']=='N'?TRUE:FALSE);?>>
                    <span>否</span>
                </label>
            </div>
        </div>
        <?=form_error('policy_class'); ?>
    </div>

    <?php if($user_bureau == '379680000A'){ ?>
    <div class="form-group col-xs-6 required">
        <label class="control-label">開放退休人員選課</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="open_retirement" type="radio" value="Y" name="open_retirement" style="zoom:1.5;" <?=set_radio('open_retirement', 'Y', $form['open_retirement']=='Y'?TRUE:FALSE);?>>
                    <span>YES</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="open_retirement" type="radio" value="N" name="open_retirement" style="zoom:1.5;" <?=set_radio('open_retirement', 'N', $form['open_retirement']=='N'?TRUE:FALSE);?>>
                    <span>NO</span>
                </label>
            </div>
        </div>
        <?=form_error('open_retirement'); ?>
    </div>
    <?php } ?>

    <div class="form-group col-xs-6">
        <label class="control-label">特殊情況</label>
        <div>
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="Y" name="not_hourfee" style="zoom:1.5;" <?=$form['not_hourfee']=='Y'?'checked':''?>>
                    <span>無須支應講座鐘點費</span>
                </label>
            </div>
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="Y" id="not_location" name="not_location" style="zoom:1.5;" <?=($form['not_location']=='Y')?'checked':''?>>
                    <span>上課地點非公訓處</span>
                </label>
            </div>
            <div class="form-group">
                <input id="special_status" type="checkbox" value="9" name="special_status" style="zoom:1.5;" <?=$form['special_status']=='9'?'checked':''?>>
                <label class="control-label">其他(請敘明)</label>
                <input class="form-control" id="special_status_other" name="special_status_other" placeholder="" value="<?=set_value('special_status_other', $form['special_status_other']); ?>">
            </div>
        </div>
        <?=form_error('special_status'); ?>
    </div>
</form>

<script type="text/javascript">
<?php if($fmap == 'N'){ ?>
$(document).ready(function() {
    detectBrowser();
    fmap_off();
});
<?php } ?>

jQuery(function(){
    if(1==jQuery("select#is_assess option:selected").val()) {
        jQuery("#is_mixed").prop( "readonly", false);
    }
    else {
        jQuery("#is_mixed").prop( "readonly", true);
    }
    changAct();
});

jQuery("#is_mixed").change(function() {
    changAct();
});

jQuery("#is_assess").change(function() {
    if(1==jQuery(this).val()) {
        jQuery("#is_mixed").prop( "readonly", false);
    }
    else {
        jQuery("#is_mixed").prop( "readonly", true);
    }
});

function changAct(){
    if(1==jQuery("select#is_mixed option:selected").val()) {
        jQuery("#online_course").css("display","") //顯示
    }
    else {
        jQuery("#online_course").css("display","none") //隱藏
    }
}

var currentSelected = "";

function chooseOne(cb) {
    if(currentSelected!="") {
        currentSelected.checked = false;
    }
    //變更目前勾選的checkbox
    if(cb.checked)  {
        currentSelected = cb;
    } else {
        currentSelected="";
    }
}

function detectBrowser(){  
    if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > 0) {  
        $(".checkbox-inline input[type=checkbox]").css({
            "position" : "absolute",
            "margin-left" : "-22px" 
        });
        $(".radio-inline input[type=radio]").css({
            "position" : "absolute",
            "margin-left" : "-22px" 
        });
    }  
}  

function fmap_off(){
    jQuery("#map1").prop( "checked", false);
    jQuery("#map1").prop( "disabled", true);
    jQuery("#map2").prop( "checked", false);
    jQuery("#map2").prop( "disabled", true);
    jQuery("#map3").prop( "checked", false);
    jQuery("#map3").prop( "disabled", true);
    jQuery("#map4").prop( "checked", false);
    jQuery("#map4").prop( "disabled", true);
    jQuery("#map5").prop( "checked", false);
    jQuery("#map5").prop( "disabled", true);
    jQuery("#map6").prop( "checked", false);
    jQuery("#map6").prop( "disabled", true);
    jQuery("#map7").prop( "checked", false);
    jQuery("#map7").prop( "disabled", true);
    jQuery("#map8").prop( "checked", false);
    jQuery("#map8").prop( "disabled", true);
    jQuery("#map9").prop( "checked", false);
    jQuery("#map9").prop( "disabled", true);
    jQuery("#map10").prop( "checked", false);
    jQuery("#map10").prop( "disabled", true);
    jQuery("#map11").prop( "checked", false);
    jQuery("#map11").prop( "disabled", true);
}

function fmap_on(){
    jQuery("#map1").prop( "disabled", false);
    jQuery("#map2").prop( "disabled", false);
    jQuery("#map3").prop( "disabled", false);
    jQuery("#map4").prop( "disabled", false);
    jQuery("#map5").prop( "disabled", false);
    jQuery("#map6").prop( "disabled", false);
    jQuery("#map7").prop( "disabled", false);
    jQuery("#map8").prop( "disabled", false);
    jQuery("#map9").prop( "disabled", false);
    jQuery("#map10").prop( "disabled", false);
    jQuery("#map11").prop( "disabled", false);
}

<?php if($page_name == 'edit') { ?>
function bookingFun(seq_no){
    var path = '../../../classroom/add/'+seq_no;
    
    alert('預約教室，另開新視窗');
    window.open(path,'booking','fullscreen=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes');
}

function updateBookingFun(seq_no){
    updateResquire(seq_no);
    var link = "<?=$link_get_room;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'seq_no': seq_no
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);
            
            if (result.length != 0) {
                document.getElementById('room_name').value = result[0]['room_name'];
                document.getElementById('set_start_date1').value = result[0]['start_date1'];
                document.getElementById('set_end_date1').value = result[0]['end_date1'];
            }else{
                document.getElementById('room_name').value = '';
                document.getElementById('set_start_date1').value = '';
                document.getElementById('set_end_date1').value = '';
            }
        }
    });
}


function updateResquire(seq_no){
    var link = "<?=$link_update_require;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'seq_no': seq_no
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {

        }
    });
}
<?php } ?>

function notAtLocalFun(type){
    if(type == '非公訓處上課'){
        document.getElementById('room_name').value = '非公訓處上課';
        document.getElementById('enableRoom').value = '本處上課';
        document.getElementById('not_location').checked=true;
        jQuery("#set_start_date1").prop( "disabled", false);
        jQuery("#set_end_date1").prop( "disabled", false);
        alert('選非公訓處上課者，請接續點選預定開課起訖日，處外場地須自行洽外機關預約');
        $("input#set_start_date1").trigger("focus");

        var today = new Date();
        var selectYear = parseInt(document.getElementsByName('year')[0].value) + 1911;
        today.setFullYear(selectYear);
        $('#set_start_date1').datepicker("setDate", today);
        $('#set_end_date1').datepicker("setDate", today);
    } else if(type == '本處上課'){
        document.getElementById('room_name').value = '';
        document.getElementById('enableRoom').value = '非公訓處上課';
        document.getElementById('not_location').checked=false;
        document.getElementById('set_start_date1').value = '';
        document.getElementById('set_end_date1').value = '';
        jQuery("#set_start_date1").prop( "disabled", true);
        jQuery("#set_end_date1").prop( "disabled", true);
    }
}

function query_classname(page_name){
    if(page_name == 'add'){
        var path = '../../../query_classname.php';
    } else if(page_name == 'edit'){
        var path = '../../../../query_classname.php';
    }
    window.open(path,'selbFee','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
}

function showBureau(para,page_name){
    if(para == 'dev_type'){
        if(page_name == 'add'){
            var path = '../../../co_bureau.php?field1=dev_type&field2=dev_type_name&mode=1';
        } else if(page_name == 'edit'){
            var path = '../../../../co_bureau.php?field1=dev_type&field2=dev_type_name&mode=1';
        }
    } else if(para == 'req_beaurau'){
        if(page_name == 'add'){
            var path = '../../../co_bureau.php?field1=req_beaurau&field2=req_beaurau_name&mode=2';
        } else if(page_name == 'edit'){
            var path = '../../../../co_bureau.php?field1=req_beaurau&field2=req_beaurau_name&mode=2';
        }

    }

    var myW=window.open(path, 'selBureau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
    myW.focus();
}

function showEcpaClassName(ecpa_class_id){
    if(ecpa_class_id == ''){
         document.getElementById('ecpa_class_name').value = '';
        return false;
    }

    var link = "<?=$link_get_ecpa_name;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'ecpa_class_id': ecpa_class_id
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            if (response.length != 0) {
                document.getElementById('ecpa_class_name').value = response;
            }
        }
    });
}

function removeOptions(selectbox) {
    var i;
    for (i = selectbox.options.length - 1; i >= 0; i--) {
        selectbox.remove(i);
    }
}

function getSecond(){
    removeOptions(document.getElementById("beaurau_id"));

    var series = document.getElementById('type').value;

    if(series == ''){
        return false;
    }

    var link = "<?=$link_get_second_category;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);
             <?php if(isset($link_get_classno) && !isset($transfer)) {?>
                getClassNO();
            <?php } ?>
            if (result.length != 0) {
                for (var i = 0; i < result.length; i++) {
                    var second = document.getElementById('beaurau_id');
                    var option_name = result[i]['name'];
                    var option_value = result[i]['item_id'];
                    var new_option = new Option(option_name, option_value);
                    second.options.add(new_option);
                }
            }
        }
    });
}

function addCourse() {
    var num = $('#course_content table tbody tr').size();
    var html = '';
    html += '<tr>';
    html += '<td>';
    html += '<input class="form-control" type="text" name="course_name[]" value="">';
    html += '</td>';
    /*
    html += '<td>';
    html += '<select class="form-control" name="material[]">';
    html += '<option value="4">無</option>';
    html += '<option value="0">實境錄製教材(單一主題)</option>';
    html += '<option value="1">實境錄製教材(系列性主題)</option>';
    html += '<option value="2">全動畫教材(貴局處無經費)</option>';
    html += '<option value="3">全動畫教材(貴局處有經費)</option>';
    html += '</select>';
    html += '</td>';
    */
    html += '<td align="right">';
    html += '<button type="button" class="btn btn-danger btn-sm" id="remove_'+ num +'" onclick="removeItem(this, '+ num +')">刪除</button>';
    html += '</td>';
    html += '</tr>';
    $('#course_content table tbody').append(html);
}

function removeItem(obj, num) {
    $(obj).closest('tr').remove();
}

function openCourSeltor() {
    <?php if($page_name == 'add'){ ?>
        window.open("../../../elearnQuery.php",'selbFee','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=580,width=600');
    <?php } else if ($page_name = 'edit') { ?>
        window.open("../../../../elearnQuery.php",'selbFee','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=580,width=600');
    <?php } ?> 
}

function explodeStr() {
    var getContent = jQuery("#hidStr").val().split("|,|");
    var num = $('#online_course table tbody tr').size();
    var html = '';
    if(getContent.length=3) {
        var num = $('#online_course table tbody tr').size();
        var html = '';
        html += '<tr>';
        html += '<td>';
        html += '<input class="form-control" type="text" name="online_course_name[]" id="online_course_name[]" value="'+getContent[1]+'">';
        html += '</td>';
        html += '<td>';
        html += '<input class="form-control" type="text" name="hours[]" id="hours[]" value="'+getContent[2]+'">';
        html += '<input type="hidden" value="'+getContent[0]+'" name="elrid[]" id="elrid[]">';
        html += '</td>';
        html += '<td align="right">';
        html += '<button type="button" class="btn btn-danger btn-sm" id="remove_'+ num +'" onclick="removeItem(this, '+ num +')">刪除</button>';
        html += '</td>';
        html += '</tr>';
        $('#online_course table tbody').append(html);
    } else {
        alert("insert exception");
    }
}

function checkSave(){
    if(document.getElementById('is_mixed').value == '1'){
        if($('#online_course table tbody tr').size() == 0){
            alert('線上課程至少1門');
            return false;
        }
    }

    if(document.getElementById('class_no').value == ''){
        alert('班期代碼不能為空');
        return false;
    }

    if(document.getElementById('not_location').value == 'Y'){
        var year = document.getElementsByName('year')[0].value ;
        year = parseInt(year)+1911;

        var sd = document.getElementsByName('start_date1')[0].value;
        var ed = document.getElementsByName('end_date1')[0].value;
        if((sd == '') || (ed == '')){
            alert('上課地點非公訓處時，需填寫開課起迄日');
            return false;
        }

        if((parseInt(sd) != year) || (parseInt(ed) != year)){
            alert('預定開課起迄日年度與開班年度不符，請重新點選年度');
            return false;
        }
    }

    var obj = document.getElementById('data-form');
    obj.submit();
}

function getDefaultYear(){
    var today = new Date();
    var selectYear = parseInt(document.getElementsByName('year')[0].value) + 1911;
    today.setFullYear(selectYear);
    $('#set_start_date1').datepicker("setDate", today);
    $('#set_end_date1').datepicker("setDate", today);
}

$(document).ready(function() {
    $( "#start_date1" ).click(function() {
        $("input#set_start_date1").trigger("focus");
    });

    $( "#end_date1" ).click(function() {
        $("input#set_end_date1").trigger("focus");
    });
});

<?php if(isset($link_get_classno) && !isset($transfer)) {?>
function getClassNO(){
    var series = document.getElementById('type').value;
    var class_name = document.getElementById('class_name').value;
    var link = "<?=$link_get_classno;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series,
        'class_name': class_name
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            document.getElementById('class_no').value = response;
        }
    });
}
<?php } ?>
</script>