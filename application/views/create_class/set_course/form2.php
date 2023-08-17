<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>

<style type="text/css">
    #scrolltable { margin-top: 20px; height: auto; overflow: auto; }
    #scrolltable table { border-collapse: collapse; font-size: 14px; line-height: 16px}
    #scrolltable tr:nth-child(even) { background: #EEE; }
    #scrolltable th div { position: absolute; margin-top: -20px; }
</style>

<form id="data-form" role="form" method="post" action="<?=$link_save_t;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <div class="form-group col-xs-12">
        <table border="1" cellspacing="1" cellpadding="1" width="100%">
            <tbody>
                <tr>
                  <td width="100" align="center" bgcolor="#dcdcdc">年度</td>
                  <td width="150" align="left" bgcolor="#ffffff"><?=$form['year'];?></td>
                  <td width="100" align="center" bgcolor="#dcdcdc">班期代碼</td>
                  <td width="150" align="left" bgcolor="#ffffff"><?=$form['class_no'];?></td>
                  <td width="100" align="center" bgcolor="#dcdcdc">班期名稱</td>
                  <td width="150" align="left" bgcolor="#ffffff"><?=$form['class_name'];?></td>
                  <td width="100" align="center" bgcolor="#dcdcdc">期別</td>
                  <td width="150" align="left" bgcolor="#ffffff"><?=$form['term'];?></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#dcdcdc">開課起日</td>
                  <td align="left" bgcolor="#ffffff"><?=date('Y-m-d',strtotime($form['start_date1']));?></td>
                  <td align="center" bgcolor="#dcdcdc">開課迄日</td>
                  <td align="left" bgcolor="#ffffff"><?=date('Y-m-d',strtotime($form['end_date1']));?></td>
                  <td align="center" bgcolor="#dcdcdc">使用教室</td>
                  <td colspan="3" align="left" bgcolor="#ffffff"><?=$form['room_name']?></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#dcdcdc">總時數</td>
                  <td align="left" bgcolor="#ffffff"><?=$form['range'];?> 小時</td>
                  <td align="center" bgcolor="#dcdcdc">實體時數</td>
                  <td align="left" bgcolor="#ffffff"><?=$form['range_real'];?> 小時</td>
                  <td align="center" bgcolor="#dcdcdc">帶班承辦人</td>
                  <td colspan="3" align="left" bgcolor="#ffffff"><?=$form['worker_name'];?></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#dcdcdc">線上時數</td>
                  <td align="left" bgcolor="#ffffff"><?=$form['range_internet'];?> 小時</td>
                  <td align="center" bgcolor="#dcdcdc">退訓標準</td>
                  <td align="left" bgcolor="#ffffff">1/<?=$form['quit_class']?></td>
                  <td align="center" bgcolor="#dcdcdc">退訓標準2</td>
                  <td align="left" bgcolor="#ffffff"><?=!empty($form['quit_class2'])?$form['quit_class2']:'未設定'?> 小時</td>
                  <td colspan="2" align="left" bgcolor="#ffffff"><font color="blue">請假時數大於退訓標準(<?=$choices['quit_class_hours'];?>小時)者退訓</font></td>
                </tr>
                <tr>
                  <td colspan="8" align="center" bgcolor="#c2c2c2" height="3"></td>
                </tr>
                <tr>
                  <td colspan="8" align="left" bgcolor="#dcdcdc">
                    <font color="red" size="3">註1、已取請款流水號後，無法修改課表；須先刪除流水號、並將請款選取回復為空值。</font>
                    <br>
                    <font color="red" size="3">註2、問卷調查截止後，課表即無法再增修。</font>
                  </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- <div class="form-group col-xs-3">
        <label class="control-label">年度</label>
        <input class="form-control" id="year" value="<?=$form['year'];?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">班期代碼</label>
        <input class="form-control" id="class_no" value="<?=$form['class_no'];?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">班期名稱</label>
        <input class="form-control" value="<?=$form['class_name'];?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">期別</label>
        <input class="form-control" id="term" value="<?=$form['term'];?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">開課起日</label>
        <input class="form-control" value="<?=date('Y-m-d',strtotime($form['start_date1']));?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">開課迄日</label>
        <input class="form-control" value="<?=date('Y-m-d',strtotime($form['end_date1']));?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">使用教室</label>
        <input type="text" class="form-control" value="<?=$form['room_name']?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">總時數</label>
        <input class="form-control" name="range" id="range" value="<?=$form['range'];?>" style="width:89%;float:left" disabled>
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">實體時數</label>
        <input class="form-control" name="range_real" id="range_real" value="<?=!empty($form['range_real'])?$form['range_real']:0;?>" style="width:88%;float:left" disabled>
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">帶班承辦人</label>
        <input class="form-control" value="<?=$form['worker_name']?>" disabled>
    </div>
    <div class="form-group col-xs-3">
        
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">線上時數</label>
        <input class="form-control" name="range_internet" id="range_internet" value="<?=!empty($form['range_internet'])?$form['range_internet']:0;?>" style="width:85%;float:left" disabled>
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">退訓標準1</label>
        <input class="form-control" value="<?=$form['quit_class']?>" style="width:84%;float:right" disabled>
        <font style="font-size: 26px;float: left">1/</font>
    </div>
    <div class="form-group col-xs-3">
        <label class="control-label">退訓標準2<font style="color: blue">(請假時數大於退訓標準(<?=$choices['quit_class_hours'];?>小時)者退訓)</font></label>
        <input class="form-control" name="quit_class2" id="quit_class2" value="<?=!empty($form['quit_class2'])?$form['quit_class2']:'未設定'?>" style="width:84%;float:left" disabled>
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="form-group col-xs-3">
        <p style="color: red">註1、已取請款流水號後，無法修改課表；須先刪除流水號、並將請款選取回復為空值。</p>
        <p style="color: red">註2、問卷輸入完成後，課表即無法再增修。</p>
    </div> -->
    <div class="tab-pane col-xs-12" id="fix">
        <label class="control-label">節次設定</label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="20%">上午開始時間</th>
                    <th width="20%">下午開始時間</th>
                    <th width="20%">晚午開始時間</th>
                    <th width="20%">每節時間(分)</th>
                    <th width="20%">休息時間(分)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input class="form-control" id="f1" name="f1" placeholder="" value="<?=set_value('f1', $form['f1']); ?>"></td>
                    <td><input class="form-control" id="f2" name="f2" placeholder="" value="<?=set_value('f2', $form['f2']); ?>"></td>
                    <td><input class="form-control" id="f3" name="f3" placeholder="" value="<?=set_value('f3', $form['f3']); ?>"></td>
                    <td><input class="form-control" id="f4" name="f4" placeholder="" value="<?=set_value('f4', $form['f4']); ?>"></td>
                    <td><input class="form-control" id="f5" name="f5" placeholder="" value="<?=set_value('f5', $form['f5']); ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="tab-pane col-xs-12">
        <label class="control-label">教室預約狀況</label>
        <table class="table" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="7%">
                        課程名稱
                    </th>
                    <th width="23%">
                        <select id="listCourse" name="listCourse" onchange="clearTeach()" <?=(isset($postdata['class_description']) && $postdata['class_description'] == '1')?'disabled':'';?>>
                            <option class="form-control" value="">請選擇</option>
                        
                        <?php
                            foreach ($form['course_list'] as $key => $value) {
                                if($value['use_id'] == ''){
                                    if(isset($postdata['listCourse']) && $postdata['listCourse'] == $value['course_code']){
                                        echo '<option value="'.$value['course_code'].'" style="color:red" selected>'.$value['course_code'].'-'.$value['name'].'</option>';
                                    } else {
                                        echo '<option value="'.$value['course_code'].'" style="color:red">'.$value['course_code'].'-'.$value['name'].'</option>';
                                    }
                                } else {
                                    if(isset($postdata['listCourse']) && $postdata['listCourse'] == $value['course_code']){
                                        echo '<option value="'.$value['course_code'].'" style="color:blue" selected>'.$value['course_code'].'-'.$value['name'].'</option>';
                                    } else {
                                        echo '<option value="'.$value['course_code'].'" style="color:blue">'.$value['course_code'].'-'.$value['name'].'</option>';
                                    }
                                }
                            }
                        ?>
                        </select>
                    </th>
                    <th width="40%">
                        講座
                        <?php 
                            if(isset($postdata['teach1']) && isset($postdata['teach1_ID'])) {
                                echo '<span name="teach1_s" id="teach1_s">';
                                $teacher_name_list = explode(',', $postdata['teach1']);
                                $teacher_name_idno = explode(',', $postdata['teach1_ID']);

                                for($i=0;$i<count($teacher_name_list);$i++){
                                    if($i != 0){
                                        echo ',';
                                    }
                                    echo '<a href="#" onclick="del('.'\''.$teacher_name_idno[$i].','.'addTeach1'.')">'.$teacher_name_list[$i].'</a>';
                                }
                                echo '</span>';
                            } else {
                                echo '<span name="teach1_s" id="teach1_s"></span>';
                            }
                        ?>
                        
                        <input type="hidden" value="<?=isset($postdata['teach1'])?$postdata['teach1']:'';?>" name="teach1" id="teach1">
                        <input type="hidden" value="<?=isset($postdata['teach1_ID'])?$postdata['teach1_ID']:'';?>" name="teach1_ID" id="teach1_ID">
                        <input type="hidden" value="<?=isset($postdata['teach1_TIT'])?$postdata['teach1_TIT']:'';?>" name="teach1_TIT" id="teach1_TIT">
                        <input type="hidden" value="<?=isset($postdata['teach1_SORT'])?$postdata['teach1_SORT']:'';?>" name="teach1_SORT" id="teach1_SORT">
                        <input type="button" class="btn btn-xs btn-primary" onclick="showTeach('addTeach1','1')" value="查詢" style="margin-left: 10px">
                    </th>
                    <th width="30%">
                        助教
                         <?php 
                            if(isset($postdata['teach2']) && isset($postdata['teach2_ID'])) {
                                echo '<span name="teach2_s" id="teach2_s">';
                                $assistant_name_list = explode(',', $postdata['teach2']);
                                $assistant_name_idno = explode(',', $postdata['teach2_ID']);

                                for($i=0;$i<count($assistant_name_list);$i++){
                                    if($i != 0){
                                        echo ',';
                                    }
                                    echo '<a href="#" onclick="del('.'\''.$assistant_name_idno[$i].','.'addTeach2'.')">'.$assistant_name_list[$i].'</a>';
                                }
                                echo '</span>';
                            } else {
                                echo '<span name="teach2_s" id="teach2_s"></span>';
                            }
                        ?>
                        
                        <input type="hidden" value="<?=isset($postdata['teach2'])?$postdata['teach2']:'';?>" name="teach2" id="teach2" >
                        <input type="hidden" value="<?=isset($postdata['teach2_ID'])?$postdata['teach2_ID']:'';?>" name="teach2_ID" id="teach2_ID">
                        <input type="hidden" value="<?=isset($postdata['teach2_TIT'])?$postdata['teach2_TIT']:'';?>" name="teach2_TIT" id="teach2_TIT">
                        <input type="hidden" value="<?=isset($postdata['teach2_SORT'])?$postdata['teach2_SORT']:'';?>" name="teach2_SORT" id="teach2_SORT">
                        <input type="button" class="btn btn-xs btn-primary" onclick="showTeach('addTeach2','2')" value="查詢" style="margin-left: 10px">
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="2" style="font-weight: bold">日期/教室</td>
                    <td colspan="3">
                        <?php if(isset($postdata['selType']) && $postdata['selType'] == '2'){ ?>
                        <input type="radio" name="selType" id="selType" value="1" style="margin-left: 8px"></input>預定
                        <?php } else { ?> 
                        <input type="radio" name="selType" id="selType" value="1" style="margin-left: 8px" checked="checked"></input>預定
                        <?php } ?>
                        <?php
                            if(isset($postdata['selType']) && $postdata['selType'] == '1' && isset($postdata['booking_date'])){
                                echo form_dropdown('booking_date', $choices['booking_date'], $postdata['booking_date'], 'class="form-control" id="booking_date" onchange="getBookingRoom('.$form['year'].',\''.$form['class_no'].'\','.$form['term'].')" style="width:14%;display:inline;margin-left:12px"');
                            } else {
                                echo form_dropdown('booking_date', $choices['booking_date'], '', 'class="form-control" id="booking_date" onchange="getBookingRoom('.$form['year'].',\''.$form['class_no'].'\','.$form['term'].')" style="width:14%;display:inline;margin-left:12px"');
                            }
                            
                        ?>
                        <select class="form-control" name='booking_room_id' id='booking_room_id' style="width:31%;display:inline">
                            <?php if(isset($postdata['selType']) && $postdata['selType'] == '1' && isset($postdata['booking_room_id'])) {?>
                            <option value="<?=$postdata['booking_room_id']?>"><?=$postdata['booking_room_name']?></option>
                            <?php } else { ?>
                            <option value="">請選擇</option>
                            <?php } ?>
                        </select>
                    <?php if(count($choices['booking_date']) > 1){ ?>
                        <input type="button" class="btn btn-xs btn-primary" onclick="delBookingData()" value="清除預約資料" style="margin-left: 10px">
                    <?php } else { ?>
                        <input type="button" class="btn btn-xs btn-primary" value="清除預約資料" style="margin-left: 10px" disabled="disabled">
                    <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: 0px;">
                        <table class="table" style="margin-bottom: 3px;">
                            <tr>
                                <td rowspan="2" style="width: 5%">
                                    <input type="radio" name="selType" id="selType" value="2" <?=(isset($postdata['selType']) && $postdata['selType'] == '2')?'checked':''?>></input>自定
                                </td>
                                <td style="width: 60%">
                                    <?php
                                        if(isset($postdata['selType']) && $postdata['selType'] == '2' && isset($postdata['old_room_use_date'])){
                                            echo form_dropdown('old_room_use_date', $choices['room_use_date'], $postdata['old_room_use_date'], 'class="form-control" id="old_room_use_date" onchange="getOldBookingRoom('.$form['year'].',\''.$form['class_no'].'\','.$form['term'].')" style="width:24%;display:inline"');
                                        } else {
                                            echo form_dropdown('old_room_use_date', $choices['room_use_date'], '', 'class="form-control" id="old_room_use_date" onchange="getOldBookingRoom('.$form['year'].',\''.$form['class_no'].'\','.$form['term'].')" style="width:24%;display:inline"');
                                        }
                                    ?>
                                    <select class="form-control" name='old_room_use_id' id='old_room_use_id' style="width:53%;display:inline">
                                        <?php if(isset($postdata['selType']) && $postdata['selType'] == '2' && isset($postdata['old_room_use_id'])) {?>
                                        <option value="<?=$postdata['old_room_use_id']?>"><?=$postdata['old_room_use_name']?></option>
                                        <?php } else { ?>
                                        <option value="">請選擇</option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td rowspan="2">
                                    <input type="checkbox" id="class_description" name="class_description" value="1" onclick="enableFun(this)" style="margin-left: 10px" <?=(isset($postdata['class_description']) && $postdata['class_description'] == '1')?'checked':''?>>是否為班務說明
                                    <select id="classFirst" name="classFirst" <?=(isset($postdata['class_description']) && $postdata['class_description'] == '1')?'':'disabled'?>>
                                    <?php if(isset($postdata['classFirst'])){?>
                                        <?php if($postdata['classFirst'] == 'O00001'){?>
                                        <option value="">請選擇</option>
                                        <option value="O00001" selected>報到(含班務說明)</option>
                                        <option value="O00002">報到程序與註冊安排</option>
                                        <option value="O00003">報到暨班務說明</option>
                                        <option value="O00004">報到</option>
                                        <option value="O00005">班務介紹</option>
                                        <?php } else if($postdata['classFirst'] == 'O00002'){?>
                                        <option value="">請選擇</option>
                                        <option value="O00001">報到(含班務說明)</option>
                                        <option value="O00002" selected>報到程序與註冊安排</option>
                                        <option value="O00003">報到暨班務說明</option>
                                        <option value="O00004">報到</option>
                                        <option value="O00005">班務介紹</option>
                                        <?php } else if($postdata['classFirst'] == 'O00003'){?>
                                        <option value="">請選擇</option>
                                        <option value="O00001">報到(含班務說明)</option>
                                        <option value="O00002">報到程序與註冊安排</option>
                                        <option value="O00003" selected>報到暨班務說明</option>
                                        <option value="O00004">報到</option>
                                        <option value="O00005">班務介紹</option>
                                        <?php } else if($postdata['classFirst'] == 'O00004'){?>
                                        <option value="">請選擇</option>
                                        <option value="O00001">報到(含班務說明)</option>
                                        <option value="O00002">報到程序與註冊安排</option>
                                        <option value="O00003">報到暨班務說明</option>
                                        <option value="O00004" selected>報到</option>
                                        <option value="O00005">班務介紹</option>
                                        <?php } else if($postdata['classFirst'] == 'O00005'){?>
                                        <option value="">請選擇</option>
                                        <option value="O00001">報到(含班務說明)</option>
                                        <option value="O00002">報到程序與註冊安排</option>
                                        <option value="O00003">報到暨班務說明</option>
                                        <option value="O00004">報到</option>
                                        <option value="O00005" selected>班務介紹</option>
                                        <?php } else {?>
                                        <option value="">請選擇</option>
                                        <option value="O00001">報到(含班務說明)</option>
                                        <option value="O00002">報到程序與註冊安排</option>
                                        <option value="O00003">報到暨班務說明</option>
                                        <option value="O00004">報到</option>
                                        <option value="O00005">班務介紹</option>
                                        <?php } ?>
                                    <?php } else {?>
                                    <option value="">請選擇</option>
                                    <option value="O00001">報到(含班務說明)</option>
                                    <option value="O00002">報到程序與註冊安排</option>
                                    <option value="O00003">報到暨班務說明</option>
                                    <option value="O00004">報到</option>
                                    <option value="O00005">班務介紹</option>
                                    <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="float: left;width:24%;margin-right:1% ">
                                        <input type="text" class="form-control datepicker" name="room_use_date" value="<?=isset($postdata['room_use_date'])?$postdata['room_use_date']:'';?>" id="room_use_date" readonly>
                                        <span class="input-group-addon" style="cursor: pointer" id="datepick2"><i
                                                class="fa fa-calendar"></i></span>
                                    </div>
                                    <input type="hidden" id="room_id" name="room_id" value="<?=isset($postdata['room_id'])?$postdata['room_id']:'';?>"></input>
                                    <input type="text" id="room_name" name="room_name" value="<?=isset($postdata['room_name'])?$postdata['room_name']:'';?>" style="width:53%;display:inline" readonly></input>
                                    <input type="button" class="btn btn-xs btn-primary" onclick="showRoom()" value="查詢" style="margin-left: 10px">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold">預排時間</td>
                    <td colspan="3">
                        <input type="text" id="pre_start_time" name="pre_start_time" value="<?=isset($postdata['pre_start_time'])?$postdata['pre_start_time']:'';?>" style="width: 10%"></input>
                        ~
                        <input type="text" id="pre_end_time" name="pre_end_time" value="<?=isset($postdata['pre_end_time'])?$postdata['pre_end_time']:'';?>" style="width: 10%"></input>
                        <input type="button" class="btn btn-xs btn-primary" onclick="getCourseTime()" value="產生預排時間" style="margin-left: 10px">
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold">鐘點費時數</td>
                    <td colspan="3">
                        <input type="text" id="hours" name="hours" value="<?=isset($postdata['hours'])?$postdata['hours']:'';?>" style="width: 10%"></input>
                        (時)
                        <!-- <input type="button" class="btn btn-xs btn-primary" onclick="" value="鐘點時數加總檢核" style="margin-left: 10px"> -->
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                       <input type="button" class="btn btn-xs btn-primary" id="addbtn" onclick="addSchedule()" value="新增至課表" style="margin-left: 10px;display: " <?php if($form['isready'] == '1') echo 'disabled="disabled"'; ?>>
                       <input type="button" class="btn btn-xs btn-primary" id="updbtn" onclick="updUse()" value="儲存修改" style="margin-left: 10px;display: none" <?php if($form['isready'] == '1') echo 'disabled="disabled"'; ?>>
                       <input type="button" class="btn btn-xs btn-primary" id="cancelbtn" onclick="updCancel()" value="取消" style="margin-left: 10px;display: none">
                       <input type="button" class="btn btn-xs btn-primary" onclick="chkHours()" value="鐘點時數加總檢核" style="margin-left: 10px">
                       <input type="button" class="btn btn-xs btn-primary" onclick="copySchedulePre()" value="複製同代碼課表" style="margin-left: 10px;">
                       <input type="button" class="btn btn-xs btn-primary" onclick="confirmSchedule()" value="課表預覽與陳核" style="margin-left: 10px;">
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="display:none">
        <input id="addClass" name="addClass" type="text" value="" size="100">
        <br>
        <input id="addTeach1" name="addTeach1" type="text" value="" size="100">
        <br>
        <input id="addTeach2" name="addTeach2" type="text" value="" size="100">
        <br>
        <input id="doBack" name="doBack" type="text" value="">
        <br>
        <input id="doClear" name="doClear" type="text" value="">
        <br>
        <input id="doAction" name="doAction" type="text" value="">
        <br>
        </div>
        <input type="hidden" id="query_year" name="query_year" value="<?=$form['year']?>">
        <input type="hidden" id="query_class_no" name="query_class_no" value="<?=$form['class_no']?>">
        <input type="hidden" id="query_term" name="query_term" value="<?=$form['term']?>">
        <input type="hidden" id="query_class_name" name="query_class_name" value="<?=$form['class_name']?>">
        <input type="hidden" id="final_course_date" name="final_course_date" value="">
        <input type="hidden" id="final_room_id" name="final_room_id" value="">
        <input type="hidden" id="per1" name="per1" value="">
        <input type="hidden" id="per2" name="per2" value="">
        <input type="hidden" id="per3" name="per3" value="">
        <input type="hidden" id="per4" name="per4" value="">
        <input type="hidden" id="per5" name="per5" value="">
        <input type="hidden" id="per6" name="per6" value="">
        <input type="hidden" id="per7" name="per7" value="">
        <input type="hidden" id="mode" name="mode" value="">
        <input type="hidden" id="delKey1" name="delKey1" value="">
        <input type="hidden" id="delKey2" name="delKey2" value="">
        <input type="hidden" id="delKey3" name="delKey3" value="">
    </div>

    <div class="tab-pane col-xs-12" >
        <label class="control-label">班期課表</label>
        <div id="scrolltable">
            <table cellspacing="0" cellpadding="0" border="1" style="width:100%;overflow:auto;">
            <?php
                if(!empty($form['course_schedule'])){
                    foreach ($form['course_schedule'] as $key => $value) {
                        echo '<tr>';
                        echo '<td rowspan="2" style="text-align:center">'.date('Y-m-d',strtotime($key)).'</td>';
                        for($i=0;$i<count($value);$i++){
                            echo '<td style="background-color:rgb(93, 123, 157);color:white;text-align:center;">'.$value[$i]['from_time'].'~'.$value[$i]['to_time'].'</td>';
                        }
                        echo '</tr>';
                        echo '<tr>';

                        for($i=0;$i<count($value);$i++){
                            $upd_teacher = '';
                            $upd_teacher_id = '';
                            $upd_teacher_title = '';
                            $upd_assistant = '';
                            $upd_assistant_id = '';
                            $upd_assistant_title = '';

                            if($value[$i]['isteacher'] == 'Y'){
                                $upd_teacher .= $value[$i]['teacher_name'].',';
                                $upd_teacher_id .= $value[$i]['teacher_id'].',';
                                $upd_teacher_title .= $value[$i]['title'].',';

                                if(!empty($value[$i]['title'])){
                                    echo '<td style="background-color:white">
                                    <p style="color:blue;text-align:center">'.$value[$i]['course_name'].'</p>
                                    <p style="text-align:center">'.$value[$i]['room_name'].'</p>
                                    <p style="text-align:center;color:rgb(255, 168, 80)">'.$value[$i]['teacher_name'].'('.$value[$i]['title'].')'.'</p>';
                                } else {
                                    echo '<td style="background-color:white">
                                    <p style="color:blue;text-align:center">'.$value[$i]['course_name'].'</p>
                                    <p style="text-align:center">'.$value[$i]['room_name'].'</p>
                                    <p style="text-align:center;color:rgb(255, 168, 80)">'.$value[$i]['teacher_name'].'</p>';
                                }
                                
                            } else {
                                $upd_assistant .= $value[$i]['teacher_name'].',';
                                $upd_assistant_id .= $value[$i]['teacher_id'].',';
                                $upd_assistant_title .= $value[$i]['title'].',';

                                if(!empty($value[$i]['title'])){
                                    echo '<td style="background-color:white">
                                        <p style="color:blue;text-align:center">'.$value[$i]['course_name'].'</p>
                                        <p style="text-align:center">'.$value[$i]['room_name'].'</p>
                                        <p style="text-align:center;color:blue">'.$value[$i]['teacher_name'].'('.$value[$i]['title'].')'.'</p>';
                                } else {
                                    echo '<td style="background-color:white">
                                        <p style="color:blue;text-align:center">'.$value[$i]['course_name'].'</p>
                                        <p style="text-align:center">'.$value[$i]['room_name'].'</p>
                                        <p style="text-align:center;color:blue">'.$value[$i]['teacher_name'].'</p>';
                                }
                            } 


                            if(isset($value[$i]['teacher_list']) && !empty($value[$i]['teacher_list'])){
                                for($j=0;$j<count($value[$i]['teacher_list']);$j++){
                                    if($value[$i]['isteacher_list'][$j] == 'Y'){
                                        $upd_teacher .= $value[$i]['teacher_list'][$j].',';
                                        $upd_teacher_id .= $value[$i]['teacher_id_list'][$j].',';
                                        $upd_teacher_title .= $value[$i]['title_list'][$j].',';

                                        echo '<p style="text-align:center;color:rgb(255, 168, 80)">'.$value[$i]['teacher_list'][$j].'</p>';
                                    } else {
                                        $upd_assistant .= $value[$i]['teacher_list'][$j].',';
                                        $upd_assistant_id .= $value[$i]['teacher_id_list'][$j].',';
                                        $upd_assistant_title .= $value[$i]['title_list'][$j].',';

                                        echo '<p style="text-align:center;color:blue">'.$value[$i]['teacher_list'][$j].'</p>';
                                    }
                                }
                            }
                            
                            echo '<p style="text-align:center">鐘點：'.$value[$i]['hrs'].'hr</p>';
                            echo '<div style="text-align:center">';

                            $upd_teacher = substr($upd_teacher,0,-1);
                            $upd_teacher_id = substr($upd_teacher_id,0,-1);
                            $upd_teacher_title = substr($upd_teacher_title,0,-1);

                            $upd_assistant = substr($upd_assistant,0,-1);
                            $upd_assistant_id = substr($upd_assistant_id,0,-1);
                            $upd_assistant_title = substr($upd_assistant_title,0,-1);

                            if(!empty($value[$i]['use_date'])){
                                $value[$i]['use_date'] = date('Y-m-d',strtotime($value[$i]['use_date']));
                            }

                            $upd_parameter = '\''.$value[$i]['use_id'].'\''.','.'\''.$value[$i]['use_date'].'\''.','.'\''.$value[$i]['room_name'].'\''.','.'\''.$value[$i]['room_id'].'\''.','.'\''.$value[$i]['use_period'].'\''.','.'\''.$value[$i]['period_name'].'\''.','.'\''.$value[$i]['from_time'].'\''.','.'\''.$value[$i]['to_time'].'\''.','.'\''.$upd_teacher.'\''.','.'\''.$upd_teacher_id.'\''.','.'\''.$upd_teacher_title.'\''.','.'\''.$upd_assistant.'\''.','.'\''.$upd_assistant_id.'\''.','.'\''.$upd_assistant_title.'\''.','.$value[$i]['hrs'];

                            $del_parameter = '\''.$value[$i]['use_date'].'\''.','.'\''.$value[$i]['use_period'].'\''.','.'\''.$value[$i]['room_id'].'\'';

                            if($value[$i]['trafic_status']=='待確認'||$value[$i]['trafic_status']=='請款確認'||$value[$i]['trafic_status']=='市庫支票'){
                                $tra_date=$value[$i]['use_date'];
                                //var_dump($tra_date);
                                $tra_year=substr($tra_date,0,4);
                                $tra_month=substr($tra_date,5,2);
                                $tra_day=substr($tra_date,8,2);
                                //echo $tra_day;
                                echo "<input type='button' value='修改' onclick='alertFocus(".$tra_year.$tra_month.$tra_day.");'>";
                            }else{
                                echo '<input type="button" value="修改" onclick="toUpd('.$upd_parameter.')">';
                            }
                            
                            //var_dump($value[$i]['use_date']);

                            //echo '<input type="button" value="修改" onclick="toUpd('.$upd_parameter.')">';
                            echo '<input type="button" value="刪除" onclick="toDel('.$del_parameter.')">';
                            /* if($form['is_volunteer'] == '1' && isset($link_change) && !empty($value[$i]['use_date']) && !empty($value[$i]['room_id']) && !empty($value[$i]['from_time']) && !empty($value[$i]['to_time'])) {
                                echo '<a href="'.$link_change.'?change_date='.$value[$i]['use_date'].'&room_id='.$value[$i]['room_id'].'&from_time='.$value[$i]['from_time'].'&to_time='.$value[$i]['to_time'].'" target="_blank"><input type="button" value="同步志工系統"></a>';
                            }  */
                            echo '<div>';

                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                }
            ?>
            </table>
        </div>
    </div>
</form>

<script type="text/javascript">
    function showTeach(x,y){
      var tmp = document.all.listCourse.value;
      if (tmp!="")
      {
        myW=window.open('../../../../co_course_teach_all.php?course=' + tmp + '&type=' + y + '&field=' + x,'sel_Course','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=640');
        myW.focus();
      }
      else
      {
        alert("請先選擇課程");
      }
    }

    function alertFocus(date){
        var news=date+"已進入請款流程,不得修改,若欲修改請先到請款作業將「處理狀態」回復成「空值";
        alert(news);
    }

    function showRoom(){
      var tmp = document.getElementById('room_use_date').value;
      if (tmp!="")
      {
        myW=window.open('../../../../co_room_popup.php?mode=2&field1=room_id&field2=room_name&course_date='+tmp,'show_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=640');
        myW.focus();
      }
      else
      {
        alert("請先選擇日期");
      }
    }

    function selTeachOK(x){
      var tmpObj = document.getElementById(x);
      if (x=="addTeach1"){
        var obj1 = document.all.teach1;
        var obj11 = document.all.teach1_s;
        var obj2 = document.all.teach1_ID;
        var obj3 = document.all.teach1_TIT;
        var obj4 = document.all.teach1_SORT;
      }
      if (x=="addTeach2"){
        var obj1 = document.all.teach2;
        var obj11 = document.all.teach2_s;
        var obj2 = document.all.teach2_ID;
        var obj3 = document.all.teach2_TIT;
        var obj4 = document.all.teach2_SORT;
      }

      var tmpSet = tmpObj.value.split(",,");

      for(i=0; i<(tmpSet.length); i++){
        var ss = tmpSet[i].split("::");
        if ((ss[0]!="")&&(obj2.value.indexOf(ss[0])==-1))
        {
          if (obj2.value==""){
            obj11.innerHTML = obj11.innerHTML +"<a href='#' onclick=\"del('"+ss[0]+"','"+x+"')\">"+ ss[1]+"</a>";
            obj1.value = obj1.value + ss[1];
            obj2.value = obj2.value + ss[0];
            obj3.value = obj3.value + ss[2];
            obj4.value = obj4.value + ss[3];
          }
          else{
            obj11.innerHTML = obj11.innerHTML +",<a href='#' onclick=\"del('"+ss[0]+"','"+x+"')\">"+ ss[1]+"</a>";
            obj1.value = obj1.value + "," + ss[1];
            obj2.value = obj2.value + "," + ss[0];
            obj3.value = obj3.value + "," + ss[2];
            obj4.value = obj4.value + "," + ss[3];
          }
        }
      }
      tmpObj.value = "";
    }

    function del(id,x){
        if (x=="addTeach1"){
            var obj1 = document.all.teach1;
            var obj11 = document.all.teach1_s;
            var obj2 = document.all.teach1_ID;
            var obj3 = document.all.teach1_TIT;
            var obj4 = document.all.teach1_SORT;
        }
        if (x=="addTeach2"){
            var obj1 = document.all.teach2;
            var obj11 = document.all.teach2_s;
            var obj2 = document.all.teach2_ID;
            var obj3 = document.all.teach2_TIT;
            var obj4 = document.all.teach2_SORT;
        }

        obj1_ary = obj1.value.split(",");
        obj2_ary = obj2.value.split(",");
        obj3_ary = obj3.value.split(",");
        obj4_ary = obj4.value.split(",");

        obj1.value = "";
        obj11.innerHTML="";
        obj2.value = "";
        obj3.value = "";
        obj4.value = "";

        for(i=0;i<obj2_ary.length;i++){
          if(id!=obj2_ary[i]){
              if (obj2.value==""){
                obj11.innerHTML = obj11.innerHTML +"<a href='#' onclick=\"del('"+obj2_ary[i]+"','"+x+"')\">"+ obj1_ary[i]+"</a>";

                obj1.value = obj1.value + obj1_ary[i];
                obj2.value = obj2.value + obj2_ary[i];
                obj3.value = obj3.value + obj3_ary[i];
                obj4.value = obj4.value + obj4_ary[i];
              }
              else{
                obj11.innerHTML = obj11.innerHTML +",<a href='#' onclick=\"del('"+obj2_ary[i]+"','"+x+"')\">"+ obj1_ary[i]+"</a>";
                obj1.value = obj1.value + "," + obj1_ary[i];
                obj2.value = obj2.value + "," + obj2_ary[i];
                obj3.value = obj3.value + "," + obj3_ary[i];
                obj4.value = obj4.value + "," + obj4_ary[i];
              }
          }
        }
    }

    function clearTeach(){
      document.all.teach1_s.innerHTML = "";
      document.all.teach1.value = "";
      document.all.teach1_ID.value = "";
      document.all.teach1_TIT.value = "";
      document.all.teach1_SORT.value="";
      document.all.teach2_s.innerHTML = "";
      document.all.teach2.value = "";
      document.all.teach2_ID.value = "";
      document.all.teach2_TIT.value = "";
      document.all.teach2_SORT.value="";
    }

    function removeOptions(selectbox) {
        var i;
        for (i = selectbox.options.length - 1; i >= 0; i--) {
            selectbox.remove(i);
        }
    }

    function getBookingRoom(year,class_no,term){
        removeOptions(document.getElementById("booking_room_id"));
        var booking_date = document.getElementById('booking_date').value;

        if(booking_date == ''){
            return false;
        }

        var link = "<?=$link_get_booking_room;?>";
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'booking_date': booking_date,
            'year': year,
            'class_no': class_no,
            'term': term,
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
                    var second = document.getElementById('booking_room_id');
                    for (var i = 0; i < result.length; i++) {
                        var option_name = result[i]['room_name'];
                        var option_value = result[i]['room_id'];
                        var new_option = new Option(option_name, option_value);
                        second.options.add(new_option);
                    }
                }
            }
        });
    }

    function copySchedulePre(){
        var copy_pre_url = "https://dcsdcourse.taipei.gov.tw/base/admin/create_class/set_course/copySchedulePre?c="+document.getElementById('query_class_no').value;
        myW=window.open(copy_pre_url,'copy_pre_url','height=50,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no');
        myW.focus();
    }

    function copyScheduleNew(copyyear,copyterm){
        var year = document.getElementById('query_year').value;
        var class_no = document.getElementById('query_class_no').value;
        var term = document.getElementById('query_term').value;
        var copy_url = "https://dcsdcourse.taipei.gov.tw/base/admin/create_class/set_course/copySchedule?y=" + year + "&c=" + class_no + "&t=" + term + "&cct=" + copyterm + "&ccy=" + copyyear;

        myW=window.open(copy_url,'copy_Course','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,fullscreen=yes');
        myW.focus();
        
    }

    function copySchedule(){
        var copyterm = prompt("請輸入欲複製之期別(同年度、同代碼)");
        if (copyterm!=null && copyterm!=""){
            var year = document.getElementById('query_year').value;
            var class_no = document.getElementById('query_class_no').value;
            var term = document.getElementById('query_term').value;
            var copy_url = "https://dcsdcourse.taipei.gov.tw/base/admin/create_class/set_course/copySchedule?y=" + year + "&c=" + class_no + "&t=" + term + "&cct=" + copyterm;

            myW=window.open(copy_url,'copy_Course','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,fullscreen=yes');
            myW.focus();
        }
    }

    function getOldBookingRoom(year,class_no,term){
        removeOptions(document.getElementById("old_room_use_id"));
        var room_use_date = document.getElementById('old_room_use_date').value;

        if(room_use_date == ''){
            return false;
        }

        var link = "<?=$link_get_room_use;?>";
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'use_date': room_use_date,
            'year': year,
            'class_no': class_no,
            'term': term,
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
                    var second = document.getElementById('old_room_use_id');
                    for (var i = 0; i < result.length; i++) {
                        if(i == 0){
                            var first_room_id = result[i]['room_id'];
                            var first_room_name = result[i]['room_name'];
                            document.getElementById('room_use_date').value = room_use_date;
                            document.getElementById('room_id').value = first_room_id;
                            document.getElementById('room_name').value = first_room_name;
                            document.getElementById('final_course_date').value = room_use_date;
                            document.getElementById('final_room_id').value = first_room_id;
                        }
                        
                        var option_name = result[i]['room_name'];
                        var option_value = result[i]['room_id'];
                        var new_option = new Option(option_name, option_value);
                        second.options.add(new_option);
                    }

                }
            }
        });
    }

    function getCourseTime(){
        if (document.all.selType[0].checked==true){
            if (document.all.booking_date.value == ""){
              alert("請先選擇日期");
              document.all.booking_date.focus();
              return false;
            } else {
                var course_date = document.all.booking_date.value;
                document.getElementById('final_course_date').value = course_date;
                document.getElementById('final_room_id').value = document.all.booking_room_id.value;
            }
        }

        if (document.all.selType[1].checked==true){
            if (document.all.room_use_date.value == ""){
              alert("請先選擇日期");
              document.all.room_use_date.focus();
              return false;
            } else {
                var course_date = document.all.room_use_date.value;
                document.getElementById('final_course_date').value = course_date;
                document.getElementById('final_room_id').value = document.all.room_id.value;
            }
        }

        if (document.all.class_description.checked==true){
            var class_description = "1";
        }
        else{
            var class_description = "0";
        }

        var year = document.getElementById('query_year').value;
        var class_no = document.getElementById('query_class_no').value;
        var term = document.getElementById('query_term').value;
        var f1 = document.getElementById('f1').value;
        var f2 = document.getElementById('f2').value;
        var f3 = document.getElementById('f3').value;
        var f4 = document.getElementById('f4').value;
        var f5 = document.getElementById('f5').value;

        var link = "<?=$link_get_course_time;?>";
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'course_date': course_date,
            'year': year,
            'class_no': class_no,
            'term': term,
            'class_description' : class_description,
            'f1' : f1,
            'f2' : f2,
            'f3' : f3,
            'f4' : f4,
            'f5' : f5,
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
                    document.getElementById('pre_start_time').value = result['from_time'];
                    document.getElementById('pre_end_time').value = result['to_time'];
                }

                console.log(result);
            }
        });
    }

    function check_update(){
        document.getElementById('mode').value = 'check_update';
        var obj = document.getElementById('data-form');
       
        obj.submit();
    }

    function chkHours(){
        var year = document.getElementById('query_year').value;
        var class_no = document.getElementById('query_class_no').value;
        var term = document.getElementById('query_term').value;

        var link = "<?=$link_check_hours;?>";
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'year': year,
            'class_no': class_no,
            'term': term,
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
                    if(result['status'] == 'N'){
                        alert('鐘點時數加總與實體時數不符');
                    } else {
                        alert('檢核正常');
                    }
                }
            }
        });
    }

    function delBookingData(){
        document.getElementById('mode').value = 'delBookingData';
        var obj = document.getElementById('data-form');

        obj.submit();
    }

    function enableFun(x){
      obj1 = document.all.classFirst;
      obj2 = document.all.listCourse;

      clearTeach();
      if (x.checked == true)
      {
        obj1.disabled = false;
        obj2.disabled = true;
        obj2.value = "";
      }
      else
      {
        obj1.disabled = true;
        obj2.disabled = false;
      }
    }


    function addSchedule(){
        if (document.all.class_description.checked == true){
          if (document.all.classFirst.value == ""){
            alert("請先選擇班務說明");
            document.all.classFirst.focus();
            return false;
          }
        } else {
          if (document.all.listCourse.value == ""){
            alert("請先選擇課程");
            document.all.listCourse.focus();
            return false;
          }
        }

        if (document.all.selType[0].checked==true){
            if (document.all.booking_date.value == ""){
              alert("請先選擇日期");
              document.all.booking_date.focus();
              return false;
            } else {
                document.getElementById('final_course_date').value = document.all.booking_date.value;
            }

            if (document.all.booking_room_id.value == ""){
              alert("請先選擇教室");
              document.all.booking_room_id.focus();
              return false;
            } else {
                document.getElementById('final_room_id').value = document.all.booking_room_id.value;
            }
        }

        if (document.all.selType[1].checked==true){
            if (document.all.room_use_date.value == ""){
              alert("請先選擇日期");
              document.all.room_use_date.focus();
              return false;
            } else {
                document.getElementById('final_course_date').value = document.all.room_use_date.value;
            }

            if (document.all.room_name.value == ""){
                alert("請先選擇教室");
                document.all.room_name.focus();
                return false;
            } else {
                document.getElementById('final_room_id').value = document.all.room_id.value;
            }
        }

        if (document.all.hours.value == ""){
            alert("請先輸入鐘點費時數");
            document.all.hours.focus();
            return false;
        }

        var number_start = document.all.pre_start_time.value.split(":");
        var number_end = document.all.pre_end_time.value.split(":");
        var pre_start_time = document.all.pre_start_time.value;
        var pre_end_time = document.all.pre_end_time.value;
        var pre_start_time = pre_start_time.trim();
        var pre_end_time = pre_end_time.trim();

        if (pre_start_time == "" && pre_end_time != ""){
            alert("預排開始時間不可為空");
            document.all.pre_start_time.focus();
            return false;
        } else if((number_start.length == 1 && pre_start_time.length != 4) || (number_start.length == 2 && pre_start_time.length != 5)) {
            alert("預排開始時間格式錯誤");
            document.all.pre_start_time.focus();
            return false;
        }

        if (pre_start_time != "" && pre_end_time == ""){
            alert("預排結束時間不可為空");
            document.all.pre_end_time.focus();
            return false;
        } else if((number_end.length == 1 && pre_end_time.length != 4) || (number_end.length == 2 && pre_end_time.length != 5)) {
            alert("預排結束時間格式錯誤");
            document.all.pre_end_time.focus();
            return false;
        }

        var fsTime = document.all.pre_start_time.value;
        var feTime = document.all.pre_end_time.value;

        fsTime = fsTime.replace(/:/, "");
        feTime = feTime.replace(/:/, "");

        if(fsTime.length==3) {
            fsTime = "0"+fsTime;
        }
        if(feTime.length==3) {
            feTime = "0"+feTime;
        }

        var sNumTime = parseInt(fsTime.substr(0,2))*60+parseInt(fsTime.substr(2,2));
        var eNumTime = parseInt(feTime.substr(0,2))*60+parseInt(feTime.substr(2,2));
        var status5 = true;
        for(var o=fsTime;o<=feTime;o++) {
            if(o>1230&&o<1300) {
              status5 = false;
              break;
            }
        }
        if(status5==false) {
            // alert("上、下午時段請分開建置");
            if(!confirm("上、下午時段請分開建置\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
                return false;
            }
        }
        if(document.all.hours.value==4&&eNumTime-sNumTime<200) {
            if(!confirm("預排時間的上課時數起迄須>=200 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        } else if(document.all.hours.value==3&&eNumTime-sNumTime<150) {
            if(!confirm("預排時間的上課時數起迄須>= 150 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        } else if(document.all.hours.value==2&&eNumTime-sNumTime<90) {
            if(!confirm("預排時間的上課時數起迄須>= 90 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        } else if(document.all.hours.value==1&&eNumTime-sNumTime<50) {
            if(!confirm("預排時間的上課時數起迄須>= 50 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        }

        document.getElementById('mode').value = 'add';
        var obj = document.getElementById('data-form');
        //var room_id=$("#room_id").val();
        //var room_use_date=$("#room_use_date").val();
        //var status=_ajax();
        //if(status){
            //return false;
        //}
        obj.submit();
    }

    /*function _ajax()
    {
    
        var url = '<?=base_url('create_class/set_course/ajax');?>';
        var room_id=$("#room_id").val();
        var room_use_date=$("#room_use_date").val();
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'room_id': room_id,
            'room_use_date': room_use_date
        }

        $.ajax({
            
            type: "POST", //傳送方式
            url: url, //傳送目的地
            dataType: "json", //資料格式
            data:data,
            success: function(message) {

            },
            error: function(message) {
                console.log('error');
            }
                    
        });
    }*/

    function updUse(){
        if (document.all.listCourse.value == ""){
            alert("請先選擇課程");
            document.all.listCourse.focus();
            return false;
        }

        if (document.all.room_name.value == ""){
            alert("請先選擇教室");
            document.all.room_name.focus();
            return false;
        }

        var fsTime = document.all.pre_start_time.value;
        var feTime = document.all.pre_end_time.value;

        fsTime = fsTime.replace(/:/, "");
        feTime = feTime.replace(/:/, "");

        if(fsTime.length==3) {
            fsTime = "0"+fsTime;
        }
        if(feTime.length==3) {
            feTime = "0"+feTime;
        }
        var sNumTime = parseInt(fsTime.substr(0,2))*60+parseInt(fsTime.substr(2,2));
        var eNumTime = parseInt(feTime.substr(0,2))*60+parseInt(feTime.substr(2,2));

        var status5 = true;
        for(var o=fsTime;o<=feTime;o++) {
            if(o>1230&&o<1300) {
              status5 = false;
              break;
            }
        }

        if(status5==false) {
      // alert("上、下午時段請分開建置");
            if(!confirm("上、下午時段請分開建置\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
                return false;
            }
        }
        if(document.all.hours.value==4&&eNumTime-sNumTime<200) {
            if(!confirm("預排時間的上課時數起迄須>=200 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        }
        else if(document.all.hours.value==3&&eNumTime-sNumTime<150) {
            if(!confirm("預排時間的上課時數起迄須>= 150 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        }
        else if(document.all.hours.value==2&&eNumTime-sNumTime<90) {
            if(!confirm("預排時間的上課時數起迄須>= 90 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        }
        else if(document.all.hours.value==1&&eNumTime-sNumTime<50) {
            if(!confirm("預排時間的上課時數起迄須>= 50 分鐘\n按【取消】進行預排時間與時數修正；\n按【確定】維持原輸入時數。")) {
              return false;
            }
        }

        document.getElementById('final_room_id').value = document.all.room_id.value;
        document.getElementById('mode').value = 'upd';
        var obj = document.getElementById('data-form');
        obj.submit();

        updCancel();
    }

    function updCancel(){
      document.all.addbtn.style.display = "";
      document.all.updbtn.style.display = "none";
      document.all.cancelbtn.style.display = "none";

      document.all.selType[0].disabled = false;
      document.all.booking_date.disabled = false;
      document.all.room_use_date.disabled = false;
      document.all.booking_room_id.disabled = false;

      document.all.class_description.disabled = false;
      document.all.class_description.checked = false;

      document.all.per1.value = "";
      document.all.per2.value = "";
      document.all.per3.value = "";
      document.all.per4.value = "";
      document.all.per5.value = "";
      document.all.per6.value = "";
      document.all.per7.value = "";
    }

    function chkHrs(){
      obj = document.all.f4;
      if (obj.value >= 50 && obj.value <= 89){
        document.all.hours.value = "1";
      }
      else if (obj.value >= 90 && obj.value <= 120){
        document.all.hours.value = "2";
      }
      else{
        document.all.hours.value = "";
      }
    }

    function toUpd(course_code,course_date,room_name,room_id,period,period_name,from_time,to_time,teacher_name,teacher_id,teacher_title,assistant_name,assistant_id,assistant_title,hrs,trafic_status){

        /*if(trafic_status=='待確認'){
            alert('已進入請款流程,不得修改,若欲修改請先到請款作業將「處理狀態」回復成「空值」');
            return false;
        }*/

        document.all.addbtn.style.display = "none";
        document.all.updbtn.style.display = "";
        document.all.cancelbtn.style.display = "";

        document.all.listCourse.disabled = false;
        document.all.listCourse.value = course_code;

        document.all.selType[0].disabled = true;
        document.all.selType[1].checked = true;

        document.all.booking_date.disabled = true;
        document.all.booking_room_id.disabled = true;

        document.all.old_room_use_date.disabled = true;
        document.all.old_room_use_date.value = course_date;
        document.all.room_use_date.value = course_date;
        document.all.final_course_date.value = course_date;

        document.all.room_name.value = room_name;
        document.all.room_id.value = room_id;
        document.all.final_room_id.value = room_id;

        document.all.per1.value = period;
        document.all.per2.value = period_name;
        document.all.per3.value = from_time;
        document.all.per4.value = to_time;
        document.all.per5.value = course_date;
        document.all.per6.value = room_id;
        document.all.per7.value = course_code;

        document.all.pre_start_time.value = from_time;
        document.all.pre_end_time.value = to_time;
        
        var obj1_ary = teacher_name.split(",");
        var obj2_ary = teacher_id.split(",");
        var obj3_ary = teacher_title.split(",");
        var t4 = '';

        for(i=0;i<obj1_ary.length;i++){ //填講座
            if(t4==''){
                if (obj3_ary[i]!=""){
                    t4 = t4 +"<a href='#' onclick=\"del('"+obj2_ary[i]+"','addTeach1')\">"+ obj1_ary[i]+"("+obj3_ary[i]+")</a>";
                }
                else{
                    t4 = t4 +"<a href='#' onclick=\"del('"+obj2_ary[i]+"','addTeach1')\">"+ obj1_ary[i]+"</a>";
                }
            }
            else{

                if (obj3_ary[i]!=""){
                    t4 = t4 +",<a href='#' onclick=\"del('"+obj2_ary[i]+"','addTeach1')\">"+ obj1_ary[i]+"("+obj3_ary[i]+")</a>";
                }
                else{
                    t4 = t4 +",<a href='#' onclick=\"del('"+obj2_ary[i]+"','addTeach1')\">"+ obj1_ary[i]+"</a>";
                }

            }
        }

        document.all.teach1_s.innerHTML = t4;
        document.all.teach1.value = teacher_name;
        document.all.teach1_ID.value = teacher_id;
        document.all.teach1_TIT.value = teacher_title;

        var obj1_ary = assistant_name.split(",");
        var obj2_ary = assistant_id.split(",");
        var obj3_ary = assistant_title.split(",");
        var a4 = '';

        for(i=0;i<obj1_ary.length;i++){
            if(a4==''){
                a4 = a4 +"<a href='#' onclick=\"del('"+obj2_ary[i]+"','addTeach2')\">"+ obj1_ary[i]+"</a>";
            }
            else{
                a4 = a4 +",<a href='#' onclick=\"del('"+obj2_ary[i]+"','addTeach2')\">"+ obj1_ary[i]+"</a>";
            }
        }

        document.all.teach2_s.innerHTML = a4;
        document.all.teach2.value = assistant_name;
        document.all.teach2_ID.value = assistant_id;
        document.all.teach2_TIT.value = assistant_title;

        document.all.hours.value = hrs;
    }

    function toDel(x,y,z){
        document.all.delKey1.value = x;
        document.all.delKey2.value = y;
        document.all.delKey3.value = z;
        document.getElementById('mode').value = 'del';
        var obj = document.getElementById('data-form');
        obj.submit();
    }

    $(document).ready(function() {
        chkHrs();
    });

    $(document).ready(function() {
        $("#room_use_date").datepicker();
        $('#datepick2').click(function(){
            $("#room_use_date").focus();
        });
    });

    function confirmSchedule(){
        var tmp = '<?=$form['seq_no'];?>';
        /* myW=window.open('<?=base_url('create_class/print_schedule/mutiPrint2');?>?seq_nos%5B%5D='+tmp,'show_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=750'); */
        myW=window.open('<?=base_url('create_class/set_course/course_sch_app');?>?seq_nos='+tmp,'show_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=1000');
        
        myW.focus();
    }
</script>