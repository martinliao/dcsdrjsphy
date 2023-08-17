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
                                    echo form_dropdown('year', $choices['query_year'], $filter['year'], 'class="form-control"');
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
                                <th class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                                <th class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                                <th>班期性質</th>
                                <th class="sorting<?=($filter['sort']=='term asc')?'_asc':'';?><?=($filter['sort']=='term desc')?'_desc':'';?>" data-field="term" >期別</th>
                                <th class="sorting<?=($filter['sort']=='apply_s_date asc')?'_asc':'';?><?=($filter['sort']=='apply_s_date desc')?'_desc':'';?>" data-field="apply_s_date" >報名起日</th>
                                <th class="sorting<?=($filter['sort']=='apply_e_date asc')?'_asc':'';?><?=($filter['sort']=='apply_e_date desc')?'_desc':'';?>" data-field="apply_e_date" >報名迄日</th>
                                <th class="sorting<?=($filter['sort']=='start_date1 asc')?'_asc':'';?><?=($filter['sort']=='start_date1 desc')?'_desc':'';?>" data-field="start_date1" >開班起日</th>
                                <th class="sorting<?=($filter['sort']=='end_date1 asc')?'_asc':'';?><?=($filter['sort']=='end_date1 desc')?'_desc':'';?>" data-field="end_date1" >開班迄日</th>
                                <th>承辦人</th>
                                <th>目前報名人數</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><a href="<?=$row['link_regist'];?>" ><?=$row['class_no'];?></a></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['classNature'];?></td>
                                <td><?=$row['term'];?></td>
                                <td><?=substr($row['apply_s_date'], 0, -8);?></td>
                                <td><?=substr($row['apply_e_date'], 0, -8);?></td>
                                <td><?=substr($row['start_date1'], 0, -8);?></td>
                                <td><?=substr($row['end_date1'], 0, -8);?></td>
                                <td><?=$row['worker_name'];?></td>
                                <td><?=$row['cnt'];?></td>

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
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

</script>
