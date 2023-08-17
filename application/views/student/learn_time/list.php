<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" method="post" target=_blank action="<?=base_url('student/learn_time/export')?>">
                <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>" >
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <button class="btn btn-info">匯出</button>
                            <a href="<?=$link_refresh?>" class="btn btn-info">清除</a>
                        </div>
                    </div>
                    <p>※單位：小時。</p>
                    <p>※每月15日後可查詢當年度1月到上個月的統計表。</p>
                    <p>※數位課程係選讀臺北e大線上課程，且已完成研習時數核發設定後取得之認證時數。</p>
                    <p>※混成課程須完成e大線上閱讀並參加實體課程且未退訓始能取得時數。</p>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
function test(strYEAR){
    
}
</script>