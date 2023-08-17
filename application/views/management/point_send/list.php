<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('year', $choices['query_year'], $filter['year'], 'class="form-control" id="year_before"');
                                ?>
                            </div>

                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input tpye="text" name="class_no" id="class_no" value="<?=$filter['class_no'];?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input tpye="text" name="class_name" id="class_name" value="<?=$filter['class_name'];?>" class="form-control">
                            </div>

                        </div>
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm">搜尋</button>
                        </div>

                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>年度</th>
                            <th>期別</th>
                            <th>班期代碼</th>
                            <th>班期名稱</th>
                            <th>功能</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row) { ?>
                        <tr>
                            <td><?=$row['year'];?></td>
                            <td><?=$row['term'];?></td>
                            <td><?=$row['class_no'];?></td>
                            <td><?=$row['class_name'];?></td>
                            <td><a href="<?=$row['url'];?>" class="btn btn-info">寄送</td>
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
function checkAll(id,check)
{
    var checkboxs=document.getElementsByName(check);
    for(var i=0;i<checkboxs.length;i++)
    {
        checkboxs[i].checked=id.checked;
    }
}
</script>