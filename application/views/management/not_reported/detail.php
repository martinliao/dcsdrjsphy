<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="data-form" role="form" class="form-inline" method="POST" action="">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" value="<?=$class_info->year?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$class_info->term?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$class_info->class_no?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$class_info->class_name?>" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- /.table head -->
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">編號</th>
                                <th class="text-center">未報到</th>
                                <th class="text-center">學號</th>
                                <th class="text-center">局處名稱</th>
                                <th class="text-center">身分證ID</th>
                                <th class="text-center">姓名</th>
                                <th class="text-center">職稱</th>
                                <th class="text-center">備註</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($not_reporteds as $key => $not_reported): ?>
                            <tr>
                                <td><?=$key+1?></td>
                                <td><input type="checkbox" name="not_reported[]" value="<?=$not_reported->id?>" <?=($not_reported->yn_sel == 5) ? 'checked' : ''; ?> ></td>
                                <td><?=$not_reported->st_no?></td>
                                <td><?=$not_reported->be_name?></td>
                                <td><?=$not_reported->id?></td>
                                <td><?=$not_reported->name?></td>
                                <td><?=$not_reported->job_title?></td>
                                <td><?=(empty($not_reported->cnt)) ? '(無刷到紀錄)' : ''; ?></td>
                            </tr>
                            <?php endforeach?>
                        </tbody>
                    </table>

                </form>            
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
