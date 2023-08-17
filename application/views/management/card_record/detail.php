<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
            <form id="filter-form" role="form" class="form-inline" action="<?=$link_sort?>">     
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="hidden" name="seq_no" value="<?=$list[0]['seq_no']?>">
                            <input type="hidden" name="use_date" value="<?=$list[0]['use_date']?>">
                            <button class="btn btn-info btn-sm">依照已刷卡未刷卡排序</button>
                            <a href="<?=base_url("management/card_record/"."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">回刷卡紀錄管理</a>
                            <!--<a href="javascript:window.history.go(-1);" class="btn btn-info">回刷卡紀錄管理</a>-->
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">期別</th>
                            <th class="text-center">組別</th>
                            <th class="text-center">學號</th>
                            <th class="text-center">服務單位</th>
                            <th class="text-center">職稱</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">刷卡日期</th>
                            <th class="text-center">遲到起算時間</th>
                            <th class="text-center">實際下課時間</th>
                            <th class="text-center">簽到時間</th>
                            <th class="text-center">簽退時間</th>
                            <th class="text-center">刷卡記錄</th>
                            <th class="text-center">時數(應/未)</th>
                            <th class="text-center">備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row) {?>
                        <tr class="text-center">
                            <td><?=$row['term']?></td>
                            <td><?=$row['group_no']?></td>
                            <td><?=$row['st_no']?></td>
                            <?php $bureau='';
                                if($row['ou_gov']!=NULL){
                                    $bureau = $row['ou_gov'];}
                                    else{
                                        $bureau = $row['bureau_name'];
                                    }
                                ?>
                            <td><?=$bureau?></td>
                            <td><?=$row['title']?></td>
                            <td><?=$row['user_name']?></td>
                            <?php 
                                $date=substr($row['use_date'],0,10);
                            ?>
                            <td><?=$date?></td>
                            <td><?=$row['checkin_time']?></td>
                            <td><?=$row['checkout_time']?></td>
                            <td><?=$row['LOGIN_TIME']?></td>
                            <td><?=$row['LOGOUT_TIME']?></td>
                            <td><?=$row['DOORLOGS_STR']?></td>
                            <?php if($row['LOGIN_TIME']==''||$row['LOGOUT_TIME']==''){
                                $unstudy=$row['SUM_HOURS'];
                            }else{
                                $unstudy=$row['unstudyhours'];
                            }
                            ?>
                            <td><?=$row['SUM_HOURS']?>/<?=$unstudy?></td>
                            <td style="color:red"><?=$row['remark']?></td>
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

<script>
$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });
  $("#test3").datepicker();
  $('#test4').click(function(){
    $("#test3").focus();
  });


  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });
});

</script>