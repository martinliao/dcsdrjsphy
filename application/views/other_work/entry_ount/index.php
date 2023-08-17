<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <div class="panel-body">
                <div class="col-xs-12">
                    <h3><p style="text-align: center; width: 100%;">臺北市政府公務人員訓練處-學員服務網點閱率統計表</p></h3>
                    <div class="col-xs-6">
                        <font style="text-align: left;color:blue;">系統計算起始日：2020/11/16</font>
                    </div>
                    <div class="col-xs-6" style="text-align: right;">
                        <font style="color:blue">單位:次</font>
                    </div>
                    <table class="table table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">年度\項目</th>
                                <th class="text-center">線上問卷調查</th>
                                <th class="text-center">線上請假登錄</th>
                                <th class="text-center">線上茹素登記</th>
                                <th class="text-center">下課刷退系統</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($list as $entry_count): ?>
                            <tr>
                                <td class="text-center"><?=$entry_count->count_year - 1911?></td>
                                <td class="text-center"><?=$entry_count->questionnaire?></td>
                                <td class="text-center"><?=$entry_count->leave_login?></td>
                                <td class="text-center"><?=$entry_count->vegetarian_apply?></td>
                                <td class="text-center"><?=$entry_count->signout?></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                       
                    </div>

                </div>                
            </div>
        </div>
    </div>
</div>
