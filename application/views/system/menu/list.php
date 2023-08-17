<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 filter-group">
                        <form id="form-filter" role="form" class="form-inline">
                        <?php
                            echo form_dropdown('port', $choices['port'], $port, 'class="form-control"');
                        ?>
                        </form>
                    </div>
                </div>

                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <!-- <th>Port</th> -->
                                <th style="width: 35%;">功能名稱</th>
                                <th style="width: 20%;">Link</th>
                                <th style="width: 20%;">授權操作</th>
                                <th style="width: 5%;">排序</th>
                                <th style="width: 5%;">啟用</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr class="<?=($row['enable']==0)?'text-danger':'';?>">
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['id'];?>"></td>
                                <!-- <td><?=$choices['port'][$row['port']];?></td> -->
                                <td><?=$row['display'];?></td>
                                <td><?=$row['link'];?></td>
                                <td>
                                <?php
                                    if (isset($row['actions'])) {
                                        foreach ($row['actions'] as $action) {
                                            echo $action['name'] .', ';
                                        }
                                    }
                                ?>
                                </td>
                                <td><?=$row['sort_order'];?></td>
                                <td><?=($row['enable'] == 1)?'<span class="text-success">是</span>':'否';?></td>
                                <td>
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
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(document).ready(function() {
    $('#form-filter select').change(function(){
        $('#form-filter').submit();
    });

});
</script>
