<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                
                                <?php
                                    echo form_dropdown('year', $choices['query_year'], $filter['year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name']?>">
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">顯示筆數</label>
                                    <?php
                            echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                        ?>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#8CBBFF">
                            <th>年度</th>
                            <th>班期代碼</th>
                            <th>班期名稱</th>
                            <th>期別</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requires as $require): ?>
                        <tr>
                            <td><?=$require->year?></td>
                            <td><?=$require->class_no?></td>
                            <td><a
                                    href="<?=base_url("customer_service/change_practice_report/detail?year={$require->year}&class_no={$require->class_no}&term={$require->term}") ?>"><?=$require->class_name?></a>
                            </td>
                            <td><?=$require->term?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <!-- /.table end -->
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>

            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<!-- /.row -->
