<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form action="<?=$selfFilename;?>?insert" name='query_form' id="query_form" method="post" role="form" class="form-inline">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <input type="text" class="form-control" value="<?=$require_data['year'];?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$require_data['class_no'];?>" disabled>
                            </div>

                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$require_data['class_name'];?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">原班期:</label>
                                <input type="text" class="form-control insert_group" value="<?=$require_data['term'];?>" disabled>
                                <span class="edit_group" id="term_text" style="display:none;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <div class="form-group">
                                <label class="control-label">姓名:</label>
                                <?php if($page_data['mode'] == 'STUDENT'){ ?>
                                <input type="hidden" name="id" value="<?=$userInfos[0]['id'];?>" class="insert_group" />
                                <?php }else{ ?>
                                <select  name="id" id="id" class="form-control insert_group" >
                                    <option value="" class="id_option"></option>
                                    <?php foreach($userInfos as $u_row){ ?>
                                    <option value="<?=$u_row['id'];?>" ref="<?=$u_row['contact'];?>" ><?=$u_row['name'];?></option>
                                    <?php } ?>
                                </select>
                                <?php } ?>
                                <span class="edit_group" id="update_name_text" style="display:none;"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">擬換班期:</label>
                                <?php if($changeTermInfosCount != '0'){ ?>
                                <?php
                                    echo form_dropdown('change_term', $changeTermInfos, '', 'class="form-control" id="change_term"');
                                ?>
                                <?php }else{ ?>
                                <span class="insert_group">無期可換</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <?php if($changeTermInfosCount != '0'){ ?>
                                <label class="control-label">聯絡方式:</label>
                                <?php if($page_data['mode'] == 'STUDENT'){ ?>
                                <textarea  name="contact" id="contact" style="width: 30%;" class="insert_group form-control"><?=$userInfos[0]['contact'];?></textarea>
                                <?php }else{ ?>
                                <textarea  name="contact" id="contact" style="width: 30%;" class="insert_group form-control"></textarea>
                                <?php } ?>
                                <textarea  name="new_contact" id="new_contact" class="edit_group form-control" style="display:none;"></textarea>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <input type="hidden" name="pagenum" id="pagenum" value="1" />
                            <?php if($changeTermInfosCount != '0'){ ?>
                            <input type="submit" name='add_btn' id="add_btn" value='新增訊息' class="btn btn-info" />
                            <?php } ?>
                            <input type="button" name="cancel_btn" id="cancel_btn" style="display:none;" class="edit_group btn btn-info" value="取消" />
                            <input type="button" name='goback' value="返回" id="goback" class="btn btn-info">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                    <input type="hidden" name="old_year" id="old_year" value="" />
                    <input type="hidden" name="old_term" id="old_term" value="" />
                    <input type="hidden" name="old_change_term" id="old_change_term" value="" />
                    <input type="hidden" name="old_change_year" id="old_change_year" value="" />
                    <input type="hidden" name="old_class_no" id="old_class_no" value="" />
                    <input type="hidden" name="old_id" id="old_id" value="" />
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">學號</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">原期別</th>
                            <th class="text-center">擬換期別</th>
                            <th class="text-center">聯絡方式</th>
                            <th class="text-center">申請日期</th>
                            <th class="text-center" colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row){ ?>
                        <tr>
                            <td><?=$row['st_no'];?></td>
                            <td><?=$row['name'];?></td>
                            <td><?=$row['year'];?>年度第<?=$row['term'];?>期</td>
                            <td>
                                <?php if($row['change_year'] != ''){ ?>
                                <?=$row['change_year'];?>年度
                                <?php }else{ ?>
                                <?=$row['year'];?>年度
                                <?php } ?>
                                第<?=$row['change_term'];?>期
                            </td>
                            <td><?=$row['contact'];?></td>
                            <td><?=substr($row['cre_date'], 0, 10);?></td>
                            <td>
                            <?php if( $this->flags->user['username'] == $row['cre_user'] || $page_data['mode'] == 'MANAGE' || $page_data['mode'] == 'WORKER'){ ?>
                            <!-- <td class="text-center"><a class="btn btn-info">修改</a></td>
                            <td class="text-center"><a class="btn btn-info">刪除</a></td> -->
                            <button class="edit_btn btn btn-info" ref_ID="<?=$row['id'];?>" ref_TERM="<?=$row['term'];?>" ref_CHANGE_TERM="<?=$row['change_term'];?>" ref_YEAR="<?=$row['year'];?>" ref_CLASS_NO="<?=$row['class_no'];?>" ref_NAME="<?=$row['name'];?>" ref_CONTACT="<?=$row['contact'];?>">修改</button>
                            <button class="delete_btn btn btn-info" ref_ID="<?=$row['id'];?>" ref_TERM="<?=$row['term'];?>" ref_CHANGE_TERM="<?=$row['change_term'];?>" ref_YEAR="<?=$row['year'];?>" ref_CLASS_NO="<?=$row['class_no'];?>">刪除</button>
                            <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <form>
                    <div class="row ">
                        <div class="col-lg-4">
                            Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                        </div>
                        <div class="col-lg-8  text-right">
                            <?=$this->pagination->create_links();?>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
    $(document).ready(function(){

            var msg = '<?=$page_data['msg'];?>';
            if (msg!=='') {
                alert(msg);
            }

            $('#goback').click(function(){
                window.location.href = '<?=base_url("student/student_match/");?>';
            });

            $('select#id').change(function(){ //事件發生
                $('#contact').text($('option:selected:first', this).attr('ref'));
            });

            $('.delete_btn').click(function(){
                window.location.href = '<?=$selfFilename;?>?delete='+$(this).attr('ref_ID')+'@'+$(this).attr('ref_TERM')+'@'+$(this).attr('ref_CHANGE_TERM')+'@'+$(this).attr('ref_YEAR')+'@'+$(this).attr('ref_CLASS_NO');
            });

            $('.edit_btn').click(function(){
                $('#query_form').attr('action', '<?=$selfFilename;?>?update');
                $('#add_btn').val('儲存');
                $('.edit_group').show();
                $('.insert_group').hide();

                // 設定參數
                $('#old_year').val($(this).attr('ref_YEAR'));
                $('#old_term').val($(this).attr('ref_TERM'));
                $('#old_change_term').val($(this).attr('ref_CHANGE_TERM'));
                $('#old_class_no').val($(this).attr('ref_CLASS_NO'));
                $('#old_id').val($(this).attr('ref_ID'));

                var ctTerm = $(this).attr('ref_CHANGE_TERM');
                $('#change_term').children().each(function(){
                    if ($(this).attr('value')==ctTerm){
                        $(this).attr("selected", "true");
                    }
                });

                $('#update_name_text').text($(this).attr('ref_NAME'));
                $('#new_contact').text($(this).attr('ref_CONTACT'));
                $('#term_text').text($(this).attr('ref_TERM'));
            });

            $('#cancel_btn').click(function(){
                $('#query_form').attr('action', '<?=$selfFilename;?>?insert');
                $('#add_btn').val('新增訊息');
                $('.edit_group').hide();
                $('.insert_group').show();
            });
        });
</script>