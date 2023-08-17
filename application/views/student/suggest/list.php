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
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期首日上課月份:</label>
                                <?php
                                    echo form_dropdown('query_month_start', $choice['month'], $filter['query_month_start'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name']?>">
                            </div>
                            <button class="btn btn-info">查詢</button>
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
                            <th class="text-center">列序</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">意見回復</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i=1;?>
                    <?php foreach($list as $row){?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row['year']?></td>
                            <td><?=$row['class_no']?></td>
                            <td><?=$row['term']?></td>
                            <td><?=$row['class_name']?></td>
                            <td class="text-center"><a href="<?=$row['link_detail']?>" class="btn btn-info">開啟</a></td>
                    <?php $i++; }?>
                    </tbody>
                   
                </table>
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