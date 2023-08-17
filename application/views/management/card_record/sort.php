<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
           
               

                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th class="text-center" colspan="3" style="color:red">未刷卡簽到學員清單</th>
                    </tr>
                        <tr>
                            <th class="text-center">組別</th>
                            <th class="text-center">學號</th>
                            <th class="text-center">姓名</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($lose as $l){?>
                        <tr class="text-center">
                            <td><?=$l['group_no']?></td>
                            <td><?=$l['st_no']?></td>
                            <td><?=$l['user_name']?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>

                 <!-- /.table head -->
                 <table class="table table-bordered table-condensed table-hover" style="margin-bottom:10%">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="4" style="color:red">已刷卡簽到學員清單</th>
                        </tr>
                        <tr>
                            <th class="text-center">組別</th>
                            <th class="text-center">學號</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">刷卡日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($list as $row) {?>
                    <?php if($row['LOGIN_TIME']!==''){?>
                        <tr>
                            <td class="text-center"><?=$row['group_no']?></td>
                            <td class="text-center"><?=$row['st_no']?></td>
                            <td class="text-center"><?=$row['user_name']?></td>
                            <?php $time = substr($row['use_date'],0,10);?>
                            <td>日期:<?=$time?> 時間:<?=$row['LOGIN_TIME']?></td>
                        </tr>
                        <?php }?>
                        <?php }?>
                    </tbody>
                </table>
                <a href="javascript:window.history.go(-1);"  class="btn btn-info">回上頁</a>

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