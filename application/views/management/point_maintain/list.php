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
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('year', $choices['query_year'], $filter['year'], 'class="form-control" id="penYear"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">類別狀態</label>
                                <?php
                                    echo form_dropdown('queryType', $choices['queryType'], $filter['queryType'], 'class="form-control" id="queryType"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm">搜尋</button>
                            <button class="btn btn-info btn-sm" onclick="doClear()" >清除</button>
                        </div>
                    </div>
                    <div class="row">
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
                        <tr bgcolor="#8CBBFF">
                            <th class="text-center">功能</th>                        
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row){ ?>
                        <tr>
                            <td><a href="<?=$row['detail'];?>" class="btn btn-info">修改</a></td>
                            <td><?=$row['year'];?></td>
                            <td><?=$row['class_no'];?></td>
                            <td><?=$row['class_name'];?></td>
                            <td><?=$row['term'];?></td>
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

function doClear(){
  document.all.year.value = "";
  document.all.class_no.value = "";
  document.all.class_name.value = "";
  document.all.queryType.value = "";
}
</script>