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
                        <input hidden id='sonegov' name='onegov' value="">                      
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">機關局處:</label>
                            <select name="onegov" id="onegov">
                                <option value="-1" <?= $sess_onegov == "-1" ?"selected":"" ?> >請選擇</option>
                                <option value="1"  <?= $sess_onegov == "1" ?"selected":"" ?>>所有一級暨所屬</option>
                                <option value="2"  <?= $sess_onegov == "2" ?"selected":"" ?> >各局處</option>
                            </select>
                        </div>
                    </div>
                    <button id="Search" class="btn btn-info">查詢</button>
                </div>
                <span align="left"><p>※單位：小時/人次 。</p></span>
                <span align="left"><p>※每月15日後可查詢當年度1月到上個月的統計表。</p></span>
                <span align="left"><p>※數位課程係選讀臺北e大線上課程，且已完成研習時數核發設定後取得之認證時數。</p></span>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>
$(document).ready(function() {
  
    $('#Search').click(function(){
        if($('#onegov').val()==-1) {
            alert("請選擇機關");
            return false;
        }
        else {
            $('#syear').val($('#year').val());
            $('#sonegov').val($('#onegov').val());
            $( "#form" ).submit();
        }
        
    });

});
</script>

<?php if(!isset($excel)){?>
    
<?php }else if(!$excel[0] && $excel[1] == 2){?>
        <script>alert("無權限下載")</script>
<?php }else {?>
        <script>alert("下載失敗")</script>
<?php }?>

