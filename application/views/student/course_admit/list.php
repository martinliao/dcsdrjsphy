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
                                <label class="control-label">班期狀態:</label>
                                <select name='start' id='query_year'>
                                    <?php $selected = (1 == $filter['start']) ? 'selected' : ''; ?>
                                    <option value='1' <?=$selected?> >已開班</option>
                                    <?php $selected = (2 == $filter['start']) ? 'selected' : ''; ?>
                                    <option value='2' <?=$selected?> >未開班</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('year', $choices['query_year'], $filter['year'], 'class="form-control" id="year_before"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" name="class_no" class="form-control" value="<?=$filter['class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" name="class_name" class="form-control" value="<?=$filter['class_name']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">機關名稱:</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info btn-sm">搜尋</button>
                            </div>
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
                        <tr>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">上課日期</th>
                            <th class="text-center">學號</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pass_list as $pass): ?>
                        <tr>
                            <td><?=$pass->year?></td>
                            <td>
                                <a href="<?=base_url("student/course_admit/detail?year={$pass->year}&class_no={$pass->class_no}&term={$pass->term}") ?>"
                                    target = "_blank">
                                    <?=$pass->class_name?>
                                </a>
                                </td>
                            <td><?=$pass->term?></td>
                            <td><?=$pass->start_date1."~".$pass->end_date1?></td>
                            <td><?=$pass->st_no?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
               
                <form>
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
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
