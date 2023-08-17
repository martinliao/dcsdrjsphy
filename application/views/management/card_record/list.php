<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">     
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
           
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" id="datepicker1" name="start_date1" value="<?=$filter['start_date1']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                    </div>
         
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr style="background-color:#8CBBFF;">
                            <th class="text-center">教室</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">應到</th>
                            <th class="text-center">實到</th>
                            <th class="text-center">人工簽到數</th>
                            <th class="text-center">身障</th>
                            <th class="text-center">承辦人</th>
                            <th class="text-center">填報與設定</th>
                            <th class="text-center">簽到退管理</th>
                            <th class="text-center">刷卡紀錄</th>
                            <th class="text-center">查堂紀錄</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $roomCount=0;$expect_sum=0;$real_sum=0; $course_count=0;?>
                        <?php foreach($list as $row) {?>
                        <tr class="text-center">
                            <td><?=$row['room_code']?></td>
                            <td><?=$row['class_name']?>第<?=$row['term']?>期</td>
                            <td><?=$row['expect_num']?></td>
                            
                            <?php if($row['enable']==1){
                                $hpn='<font color="red">'.$row['hpn'].'</font>';
                                $dpn='<font color="red">'.$row['dpn'].'</font>';
                            }else{
                                if(isset($row['dpn'])){
                                    $dpn=$row['dpn'];
                                }else{
                                    $dpn=$row['phydisabled'];
                                }
                                if(isset($row['dpn'])){
                                    $hpn=$row['hpn'];
                                }else{
                                    $hpn=0;
                                }
                                
                            }?>
                            <td><?php  if($row['enable']==1){
                                echo $row['real_num']+$row['hpn'];
                            }else{
                                echo $row['real_num']+$hpn;
                            }  ?> </td>
                            <td><?=$hpn?></td>
                            <td><?=$dpn?></td>
                            <td><?=$row['worker_name']?></td>
                            <?php if($row['enable']==1){
                                $link="javascript:void(0);";
                            }else{
                                $link=$row['link_new'];
                            }?>
                            <td><a href="<?=$link?>" id='test'>前往</a></td>
                            <td><a href="<?=$row['link_detail']?>">前往</a></td>
                            <td><a href="<?=$row['link_export']?>" target=_blank>匯出</a>/<a href="<?=$row['link_import']?>">匯入</a>/<a href="<?=$row['link_add']?>">補登</a></td>
                            <td><a href="<?=$row['link_patrol']?>">前往</a></td>
                        </tr>
                        <?php $expect_sum+=$row['expect_num']; $real_sum+=$row['real_num']; $course_count+=1;}?>
                        <span style="color:blue"><label class="control-label">辦理班期<?=$course_count?>班、使用教室<?=$course_count?>間、應到<?=$expect_sum?>人、實到<?=$real_sum?>人

                        <span style="color:red">當日申請素食人數:<?=$data3['fields_total']?>人</span>
                        </label>
                        </span>
                        
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