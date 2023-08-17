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
                                <label class="control-label">申請單編號</i></label>
                                <input type="text" class="form-control" name="appi_id" value="<?=$filter['appi_id'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">申請單位</i></label>
                                <input type="text" class="form-control" name="app_name" value="<?=$filter['app_name'];?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <label class="control-label">申請日期</label>
                            <div class="input-group" id="cre_start_date" >
                                <input type="text" class="form-control datepicker" id="test1" name="start_cre_date" value="<?=$filter['start_cre_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;"id="test2"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label"></label>
                            <div class="input-group" id="cre_end_date" >
                                <input type="text" class="form-control datepicker" id="test3" name="end_cre_date" value="<?=$filter['end_cre_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;" id="test4"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <label class="control-label">使用起訖日</label>
                            <div class="input-group" id="start_date" >
                                <input type="text" class="form-control datepicker" id="test5" name="start_date" value="<?=$filter['start_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;"id="test6" ><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label"></label>
                            <div class="input-group" id="end_date" >
                                <input type="text" class="form-control datepicker" id="test7" name="end_date" value="<?=$filter['end_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;"id="test8" ><i class="fa fa-calendar"></i></span>
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
                        <div class="col-xs-6 text-right">

                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th >申請單編號</th>
                                <th >使用起訖日</th>
                                <th >使用教室</th>
                                <th >申請單位</th>
                                <th >聯絡人姓名</th>
                                <th >聯絡電話</th>
                                <th>申請日期</th>
                                <th>活動名稱暨內容說明</th>
                                <th>金額總計</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['appi_id'];?>"></td>
                                <td><?=$row['appi_id'];?></td>
                                <td><?=$row['room_date'];?></td>
                                <td><?=$row['room_name'];?></td>
                                <td><?=$row['app_name'];?></td>
                                <td><?=$row['contact_name'];?></td>
                                <td><?=$row['tel'];?></td>
                                <td><?=$row['cre_date'];?></td>
                                <td><?=$row['app_reason'];?></td>
                                <td><?=$row['total_expense'];?></td>
                                <td class="text-center" id="btn_group">
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

    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });
    $("#test3").datepicker();
    $('#test4').click(function(){
        $("#test3").focus();
    });
    $("#test5").datepicker();
    $('#test6').click(function(){
        $("#test5").focus();
    });
    $("#test7").datepicker();
    $('#test8').click(function(){
        $("#test7").focus();
    });

    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});
</script>
