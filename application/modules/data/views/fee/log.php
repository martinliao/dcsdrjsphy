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
                                <label class="control-label">身分別:</label>
                                <?php
                                    echo form_dropdown('teacher_type', $choices['teacher_type'], $filter['teacher_type'], 'class="form-control"');
                                ?>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">鐘點費類別:</label>
                                <?php
                                    echo form_dropdown('hourlyfee_category', $choices['hourlyfee_category'], $filter['hourlyfee_category'], 'class="form-control"');
                                ?>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">助教聘請類別:</label>
                                <?php
                                    echo form_dropdown('insert_teachingassistant', $choices['hire_category'], $filter['insert_teachingassistant'], 'class="form-control"');
                                ?>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">講師聘請類別:</label>
                                <?php
                                    echo form_dropdown('insert_Lecturer', $choices['hire_category'], $filter['insert_Lecturer'], 'class="form-control"');
                                ?>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <button  class="btn btn-info">查詢</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                            ?>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">鐘點費類別</th>
                            <th class="text-center">身分別</th>
                            <th class="text-center">講師聘請類別</th>
                            <th class="text-center">助教聘請類別</th>
                            <th class="text-center">鐘點費</th>
                            <th class="text-center">交通費</th>
                            <th class="text-center">執行作業</th>
                            <th class="text-center">異動者</th>
                            <th class="text-center">異動日期</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                        <tr class="text-center">
                            <?php if(array_key_exists($row['class_type_id'], $choices['hourlyfee_category'])){ ?>
                            <td><?=$choices['hourlyfee_category'][$row['class_type_id']];?></td>
                            <?php }else{ ?>
                            <td></td>
                            <?php } ?>
                            <td><?=$choices['teacher_type'][$row['type']];?></td>
                            <td><?=$choices['hire_category'][$row['teacher_type_id']];?></td>
                            <?php if(!empty($row['assistant_type_id'])){ ?>
                            <td><?=$choices['hire_category'][$row['assistant_type_id']];?></td>
                            <?php }else{ ?>
                            <td></td>
                            <?php } ?>
                            <td><?=$row['hour_fee'];?></td>
                            <td><?=$row['traffic_fee'];?></td>
                            <td><?=$row['task'];?></td>
                            <td><?=$row['cre_user'];?></td>
                            <td><?=$row['cre_date'];?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>