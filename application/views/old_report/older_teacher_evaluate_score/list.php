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
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label" style='min-width:90px;'>講座姓名:</label>
                            <input type="text" id="teacher_name" name="teacher_name" class="form-control" style='min-width:170px;' value="<?=htmlspecialchars($sess_teacher_name,ENT_HTML5|ENT_QUOTES)?>">
                            <label class="control-label" style='min-width:90px;'>身分證字號:</label>
                            <input type="text" id="teacher_id" name="teacher_id" class="form-control" style='min-width:170px;' value="<?=htmlspecialchars($sess_teacher_id,ENT_HTML5|ENT_QUOTES)?>">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label" style='min-width:90px;'>課程名稱:</label>
                            <input type="text" id="course_name" name="course_name" class="form-control" style='min-width:170px;' value="<?=htmlspecialchars($sess_course_name,ENT_HTML5|ENT_QUOTES)?>">
                            <label class="control-label" style='min-width:90px;'>班期名稱:</label>
                            <input type="text" id="class_name" name="class_name" class="form-control" style='min-width:170px;' value="<?=htmlspecialchars($sess_class_name,ENT_HTML5|ENT_QUOTES)?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">依日期區間查詢:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=htmlspecialchars($sess_start_date,ENT_HTML5|ENT_QUOTES)?>" id="datepicker1" name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=htmlspecialchars($sess_end_date,ENT_HTML5|ENT_QUOTES)?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group">
                                <button class="btn btn-info" onclick="fowardweek(-7,1);"><<</button>
                                <button class="btn btn-info" onclick="getCurrentWeek(1);">本週</button>
                                <button class="btn btn-info" onclick="fowardweek(7,1);">>></button>
                            </div>
                            <div class="input-group">
                                <button class="btn btn-info" onclick="setToday(1)">設定今天</button>
                            </div>
                            <div class="input-group">
                                <button class="btn btn-info" onclick="ClearData()">清除日期</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <!-- <a href="<?=htmlspecialchars($link_new,ENT_HTML5|ENT_QUOTES)?>">新系統資料查詢區</a> -->
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
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">課程名稱</th>
                            <th class="text-center" rowspan="2">班期名稱</th>
                            <th class="text-center" rowspan="2">授課日期(最小)</th>
                            <th class="text-center" rowspan="2">講座姓名</th>
                            <th class="text-center" colspan="4">課程評估分數(分)</th>
                            <th class="text-center" rowspan="2">開放性<br>意見表<br>下載</th>
                        </tr>
                        <tr>
                            <th class="text-center">課程對您是否有助益</th>
                            <th class="text-center">課程內容是否適當</th>
                            <th class="text-center">教學方式是否滿意</th>
                            <th class="text-center">總平均</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <tr class="text-center">
                            <td><?=htmlspecialchars($data["course_name"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars(($data["year"].'年 '.$data["class_name"].' 第'.$data["term"].'期'),ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars(date('Y-m-d',strtotime($data["start_date1"])),ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["teacher_name"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["e_score1"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["e_score2"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["e_score3"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td><?=htmlspecialchars($data["avg"],ENT_HTML5|ENT_QUOTES)?></td>
                            <td>
                                <?php if(!empty($data['question_id'])){ $question_id = intval($data['question_id']); $export_url = base_url("old_report/Older_teacher_evaluate_score/exportCsv?question_id={$question_id}");?>
                                    <a href="<?=$export_url?>"><button type="button" class="btn btn-info btn-sm">開放性<br>意見表</button></a>
                                <?php } ?>
                            </td>
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
            </form>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</div>
</div>

<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function sendFun(){
    $('#Search').click();
}
</script>

<script type="text/javascript">

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    var dd = result.getDate();
    var mm = result.getMonth()+1;
    var yy = result.getFullYear();
    result = yy+'-'+mm+'-'+dd;
    return result;
}

$(document).ready(function() {
    $("#test1").datepicker( { defaultDate: "2012-10-01" });
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
       
        $( "#form" ).submit();
    });
    
    $("#datepicker1").datepicker( { defaultDate: "2012-10-01" });
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();   
    });
});
</script>