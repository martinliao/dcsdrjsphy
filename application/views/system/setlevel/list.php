<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="sort" value="" />
                    <div class="row">
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
                    </div>
                </form>

                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th class="sorting<?=($filter['sort']=='name asc')?'_asc':'';?><?=($filter['sort']=='name desc')?'_desc':'';?>" data-field="name" >名稱</th>
                                <th class="sorting<?=($filter['sort']=='link asc')?'_asc':'';?><?=($filter['sort']=='link desc')?'_desc':'';?>" data-field="link" >Link</th>
                                <th class="sorting<?=($filter['sort']=='sort_order asc')?'_asc':'';?><?=($filter['sort']=='sort_order desc')?'_desc':'';?>" data-field="sort_order" >排序</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($row['id'] > 4) { ?>
                                    <input type="checkbox" name="rowid[]" value="<?=$row['id'];?>">
                                    <?php } ?>
                                </td>
                                <td><?=isset($row['name'])?$row['name']:'';?></td>
                                <td><?=isset($row['link'])?$row['link']:'';?></td>
                                <td><?=$row['sort_order'];?></td>
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
