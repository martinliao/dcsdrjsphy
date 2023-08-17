<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">

                <form id="form" method="GET">
                        <input hidden id='syear' name='year' value="">                      
                        <input hidden id='sminnumber' name='minnumber' value="0">                      
                        <input hidden id='smaxnumber' name='maxnumber' value="">
                        <input hidden id='sfirstSeries' name='firstSeries' value="">
                        <input hidden id='ssecondSeries' name='secondSeries' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                        <input hidden id='srows' name='rows'>
                    </form>

                <div id="filter-form" role="form" class="form-inline">
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">人數區間:</label>
                            <input type="text" id="minnumber" name="minnumber" class="form-control" value="<?= $sess_minnumber?>" onkeyup="value=value.replace(/[^\d]/g,'');">
                            -
                            <input type="text" id="maxnumber" name="maxnumber" class="form-control" value="<?= $sess_maxnumber?>" onkeyup="value=value.replace(/[^\d]/g,'');">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">系列別代碼</label>
                            <?php
                                    echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                                ?>

                            <label class="control-label">次類別代碼</label>
                            <select class="form-control" name='query_second' id='query_second'>
                                    <option value="">請選擇次類別</option>
                                    <?php if(isset($choices['query_second']) && !empty($choices['query_second'])){
                                        for($i=0;$i<count($choices['query_second']);$i++){
                                        if($choices['query_second'][$i]['item_id'] == $filter['query_second']){
                                        echo '<option value="'.$choices['query_second'][$i]['item_id'].'" selected>'.$choices['query_second'][$i]['name'].'</option>';
                                        } else {
                                            echo '<option value="'.$choices['query_second'][$i]['item_id'].'">'.$choices['query_second'][$i]['name'].'</option>';
                                                }
                                            }
                                        }
                                    ?>
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出</button>
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
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">人數</th>
                            <th class="text-center">一月</th>
                            <th class="text-center">二月</th>
                            <th class="text-center">三月</th>
                            <th class="text-center">四月</th>
                            <th class="text-center">五月</th>
                            <th class="text-center">六月</th>
                            <th class="text-center">七月</th>
                            <th class="text-center">八月</th>
                            <th class="text-center">九月</th>
                            <th class="text-center">十月</th>
                            <th class="text-center">十一月</th>
                            <th class="text-center">十二月</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">

                            <td><?= $data["class_name"]?></td>
                            <td><?= $data["term"]?></td>
                            <td><?= $data["no_persons"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M1"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M2"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M3"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M4"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M5"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M6"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M7"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M8"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M9"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M10"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M11"]?></td>
                            <td style='text-align: center; color:red;'><?= $data["M12"]?></td>

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
</div>
</div>
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
/*$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});*/
function sendFun(){
    if(window.location.href.indexOf("?") != -1) {
        $('#Search').click();
    }
}

function removeOptions(selectbox) {
    var i;
    for (i = selectbox.options.length - 1; i >= 0; i--) {
        selectbox.remove(i);
    }
}

function getSecond(){
    removeOptions(document.getElementById("query_second"));
    var series = document.getElementById('query_type').value;

    if(series == ''){
        return false;
    }

    var link = "<?=$link_get_second_category;?>";
  
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);

            if (result.length != 0) {
                var second = document.getElementById('query_second');
                var option_name = '請選擇次類別代碼';
                var option_value = '';
                var new_option = new Option(option_name, option_value);
                second.options.add(new_option);
                for (var i = 0; i < result.length; i++) {
                    var option_name = result[i]['name'];
                    var option_value = result[i]['item_id'];
                    var new_option;

                    if("<?= $sess_secondSeries;?>" != "" && "<?= $sess_secondSeries;?>" == option_value) {
                        new_option = new Option(option_name, option_value, true, true);
                    }
                    else {
                        new_option = new Option(option_name, option_value);
                    }
                    
                    second.options.add(new_option);
                }
            }
        }
    });
}
</script>

<script type="text/javascript">

$(document).ready(function() {
    if("<?= $sess_firstSeries;?>" != "") {
        
        $("#query_type").val("<?= $sess_firstSeries;?>");
        getSecond();
    }
  
    $('#Search').click(function(){


        $change ='';

        if($('#minnumber').val()!='' || $('#maxnumber').val()!='')
        {


        if(parseInt($('#maxnumber').val()) < parseInt($('#minnumber').val())){
            $change = $('#maxnumber').val();
            $('#maxnumber').val($('#minnumber').val());
            $('#minnumber').val($change);
        }
        }
             
        $('#syear').val($('#year').val());
        $('#smaxnumber').val($('#maxnumber').val());
        $('#sminnumber').val($('#minnumber').val());
        $('#sfirstSeries').val($('#query_type').val());
        $('#ssecondSeries').val($('#query_second').val());
        $('#siscsv').val(0);
        $('#srows').val($('select[name=rows]').val());
        $( "#form" ).submit();




    });


    $('#print').click(function(){
        printData("printTable");
    });

    $('#csv').click(function(){
                     
        $('#syear').val($('#year').val());
        $('#smaxnumber').val($('#maxnumber').val());
        $('#sminnumber').val($('#minnumber').val());
        $('#sfirstSeries').val($('#query_type').val());
        $('#ssecondSeries').val($('#query_second').val());
        $('#siscsv').val(1);
        $( "#form" ).submit();

    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
        $("#datepicker1").focus();   
    });
});
</script>