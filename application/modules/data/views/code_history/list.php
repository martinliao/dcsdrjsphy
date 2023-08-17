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
                            <?php
                                echo form_dropdown('type_id', $type_id, $filter['type_id'], 'class="form-control"');
                            ?>
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
                                <label class="control-label">名稱</label>
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
                                <th>代碼類別</th>
                                <th>項目編號</th>
                                <th>項目名稱</th>
                                <th>輔助欄位1</th>
                                <th>輔助欄位2</th>
                                <th>備註</th>
                                <th>執行作業</th>
                                <th>異動者</th>
                                <th>異動日期</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$type_id[$row['type_id']];?></td>
                                <td><?=$row['item_id'];?></td>
                                <td><?=$row['description'];?></td>
                                <td><?=$row['add_val1'];?></td>
                                <td><?=$row['add_val2'];?></td>
                                <td><?=$row['memo'];?></td>
                                <td><?=$row['task'];?></td>
                                <td><?=$row['cre_user'];?></td>
                                <td><?=$row['cre_date'];?></td>
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
