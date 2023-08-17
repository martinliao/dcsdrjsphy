<!-- <?php print_r($datas)?> -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">出單日期</label>
                            <input class="form-group" value="<?= $sess_bldt?>" disabled>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr class="text-center">
                            <th align="center" rowspan="2">序號</th>
                            <th align="center" rowspan="1">總代號</th>
                            <th align="center" rowspan="1">金融機關名稱</th>
                            <th align="center" rowspan="2">存款人姓名或名稱(全銜)</th>
                            <th align="center" rowspan="2">鐘點費</th>
                            <th align="center" rowspan="2">交通費</th>
                            <th align="center" rowspan="2">稅率</th>
                            <th align="center" rowspan="2">稅額</th>
                            <th align="center" rowspan="2">二代健保稅額</th>
                            <th align="center" rowspan="2">免扣二代健保費</th>
                            <th align="center" rowspan="2">實付金額</th>
                            <th align="center" rowspan="2">備註</th>
                        </tr>
                        <tr>
                            <th align="center" colspan="2">分行別‧科目‧存戶帳號</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=0;$i<sizeof($datas);$i++){
                            $TRAFFIC_FEE = ($datas[$i]['traffic_fee'] < 0 ? 0 : $datas[$i]['traffic_fee'])
                        
                        ?>
                        
                        <tr>
                            <td rowspan="2"><?=($i+1);?></td>
                            <td><?=$datas[$i]["teacher_bank_id"];?></td>
                            <td><?=$datas[$i]["bankname"];?></td>
                            <td rowspan="2"><?=$datas[$i]["teacher_acct_name"];?> 
                            <td rowspan="2"><?=$datas[$i]["hour_fee"];?></td> <!--</td>  number_format($fields['HOUR_FEE'],",") -->
                            <td rowspan="2"><?=$TRAFFIC_FEE;?></td> <!--</td>  number_format($TRAFFIC_FEE,",") -->
                            <td rowspan="2"><a href="#" onclick="chgRate('<?= $datas[$i]['key']?>')"><?=($datas[$i]['tax_rate'] <= 0 ? 0 : $datas[$i]['tax_rate']);?></a></td>
                            <td rowspan="2"><?=$datas[$i]['hour_fee']*$datas[$i]['tax_rate'];?></td>
                            <td rowspan="2"><?=$datas[$i]["h_tax"];?></td>
                            <td rowspan="2"><a href="#" onclick="chg2HRate('<?= $datas[$i]['key']?>','<?= $datas[$i]['cnt']>0?'y':''?>')"><?=$datas[$i]["cnt"] > 0 ? "y" :"n" ?></a></td>
                            <td rowspan="2"><?=$datas[$i]["aftertax"];?></td>
                            <td rowspan="2"><?=$datas[$i]["note"];?></td>
                        </tr>
                        <tr class="text-center">
                            <td colspan="2"><?=$datas[$i]["teacher_account"];?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
                <a href="<?=base_url('pay/epay/')?>" class="btn btn-info">返回</a>
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
    function chgRate(x){
        var link = "<?=$link_refresh;?>";
        var myW=window.open(link+'?act=rate&key=' + x,'chgTax','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=600,width=900');
        myW.focus();
    }
    function chg2HRate(x,y){
        var link = "<?=$link_refresh;?>";
        var myW=window.open(link+'?act=hrate&key=' + x + '&p_2htax=' + y,'chgTax','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=600,width=900');
        myW.focus();
    }
</script> 