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
                        <input hidden id='ischedule' name='nschedule' value="">                      
                        <input hidden id='iname' name='nname' value="">                      
                        <input hidden id='igender' name='ngender' value="">
                        <input hidden id='iid' name='nid' value="">
                        <input hidden id='ilocation' name='nlocation' value="">
                        <input hidden id='ibirthday' name='nbirthday' value="">
                        <input hidden id='iclassdate' name='nclassdate' value="">
                        <input hidden id='sact' name='act' value="">
                        <input hidden id='srows' name='rows' value="0">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style='width: 113px;'>班期名稱:</label>
                            <input type="text" id="schedule" name="schedule" value="<?php echo $schedule ?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style='width: 113px;'>上課日期:</label>
                            <div class="input-group"  >
                                <input type="text" class="form-control datepicker" id="datepicker3" name="datepicker3" value="<?php echo $classdate ?>">
                                <span class="input-group-addon" style="cursor: pointer;"id="datepicker4"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label">生日:</label>
                            <div class="input-group"  >
                                <input type="text" class="form-control datepicker" id="datepicker1" name="datepicker1" value="<?php echo $birthday ?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style='width: 113px;'>身分證字號:</label>
                            <input type="text" id="id" name="id" value="<?php echo $id ?>" class="form-control" style="margin-right: 38px;">
                            <label class="control-label">姓名:</label>
                            <input type="text" id="name" name="name" value="<?php echo $name ?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style='width: 113px;'>局處:</label>
                            <input type="text" id="location" name="location" value="<?php echo $location ?>" class="form-control" style="margin-right: 38px;">
                            <label class="control-label">性別:</label>
                            <select id='gender' class='form-control'>
                                <option value='' <?= $gender == '' ?"selected":"" ?>></option>
                                <option value='m' <?= $gender == 'm' ?"selected":"" ?>>男</option>
                                <option value='f' <?= $gender == 'f' ?"selected":"" ?>>女</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出</button>
                            <button id="print" class="btn btn-info btn-sm">列印</button>
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
                <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="7">臺北市政府公務人員訓練處 班期學員上課紀錄</th>
                        </tr>
                        <tr>
                            <th class="text-center">學號</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">年度/班期名稱/期別</th>
                            <th class="text-center">職稱</th>
                            <th class="text-center">報名單位</th>
                            <th class="text-center">教室(課程表)</th>
                            <th class="text-center">上課日期</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><?= $data["ST_NO"]?></td>
                            <td><?= $data["PNAME"]?></td>
                            <td><?= $data["YEAR"]?>/<?= $data["CLASS_NAME"]?>/<?= $data["TERM"]?></td>
                            <td><?= $data["NAME"]?></td>
                            <td><?= $data["DESCRIPTION"]?></td>
                            <!-- <td><?= $data["ROOM_CODE"]?></td> -->
                            <td>
                                <a class="btn btn-info btn-sm" title="課程表"
                                            href="<?=base_url('create_class/print_schedule/print/'.$data["seq_no"].'?query_year='.$data["YEAR"].'&query_class_no='.$data["CLASS_ID"].'&rows=10&query_class_name='.$data["CLASS_NAME"])?>"
                                            onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">課程表</a>
                                <a class="btn btn-info btn-sm" title="名冊"
                                            href="<?=base_url('student_list_pdf.php?uid=55&tmp_seq=0&ShowRetirement=1&year='.$data["YEAR"].'&class_no='.$data["CLASS_ID"].'&term='.$data["TERM"])?>"
                                            onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">名冊</a>
                            </td>
                            <td><?= substr($data["START_DATE1"],0,10)?></td>
                        </tr>
                        
                        <?php endforeach?>
                        
                        
                    </tbody>
                </table>
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div>
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
<script>
function sendFun(){
    $('#Search').click();
}


$(document).ready(function() {
    $("#datepicker3").datepicker();
    $('#datepicker4').click(function(){
        $("#datepicker3").focus();
    });

    $('#Search').click(function(){

        $('#ischedule').val($('#schedule').val());
        $('#iname').val($('#name').val());
        $('#igender').val($('#gender').val());
        $('#iid').val($('#id').val());
        $('#ilocation').val($('#location').val());
        $('#ibirthday').val($('#datepicker1').val());
        $('#iclassdate').val($('#datepicker3').val());
        $('#sact').val('search');
        $('#srows').val($('select[name=rows]').val());
        $( "#form" ).submit();

    });

    $('#csv').click(function(){
        $('#ischedule').val($('#schedule').val());
        $('#iname').val($('#name').val());
        $('#igender').val($('#gender').val());
        $('#iid').val($('#id').val());
        $('#ilocation').val($('#location').val());
        $('#ibirthday').val($('#datepicker1').val());
        $('#iclassdate').val($('#datepicker3').val());
        $('#sact').val('csv');
        $( "#form" ).submit();
    });

    $('#print').click(function(){
        printData("printTable");
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
        $("#datepicker1").focus();
    });
});
</script>