<!-- <?php print_r($datas); ?>
<?php print_r($trafficList); ?> -->
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
                        <input hidden id='syear' name='year' value="<?=$sess_year?>">
                        <input hidden id='sclass' name='class' value="<?=$sess_class?>">
                        <input hidden id='sterm' name='term' value="<?=$sess_term?>">
                        <input hidden id='sclassname' name='classname' value="<?=$sess_classname?>">
                        <input hidden id='sstartdate' name='startdate' value="<?=$sess_startdate?>">
                        <input hidden id='senddate' name='enddate' value="<?=$sess_enddate?>">
                        <input hidden id='scount' name='count' value="">
                        <input hidden id='sact' name='act' value="">
                        <input hidden id='spriceType' name='priceType' value="">
                        <input hidden id='sseq' name='seq' value="">
                        <input hidden id='schklist' name='chklist' value="">
                        <input hidden id='sselectlist' name='selectlist' value="">
                        <input hidden id='sumoney' name='umoney' value="">
                        <input hidden id='shmoney' name='hmoney' value="">
                        <input hidden id='stmoney' name='tmoney' value="">
                        <input hidden id='seditOne' name='editOne' value="">
                        <input hidden id='seditValue' name='editValue' value="">
                        <input hidden id='stex_data' name='tex_data' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" style="width: 60px;" value="<?=$sess_year?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" style="width: 80px;" value="<?=$sess_class?>" disabled>
                            </div>
                            <label class="control-label">期別:</label>
                            <div class="form-group" id="start_date">
                                <input type="text" class="form-control" style="width: 50px;" value="<?=$sess_term?>" disabled>
                            </div>
                            <label class="control-label">班期名稱:</label>
                            <div class="form-group" id="end_date">
                                <input type="text" class="form-control" style="width: 250px;" value="<?=$sess_classname?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">說明:</label>
                            <p style="font-size: 16px;"><span>★</span> <span>講師基本資料（例：帳號、地址...等，但不含身分證字號）在15A請款確認前如經修改，系統（15A及15B）資料會自動更新。</span></p>
                            <p style="font-size: 16px;"><span>★</span> <span>如有更動課表或換講師（例：功能9B與9D...等）致影響鐘點費計算時，請先將15B流水編號刪除並於15A點按〔重新轉入〕後，系統會自動將本週資料處理狀態清空後更新。</span></p>
                            <p style="font-size: 16px;"><span>★</span> <span>鐘點費單價及交通費，原則由班期〔鐘點費類別〕及〔聘請類別〕決定，仍可自行編修。</span></p>
                            <p style="font-size: 16px"><span>★</span> <span>15A點選〔待確認〕之前，請確認班期名稱是否有誤，有誤請先至9B逐一修改並儲存每一堂課表，再到15A重新轉入，班名即可更新。</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm" onclick="check_all(this,'c')">全選</button>
                            <button id='reenter' class="btn btn-info btn-sm">重新轉入</button>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr class="text-center" style="background-color: #8CBBFF;">
                            <th align="center" rowspan="2">選取</th>
                            <th align="center" rowspan="2">處理狀態</th>
                            <th align="center" rowspan="2">上課日期</th>
                            <th align="center" rowspan="2">擬設定請款方式</th>
                            <th align="center" rowspan="2">註記</th>
                            <td align="center" colspan="8" style="font-weight: bold;">講師</td>
                            <th align="center" rowspan="2">上課時數</th>
                            <th align="center" rowspan="2">單價</th>
                            <th align="center" rowspan="2">鐘點費</th>
                            <th align="center" rowspan="2">交通費</th>
                            <th align="center" rowspan="2">合計</th>
                        </tr>
                        <tr style="background-color: #8CBBFF;">
                            <th align="center">姓名</th>
                            <th align="center">聘請類別</th>
                            <th align="center">銀行/郵局</th>
                            <th align="center">銀行代碼</th>
                            <th align="center">帳號</th>
                            <th align="center">帳戶名稱</th>
                            <th align="center">通訊地址</th>
                            <th align="center">身份證字號</th>
                        </tr>
                    </thead>
                    <div id="dialog" title="" style="display:none;">
                    <tbody>
                        <?php foreach ($datas as $key => $data): ?>
                        <tr>
                            <?php if($data["status"]=="已設定為不請款"){?>
                                <td><input type="checkbox" name="c" value="<?= $data["seq"]?>" disabled></td>
                            <?php }else{?>
                                <td><input type="checkbox" name="c" value="<?= $data["seq"]?>"></td>
                            <?php }?>
                            
                            <td><?= $data["status"] ?></td>
                            <td id="use_date<?= $data["seq"] ?>" value="<?= $data["use_date"] ?>"><?= substr($data["use_date"],0,-8) ?></td>
                            <td>
                                <a style='cursor:pointer;'>
                                    <?php if($data["status"]=="已設定為不請款"){?>
                                        <span onclick="modechg('<?= $data["seq"] ?>','Y');" id="status1" value="0">設為請款<span>
                                    <?php }else if($data["status"]==""){?>
                                        <span onclick="modechg('<?= $data["seq"] ?>','N');" id="status1" value="0">設為不請款<span>
                                    <?php }else if($data["status"]=="待確認"){?>
                                        <span onclick="modechg('<?= $data["seq"] ?>','A');" id="status1" value="0">取消確認<span>
                                    <?php }?>                                  
                                </a>
                            </td>
                            <td>
                                <select name="remark_<?=$data['seq']?>">
                                    <option <?php echo $data["remark"]=="無"? 'selected':($data["remark"]==""? 'selected':'')?> value="無">無</option>
                                    <option <?php echo $data["remark"]=="領現金"? 'selected':''?> value="領現金">領現金</option>
                                    <option <?php echo $data["remark"]=="出席費"? 'selected':''?> value="出席費">出席費</option>
                                    <option <?php echo $data["remark"]=="監考費"? 'selected':''?> value="監考費">監考費</option>
                                </select>
                            </td>
                            <td id="thacher_name<?= $data["seq"] ?>" value="<?= $data["teacher_name"] ?>"><?= $data["teacher_name"] ?></td>
                            <td><?= $data["description"] ?></td>
                            <td><?= $data["bp_name"] ?></td>
                            <td><?= $data["teacher_bank_id"] ?></td>
                            <td><?= $data["teacher_account"] ?></td>
                            <td><?= $data["teacher_acct_name"] ?></td>
                            <td><?= $data["teacher_addr"] ?></td>
                            <td id="thacher_id<?= $data["seq"] ?>" value="<?= $data["teacher_id"] ?>"><?= $data["teacher_id"] ?></td>
                            <td><?= $data["hrs"] ?></td>
                            <td style="cursor:pointer" onclick="openPop('<?= $data["hrs"] ?>','<?= $data["unit_hour_fee"] ?>','<?= $key?>','<?= $data["seq"] ?>','unit')"><a class='moneyU_<?= $key?>'><?= $data["unit_hour_fee"] ?></a><input type="number" name='Umoney_<?= $data["seq"] ?>' value='<?= $data["unit_hour_fee"] ?>' hidden></td>
                            <td style="cursor:pointer" onclick="openPop('<?= $data["hrs"] ?>','<?= $data["hour_fee"] ?>', '<?=$key?>','<?= $data["seq"] ?>','hour')"><a class='moneyH_<?= $key?>'><?= $data["hour_fee"] ?></a><input type="number" name='Hmoney_<?= $data["seq"] ?>' value='<?= $data["hour_fee"] ?>' hidden></td>
                            <td style="cursor:pointer" onclick="openPop('<?= $data["hrs"] ?>','<?= $data["traffic_fee"] ?>', '<?=$key?>','<?= $data["seq"] ?>','traffic')"><a class='moneyT_<?= $key?>'><?= $data["traffic_fee"] ?></a><input type="number" name='Tmoney_<?= $data["seq"] ?>' value='<?= $data["traffic_fee"] ?>' hidden></td>
                            <td id="subtotalM_<?= $data["seq"] ?>"><?= $data["subtotal"] ?></td>
                        </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-12">
                        <button id='comfirm' class="btn btn-info btn-sm">待確認</button>
                        <a class="btn btn-info btn-sm" href="<?=base_url('pay/pay')?>">返回</a>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<!-- change Modal -->
<div class="modal fade bd-example-modal-lg moneyPop" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <h5 class="modal-title" id="exampleModalLongTitle"></h5> -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style='margin-top: -10px;'>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="display:flex;align-items: center;">
                    <span style='margin-right:5px;width:170px;font-size:14px'>請輸入要調整的金額</span>
                    <input id='moneyIpt' style='font-size:14px' type="text" class="form-control" value="">
                </div>
            </div>

            <table id='trafficTable' class='table table-bordered table-condensed table-hover' align='center'>
                <!-- 2021-0427, 增加台鐵票價查詢超連結 -->            
                <tr>
                    <th style='text-align: center; padding: 10px 0px' colspan="3"><a href="https://www.railway.gov.tw/tra-tip-web/tip/tip001/tip114/query" target="_blank" style='margin-right:5px; color:blue'>台鐵票價查詢</a><span style='color: blue'>(交通費合計=莒光號單程費用*2+300元)</span></th>
                </tr>
                <tr>
                    <th style='text-align: center;'>選取</th>
                    <th style='text-align: center;'>站名</th>
                    <th style='text-align: center;'>金額</th>
                </tr>

                <input type='radio' name='trafficRadio' value='0' hidden>
            <?php foreach ($trafficList as $key => $data): ?>
                <tr align='center'>
                    <td>
                        <input type='radio' name='trafficRadio' value='<?= $data['add_val2']?>' onclick="changeIptVal('<?= $data['add_val2']?>')">
                    </td>
                    <td><?= $data['description']?></td>
                    <td><?= $data['add_val2']?></td>
                </tr>
            <?php endforeach?>
            </table>
        
            <div class="modal-footer">
                <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">返回</button>
                <button type="button" class="btn btn-info btn-sm" onclick="tempSaveMoney()">確認</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
if("<?php echo ($result); ?>" != "0"){
    alert("<?php echo ($result); ?>");
    history.go(-1);
} 
    function check_all(obj,cName) 
    { 
        var checkboxs = document.getElementsByName(cName); 
        for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = true;} 
    } 

    function modechg(editOne,editValue)
    {   
        $('#seditOne').val(editOne);
        $('#seditValue').val(editValue);
        $('#sact').val("invoice");

        $( "#form" ).submit();
    }
    $(document).ready(function() {

        $('#reenter').click(function(){

            $('#sact').val("reenter");

            $( "#form" ).submit();

        });

        $('#comfirm').click(function(){
            var array = "";
            $("input:checkbox[name=c]:checked").each(function () {
                if(array==""){
                    array=this.value;
                }
                else{
                    array=array+",,"+this.value;
                }
            });
            if(array==""){
                alert("請選擇資料");
                return;
            }

            var array2 = "";
            $("select[name^='remark']").each(function () {
    
                if(array2==""){
                    array2=this.name+"_"+this.value;
                }
                else{
                    array2=array2+",,"+this.name+"_"+this.value;
                }
            });

            var array3 = "";
            $("input[name^='Umoney']").each(function () {
                if(array3==""){
                    array3=this.name+"_"+this.value;
                }
                else{
                    array3=array3+",,"+this.name+"_"+this.value;
                }
            });

            var array4 = "";
            $("input[name^='Hmoney']").each(function () {
                if(array4==""){
                    array4=this.name+"_"+this.value;
                }
                else{
                    array4=array4+",,"+this.name+"_"+this.value;
                }
            });

            var array5 = "";
            $("input[name^='Tmoney']").each(function () {
                if(array5==""){
                    array5=this.name+"_"+this.value;
                }
                else{
                    array5=array5+",,"+this.name+"_"+this.value;
                }
            });
            $('#schklist').val(array);
            $('#sselectlist').val(array2);
            $('#sumoney').val(array3);
            $('#shmoney').val(array4);
            $('#stmoney').val(array5);
            $('#sstart_date').val($('#datepicker1').val());
            $('#send_date').val($('#test1').val());
            
            var tex_data ='';
	
            $('input:checkbox[name=c]:checked').each(function(i) {
                tex_data +=  (($("#thacher_name"+$(this).val()).text()+"_"+$("#use_date"+$(this).val()).text())+"_"+$("#thacher_id"+$(this).val()).text()+",");
            }); 
            $('#stex_data').val(tex_data);
            $('#sact').val("comfirm");
            $( "#form" ).submit();

        });
        
        var checkboxs = document.getElementsByName('c'); 
        for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = false;} 
    });

    var tempData = [-1,-1,-1,-1];
    function openPop(hrs, money, index, seq, type) {
        tempData = [index, type, seq, hrs];

        $(".moneyPop").modal('show');
        $("#moneyIpt").val(money);

        if(type == 'traffic') {
            $("input[value=0]").prop('checked', true);

            $("#trafficTable").css("display", "inline-table");
        }
        else {
            $("#trafficTable").css("display", "none");
        }
    }

    function tempSaveMoney() {
        if(tempData[1]=="unit") {
            $(".moneyU_"+tempData[0]).html($("#moneyIpt").val());
            $("input[name=Umoney_"+tempData[2]+"]").val($("#moneyIpt").val());

            $(".moneyH_"+tempData[0]).html($("#moneyIpt").val()*Number(tempData[3]));
            $("input[name=Hmoney_"+tempData[2]+"]").val($("#moneyIpt").val()*Number(tempData[3]));
            
            let subtotal = Number($("input[name=Hmoney_"+tempData[2]+"]").val())+Number($("input[name=Tmoney_"+tempData[2]+"]").val());
            $("#subtotalM_"+tempData[2]).html(subtotal);
            $('#sumoney').val($("#moneyIpt").val());
            $('#shmoney').val($("#moneyIpt").val()*Number(tempData[3]));
        }
        else if(tempData[1]=="hour") {
            $(".moneyH_"+tempData[0]).html($("#moneyIpt").val());
            $("input[name=Hmoney_"+tempData[2]+"]").val($("#moneyIpt").val());

            $(".moneyU_"+tempData[0]).html($("#moneyIpt").val()/Number(tempData[3]));
            $("input[name=Umoney_"+tempData[2]+"]").val($("#moneyIpt").val()/Number(tempData[3]));

            let subtotal = Number($("input[name=Hmoney_"+tempData[2]+"]").val())+Number($("input[name=Tmoney_"+tempData[2]+"]").val());
            $("#subtotalM_"+tempData[2]).html(subtotal);
            $('#sumoney').val($("#moneyIpt").val()/Number(tempData[3]));
            $('#shmoney').val($("#moneyIpt").val());
        }
        else if(tempData[1]=="traffic") {
            $(".moneyT_"+tempData[0]).html($("#moneyIpt").val());
            $("input[name=Tmoney_"+tempData[2]+"]").val($("#moneyIpt").val());

            let subtotal = Number($("input[name=Hmoney_"+tempData[2]+"]").val())+Number($("input[name=Tmoney_"+tempData[2]+"]").val());
            $("#subtotalM_"+tempData[2]).html(subtotal);
            $('#stmoney').val($("#moneyIpt").val());
        }
        
        $(".moneyPop").modal('hide');

        $('#sact').val("setPrice");
        $('#spriceType').val("set"+tempData[1]+"price");
        $('#sseq').val(tempData[2]);
        $( "#form" ).submit();
    }

    function changeIptVal(price) {
        $("#moneyIpt").val(price);
    }
</script> 