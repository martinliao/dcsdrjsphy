<!-- "<?= json_encode($choices['query_season']['']);?>" -->
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
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year' name="year">
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">依日期區間查詢:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$end_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" id="class_name" name="class_name" class="form-control"  value="<?=$class_name?>" >
                            </div>
                            <div class="form-group">
                                <label class="control-label">學員姓名:</label>
                                <input type="text" id="student_name" name="student_name" class="form-control"  value="<?=$student_name?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">身分證字號:</label>
                                <input type="text" id="idno" name="idno" class="form-control"  value="<?=$idno?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="" id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id="clear" class="btn btn-info btn-sm" onclick="clearFun()">清除</button>
                            <font style="color: red">(勾稽註記：提供人事比對差勤系統註記用)</font>
                            ※<a href="<?=base_url('/files/txt/公訓處實體課程研習紀錄查詢操作說明(人事人員作業流程).pdf');?>" target="_blank"><font style="color: blue">研習紀錄查詢操作說明</font></a>
                        </div>
                    </div>
                </div>
                </form>
                <form id="data-form" role="form" class="form-inline" method="POST" action="<?=$link_save2?>">     
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" >
                <!-- /.table head -->
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="19">臺北市政府公務人員訓練處 學員研習紀錄</th>
                        </tr>
                        <tr>
                            <th class="text-center">序號</th>
                            <th class="text-center">學號</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">年度/班期名稱/期別</th>
                            <th class="text-center">職稱</th>
                            <th class="text-center">上課日期</th>
                            <th class="text-center">研習紀錄表</th>
                            <th class="text-center" style="background-color: #fec660">勾稽註記<br>全選<input type="checkbox" onclick="check_all(this,'mark[]')"></th>
                            <th class="text-center" style="background-color: #fec660">勾稽取消<br>全選<input type="checkbox" onclick="check_all(this,'cancel[]')"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($list)) {
                            for($i=0;$i<count($list);$i++){
                                if(empty($list[$i]['vacation_date']) && ($list[$i]['yn_sel'] == 1 || $list[$i]['yn_sel'] == 3 || $list[$i]['yn_sel'] == 8)){
                                    continue;
                                }

                                if($list[$i]['online_app_mark'] == 1 || $list[$i]['vacation_mark'] == 1){
                                    $checked = "checked";
                                    $text_color = 'style="color:black"';
                                } else {
                                    $checked = '';
                                    $text_color = 'style="color:blue"';
                                }

                                echo '<tr '.$text_color.'>';
                                echo '<td class="text-center">'.($i+1).'</td>';
                                echo '<td class="text-center">'.$list[$i]['st_no'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['name'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['year'].'/'.$list[$i]['class_name'].'/'.$list[$i]['term'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['job_name'].'</td>';
                                echo '<td class="text-center">'.$list[$i]['vacation_date'].'</td>';
                                echo '<td class="text-center"><a target="_blank" href="'.base_url("statistics_paper/learn_record/record?year={$list[$i]['year']}&class_no={$list[$i]['class_no']}&term={$list[$i]['term']}&vacation_date={$list[$i]['vacation_date']}&id={$list[$i]['seq_no']}&no={$list[$i]['st_no']}").'"><button type="button" style="background-color: #337ab7;border-color: #2e6da4;color:white">研習記錄表</button></a></td>';
                                if(!empty($list[$i]['vacation_date'])){
                                    echo '<td class="text-center"><input type="checkbox" name="mark[]" value="'.$list[$i]['seq_no'].'"'.' '.$checked.'></td>';
                                    echo '<td class="text-center"><input type="checkbox" name="cancel[]" value="'.$list[$i]['seq_no'].'"></td>';
                                } else {
                                    $check_value = $list[$i]['year'].'_'.$list[$i]['class_no'].'_'.$list[$i]['term'].'_'.$list[$i]['st_no'];
                                    echo '<td class="text-center"><input type="checkbox" name="mark[]" value="'.$check_value.'"'.' '.$checked.'></td>';
                                    echo '<td class="text-center"><input type="checkbox" name="cancel[]" value="'.$check_value.'"></td>';
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
                </form>
                <!-- <span align="right"><p>列印時間：2019/08/30 17:06</p></span> -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>


<script type="text/javascript">

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

function clearFun() {
    document.getElementById('datepicker1').value = '';
    document.getElementById('test1').value = '';
    document.getElementById('class_name').value = '';
    document.getElementById('student_name').value = '';
    document.getElementById('idno').value = '';
}

function check_all(obj,cName)
{
    var checkboxs = document.getElementsByName(cName);
    for(var i=0;i<checkboxs.length;i++){
        heckboxs[i].checked = obj.checked;
    }
}

function checkSave() {
    var obj = document.getElementById('data-form');
    obj.submit();
}
</script>