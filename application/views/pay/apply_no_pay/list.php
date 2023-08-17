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
                            <input hidden id='srows' name='rows'>
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
                        <button id="Search" class="btn btn-info">產製報表</button>
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
                    <thead>
                        <tr style="background-color:#8CBBFF;">
                            <th>年度</th>
                            <th>班期名稱</th>  
                            <th>期別</th>
                            <th>上課日</th>                          
                            <th>姓名</th>
                            <th>時數</th>
                            <th>單價</th>
                            <th>鐘點費</th>
                            <th>交通費</th>
                            <th>合計</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><?= $data["YEAR"]?></td>
                            <td><?= $data["CLASS_NAME"]?></td>
                            <td><?= $data["TERM"]?></td>
                            <td><?= $data["USE_DATE"]?></td>
                            <td><?= $data["TEACHER_NAME"]?></td>
                            <td><?= $data["HRS"]?></td>
                            <td><?= $data["UNIT_HOUR_FEE"]?></td>
                            <td><?= $data["HOUR_FEE"]?></td>
                            <td><?= $data["TRAFFIC_FEE"]?></td>
                            <td><?= $data["SUBTOTAL"]?></td>
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
    if($('#query_year').val() == "" && $('#query_month').val() == "") {
        alert("請選擇篩檢條件");
    }
    else {
        $('#Search').click();
    }
}

$(document).ready(function() {
    $('#Search').click(function(){
        if($('#query_year').val() == "" && $('#query_month').val() == "") {
            alert("請選擇篩檢條件");
        }
        else {
            $('#syear').val($('#query_year').val());
            $('#smonth').val($('#query_month').val());
            $('#srows').val($('select[name=rows]').val());
            $( "#form" ).submit();
        }
    });
});
</script>