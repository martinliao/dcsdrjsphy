<!-- <?php print_r($datas)?> -->
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
                        <tr>
                            <th>姓名</th>
                            <th>帳戶名稱</th>
                            <th>身分證</th>
                            <th>職稱</th>
                            <th>任職機關</th>
                            <th>學歷</th>
                            <th>經歷</th>
                            <th>通訊地址</th>
                            <th>手機</th>
                            <th>EMAIL</th>
                            <th>銀行代碼</th>
                            <th>銀行帳號</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        
                            <tr class="text-center">
                                <td><?= $data["name"]?></td>
                                <td><?= $data["account_name"]?></td>
                                <td><?= $data["rpno"]!=''?$data["rpno"]:$data["idno"]?></td>
                                <td><?= $data["job_title"]?></td>
                                <td><?= $data["institution"]?></td>
                                <td><?= $data["education"]?></td>
                                <td><?= $data["major"]?></td>
                                <td><?= $data["CITY_NAME"]?><?= $data["SUBCITY_NAME"]?><?= $data["route"]?><?= $data["address"]?></td>
                                <td><?= $data["mobile"]?></td>
                                <td><?= $data["email"]?></td>
                                <td><?= $data["bank_code"]?>(<?= $data["DESCRIPTION"]?>)</td>
                                <td><?= $data["bank_account"]?></td>
                            </tr>
                        
                        <?php endforeach?>
                    </tbody>
                </table>

                <a href="<?=base_url('pay/lecture_money_search/')?>" class="btn btn-info">返回</a>
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