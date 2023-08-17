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
                    <form id="form" method="get">
                        <input hidden id='iteacher' name='nteacher' value="">                      
                        <input hidden id='iid' name='nid' value="">                      
                        <input hidden id='istart' name='nstart' value="">
                        <input hidden id='iend' name='nend' value="">
                        <input hidden id='iperpage' name='nperpage' value="">
                        <input hidden id='irows' name='rows' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">講師姓名:</label>
                            <input type="text" id="teacher" value="<?=$sess_nteacher?>" name="teacher" class="form-control">
                            <label class="control-label">身分證字號:</label>
                            <input type="text" id="id" value="<?=$sess_nid?>" name="id" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">上課日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_nstart?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <span>至</span>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_nend?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="ClearData()">清除</button>
                            <button type="button" class="btn btn-info btn-sm" onclick="resendFun()">重新寄信</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <form id="sendform" method="post">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                        <tr>
                            <th class="text-center" width='30%'>班期名稱</th>
                            <th class="text-center" width='15%'>上課日期</th>
                            <th class="text-center" width='10%'>講座姓名</th>
                            <th class="text-center" width='20%'>E-Mail</th>
                            <th class="text-center" width='15%'>錯誤原因</th>
                            <th class="text-center" width='10%'>重新寄信</th>
                        </tr>
                        <?php if(sizeof($datas) > 0) { ?>
                            <?php foreach ($datas as $data): ?>
                            
                                <tr class="text-center">
                                    <td><?= htmlspecialchars($data["class_name"],ENT_HTML5|ENT_QUOTES)?>(第<?= htmlspecialchars($data["term"],ENT_HTML5|ENT_QUOTES)?>期)</td>
                                    <td><?= htmlspecialchars($data["use_date"],ENT_HTML5|ENT_QUOTES)?></td>
                                    <td><?= htmlspecialchars($data["teacher_name"],ENT_HTML5|ENT_QUOTES)?></td>
                                    <td><?= htmlspecialchars($data["email"],ENT_HTML5|ENT_QUOTES)?></td>
                                    <td><?= (!empty($data["error_info"]))?htmlspecialchars($data["error_info"],ENT_HTML5|ENT_QUOTES):'尚未收信'?></td>
                                    <td><input type="checkbox" name="resend[]" value="<?=htmlspecialchars($data["seq"],ENT_HTML5|ENT_QUOTES)?>"></td>
                                </tr>
                            <?php endforeach?>
                        <?php } ?>
                </table>
                </form>
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div><br>
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>


<script type="text/javascript">
function sendFun(){
    if($('#datepicker1').val() == "" || $('#test1').val() == ""){
        alert("請選擇日期區間")
        return;
    }
    
    $('#Search').click();
}

function check_all(obj,cName) 
{ 
    var checkboxs = document.getElementsByName(cName); 
    for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
} 

function resendFun(){
    if(confirm('是否確認重新寄送?')){
        $( "#sendform" ).submit();
    }
   
    return false;
}

$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        if($('#datepicker1').val() == "" || $('#test1').val() == ""){
            alert("請選擇日期區間")
            return;
        }

        $('#iteacher').val($('#teacher').val());
        $('#iid').val($('#id').val());
        $('#istart').val($('#datepicker1').val());
        $('#iend').val($('#test1').val());
        $('#iperpage').val($('#perpage').val());
        $('#irows').val($('select[name=rows]').val());

        $( "#form" ).submit();

    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });

    $("#money1").datepicker();
    $('#money2').click(function(){  
    $("#money1").focus();   
  });
});
</script>