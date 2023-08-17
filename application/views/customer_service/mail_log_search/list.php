<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="sort" value="" />
                    <div class="col-xs-12">
                        <div class="form-group row">
                            <label class="control-label">年度</label>
                            <select name='year' id='query_year'>
                                <?php for($year=(int)date("Y")-1911; $year>=90; $year--) : ?>
                                <option value='<?=$year?>'
                                    <?php echo ($year == $filter['year']) ? 'selected' : '';  ?>
                                    ><?=$year?></option>
                                <?php endfor?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">班期代碼</label>
                            <input type="text" class="form-control" name="class_no">
                        </div>
                        <div class="form-group">
                            <label class="control-label">班期名稱</label>
                            <input type="text" class="form-control" name="class_name">
                        </div>
                        <button class="btn btn-info btn-sm">查詢</button>
                        <div class="row">
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
                        <tr>
                            <th>年度</th>
                            <th>班期名稱</th>
                            <th>期別</th>
                            <th>主題</th>
                            <th>寄送對象</th>
                            <th>寄件者</th>
                            <th>承辦人</th>
                            <th>寄送時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mail_logs as $log): ?>
                        <tr>
                            <td><?=$log->year?></td>
                            <td><?=$log->class_name?></td>
                            <td><?=$log->term?></td>
                            <td><?=$log->subject?></td>
                            <td><?=$log->mail_to?></td>
                            <td><?=$log->creater_name?></td>
                            <td><?=$log->worker_name?></td>
                            <td><?=$log->cre_date?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                </form>
                <div class="row ">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
                </form>

            </div>
            <!-- /.table end -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->