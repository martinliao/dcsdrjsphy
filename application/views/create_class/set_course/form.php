<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save_next;?>" enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6">
        <label class="control-label">年度</label>
        <input class="form-control" name="year" value="<?=$form['year'];?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期代碼</label>
        <input class="form-control" name="class_no" value="<?=$form['class_no'];?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期名稱</label>
        <input class="form-control" value="<?=$form['class_name'];?>" disabled>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">期別</label>
        <input class="form-control" name="term" value="<?=$form['term'];?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">開課起日</label>
        <input type="text" class="form-control <?=form_error('start_date1')?'has-error':'';?> datepicker" id="set_start_date1" name="start_date1" value="<?=set_value('start_date1', date('Y-m-d',strtotime($form['start_date1']))); ?>" readonly />
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">開課迄日</label>
        <input type="text" class="form-control <?=form_error('end_date1')?'has-error':'';?> datepicker" id="set_end_date1" name="end_date1" value="<?=set_value('end_date1', date('Y-m-d',strtotime($form['end_date1']))); ?>" readonly />
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">季別</label>
        <input class="form-control" value="<?=$form['reason'];?>" disabled>
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">使用教室</label>
        <!-- <input type="button" class="btn btn-xs btn-primary" onclick="get_room('room_code','room_name')" value="查詢"> -->
        <input type="hidden" class="form-control" id="room_code" name="room_code" value="<?=$form['room_code']?>">
        <input type="text" class="form-control" id="room_name" name="room_name" value="<?=$form['room_name']?>" readonly>
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">帶班承辦人</label>
        <input class="form-control" name="worker" id="worker" value="<?=$form['worker_name'];?>" disabled>
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">總時數</label>
        <input class="form-control" name="range" id="range" value="<?=$form['range'];?>" style="width:89%;float:left">
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">實體時數</label>
        <input class="form-control" name="range_real" id="range_real" value="<?=!empty($form['range_real'])?$form['range_real']:$form['range'];?>" style="width:88%;float:left">
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">線上時數</label>
        <input class="form-control" name="range_internet" id="range_internet" value="<?=!empty($form['range_internet'])?$form['range_internet']:0;?>" style="width:88%;float:left">
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">退訓標準1</label>
        <?php
            if(empty($form['quit_class'])){
                $form['quit_class'] = 5;
            }
            echo form_dropdown('quit_class', $choices['quit_class'], set_value('quit_class', $form['quit_class']), "class='form-control' style='width:97%;float:right'");
        ?>
        <font style="font-size: 26px;float: left">1/</font>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">退訓標準2<font style="color: blue">(請假時數大於退訓標準(<?=$choices['quit_class_hours'];?>小時)者退訓)</font></label>
        <?php
            echo form_dropdown('quit_class2', $choices['quit_class2'], set_value('quit_class2', $form['quit_class2']), "class='form-control' style='width:92%;float:left'");
        ?>
        <font style="font-size: 26px;float: right">小時</font>
    </div>
    <div class="tab-pane col-xs-12" id="file_content">
        <label class="control-label">上傳檔案</label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="10%">列序</th>
                    <th width="80%">檔案</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $rows = 0;
                    for ($i=0;$i<count($form['myfile']);$i++) {
                        $file_path = base_url("files/upload_require/{$form['myfile'][$i]['file_path']}"); 
                        $rows = $i+1;
                        echo '<tr>';
                        echo '<td>'.$rows.'</td>';
                        echo '<td><a href="'.$file_path.'">'.$form['myfile'][$i]['file_path'].'</a></td>';
                        echo '<td align="right">';
                        echo '<button type="button" class="btn btn-danger btn-sm" id="remove_'.$rows.'" onclick="removeItem2(this, '.$rows.','.'\''.$form['myfile'][$i]['file_path'].'\''.')">刪除</button>';
                        echo '</tr>';
                    }
                ?>
                <tr>
                    <td><?=$rows+1?></td>
                    <td><input type="file" name="myfile[]" class="button" accept=".odt,.ods,.odp,.docx,.xlsx,.pptx,.doc,.xls,.ppt,.zip,.rar,.jpg,.png,.gif,.pdf"></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right"><button type="button" class="btn btn-success btn-sm" onclick="addFile()">更多附加檔案</button></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="form-group col-xs-3 required <?=form_error('class_attribute')?'has-error':'';?>">
        <label class="control-label">班期性質</label>
        <?php
            echo form_dropdown('class_attribute', $choices['class_attribute'], set_value('class_attribute', $form['class_attribute']), "class='form-control' id='class_attribute'");
        ?>
        <?=form_error('class_attribute'); ?>
    </div>
    <div class="form-group col-xs-9 required <?=form_error('ecpa_class_id')?'has-error':'';?>" >
        <label class="control-label">終身學習類別代碼<a href="/base/admin/files/example_files/planning/ecpa.pdf" target="_blank">(查詢終身學習代碼表)</a></label>
        <span style="width: 70%;float:right"></span>
        <input class="form-control" id="ecpa_class_id" name="ecpa_class_id" placeholder="" value="<?=set_value('ecpa_class_id', $form['ecpa_class_id']); ?>" onblur="showEcpaClassName(this.value)" style="width: 6%;float:left">
        <input class="form-control" id="ecpaName" value="<?=set_value('ecpa_class_name', $form['ecpa_class_name']); ?>" style="width: 93%;float:right;font-size: 14px" disabled="">
        <?=form_error('ecpa_class_id'); ?>
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">分時數上傳</label>
        <br>
        <input class="btn btn-primary" type="button" value="設定時數" onclick="cut_hour(<?=$form['year'];?>,'<?=$form['class_no'];?>',<?=$form['term'];?>)">
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">志工系統</label>
        <div>
            <div class="checkbox-inline">
                <label>
                    <input id="is_volunteer" type="checkbox" value="1" name="is_volunteer" <?=$form['is_volunteer']=='1'?'checked':''?>>
                    <span>是否需要志工</span>
                </label>
            </div>
            <div class="checkbox-inline">
                <label>
                    <input id="is_longrange" type="checkbox" value="1" name="is_longrange" <?=$form['is_longrange']=='1'?'checked':''?>>
                    <span>是否長期班</span>
                </label>
            </div>
            <div class="checkbox-inline">
                <label>
                    <a href="https://elearning.taipei/eda/volunteer_transfer.php" target="_blank">立即轉入志工系統</a>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group col-xs-4">
        <label class="control-label">問卷網址</label>
        <input class="form-control" name="question_addr" id="question_addr" value="<?=$form['question_addr'];?>">
    </div>
    <div class="tab-pane col-xs-12">
        <label class="control-label">全教代碼<a href="http://www1.inservice.edu.tw/script/GetSetting.aspx" target="_blank">(查詢全教代碼)</a></label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="22%">全教課程類別代碼 (1-3碼)</th>
                    <th width="22%">全教課程類別細項代碼 (1-3碼)</th>
                    <th width="22%">全教課程類別科目代碼 (1-3碼)</th>
                    <th width="30%">全教研習進修範疇細項 (13碼)</th>
                    <th width="4%"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input class="form-control" id="env_r1" name="env_r1" placeholder="" value="<?=set_value('env_r1', $form['env_r1']); ?>"></td>
                    <td><input class="form-control" id="env_r2" name="env_r2" placeholder="" value="<?=set_value('env_r2', $form['env_r2']); ?>"></td>
                    <td><input class="form-control" id="env_r3" name="env_r3" placeholder="" value="<?=set_value('env_r3', $form['env_r3']); ?>"></td>
                    <td><input class="form-control" id="env_r4" name="env_r4" placeholder="" value="<?=set_value('env_r4', $form['env_r4']); ?>"></td>
                    <td><button type="button" class="btn btn-success btn-sm" onclick="copyEduCode()" <?=$form['term']=='1'?'disabled':'';?>>複製當年度同班期代碼</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="form-group col-xs-12">
        <label class="control-label">考核方式</label>
        <div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type1" type="checkbox" value="1" name="type1" <?=set_checkbox('type2', '1', $form['type1']=='1'?TRUE:FALSE);?>>
                    <span>測驗</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type2" type="checkbox" value="1" name="type2" <?=set_checkbox('type2', '1', $form['type2']=='1'?TRUE:FALSE);?>>
                    <span>書面報告</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type3" type="checkbox" value="1" name="type3" <?=set_checkbox('type3', '1', $form['type3']=='1'?TRUE:FALSE);?>>
                    <span>成果發表</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type4" type="checkbox" value="1" name="type4" <?=set_checkbox('type4', '1', $form['type4']=='1'?TRUE:FALSE);?>>
                    <span>實作演練</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type5" type="checkbox" value="1" name="type5" <?=set_checkbox('type5', '1', $form['type5']=='1'?TRUE:FALSE);?>>
                    <span>心得分享</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type6" type="checkbox" value="1" name="type6" <?=set_checkbox('type6', '1', $form['type6']=='1'?TRUE:FALSE);?>>
                    <span>案例研討</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="type7" type="checkbox" value="1" name="type7" <?=set_checkbox('type7', '1', $form['type7']=='1'?TRUE:FALSE);?>>
                    <span>意見交流</span>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label"><input type="checkbox" value="">其他</label>
                <input class="form-control" id="type8" name="type8" placeholder="" value="<?=set_value('type8', $form['type8']); ?>">
                <?=form_error('type8'); ?>
            </div>
        </div>
    </div>
    
    <div class="form-group col-xs-12">
        <label class="control-label">註解</label>
        <textarea class="form-control" name="memo"><?=$form['memo']?></textarea>
    </div>
    <div class="form-group col-xs-12">
        <label class="control-label">承辦班期注意事項</label>
        <textarea class="form-control" name="note"><?=$form['note']?></textarea>
    </div>
    <div class="tab-pane col-xs-12" id="course_schedule_content">
        <label class="control-label">課程表上傳<font color="red">(限JPG或PNG格式，英文檔名)</font></label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="90%">檔案</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($form['course_schedule_file_path'])) { ?>
                <?php $schedule_url = base_url("files/upload_course_schedule/{$form['course_schedule_file_path']}"); ?>
                <tr>
                    <td><a href="<?=$schedule_url?>" target="_blank"><?=$form['course_schedule_file_path'];?></a><td>
                    <td><button type="button" class="btn btn-danger btn-sm" id="remove_schedule" onclick="removeSchedule(this)">刪除</button></td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td><input type="file" name="course_schedule_file" class="button" accept=".jpg,.png"></td>
                    <td></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="tab-pane col-xs-12" id="mix_content">
        <label class="control-label">混成公告</label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="20%">公告至e大混成專區</th>
                    <th width="40%">公告起日</th>
                    <th width="40%">公告迄日</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" id="notice_elearn" name="notice_elearn" value="1" placeholder="" <?=set_checkbox('notice_elearn', '1', $form['notice_elearn']=='1'?TRUE:FALSE);?> style="zoom:150%"></td>
                    <td>
                        <div class="input-group" id="notice_start">
                            <input type="text" class="form-control <?=form_error('notice_start')?'has-error':'';?> datepicker" id="set_notice_start" name="notice_start" value="<?=set_value('notice_start', $form['notice_start']); ?>"/>
                            <span class="input-group-addon" style="cursor: pointer;" ><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group" id="notice_end">
                            <input type="text" class="form-control <?=form_error('end_date1')?'has-error':'';?> datepicker" id="set_notice_end" name="notice_end" value="<?=set_value('notice_end', $form['notice_end']); ?>"/>
                            <span class="input-group-addon" style="cursor: pointer;" ><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="tab-pane col-xs-12" id="online_course">
        <input type="hidden" name="hidStr" id="hidStr" value="" disabled />
        <label class="control-label">線上課程</label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="15%">起日</th>
                    <th width="15%">迄日</th>
                    <th width="30%">線上課程名稱</th>
                    <th width="5%">時數</th>
                    <th width="10%">講座名稱</th>
                    <th width="20%">上課地點</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <tbody>
            <?php 
                if(isset($form['online_course']) && !empty($form['online_course'])){
                    for ($i=0;$i<count($form['online_course']);$i++) { 
                        $rows = $i+1;
                        $form['online_course'][$i]['start_date'] = !empty($form['online_course'][$i]['start_date'])?date("Y-m-d",strtotime($form['online_course'][$i]['start_date'])):'';
                        $form['online_course'][$i]['end_date'] = !empty($form['online_course'][$i]['end_date'])?date("Y-m-d",strtotime($form['online_course'][$i]['end_date'])):'';
                        echo '<tr>';
                        echo '<td><input class="form-control" type="text" name="r_start_date[]" id="r_start_date[]" value="'.$form['online_course'][$i]['start_date'].'" onchange="check_date(this)"></td>';
                        echo '<td><input class="form-control" type="text" name="r_end_date[]" id="r_end_date[]" value="'.$form['online_course'][$i]['end_date'].'" onchange="check_date(this)"></td>';
                        echo '<td><input class="form-control" type="text" name="online_course_name[]" id="online_course_name[]" value="'.$form['online_course'][$i]['class_name'].'"></td>';
                        echo '<td><input class="form-control" type="text" name="hours[]" id="hours[]" value="'.$form['online_course'][$i]['hours'].'"></td>';
                        echo '<td><input class="form-control" type="text" name="teacher_name[]" id="teacher_name[]" value="'.$form['online_course'][$i]['teacher_name'].'"></td>';
                        echo '<td><input class="form-control" type="text" name="place[]" id="place[]" value="'.$form['online_course'][$i]['place'].'"><input type="hidden" value="'.$form['online_course'][$i]['elearn_id'].'" name="elrid[]" id="elrid[]"></td>';
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
    <div class="tab-pane col-xs-12" id="questions">
        <label class="control-label">課前提問</label>
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="15%">起日</th>
                    <th width="15%">迄日</th>
                    <th width="35%">提問問題</th>
                    <th width="25%">提問結果</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php if(isset($form['preq_main'][0]['start_date']) && isset($form['preq_main'][0]['end_date'])){ ?>
                    <td><?=date('Y-m-d',strtotime($form['preq_main'][0]['start_date']))?></td>
                    <td><?=date('Y-m-d',strtotime($form['preq_main'][0]['end_date']))?></td>
                    <td><a href="<?=$link_preq_enter?>" target="_blank"><?=$form['preq_item']?></a></td>
                    <td><a href="<?=$link_preq_export?>" target="_blank"><?=$form['preq_count']?></a></td>
                    <td></td>
                    <?php } else { ?>
                    <?php } ?>
                <tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" align="right"><a href="<?=$link_preq?>" target="_blank"><button type="button" class="btn btn-success btn-sm">提問設定</button></a></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="form-group col-xs-12">
        <label class="control-label">特殊情況</label>
        <div>
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="Y" name="not_hourfee" <?=$form['not_hourfee']=='Y'?'checked':''?>>
                    <span>無須支應講座鐘點費</span>
                </label>
            </div>
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="Y" name="not_location" <?=($form['not_location']=='Y')?'checked':''?>>
                    <span>上課地點非公訓處</span>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label"><input type="checkbox" name="special_status" value="9" <?=($form['special_status']=='9')?'checked':''?>>其他(請敘明)</label>
                <input class="form-control" id="special_status_other" name="special_status_other" placeholder="" value="<?=set_value('special_status_other', $form['special_status_other']); ?>">
            </div>
        </div>
        <?=form_error('special_status'); ?>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期課程建檔</label>
        <select class="form-control" name="listCourse" id="listCourse" size="10">
            <?php 
                if($page_name == 'edit' && isset($form['course_list']) && !empty($form['course_list'])) { 
                    foreach ($form['course_list'] as $key => $value) {
                        echo '<option value="'.$value['course_code'].'">'.$value['course_code'].'-'.$value['name'].'</option>';
                    }
                }
            ?>
        </select>
        <?=form_error('class'); ?>
    </div>
    <div class="form-group col-xs-6" style="margin-top: 2%">
        <p style="color: red">上方資訊如有增修，請先點儲存至下一步，再複製前期課程內容</p>
        <input type="button" class="btn btn-xs btn-primary" onclick="show_course('<?=$u_id?>')" value="新增">
        <input type="button" class="btn btn-xs btn-primary" onclick="delCS()" value="刪除">
        <input type="button" class="btn btn-xs btn-primary" onclick="import_csv('<?=$u_id?>')" value="匯入">
        <input type="button" class="btn btn-xs btn-primary" onclick="copyFun()" value="複製">
        <input type="hidden" id="copy_status" name="copy_status" value="">
        <br>
        <br>
        <label class="control-label text-inline">年度</label>
        <input type="text" id="copy_year" name="copy_year" value="" style="width: 10%">
        <label class="control-label text-inline">班期代碼</label>
        <input type="text" id="copy_class_no" name="copy_class_no" value="<?=$form['class_no']?>" style="width: 15%">
        <label class="control-label text-inline">期別</label>
        <input type="text" id="copy_term" name="copy_term" value="" style="width: 10%">
    </div>
    <div id="hidden_list">
        <input type="hidden" name="course" id="addCourse" value="<?=$form['course']?>">
        <input type="hidden" name="del_file" id="del_file" value="">
        <input type="hidden" name="del_schedule" id="del_schedule" value="">
        <input type="hidden" name="not_next" id="not_next" value="">
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        // $( "#start_date1" ).click(function() {
        //     $("input#set_start_date1").trigger("focus");
        // });

        // $( "#end_date1" ).click(function() {
        //     $("input#set_end_date1").trigger("focus");
        // });

        $( "#notice_start" ).click(function() {
            $("input#set_notice_start").trigger("focus");
        });

        $( "#notice_end" ).click(function() {
            $("input#set_notice_end").trigger("focus");
        });

    });

    function checkSave(){
        if(document.getElementById('class_attribute').value == '2'){
            if(document.getElementById('range_internet').value == ''){
                alert('請輸入線上時數');
                return false;
            }

            if($('#online_course table tbody tr').size() == 0){
                alert('線上課程至少1門');
                return false;
            }
        }

        document.getElementById('not_next').value = '';

        var obj = document.getElementById('data-form');
        obj.submit();
    }

    function checkSaveNotNext(){
        if(document.getElementById('class_attribute').value == '2'){
            if(document.getElementById('range_internet').value == ''){
                alert('請輸入線上時數');
                return false;
            }

            if($('#online_course table tbody tr').size() == 0){
                alert('線上課程至少1門');
                return false;
            }
        }

        document.getElementById('not_next').value = 1;

        var obj = document.getElementById('data-form');
        obj.submit();
    }

    function get_room(field1,field2) {
        var path = '../../../../co_room_popup.php?mode=2&field1='+field1+'&field2='+field2;

        window.open(path,'get_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
    }

    var cnt = <?=$rows+1?>;
    function addFile() {
        var num = $('#file_content table tbody tr').size();
        var html = '';
        html += '<tr>';
        html += '<td>';
        html += ++cnt;
        html += '</td>';
        html += '<td>';
        html += '<input type="file" name="myfile[]" class="button" accept=".odt,.ods,.odp,.docx,.xlsx,.pptx,.doc,.xls,.ppt,.zip,.rar,.jpg,.png,.gif,.pdf">';
        html += '</td>';
        html += '<td align="right">';
        html += '<button type="button" class="btn btn-danger btn-sm" id="remove_'+ num +'" onclick="removeItem(this, '+ num +')">刪除</button>';
        html += '</td>';
        html += '</tr>';
        $('#file_content table tbody').append(html);
    }

    function removeItem(obj, num) {
        var cnt = $('#file_content table tbody tr').size();
        
        $(obj).closest('tr').remove();
    }

    function removeItem2(obj, num, file_name) {
        document.getElementById('del_file').value = document.getElementById('del_file').value + file_name + ',';
        var cnt = $('#file_content table tbody tr').size();
        
        $(obj).closest('tr').remove();
    }

    function removeSchedule(obj) {
        document.getElementById('del_schedule').value = 1;
        
        $(obj).closest('tr').remove();
        addSchedule();
    }

    function addSchedule() {
        var html = '';
        html += '<tr>';
        html += '<td>';
        html += '<input type="file" name="course_schedule_file" class="button">';
        html += '</td>';
        html += '<td>';
        html += '</td>';
        html += '</tr>';
        $('#course_schedule_content table tbody').append(html);
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
                    document.getElementById('ecpaName').value = response;
                }
            }
        });
    }

    function cut_hour(year,class_no,term){
        var total_hour = document.getElementById("range_real").value;

        if(!total_hour.trim()){
            alert("總時數不能空");
        } else {
            window.open('../../../../cut_hour.php?year=' + year + "&class_no=" + class_no + "&term=" + term + "&total_hour=" + total_hour, 'cuthour', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
        }
      
    }

    function copyEduCode(){
        document.getElementById('env_r1').value = <?=$form['new_env_r1'];?>;
        document.getElementById('env_r2').value = <?=$form['new_env_r2'];?>;
        document.getElementById('env_r3').value = <?=$form['new_env_r3'];?>;
        document.getElementById('env_r4').value = <?=$form['new_env_r4'];?>;
    }

    function show_course(u_id){
        var path = '../../../../co_course_popup.php?type=A&u_id='+u_id;

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

    function import_csv(u_id){
        var path = '../../../../course_import.php?uid='+u_id;
        var myW=window.open(path,'selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
        myW.focus();
    }

    function copyFun(){
        var re =  /^[0-9]*[1-9][0-9]*$/;
        var year = document.getElementById('copy_year').value;
        var class_no = document.getElementById('copy_class_no').value;
        var term = document.getElementById('copy_term').value;
　
        if(!re.test(year)){
            alert('請輸入正確年度');
            return false;
        }

        if(class_no.trim() == ''){
            alert('請輸入正確班期代碼');
            return false;
        }

        if(!re.test(term)){
            alert('請輸入正確期別');
            return false;
        }

        var obj = document.getElementById('data-form');
        document.getElementById('copy_status').value = 'copy_course';

        obj.submit();
    }

    function check_date(obj){
        startdate = obj.value;
        if(startdate!=""){
            if ((IsDate(startdate.replace(/\//g,"-")))==false){
                alert("日期格式錯誤!");
                obj.value="";
                return false;
            }
        }
    }

    function IsDate(str){
        var re = /^\d{4}-\d{1,2}-\d{1,2}$/;
        if(re.test(str)){
            var array = str.split('-');
            var date = new Date(array[0], parseInt(array[1], 10) - 1, array[2]);
            if(!((date.getFullYear() == parseInt(array[0], 10))
                && ((date.getMonth() + 1) == parseInt(array[1], 10))
                && (date.getDate() == parseInt(array[2], 10))))
            {
                return false;
            }
            return true;
       }
       return false;
    }

    function openCourSeltor() {
        window.open("../../../../elearnQuery.php",'selbFee','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=580,width=600');
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
            html += '<input class="form-control" type="text" name="r_start_date[]" id="r_start_date[]" value="" onchange="check_date(this)">';
            html += '</td>';
            html += '<td>';
            html += '<input class="form-control" type="text" name="r_end_date[]" id="r_end_date[]" value="" onchange="check_date(this)">';
            html += '</td>';
            html += '<td>';
            html += '<input class="form-control" type="text" name="online_course_name[]" id="online_course_name[]" value="'+getContent[1]+'">';
            html += '</td>';
            html += '<td>';
            html += '<input class="form-control" type="text" name="hours[]" id="hours[]" value="'+getContent[2]+'">';
            html += '</td>';
            html += '<td>';
            html += '<input class="form-control" type="text" name="teacher_name[]" id="teacher_name[]" value="">';
            html += '</td>';
            html += '<td>';
            html += '<input class="form-control" type="text" name="place[]" id="place[]" value="臺北e大">';
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
</script>