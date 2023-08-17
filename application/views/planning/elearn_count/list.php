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
                            <label class="control-label">年度:</label>
                            <?php
echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
?>
                            <button class="btn btn-info btn-sm" onclick="selectAction(1);">搜尋</button>
                            <button class="btn btn-info btn-sm" onclick="selectAction(2);">匯出CSV</button>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">編號</th>
                            <th class="text-center">機關名稱</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">訓練期數</th>
                            <th class="text-center">訓練人數(每期)</th>
                            <th class="text-center">訓練人數(總計)</th>
                            <th class="text-center">每次課程時數</th>
                            <th class="text-center">辦班時間(月份)</th>
                            <th class="text-center">實境錄製教材(單一主題)</th>
                            <th class="text-center">實境錄製教材(系列性主題)</th>
                            <th class="text-center">全動畫教材(貴局處無經費)</th>
                            <th class="text-center">全動畫教材(貴局處有經費)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;?>
                        <?php foreach ($list as $row) {?>
                        <tr class="text-center">
                            <td><?=$i?></td>
                            <td><?=$row['bname']?></td>
                            <td><?=$row['class_name']?></td>
                            <td><?=$row['term']?></td>
                            <td><?=$row['no_persons']?></td>
                            <td><?=$row['no_persons'] * $row['term']?></td>
                            <td><?=$row['range']?></td>

                            <?php
$mon = array();
    $mon[0] = $row["start_date1"];
    $mon[1] = $row["start_date2"];
    $mon[2] = $row["start_date3"];
    $mon[3] = $row["end_date1"];
    $mon[4] = $row["end_date2"];
    $mon[5] = $row["end_date3"];
    for ($ii = 0; $ii < count($mon); $ii++) {
        if (strlen($mon[$ii] < 10)) {
            $mon[$ii] = -1;
        } else {
            $mon[$ii] = (int) date("m", strtotime($mon[$ii]));
        }
    }
    $mon = array_unique($mon);
    $monStr = "";
    foreach ($mon as $aa) {
        if ($aa != -1) {
            $monStr .= $aa . ",";
        }

    }
    $show = substr($monStr, 0, strlen($monStr) - 1);
    ?>

                            <td><?=$show?></td>
                            <?php $course = '';
    if ((empty($row['course_zero'] && empty($row['course_one']) && empty($row['course_two']) && empty($row['course_three']))) && !$row['course_flag']) {
        $course = '課程規劃中';
        echo "<td colspan='3'>" . $course . "</td>";
    } else {
        echo "<td>";
        if (!empty($row['course_zero'])) {
            echo "V";
        }
        echo "</td>";
        echo "<td>";
        if (!empty($row['course_one'])) {
            echo "V";
        }
        echo "</td>";
        echo "<td>";
        if (!empty($row['course_two'])) {
            echo "V";
        }
        echo "</td>";
    }
    echo "<td>";
    if (!empty($row['course_three'])) {
        echo "V";
    }
    echo "</td>";

    ?>

                        </tr>
                        <?php $i++;}?>
                    </tbody>
                </table>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>


<script type="text/javascript">

function selectAction($number){
    if($number==1){
        document.getElementById("filter-form").action = "<?=$link_detail;?>";
        //document.filter-form.submit();

    }
    if($number==2){
        document.getElementById("filter-form").action = "<?=$link_export;?>";
        document.getElementById("filter-form").setAttribute("target","blank");

        //document.filter-form.submit();
    }
}

function getCurrentWeek()
{
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,-diff);
    edate = addDays(sdate,6);
    document.getElementById("datepicker1").value = sdate;
    document.getElementById("test1").value = edate;
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    var dd = result.getDate();
    var mm = result.getMonth()+1;
    var yy = result.getFullYear();
    result = yy+'-'+mm+'-'+dd;
    return result;
}

function fowardweek(days)
{
    var date1 = document.getElementById("datepicker1").value;
    var date2 = document.getElementById("test1").value;
    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        document.getElementById("datepicker1").value = sdate;
        document.getElementById("test1").value = edate;
    }
    else
    {
        var today = getCurrentWeek();
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