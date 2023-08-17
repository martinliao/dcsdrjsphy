<style>
    .inputlabel{
        min-width: 100px;
        text-align: right;
    }
    table th{
        text-align:center;
    }
    table td{word-break: keep-all;}    
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-horizontal">
                        <input type="hidden" name="sort" value="" />
                        <div class="form-group">
                            <label class="control-label col-sm-1">季別</label>
                            <div class="col-sm-2">
                            <?php
                                echo form_dropdown('season', $choices['season'], $filter['season'], 'class="form-control" onchange="select_season()"');
                            ?>
                            </div>
                            <label class="control-label col-sm-1">起日</label>
                            <div class="input-group col-sm-2" id="start_date" style="float:left;padding-right: 15px;padding-left: 15px">
                                <input type="text" class="form-control datepicker" value="<?=$filter['start_date'];?>" id="datepicker1" name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>   
                            <label class="control-label col-sm-1">迄日</label>
                            <div class="input-group col-sm-2" id="end_date" style="float:left;padding-right: 15px;padding-left: 15px">
                                <input type="text" class="form-control datepicker" value="<?=$filter['end_date'];?>" id="datepicker3" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                            </div> 
                        </div>                           

                        <div class="form-group">
                            <label class="control-label col-sm-1">班期代碼</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no'];?>">
                            </div>
                            <label class="control-label col-sm-1">班期名稱</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name'];?>">
                            </div>
                        </div>    
                        <div class="form-group">
                            <label class="control-label col-sm-1">排序</label>
                            <div class="col-sm-2">
                            <?php
                                echo form_dropdown('sort', $choices['sort'], $filter['sort'], 'class="form-control"');
                            ?>
                            </div>
                            <label class="control-label col-sm-1">顯示筆數</label>
                            <div class="col-sm-1">
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" style="width:120px"');
                                ?>
                            </div>                            
                        </div>
                        <div class="col-xs-6" >
                            <input type="checkbox" style="height: auto;" name="checkAllClass" value="on" <?= isset($filter['checkAllClass']) && $filter['checkAllClass']=='on'?'checked':'';?>>
                            <label class="control-label">查詢所有班期</label>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <a class="btn btn-info btn-sm" onclick="$('#list-form').submit()">合併列印課表</a>
                            <button class="btn btn-info btn-sm" name="isexcel" value="1">匯出</button>
                        </div>

                    </form>
                </div>
                <div style="margin-top: 5px;">
                <form id="list-form" target="print_popup" action="<?=base_url("create_class/print_schedule/mutiPrint#")?>" onsubmit="return muti_print()">
                    <div class="table-responsive">
                    <!-- <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" /> -->
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr bgcolor="#8CBBFF">
                                <th>系列</th>
                                <th>年度</th>
                                <th>班期<br>代碼</th>
                                <th>班期名稱</th>
                                <th>期別</th>
                                <th>班期<br>性質</th>
                                <th>帶班<br>完成</th>
                                <th>開班起日</th>
                                <th>開班迄日</th>
                                <th>期程</th>
                                <th>教室</th>
                                <th>預計人數</th>
                                <th>實招人數</th>
                                <th>擬評估<br>講師</th>
                                <th>名冊</th>
                                <th>課表</th>
                                <th>e大研習紀錄</th>
                                <th>Mail<br>給<br>老師</th>
                                <th>Mail<br>給<br>人事</th>
                                <th>異動否</th>
                                <th>Mail<br>給<br>學員</th>
                                <th>Mail<br>給<br>單位承辦人</th>
                                <th>Mail<br>給<br>未錄取</th>
                                <th>Mail<br>報名資訊</th>
                                <th>合併印否</th>
                                <th>取消<br>開班</th>
                                <th>進度查詢</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$class_type[$row['type']];?></td>
                                <td><?=$row['year'];?></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['term'];?></td>
                                <td>
                                    <?php if ($row['is_assess'] == '1' && $row['is_mixed'] == '1') { ?>
                                        <font  color="red">混成</font>
                                        <?php }else{ ?>
                                            <?php if ($row['is_assess'] == '1') { ?>
                                            <font  color="red">考核</font>
                                            <?php } ?>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['isend'] == 'Y') { ?>
                                        是
                                    <?php }else{ ?>
                                        否-
                                        <?php if ($row['is_cancel'] == '1') { ?>
                                        設
                                        <?php }else{ ?>
                                        <a href='javascript:void(0);' onclick='go_ClassTeacherStatic_update01("<?=$row['year']?>","<?=$row['class_no']?>","<?=$row['term']?>","Y","<?=$row['sqno']?>","<?=$row['worker']?>")'  >設</a>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td><?=$row['start_date1_format'];?></td>
                                <td><?=$row['end_date1_format'];?></td>
                                <td><?=$row['range'];?></td>
                                <td><?=$row['sname'];?></td>
                                <td><?=$row['no_persons'];?></td>
                                <td><?=$row['true_count'];?></td>
                                <td>
                                    <?php if ($row['ISEVALUATE'] == 'Y' && $row['teacher_assess_count'] > 0) { ?>
                                    <a href='<?=base_url('create_class/progress/setEvaluationTeacher/'.$row['seq_no'].'')?>'><?=$row['teacher_count'];?></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['student_count'] > '0') { ?>
                                    <a href='#' onclick='go_schedule_Register_update("<?=$row['year']?>","<?=$row['class_no']?>","<?=$row['term']?>","<?=$uid?>")'>名冊</a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['course_count'] > '0') { ?>
                                    <a href='#' onclick='go_schedule_undertake_update("<?=$row["seq_no"]?>")'>課表</a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['is_mixed'] > '0') { ?>
                                        是
                                        <a href="<?=base_url('create_class/progress/onlineRecord?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'])?>">設</a>  
                                    <?php } ?>

                                </td>
                                <td>
                                    <?php if ($row['course_count'] > '0') { ?>
                                        <?php if ($row['mail_teacher_count'] > '0') { ?>
                                        是
                                        <?php }else{ ?>
                                        否
                                        <?php } ?>
                                        <?php if ($row['is_cancel'] == '1') { ?>
                                        <a href="<?=base_url('create_class/progress/mail_select/2?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'])?>">設</a>
                                        <?php }else{ ?>
                                        <a href="<?=base_url('create_class/progress/mail_select/2?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'])?>">設</a>
                                        <?php } ?>
                                    <?php } ?>
                                </td>

                                <td>
                                    <?php if ($row['student_count'] > '0') { ?>
                                        <?php if ($row['mail_mag_count'] > '0') { ?>
                                        是
                                        <?php }else{ ?>
                                        否
                                        <?php } ?>
                                        <?php if ($row['is_cancel'] == '1') { ?>
                                        設
                                        <?php }else{ ?>
                                        <a href="<?=base_url('create_class/progress/mail_select/3?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'])?>">設</a>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['sd_modify'] == '1') { ?>
                                        是
                                    <?php }else{ ?>
                                        否
                                    <?php } ?>

                                    <?php if ($row['is_cancel'] == '1') { ?>
                                        設
                                    <?php }else{ ?>
                                        <a href="<?=base_url('create_class/progress/student_change_setting?class_no='.$row['class_no'].'&year='.$row['year'].'&term='.$row['term'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'])?>">設</a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if($row['student_count']>0){?>
                                        <?php if ($row['mail_student_count'] > '0') { ?>
                                        是
                                        <?php }else{ ?>
                                        否
                                        <?php } ?>
                                        <?php if ($row['is_cancel'] == '1') { ?>
                                        設
                                        <?php }else{ ?>
                                        <a href="<?=base_url('create_class/progress/mail_select/1?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'])?>">設</a>
                                        <?php } ?>
                                    <?php }?>
                                </td>
                                <td>
                                    <?php if ($row['CONTACTOR_EMAIL'] != '') { ?>
                                        <?php if ($row['mail_undertaker_count'] > '0') { ?>
                                        是
                                        <?php }else{ ?>
                                        否
                                        <?php } ?>
                                        <?php if ($row['is_cancel'] == '1') { ?>
                                        設
                                        <?php }else{ ?>
                                            <a href="<?=base_url("create_class/progress/mail_to/4?class_no=".$row['class_no']."&year=".$row['year']."&term=".$row['term']."&email=".$row['CONTACTOR_EMAIL'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass']);?>">設</a>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['mail_norecd_count'] > '0') { ?>
                                    是
                                    <?php }else{ ?>
                                    否
                                    <?php } ?>

                                    <?php if ($row['is_cancel'] == '1') { ?>
                                    設
                                    <?php }else{ ?>
                                    <a href="<?=base_url('create_class/progress/mail_select/9?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'])?>">設</a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['mail_adm_b_count'] > '0') { ?>
                                    是
                                    <?php }else{ ?>
                                    否
                                    <?php } ?>

                                    <?php if ($row['is_cancel'] == '1') { ?>
                                    設
                                    <?php }else{ ?>
                                    <a href="<?=base_url('create_class/progress/mail_select/10?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'])?>">設</a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <input type='checkbox' id='chkAdd_Print[]' name='seq_nos[]' value='<?=$row['seq_no']?>'>
                                </td>
                                <td>
                                    <?php if ($row['is_cancel'] == '1') { ?>
                                    是-
                                    <a href="<?=base_url('create_class/progress/mail_select/8?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'].'&status=0')?>"
                                        onclick="return resetCancel()">設</a>
                                    <?php }else{ ?>
                                    否-
                                    <a href="<?=base_url('create_class/progress/mail_select/8?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'].'&start_date='.$filter['start_date'].'&end_date='.$filter['end_date'].'&class_name='.$filter['class_name']."&checkAllClass=".$filter['checkAllClass'].'&status=1')?>"
                                                onclick='return cancelNew("<?=$row['year']?>","<?=$row['class_no']?>","<?=$row['term']?>")'>設</a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href='<?=base_url('create_class/progress/query_schedule?year='.$row['year'].'&term='.$row['term'].'&class_no='.$row['class_no'])?>' target="_blank">進度</a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    </div>
                </form>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>

<script>
$(document).ready(function() {
    $("#datepicker3").datepicker();
    $('#datepicker4').click(function(){
        $("#datepicker3").focus();
    });
    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
        $("#datepicker1").focus();
    });
});

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>


function go_ClassTeacherStatic_update01(strYEAR,strCLASS_NO,strTERM,strISEND,strREASON,strLOGINNAME){
    end_date=document.getElementById("datepicker3").value;
    start_date=document.getElementById("datepicker1").value;

    var link = "<?=$link_get_end_status;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'year': strYEAR,
        'class_no': strCLASS_NO,
        'term': strTERM,
        'is_end':strISEND
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
            console.log(response);
            if (response == 'OK') {
                alert('帶班完成');
                location.reload();
            } else if(response == '12B'){
                alert('請先完成17B學員成績建檔設定');
            } else if(response == 'onlineApp_error'){
                alert('更新學員狀態發生錯誤');
            } else if(response == 'require_error'){
                alert('更新班期狀態發生錯誤');
            } else {
                alert('更新過程中發生錯誤');
            }
        }
    });
    

}

function go_schedule_Register_update(strYEAR,strCLASS_NO,strTERM,uid){
    var myW=window.open ('<?=base_url("student_list_pdf.php")?>?uid='+uid+'&year='+strYEAR+'&class_no='+strCLASS_NO+'&term='+strTERM+'&tmp_seq=0&ShowRetirement=1', 'newwindow', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function go_schedule_undertake_update(seq_no){
     var myW=window.open ('<?=base_url("create_class/print_schedule/print/")?>' + seq_no + '#', 'newwindow', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
     myW.focus();
}

function cancelNew(strYEAR,strCLASS_NO,strTERM){
    if (confirm('是否確定取消開班?')){
        var exe_status = false;
        var link = "<?=$link_check_status;?>";
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'year': strYEAR,
            'class_no': strCLASS_NO,
            'term': strTERM,
        }

        $.ajax({
            url: link,
            data: data,
            dataType: 'text',
            async: false,
            type: "POST",
            error: function(xhr) {
                alert('Ajax request error');
            },
            success: function(response) {
                if (response == 'OK') {
                    if(confirm('系統將刪除所佔教室(但保留課表)後取消開班?')){
                        alert("請mail給老師、人事、學員，\n通知本班期取消開班!!");
                        exe_status = true;
                    }
                } else {
                    if (response == 'WA') {
                        alert('請先刪除待確認及流水號，才能取消開班');
                    } else if(response == 'W'){
                        alert('請先刪除待確認，才能取消開班');
                    } else if(response == 'A'){
                        alert('請先流水號，才能取消開班');
                    } else {
                        alert('發生異常');
                    }
                }
            }
        });

        return exe_status;
        // alert("請mail給老師、人事、學員，\n通知本班期取消開班!!");
        // return true;
    }else{
        return false;
    }  
}

function cancel(){
    if (confirm('是否確定取消開班?')){
        alert("請mail給老師、人事、學員，\n通知本班期取消開班!!");
        return true;
    }else{
        return false;
    }  
}
function resetCancel(){
if (confirm('是否確定重設取消開班?')){
        //alert("請mail給老師、人事、學員，\n通知本班期取消開班!!");
        return true;
    }else{
        return false;
    }  
}



function muti_print(){
    if($('input[name="seq_nos[]"]:checked').length == 0){
        alert("請勾選要合併列印的班期");
        return false;
    }
    window.open('','print_popup','width=1000,height=800');
}

function select_season(){
    var season = $("select[name=season]")[0].value;
    var d = new Date();
    //alert(d.getFullYear());
    year=d.getFullYear();
    switch (season) {
        case "1":
            $("#datepicker1")[0].value = year+"-01-01";
            $("#datepicker3")[0].value = year+"-03-31";
            break;
        case "2":
            $("#datepicker1")[0].value = year+"-04-01";
            $("#datepicker3")[0].value = year+"-06-30";        
            break;
        case "3":
            $("#datepicker1")[0].value = year+"-07-01";
            $("#datepicker3")[0].value = year+"-09-30";        
            break;
        case "4":
            $("#datepicker1")[0].value = year+"-10-01";
            $("#datepicker3")[0].value = year+"-12-31";        
            break;                              
        default:
            break;
    }
}

</script>


