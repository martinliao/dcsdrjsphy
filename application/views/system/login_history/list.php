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
                                <label class="control-label">使用者帳號</label>
                                <input type="text" class="form-control" name="username" value="<?=$filter['username'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">日期區間</label>
                                <div class="input-daterange input-group" id="datepicker" style="">
                                    <input type="text" class="form-control datepicker" id="datepicker1" name="start_date" value="<?=$filter['start_date'];?>"/>
                                    <span class="input-group-addon" style="cursor: pointer;"  id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control datepicker"  id="test1" name="end_date"  value="<?=$filter['end_date'];?>"/>
                                    <span class="input-group-addon" style="cursor: pointer;"  id="test2"><i
                                        class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                        <div class="col-xs-6" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
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
                                <th class="sorting<?=($filter['sort']=='login_time asc')?'_asc':'';?><?=($filter['sort']=='login_time desc')?'_desc':'';?>" data-field="login_time" >登入時間</th>
                                <th class="sorting<?=($filter['sort']=='user asc')?'_asc':'';?><?=($filter['sort']=='user desc')?'_desc':'';?>" data-field="user" >使用者帳號/姓名</th>
                                <th class="sorting<?=($filter['sort']=='status asc')?'_asc':'';?><?=($filter['sort']=='status desc')?'_desc':'';?>" data-field="status" >登入狀態</th>
                                <th class="sorting<?=($filter['sort']=='ip asc')?'_asc':'';?><?=($filter['sort']=='ip desc')?'_desc':'';?>" data-field="ip" >IP</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$row['login_time'];?></td>
                                <td><?=$row['username'];?>/<?=$row['name'];?></td>
                                <td><?=$row['status'];?></td>
                                <td><?=$row['ip'];?></td>
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

$(document).ready(function() {
  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });
});

$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });
});
</script>
