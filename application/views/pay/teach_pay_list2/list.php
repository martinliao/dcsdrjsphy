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
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='srows' name='rows' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">上課日期區間:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_end_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div> 
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
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
                <table class="table table-bordered table-condensed table-hover">
                    <thead style="background-color: #8CBBFF">
                        <tr>
                            <th class="text-center" colspan="3">班期</th>
                            <th class="text-center" rowspan="2">上課日期</th>
                            <th class="text-center" rowspan="2">帶班人員</th>
                            <th class="text-center" rowspan="2">請款狀態</th>
                        </tr>
                        <tr>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><?= $data["year"]?></td>
                            <td><?= $data["class_name"]?></td>
                            <td><?= $data["term"]?></td>
                            <td><?= substr($data["use_date"],0,-8)?></td>
                            <td><?= $data["WORKER_NAME"]?></td>
                            <td><?= ($data["status"] == null || $data["status"] == "") ?"未作請款選取":$data["status"] ?></td>
                            <!-- <td><?= $data["teacher_name"]?></td> -->
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
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->



<script type="text/javascript">
function sendFun(){
    if($('#datepicker1').val() == "" || $('#test1').val() == ""){
        alert("請選擇日期區間")
        return;
    }
    
    $('#Search').click();
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

    $('#Search').click(function(){
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#srows').val($('select[name=rows]').val())

        $( "#form" ).submit();
    });

    $("#money1").datepicker();
    $('#money2').click(function(){  
        $("#money1").focus();   
    });

    $("#money3").datepicker();
    $('#money4').click(function(){  
        $("#money3").focus();   
    });
});
</script>