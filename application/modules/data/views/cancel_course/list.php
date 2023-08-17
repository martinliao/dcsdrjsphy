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
                            <button class="btn btn-info btn-sm">查詢</button>
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
                    <input type="hidden" name="mode" id="mode" value="" />
                    <input type="hidden" name="year" id="year" value="" />
                    <input type="hidden" name="class_no" id="class_no" value="" />
                    <input type="hidden" name="term" id="term" value="" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">取消帶班完成</th>
                                <th class="text-center">班期代碼</th>
                                <th class="text-center">期別</th>
                                <th class="text-center">班期名稱</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input class="btn btn-info" onclick='cancelFun("<?=$row['year']?>","<?=$row['class_no']?>","<?=$row['term']?>")' value="取消帶班完成"></input></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['class_name'];?></td>
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
        </div>
    </div>
</div>
    <!-- /.panel -->
    <!-- /.col-lg-12 -->
<script type="text/javascript">
    function cancelFun(year,class_no,term) {
        document.getElementById('year').value = year;
        document.getElementById('class_no').value = class_no;
        document.getElementById('term').value = term;
        document.getElementById('mode').value = 'cancel';

        var obj = document.getElementById('list-form');
        obj.submit();
    }
</script>