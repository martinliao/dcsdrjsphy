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
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="query_class_no" value="<?=$filter['query_class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name']?>">
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
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#8CBBFF">
                            <th>班期代碼</th>
                            <th>班期名稱</th>
                            <th>時數</th>
                            <th>期別</th>
                            <th>報名起訖日</th>
                            <th>開課起訖日</th>
                            <th>承辦人(分機)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requires as $require): ?>
                        <tr class="text-center">
                            <td><?=$require->class_no?></td>
                            <td><a href="<?=base_url("management/signup_change_report/detail?year=".htmlspecialchars($require->year, ENT_HTML5|ENT_QUOTES)."&class_no=".htmlspecialchars($require->class_no, ENT_HTML5|ENT_QUOTES)."&term=".htmlspecialchars($require->term, ENT_HTML5|ENT_QUOTES)."&?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."") ?>"><?=$require->class_name?></a></td>
                            <td><?=$require->range?></td>
                            <td><?=$require->term?></td>
                            <td><?=substr($require->apply_s_date,0,10)?>～<?=substr($require->apply_e_date,0,10)?><br><?=substr($require->apply_s_date2,0,10)?>～<?=substr($require->apply_e_date2,0,10)?></td>
                            <!--<td><?=date_format(date_create($require->apply_s_date),'Y-m-d').'～'.date_format(date_create($require->apply_e_date),'Y-m-d').'<br>'.date_format(date_create($require->apply_s_date2),'Y-m-d').'～'.date_format(date_create($require->apply_e_date2),'Y-m-d') ?></td>-->
                            <td><?=date_format(date_create($require->start_date1),'Y-m-d').'～'.date_format(date_create($require->end_date1),'Y-m-d') ?></td>
                            <td><?=$require->worker.'('.$require->phone.')'?></td>
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
