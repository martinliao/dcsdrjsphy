<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<?php if($page_name == 'add'){ ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>" onsubmit="return checkForm(this);">
<?php } else if($page_name == 'delete'){ ?>
    <form id="data-form" role="form" method="post" action="<?=$link_save_delete;?>">
<?php } else if($page_name="cancel_class"){ ?>
    <form id="data-form" role="form"  method="post" action="<?=$link_save_cancel;?>">
<?php }?>
    <?php if($page_name!="cancel_class"){?>
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="year" value="<?=$form[0]['year'];?>" />
    <input type="hidden" name="class_no" value="<?=$form[0]['class_no'];?>" />
    <?php }else{?>
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" /> 
    <input type="hidden" name="year" value="<?=$cancel_class_form[0]['year'];?>" />
    <input type="hidden" name="class_no" value="<?=$cancel_class_form[0]['class_no'];?>" />
    <?php } ?>
    

    <div class="tab-pane">
        <table class="table table-hover" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="10%">年度</th>
                    <th width="20%">班期代碼</th>
                    <th width="70%">班期名稱</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php if($page_name!="cancel_class"){?>
                    <td><?=$form[0]['year'];?></td>
                    <td><?=$form[0]['class_no'];?></td>
                    <td><?=$form[0]['class_name'];?></td>
                    <?php }else{?>
                    <td><?=$cancel_class_form[0]['year'];?></td>
                    <td><?=$cancel_class_form[0]['class_no'];?></td>
                    <td><?=$cancel_class_form[0]['class_name'];?></td>
                    <?php } ?>
                </tr>

            </tbody>
        </table>
        <?php if($page_name == 'add'){ ?>
       <!--  <div class="form-group">
            <label class="control-label">插入新期數</label>
            <input class="form-control" id="insert" name="insert" placeholder="" value="">
        </div> -->
        <div class="form-group">
            <label class="control-label">新增期別數</label>
            <input class="form-control" id="add" name="add" placeholder="" value="" >
        </div>
        <div class="form-group">
            <label class="control-label">計畫類別</label>
            <?php
                echo form_dropdown('class_status', $choices['class_status'], set_value('class_status', '1'), "class='form-control'");
            ?>
            <?=form_error('class_status'); ?>
        </div>
        <?php } ?>

        <hr>

        <table class="table table-bordered table-condensed table-hover">
            <thead>
                <tr>
                    <?php if($page_name == 'delete'||$page_name=='cancel_class'){ ?>
                    <th width="2%"><input type="checkbox" onclick="check_all(this)"></th>
                    <?php } ?>
                    <th width="8%">年度</th>
                    <th width="50%">班期名稱</th>
                    <th width="5%">期別</th>
                    <th width="10%">開班起日</th>
                    <th width="10%">開班迄日</th>
                    <th width="15%">承辦人</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(!empty($form)){
                for($i=0;$i<count($form);$i++){
                    echo '<tr>';
                    if($page_name == 'delete'||$page_name=='cancel_class'){
                        echo '<td class="text-center"><input type="checkbox" name="term[]" value="'.$form[$i]['term'].'"></td>';
                    }
                    echo '<td>'.$form[$i]['year'].'</td>';

                    if($page_name == 'add'){
                        echo '<td><a href="'.base_url("planning/createclass/edit/{$form[$i]['seq_no']}/?").'">'.$form[$i]['class_name'].'</a></td>';
                    } else {
                        echo '<td>'.$form[$i]['class_name'].'</td>';
                    }

                    echo '<td>'.$form[$i]['term'].'</td>';
                    echo '<td>'.date('Y-m-d',strtotime($form[$i]['start_date1'])).'</td>';
                    echo '<td>'.date('Y-m-d',strtotime($form[$i]['end_date1'])).'</td>';
                    echo '<td>'.$form[$i]['contactor'].'</td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</form>
<script type="text/javascript">
    function checkForm(obj){
        // if((!isNaN(document.getElementById('insert').value) && document.getElementById('insert').value > 0) && (!isNaN(document.getElementById('add').value) && document.getElementById('add').value > 0)){
        //     alert('插入新期數和新增期別數不能同時存在');
        //     return false;
        // } else if(isNaN(document.getElementById('insert').value) && isNaN(document.getElementById('add').value)){
        //     alert('資料格式錯誤，必須是正整數');
        //     return false;
        // } else if(document.getElementById('insert').value.trim() == '' && document.getElementById('add').value.trim() == ''){
        //     alert('插入新期數和新增期別數不能同時為空');
        //     return false;
        // }

        if(isNaN(document.getElementById('add').value)){
            alert('資料格式錯誤，必須是正整數');
            return false;
        } else if(document.getElementById('add').value.trim() == ''){
            alert('新增期別數不能為空');
            return false;
        }
    }

    function checkSaveDelete(){
        var list = '';
        $('input:checkbox:checked[name="term[]"]').each(function(i) { 
            list += this.value + '、'; 
        });

        if(list == ''){
           alert("請至少勾選一個期別");
           return false;
        } else {
            var msg = '確定刪除第';
            msg += list.substr(0,list.length-1);
            msg += '期嗎?';

            if(confirm(msg)){
                var obj = document.getElementById('data-form');
                obj.submit();
            }
        }
        
        return false;
    }

    function checkSaveCancel(){
        var list = '';
        $('input:checkbox:checked[name="term[]"]').each(function(i) { 
            list += this.value + '、'; 
        });

        if(list == ''){
           alert("請至少勾選一個期別");
           return false;
        } else {
            var msg = '確定取消第';
            msg += list.substr(0,list.length-1);
            msg += '期嗎?';

            if(confirm(msg)){
                var obj = document.getElementById('data-form');
                obj.submit();
            }
        }
        
        return false;
    }

    function check_all(obj){
        var checkboxs = document.getElementsByName('term[]');
        for(var i=0;i<checkboxs.length;i++){
            checkboxs[i].checked = obj.checked;
        }
    }
</script>