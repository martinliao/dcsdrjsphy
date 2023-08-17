<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <div class="panel-body">
                <div class="col-xs-12">
                    <form id="filter-form" role="form" class="form-inline">
                        <div class="row">
                            <div class="col-xs-12" >
                                <label class="control-label">身分證</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?=$filter['idno']?>"  name="idno">
                                    
                                </div>                                                          
                            </div>
                            <div class="col-xs-12" >
                                <label class="control-label">姓名</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?=$filter['member_name']?>" name="member_name">
                                    <!-- <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span> -->
                                </div>                                                          
                            </div>
                            <div class="col-xs-12" >
                                <label class="control-label">身份</label>
                                <div class="input-group">
                                    <select type="text" class="form-control" name="member_type">
                                        <option value="teacher" <?php echo ($filter['member_type'] == 'teacher') ? 'selected' : ''; ?> >講座</option>
                                        <option value="student" <?php echo ($filter['member_type'] == 'student') ? 'selected' : ''; ?> >學員</option>
                                    </select>
                                </div>                                                          
                            </div>
                            <div class="col-xs-12" >
                                <button class="btn btn-info btn-sm">搜尋</button>                                                      
                                <a href="<?=base_url('other_work/ArriveRemind/create');?>"><button type="button" class="btn btn-warning btn-sm">新增</button></a>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteArriveRemind()">刪除</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="<?=base_url('other_work/ArriveRemind/delete')?>" id="deleteForm">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <table class="table table-hover table-condensed table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">刪除</th>
                                    <th class="text-center">身分證</th>
                                    <th class="text-center">蒞臨人員</th>
                                    <th class="text-center">寄送信箱</th>
                                    <th class="text-center">寄送對象</th>
                                    <th class="text-center">提醒時間</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($list as $arriveRemind): ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" name="ids[]" value="<?=$arriveRemind->id?>"></td>
                                    <td class="text-center"><?=$arriveRemind->idno?></td>
                                    <td class="text-center"><?=$arriveRemind->name?></td>
                                    <td class="text-center"><?=str_replace(',', '<br>', $arriveRemind->email)?></td>
                                    <td class="text-center"><?=$arriveRemind->remind_member_name?></td>
                                    <td class="text-center"><?=$arriveRemind->remind_sdate.'~'.$arriveRemind->remind_edate?></td>
                                </tr>
                                <?php endforeach?>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                       
                    </div>
                    <?php if (!empty($list)): ?>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                    <?php endif ?>
                </div>                
            </div>
        </div>
    </div>
</div>
<script>


    function deleteArriveRemind()
    {
        if(confirm('確定要刪除選中的資料嗎?')){
            $("#deleteForm").submit();
        }
    }
</script>