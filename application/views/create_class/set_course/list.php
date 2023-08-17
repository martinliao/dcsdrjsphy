<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                        
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="query_class_no" value="<?=$filter['query_class_no'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                            </div>
                            <input type="checkbox" style="height: auto;" name="checkAllClass" value="on" <?= isset($filter['checkAllClass']) && $filter['checkAllClass']=='on'?'checked':'';?>>
                            <label class="control-label">查詢所有班期</label>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <?php //20210713加入
                            if ($_SESSION['sign_sl'] == 'boss'){
                            ?>
                                <a class="btn btn-danger btn-sm" href="<?=base_url('create_class/set_course/list_sign');?>">課程簽核</a>
                                <label class="control-label">　主管您好！有您待簽核之課程</label>
                            <?php
                            
                            }elseif ($_SESSION['sign_sl'] == 'leader'){
                                ?>
                                <a class="btn btn-danger btn-sm" href="<?=base_url('create_class/set_course/list_sign');?>">課程簽核</a>
                                <label class="control-label">　會辦承辦人您好！有您待簽核之課程</label>
                            <?php 
                            }
                        
                            ?>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <!-- <th class="text-center"><input type="checkbox" id="chkall"></th> -->
                                <th>列序</th>
                                <th class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                                <th class="sorting<?=($filter['sort']=='term asc')?'_asc':'';?><?=($filter['sort']=='term desc')?'_desc':'';?>" data-field="term" >期別</th>
                                <th class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                                <th class="center">課表簽核/記錄</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $count = 0;?>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <!-- <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['seq_no'];?>"></td> -->
                                <td><?=++$count?></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td align="center" valign="center">
                                    <span style="color:rgb(138, 138, 138">簽核</span>/
                                    <span style="color:rgb(138, 138, 138">核閱</span>/
                                    <a href="#" onclick="window.open('<?=base_url('create_class/set_course/course_sch_app');?>?seq_nos=<?=$row['seq_no'];?>','show_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=1000')">記錄</a>
                                </td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_view'])) { ?>
                                    <a type="button" class="btn btn-outline btn-success btn-xs btn-toggle" title="View" href="<?=$row['link_view'];?>">
                                        <i class="fa fa-eye fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_edit'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$row['link_edit'];?>">
                                        <i class="fa fa-pencil fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_delete'])) { ?>
                                    <button type="button" class="btn btn-outline btn-danger btn-xs" onclick="ajaxDelete(this, '確認要刪除選單「<?=$row['name'];?>」?', '<?=$row['link_delete'];?>')">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </form>
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
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
</script>
