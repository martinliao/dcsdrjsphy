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
                        <div class="col-xs-12">
                            <label class="control-label">班期名稱</label>
                            <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                            <label class="control-label">群組</label>
                            <?php
                                echo form_dropdown('group_name', $choices['group_name'], $filter['group_name'], 'class="form-control"');
                            ?>
                            <button class="btn btn-info">查詢</button>
                        </div>
                        <div class="col-xs-6" >
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
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th class="sorting<?=($filter['sort']=='group_id asc')?'_asc':'';?><?=($filter['sort']=='group_id desc')?'_desc':'';?>" data-field="group_id" >群組代碼</th>
                                <th class="sorting<?=($filter['sort']=='group_name asc')?'_asc':'';?><?=($filter['sort']=='group_name desc')?'_desc':'';?>" data-field="group_name" >群組名稱</th>
                                <th>班期名稱</th>
                                <th class="sorting<?=($filter['sort']=='limited asc')?'_asc':'';?><?=($filter['sort']=='limited desc')?'_desc':'';?>" data-field="limited" >限制參訓數</th>
                                <th>功能</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['group_id'];?>"></td>
                                <td><?=$row['group_id'];?></td>
                                <td><?=$row['group_name'];?></td>
                                <td>
                                    <?php
                                        for($i=0;$i<count($row['class_list']);$i++){
                                            echo $row['class_list'][$i]['class_no'].'-'.$row['class_list'][$i]['class_name'].'<br>';
                                        }
                                    ?>
                                </td>
                                <td><?=$row['limited'];?></td>
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
/*$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});*/
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
</script>
