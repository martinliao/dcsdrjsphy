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
                            <label class="control-label">年度:</label>
                            <select name="year">
                                <option>請選擇</option>
                            <?php for($year=(int)date("Y")-1909;$year>=105;$year--): ?>
                                <?php $selected = ($filter['year'] == $year)? 'selected': '';?>
                                <option value="<?=$year?>" <?=$selected?>><?=$year?></option>
                            <?php endfor ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">月份:</label>
                            <select name="month">
                                <option>請選擇</option>
                                <?php for($month=1;$month<=12;$month++): ?>
                                    <?php $selected = ($filter['month'] == $month)? 'selected': '';?>
                                    <option value="<?=$month?>" <?=$selected?>><?=$month?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info">查詢</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-hover table-condensed table-bordered">
                            <thead>
                                <th class="text-center">年度/班期名稱/期別</th>
                                <th class="text-center">起日yyyy-mm-dd</th>
                                <th class="text-center">迄日yyyy-mm-dd</th>
                                <th class="text-center">線上名稱課程</th>
                                <th class="text-center">時數</th>
                                <th class="text-center">講座名稱</th>
                            </thead>
                            <tbody>

                                <?php foreach($stu_mixs as $mix): ?>
                                <tr>
                                    <td><?="{$mix->year}年{$mix->require_name}(第{$mix->term})期"?></td>
                                    <td class="text-center"><?=$mix->groupstartdate?></td>
                                    <td class="text-center"><?=$mix->groupenddate?></td>
                                    <td>
                                        <?php foreach(explode(',', $mix->groupname) as $name): ?>
                                            <?php 
                                                $data = explode('&$2', $name);
                                            ?>
                                            <a target='_blank' href='https://elearning.taipei/elearn/courseinfo/so.php?v=<?=$data[1]?>'>
                                            <?=$data[0]?>
                                            <a>
                                            <br>
                                        <?php endforeach ?>
                                    </td>
                                    <td class="text-center"><?=$mix->hours?></td>
                                    <td class="text-center"><?=$mix->teacher_name?></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>