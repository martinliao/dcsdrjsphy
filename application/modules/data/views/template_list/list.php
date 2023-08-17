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
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">範本類別</label>
                                <?php
                                    echo form_dropdown('item_id', $item_id, $filter['item_id'], 'class="form-control"');
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
                    </div>
                </form>

                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall" ></th>
                                <th>範本類別</th>
                                <th>項目名稱</th>
                                <th>順序</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr data-rowid="<?=$row['id'];?>" class="<?=($row['is_open']==0)?'text-danger':'';?>" >
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['id'];?>"></td>
                                <td><?=$item_id[$row['item_id']];?></td>
                                <td><?=$row['title'];?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_up'])) { ?>
                                    <a type="button" class="btn btn-outline btn-success btn-xs btn-toggle" href="<?=$row['link_up'];?>">
                                        上移
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_dn'])) { ?>
                                    <a type="button" class="btn btn-outline btn-success btn-xs btn-toggle" href="<?=$row['link_dn'];?>">
                                        下移
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_edit'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$row['link_edit'];?>">
                                        修改
                                    </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </form>

                <div class="row">
                    <div class="col-md-5">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-md-7 text-right">
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
