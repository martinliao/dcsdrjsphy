<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <form id="form" method="GET">
                        <input hidden id='sworkname' name='workname' value="">
                        <input hidden id='sappnos' name='appno' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">申請編號:</label>
                            <input id="appno" value="<?= $sess_appno?>" type="text" class="form-control">
                        </div>                
                    </div>
                    
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">編號</th>
                            <th class="text-center" rowspan="2">出單日期</th>
                            <th class="text-center" rowspan="2">入帳日期</th>
                            <th class="text-center" rowspan="2">年度</th>
                            <th class="text-center" rowspan="2">班期名稱</th>
                            <th class="text-center" rowspan="2">期別</th>
                            <th class="text-center" rowspan="2">上課日期</th>
                            <th class="text-center" colspan="7">講師</th>
                            <th class="text-center" rowspan="2">上課時數</th>
                            <th class="text-center" rowspan="2">單價</th>
                            <th class="text-center" rowspan="2">鐘點費</th>
                            <th class="text-center" rowspan="2">交通費</th>
                            <th class="text-center" rowspan="2">合計</th>
                        </tr>
                        <tr>
                            <th class="text-center">姓名</th>
                            <th class="text-center">聘請類別</th>
                            <th class="text-center">銀行/郵局</th>
                            <th class="text-center">銀行代碼</th>
                            <th class="text-center">帳號</th>
                            <th class="text-center">帳戶名稱</th>
                            <th class="text-center">身分證字號</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $total_hour_fee = 0;
                        $total_traffic_fee = 0;
                        $total_subtotal = 0;
                    ?>
                    <?php $count=1; foreach ($datas as $data): ?>
                        <tr class="text-center">
                            <td><?=$count?></td>
                            <td><?=substr($data["bill_date"],0,10)?></td>
                            <td><?=substr($data["entry_date"],0,10)?></td>
                            <td><?=$data["year"]?></td>
                            <td><?=$data["class_name"]?></td>
                            <td><?=$data["term"]?></td>
                            <td><?=substr($data["use_date"],0,10)?></td>
                            <td><?=$data["teacher_name"]?></td>
                            <td><?=$data["description"]?></td>
                            <td><?=$data["bank_name"]?></td>
                            <td><?=$data["teacher_bank_id"]?></td>
                            <td><?=$data["teacher_account"]?></td>
                            <td><?=$data["teacher_acct_name"]?></td>
                            <td><?=$data["teacher_id"]?></td>
                            <td><?=$data["hrs"]?></td>
                            <td><?=number_format($data["unit_hour_fee"])?></td>
                            <td><?=number_format($data["hour_fee"])?></td>
                            <td><?=$data["traffic_fee"]<0?0:number_format($data["traffic_fee"])?></td>
                            <td><?=number_format($data["subtotal"])?></td>
                        </tr>
                        <?php
                            $total_hour_fee += $data["hour_fee"];
                            $total_traffic_fee += $data["traffic_fee"]<0?0:$data["traffic_fee"];
                            $total_subtotal += $data["subtotal"];
                        ?>
                    <?php $count++; endforeach?>

                        <?php if(isset($datas)) {?>
                            <tr class="text-center">
                                <td colspan="16" class="text-right"><strong>小計</strong></td>
                                <td><?=number_format($total_hour_fee)?></td>
                                <td><?=number_format($total_traffic_fee)?></td>
                                <td><?=number_format($total_subtotal)?></td>
                            </tr>
                            <tr class="text-center">
                                <td colspan="16" class="text-right"><strong>總計</strong></td>
                                <td><?=number_format($total_hour_fee)?></td>
                                <td><?=number_format($total_traffic_fee)?></td>
                                <td><?=number_format($total_subtotal)?></td>
                            </tr>
                        <?php }?>
                        
                    </tbody>
                </table>
                <a href="<?=base_url('pay/teach_pay_list')?>" class="btn btn-info">返回</a>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->