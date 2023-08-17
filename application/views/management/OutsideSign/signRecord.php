<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> 刷卡紀錄
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">  
                <div class="row">
                    <div class="col-xs-12">
                        <a href="<?=$link_export?>" class="btn btn-info">匯出</a>
                        <!--<a href="javascript:window.history.go(-1);" class="btn btn-info">回刷卡紀錄管理</a>-->
                    </div>
                </div>
                
                <!-- /.table head -->
                <p style="text-align: center">應到<?=$list[count($list)-1]['total']?>人 / 實到<?=$list[count($list)-1]['actually']?>人 / 未刷到<?=$list[count($list)-1]['not_arrived']?>人</p>
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">學號</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">刷卡日期</th>
                            <th class="text-center">簽到時間</th>
                            <th class="text-center">簽退時間</th>
                            <th class="text-center">刷卡記錄</th>
                            <th class="text-center">時數(應/未)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row) {?>
                        <tr class="text-center">
                            <td><?=$row['no']?></td>
                            <td><?=$row['name']?></td>
                            <td><?=$row['sign_date']?></td>
                            <td><?=$row['signInTime']?></td>
                            <td><?=$row['signOutTime']?></td>
                            <td><?=$row['signLog']?></td>
                            <td><?=$row['hours']?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
               
            </div>

            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
