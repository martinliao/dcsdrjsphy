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
                                <label class="control-label">類別</label>
                                <?php
                                    $choices['room_type'] = array('all'=>'全部') + $choices['room_type'];
                                    echo form_dropdown('room_type', $choices['room_type'], $filter['room_type'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-6" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-6 text-right">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-search"></i></label>
                                <input type="text" class="form-control" name="q" value="<?=$filter['q'];?>">
                            </div>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th class="sorting<?=($filter['sort']=='room_type asc')?'_asc':'';?><?=($filter['sort']=='room_type desc')?'_desc':'';?>" data-field="room_type" >類別</th>
                                <th class="sorting<?=($filter['sort']=='room_id asc')?'_asc':'';?><?=($filter['sort']=='room_id desc')?'_desc':'';?>" data-field="room_id" >代碼</th>
                                <th class="sorting<?=($filter['sort']=='room_name asc')?'_asc':'';?><?=($filter['sort']=='room_name desc')?'_desc':'';?>" data-field="room_name" >名稱</th>
                                <th class="sorting<?=($filter['sort']=='room_sname asc')?'_asc':'';?><?=($filter['sort']=='room_sname desc')?'_desc':'';?>" data-field="room_sname" >簡稱</th>
                                <th class="sorting<?=($filter['sort']=='room_location asc')?'_asc':'';?><?=($filter['sort']=='room_location desc')?'_desc':'';?>" data-field="room_location" >位置</th>
                                <th class="sorting<?=($filter['sort']=='room_bel asc')?'_asc':'';?><?=($filter['sort']=='room_bel desc')?'_desc':'';?>" data-field="room_bel" >所屬單位</th>
                                <th class="sorting<?=($filter['sort']=='room_manage asc')?'_asc':'';?><?=($filter['sort']=='room_manage desc')?'_desc':'';?>" data-field="room_manage" >管理單位</th>
                                <th class="sorting<?=($filter['sort']=='room_contact asc')?'_asc':'';?><?=($filter['sort']=='room_contact desc')?'_desc':'';?>" data-field="room_contact" >聯絡人</th>
                                <th>功能</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['id'];?>"></td>
                                <td><?=$choices['room_type'][$row['room_type']];?></td>
                                <td><?=$row['room_id'];?></td>
                                <td><?=$row['room_name'];?></td>
                                <td><?=$row['room_sname'];?></td>
                                <td><?=$row['room_location'];?></td>
                                <td>
                                    <?php if(isset($row['room_bel']) && isset($choices['room_bel'][$row['room_bel']])): ?>
                                    <?=$choices['room_bel'][$row['room_bel']];?>
                                    <?php endif ?>        
                                </td>
                                <td><?=$row['room_manage'];?></td>
                                <td><?=$row['room_contact'];?></td>
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
$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});
</script>
