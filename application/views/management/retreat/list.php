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
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no']?>" style="width: 95px;">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" name="class_name"value="<?=$filter['class_name']?>" style="width: 288px;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm">搜尋</button>
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
                            <th>列序</th>
                            <th>班期代碼</th>
                            <th>期別</th>
                            <th>班期名稱</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requires as $key => $require): ?>
                        <tr>
                            <td><?=$key+1?></td>
                            <td><a href="<?=base_url("management/retreat/detail?year={$require->year}&class_no={$require->class_no}&term={$require->term}")?>"><?=$require->class_no?></a></td>
                            <td><?=$require->term?></td>
                            <td><?=$require->class_name?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <form>
                    <div class="row ">
                        <div class="col-lg-4">
                            Showing <?=count($requires)?> / <?=$paginate_config['total_rows']?> entries
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