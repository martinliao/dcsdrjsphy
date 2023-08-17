<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> 便當登記-新增
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <p>※非當日班期或工作人員、學員需用餐請自行新增，星號欄位為必填</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                        <form id="form-list" method="POST">
                        <input type="hidden" name="mode" id="mode" value=""></input>
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <table border="1" class="table table-bordered table-condensed table-hover" style="min-width: 580px">
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*用餐日期：</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control datepicker" value="" id="datepicker1" name="use_date">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                                    class="fa fa-calendar"></i></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*班期名稱：</td>
                                    <td>
                                        <input type="text" class="form-control" value="" id="class_name" name="class_name">
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*教室代碼：</td>
                                    <td>
                                        <input type="text" class="form-control" value="" id="room_id" name="room_id">
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*用餐人員姓名：</td>
                                    <td>
                                        <input type="text" class="form-control" value="" id="dining_name" name="dining_name">
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*用餐人員類別：</td>
                                    <td>
                                        <select id="type" name="type">
                                            <option value="">請選擇</option>
                                            <option value="1">講座</option>
                                            <option value="2">學員</option>
                                            <option value="3">工作人員</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;width: 30%">*放置或用餐地點：</td>
                                    <td>
                                        <select id="place" name="place">
                                            <option value="">請選擇</option>
                                            <option value="B">B區</option>
                                            <option value="C">C區</option>
                                            <option value="E">E區</option>
                                            <option value="R">餐廳</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*用餐方式：</td>
                                    <td>
                                        <select id="way" name="way">
                                            <option value="">請選擇</option>
                                            <option value="1">紙盒</option>
                                            <option value="2">鐵盒</option>
                                            <option value="3">餐盤</option>
                                        </select>
                                        (餐盤外送須自取)
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*葷／素：</td>
                                    <td>
                                        <select id="food_type" name="food_type">
                                            <option value="">請選擇</option>
                                            <option value="1">葷</option>
                                            <option value="2">素</option>
                                        </select>
                                        (葷、素請分開登記)
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">*數量：</td>
                                    <td>
                                        <input type="text" class="form-control" value="1" id="num" name="num">
                                    </td>
                                </tr>
                                <tr style="background-color: #c9eeff">
                                    <td style="color: red;font-weight: bold;text-align: right;">備註：</td>
                                    <td>
                                        <input type="text" class="form-control" style="width: 100%" maxlength="50" value="" id="remark" name="remark">
                                    </td>
                                </tr>
                            </table>
                        </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='clear' class="btn btn-info btn-sm" style="font-weight: bold;background-color: #ffc107;color: white">清除</button>
                            <button id="save" class="btn btn-info btn-sm" style="font-weight: bold;background-color: #ffc107;color: white">儲存</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
function dateDiff(sDate1, sDate2){    
   var aDate, oDate1, oDate2, iDays;
   
   oDate1 = new Date(sDate1);    
   oDate2 = new Date(sDate2); 
   iDays = (oDate2-oDate1)/86400000;;   

   return  iDays; 
}

function dateCompare(sDate1, sDate2){
   var aDate, oDate1, oDate2, iDays;
   
   oDate1 = new Date(sDate1);    
   oDate2 = new Date(sDate2); 
   if(oDate1 > oDate2){
        return true;
   }

   return  false; 
}

$(document).ready(function() {
    $("#datepicker1").datepicker({
        minDate: new Date()
    });
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();   
    });
});

$('#clear').click(function(){
    $('#datepicker1').val('');
    $('#class_name').val('');
    $('#room_id').val('');
    $('#dining_name').val('');
    $('#type').val('');
    $('#place').val('');
    $('#way').val('');
    $('#food_type').val('');
    $('#num').val('');
    $('#remark').val('');
});
$(document).ready(function() {
    <?php
        if($reload){
            echo 'self.opener.location.reload();';
        }
    ?>
    $('#save').click(function(){


        if($('#datepicker1').val() == ''){
            alert('請輸入用餐日期');
            return false;
        }

        if($('#class_name').val().trim() == ''){
            alert('請輸入班期名稱');
            return false;
        }

        if($('#room_id').val().trim() == ''){
            alert('請輸入教室代碼');
            return false;
        }

        if($('#dining_name').val().trim() == ''){
            alert('請輸入用餐人員姓名');
            return false;
        }

        if($('#type').val() == ''){
            alert('請選擇用餐人員類別');
            return false;
        }

        if($('#place').val() == ''){
            alert('請選擇放置或用餐地點');
            return false;
        }

        if($('#way').val() == ''){
            alert('請選擇用餐方式');
            return false;
        }

        if($('#food_type').val() == ''){
            alert('請選擇葷/素');
            return false;
        }

        if($('#num').val() == ''){
            alert('請輸入數量');
            return false;
        }

        $('#mode').val('add');
        $("#form-list").submit();
    });
});
</script>