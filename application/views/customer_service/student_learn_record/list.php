<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" action="">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" id="query_class_name" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                            </div>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">學員姓名:</label>
                                <input type="text" class="form-control" id="query_student_name" name="query_student_name" value="<?=$filter['query_student_name']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">局處名稱:</label>
                                <input type="text" class="form-control" id="query_bureau_name" name="query_bureau_name" value="<?=$filter['query_bureau_name']?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">依日期區間查詢:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" id="datepicker1" name="start_date1" value="<?=$filter['start_date1']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text"  class="form-control datepicker" id="test1"  name="end_date1" value="<?=$filter['end_date1']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="clear" id="clear"value="">
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm" onclick="selectAction(1)">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="doClear()">清除</button>
                            <button class="btn btn-info btn-sm" onclick="selectAction(2)">匯出</button>
                            <button class="btn btn-info btn-sm" onclick="selectAction(3)">匯入</button>
                        </div>
                    </div>
                    <?php if(!empty($list)) {?>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
            
                </form>
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
                            <th class="text-center">書證下載</th>
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
                        
                        <?php if($tmp_count!=0){?>
                        <tr>
                            <td><?=$row['name']?></td>
                            <td><?=$row['company']?></td>
                            <td><?=$row['id']?></td>
                            <td><?=$row['class_name']?></td>
                            <td><?=$row['term']?></td>
                            <td><?=substr($row['start_date1'],0,10)?>~<?=substr($row['end_date1'],0,10)?></td>
                            <td><?=$row['range']?></td>
                            <td class="text-center" colspan="6">
                                <?php if (isset($row['certs'])): ?>
                                    <?php foreach ($row['certs'] as $cert): ?>
                                    <a href="/base/admin/management/certificate_list/download_cer_pdf/<?=htmlspecialchars($cert->id, ENT_HTML5|ENT_QUOTES)?>"><?=htmlspecialchars($cert->cer_name, ENT_HTML5|ENT_QUOTES)?></a><br>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </td>                          
                        </tr>
                       
                        <?php }?>
                        <?php $count++;}?>
                        <?php if($i==$count){?>
                        <tr>
                            <td class="text-right" colspan="6">總計</td>
                            <td><?=$tmp_count?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
                
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
                <?php }?>
            </div>
            
        </div>
    </div>
</div>

<script>
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

function doClear(){
    /*document.all.query_class_name.value = "";
    document.all.query_student_name.value = "";  
    document.all.query_bureau_name.value = "";
    document.all.datepick1.value = "";
    document.all.test1.value = "";
    document.all.query_year.value = "";*/
    document.getElementById("clear").value = 0;
}

function selectAction($number){
    if($number==1){
        document.getElementById("filter-form").action = "<?=$link_search;?>";
        document.filter-form.submit();
    }
    if($number==2){
        document.getElementById("filter-form").action = "<?=$link_export;?>";
        document.filter-form.submit();
    }
    if($number==3){
        document.getElementById("filter-form").action = "<?=$link_import;?>";
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
