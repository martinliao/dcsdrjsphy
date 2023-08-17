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
                        <input hidden id='syear' name='year' value="">                      
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">
                        <input hidden id='stype' name='type' value="">
                    </form>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">月份:</label>
                            <select id='startMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_startMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>-
                            <select id='endMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_endMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm" onclick="doCSV(2)">匯出申報CSV</button>
                            <button class="btn btn-info btn-sm" onclick="doCSV(0)">匯出申報格式CSV</button>
                            <button class="btn btn-info btn-sm" onclick="doCSV(1)">匯出講座格式CSV</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.panel -->
        </div>
    <!-- /.col-lg-12 -->
    </div>
</div>
<script type="text/javascript">

if("<?php echo ($result); ?>" != "0"){
    alert("<?php echo ($result); ?>");
}
function doCSV(type){
    $('#syear').val($('#year').val());
    $('#sstartMonth').val($('#startMonth').val());
    $('#sendMonth').val($('#endMonth').val());
    $('#stype').val(type);
        
    $( "#form" ).submit();
}
$(document).ready(function() {


    $('#Search').click(function(){

        $('#syear').val($('#year').val());
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        
        $( "#form" ).submit();
    });

});

</script>