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
                        <input type="hidden" name="post" value="post" />
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">姓名</i></label>
                                <input type="text" class="form-control" name="name" value="<?=$filter['name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">局處名稱</i></label>
                                <input style="width: 280px;" type="text" class="form-control" name="bureau_name" value="<?=$filter['bureau_name'];?>">
                            </div>

                        </div>
                        <div class="col-xs-12" >
                           <button type="submit" class="btn btn-info">查詢</button>
                           <a id="clear" class="btn btn-warning">清除</a>
                           <font color="red">【承辦人事人員資料異動時，請至「人事資料維護」修正基本資料，以免漏失調訓通知】</font>
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
                                <th>帳號</th>
                                <th>姓名</th>
                                <th>承辦人</th>
                                <th>局處名稱</th>
                                <th>職稱</th>
                                <th>公司電話</th>
                                <th>公司傳真</th>
                                <th>Email</th>
                                <th>切換帳號</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$row['u_name'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['co_usrnick'];?></td>
                                <td><?=$row['bureau_name'];?></td>
                                <td><?=$row['job_title_name'];?></td>
                                <td><?=$row['office_tel'];?></td>
                                <td><?=$row['office_fax'];?></td>
                                <td><?=$row['email'];?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_switch'])) { ?>
                                    <a type="button" class="btn btn-primary btn-xs btn-toggle" href="<?=$row['link_switch'];?>">
                                        <i>切換帳號</i>
                                    </a>
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

    $("#clear").click(function(){
        $("input[name=bureau_name]")[0].value="";
        $("input[name=name]")[0].value="";
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});
</script>
