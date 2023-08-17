<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php if(empty($list)){?>
                <form id="data-form" role="form" class="form-inline" method="post" action="<?=$link_import?>" enctype="multipart/form-data">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash()?>" />
                    <input type="hidden" name="import" value="import">
                    <input type="file" name="myfile" class="button" style="float: left">
                    <input type="submit" value="上傳" class="button">
                    <a href="../../files/example_files/student_record.csv" target="_blank">CSV格式下載</a>
                </form>
                <a href="<?=base_url('customer_service/student_learn_record/')?>" class="btn btn-info">返回</a>
                <?php }?>
                <!-- /.table head -->
                <?php if(!empty($list)){?>
                    
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">姓名</th>
                            <th class="text-center">機關</th>
                            <th class="text-center">身分證字號</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">開課起訖日</th>
                            <th class="text-center">研習時數</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $count=0; $tmp_count=0;$tmp='';$i=0;?>
                        <?php foreach($list as $row){$i++;?>
                        <?php 
                            if($tmp==$row['name']||$count==0){
                                $tmp=$row['name'];
                                $tmp_count+=$row['range'];
                            }else{
                                echo "<tr><td class='text-right' colspan='6'>總計</td>";
                                echo '<td>'.$tmp_count.'</td>';
                                echo "</tr>";
                                $tmp=$row['name'];
                                $tmp_count=0;
                                $tmp_count+=$row['range'];
                            }
                        ?>
                        <tr>
                            <td><?=$row['name']?></td>
                            <td><?=$row['company']?></td>
                            <td><?=$row['id']?></td>
                            <td><?=$row['class_name']?></td>
                            <td><?=$row['term']?></td>
                            <td><?=substr($row['start_date1'],0,10)?>~<?=substr($row['end_date1'],0,10)?></td>
                            <td><?=$row['range']?></td>
                        </tr>
                        <?php $count++; }?>
                        <?php if($i==$count){?>
                        <tr>
                            <td class="text-right" colspan="6">總計</td>
                            <td><?=$tmp_count?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
                <?php }?>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>
function doClear(){
    document.all.query_class_name.value = "";
    document.all.query_student_name.value = "";  
    document.all.query_bureau_name.value = "";
    document.all.datepick1.value = "";
    document.all.test1.value = "";
    document.all.query_year.value = "";
}

function selectAction($number){
    if($number==1){
        document.getElementById("filter-form").action = "<?=$link_index;?>";
        document.filter-form.submit();
    }
    if($number==2){
        document.getElementById("filter-form").action = "<?=$link_export;?>";
        document.filter-form.submit();
    }
}

$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });

  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });
});

</script>
