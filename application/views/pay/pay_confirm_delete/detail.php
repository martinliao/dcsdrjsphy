<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr class="text-center">
                            <th class="text-center" rowspan="2">年度</th>
                            <th class="text-center" rowspan="2">班期代碼</th>
                            <th class="text-center" rowspan="2">班期名稱</th>
                            <th class="text-center" rowspan="2">期別</th>
                            <th class="text-center" rowspan="2">處理狀態</th>
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
                            <th class="text-center">銀行/郵局分行</th>
                            <th class="text-center">銀行代碼</th>
                            <th class="text-center">帳號</th>
                            <th class="text-center">通訊地址</th>
                            <th class="text-center">身份證字號</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                            <tr class="text-center">
                                <td><?= $data["year"]?></td>
                                <td><?= $data["class_no"]?></td>
                                <td><?= $data["class_name"]?></td>
                                <td><?= $data["term"]?></td>
                                <td><?= $data["status"]?></td>
                                <td><?= substr($data["use_date"], 0, 10)?></td>
                                <td><?= $data["teacher_name"]?></td>
                                <td><?= $data["cDESCRIPTION"]?></td>
                                <td><?= $data["dDESCRIPTION"]?></td>
                                <td><?= $data["teacher_bank_id"]?></td>
                                <td><?= $data["teacher_account"]?></td>
                                <td><?= $data["teacher_addr"]?></td>
                                <td><?= $data["teacher_id"]?></td>
                                <td><?= $data["hrs"]?></td>
                                <td><?= $data["unit_hour_fee"]?></td>
                                <td><?= $data["hour_fee"]?></td>
                                <td><?= $data["traffic_fee"]?></td>
                                <td><?= $data["subtotal"]?></td>
                            </tr>
                        
                        <?php endforeach?>
                    </tbody>
                </table>
                <a href="<?=base_url('pay/pay_confirm_delete/')?>" class="btn btn-info">返回</a>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script type="text/javascript"> 
    function check_all(obj,cName) 
    { 
        var checkboxs = document.getElementsByName(cName); 
        for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
    } 

    function modechg(status1)
    {   
        var status=status1.id;
        if(status=='status1')
        {   
            document.getElementById("status1").innerText = "設為不請款"; 
            document.getElementById("test").innerText= "已設為請款"; 
            document.getElementById("status1").id="status2";
        }
        if(status=='status2')
        {
            document.getElementById("status2").innerText = "設為請款"; 
            document.getElementById("test").innerText= "已設為不請款"; 
            document.getElementById("status2").id="status1";
        }
    }
</script> 