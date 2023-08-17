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
                                    echo form_dropdown('year', $choices['year'], $filter['year'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name'];?>">
                            </div>
                            <div class="checkbox">
                                &nbsp;<input type="checkbox" name="allClass" value="allClass" <?=set_checkbox('allClass', 'allClass', $filter['allClass']=='allClass');?>>
                            </div>
                            <label class="control-label">查詢所有班期</label>
                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                        <div class="col-xs-6">
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
                                <th>列序</th>
                                <th>班期代碼</th>
                                <th>期別</th>
                                <th>班期名稱</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=1; foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$i;?></td>
                                <td><a href="<?=$row['link_add'];?>" ><?=$row['class_no'];?></a></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['class_name'];?></td>
                            </tr>
                        <?php $i++; } ?>
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
