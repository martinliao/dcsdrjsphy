<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="sort" value="" />
                    <div class="col-xs-12">
                        <form id="form" method="GET">
                            <input hidden id='syear' name='year' value="">
                            <input hidden id='smonth' name='month' value="">
                            <input hidden id='siscsv' name='iscsv' value="0">
                            <input hidden id='srows' name='rows' value="">
                        </form>
                        <div class="form-group row">
                            <label class="control-label">年度</label>
                            <select name='query_year' id='query_year'>
                            <?php foreach (array_reverse($choices['query_year']) as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">月份</label>
                            <select name='query_month' id='query_month'>
                            
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_month == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                        </div>
                        <button id="Search" class="btn btn-info">查詢</button>
                        <button id="csv" class="btn btn-info">下載Excel</button>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                            <label class="control-label">請選擇年度、月份後按查詢始能產製經費概況</label>
                        </div>
                    </div>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr style="background-color:#8CBBFF;">
                            <th class="text-center">類別</th>
                            <th class="text-center">次類別</th>
                            <th class="text-center">班期名稱</th>  
                            <th class="text-center">期別</th>
                            <th class="text-center">帶班人員</th>                          
                            <th class="text-center">開課日期</th>
                            <th class="text-center">講座</th>
                            <th class="text-center">鐘點</th>
                            <th class="text-center">費用</th>
                            <th class="text-center">鐘點費</th>
                            <th class="text-center">交通費</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><?php echo $data["type"]=="A"?"行政":"發展" ?>系列</td>
                            <td><?= $data["NAME"]?></td>
                            <td><?= $data["CLASS_NAME"]?></td>
                            <td><?= $data["term"]?></td>
                            <td><?= $data["workername"]?></td>
                            <td><?= substr($data["START_DATE"], 0, 10)?>~<?= substr($data["END_DATE"], 0, 10)?></td>
                            <td><?= $data["teacher_name"]?></td>
                            <td><?= $data["hrs"]?></td>
                            <td><?= $data["unit_hour_fee"]?></td>
                            <td><?= $data["HOUR_FEE"]?></td>
                            <td><?= $data["TRAFFIC_FEE"]?></td>
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
            <!-- /.table end -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->

<script>
function sendFun(){
    $('#Search').click();
}

$(document).ready(function() {
    $('#Search').click(function(){
        $('#syear').val($('#query_year').val());
        $('#smonth').val($('#query_month').val());
        $('#siscsv').val(0);
        $('#srows').val($('select[name=rows]').val());

        $( "#form" ).submit();
    });
    $('#csv').click(function(){
        $('#syear').val($('#query_year').val());
        $('#smonth').val($('#query_month').val());
        $('#siscsv').val(1);
        $( "#form" ).submit();
    });
});
</script>