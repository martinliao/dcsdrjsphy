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
                                <input type="text" class="form-control" id="appi_id" name="appi_id" value="<?=$filter['appi_id'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">申請單位</i></label>
                                <input type="text" class="form-control" id="app_name" name="app_name" value="<?=$filter['app_name'];?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <label class="control-label">申請日期</label>
                            <div class="input-group" id="cre_start_date" >
                                <input type="text" class="form-control datepicker" id="test1" name="start_cre_date" value="<?=$filter['start_cre_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;"id="test2" ><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label"></label>
                            <div class="input-group" id="cre_end_date" >
                                <input type="text" class="form-control datepicker" id="test3" name="end_cre_date" value="<?=$filter['end_cre_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;"id="test4" ><i class="fa fa-calendar"></i></span>
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <a class="btn btn-info btn-sm" onclick="actionSelect('<?=$print_room;?>')" >案件統計表</a>
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
                                <!-- <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th> -->
                                <th >申請單編號</th>
                                <th >申請單位</th>
                                <th >聯絡人姓名</th>
                                <th>申請日期</th>
                                <th>活動名稱暨內容說明</th>
                                <th>金額總計</th>
                                <th>報表</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <!-- <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['appi_id'];?>"></td> -->
                                <td><?=$row['appi_id'];?></td>
                                <td><?=$row['app_name'];?></td>
                                <td><?=$row['contact_name'];?></td>
                                <td><?=$row['cre_date'];?></td>
                                <td><?=$row['app_reason'];?></td>
                                <td><?=$row['total_expense'];?></td>
                                <td id="btn_group">
                                    <?php if (isset($row['print_premises'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" onclick="actionOpen('<?=$row['print_premises'];?>')" >
                                        場地使用費明細表
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['mail_detail'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" onclick="actionOpen('<?=$row['mail_detail'];?>')" >
                                        [MAIL]
                                    </a><br>
                                    <?php } ?>
                                    <?php if (isset($row['print_accounting'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" onclick="actionOpen('<?=$row['print_accounting'];?>')" >
                                        會計明細表
                                    </a><br>
                                    <?php } ?>
                                    <?php if (isset($row['print_application'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" onclick="actionOpen('<?=$row['print_application'];?>')" >
                                        申請表
                                    </a><br>
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
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});

var actionSelect = function(url) {
    var appi_id = $('#appi_id').val();
    var app_name = $('#app_name').val();
    var start_date = $('#test1').val();
    var end_date = $('#test3').val();

    url = url + "?appi_id="+ appi_id +"&app_name=" + app_name + "&start_date=" + start_date + "&end_date=" + end_date;

    var myW=window.open (url, "printRoom", "height=800, width=1024, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no");
    myW.focus();

}

var actionOpen = function(url) {

    var myW=window.open (url, "printRoom", "height=800, width=1024, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, status=no");
    myW.focus();

}
</script>
