<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">LOG種類:</label>
                                <select name='query_year' id='query_year'>
                                    <option selected>首頁</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">USER ID:</label>
                                <input type="text" class="form-control" name="query_class_no">
                            </div>
                            <div class="form-group">
                                <label class="control-label">LOG日期:</label>
                                <input type="text" class="form-control" name="query_class_name">
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">LOG種類</th>
                            <th class="text-center">LOG日期時刻</th>
                            <th class="text-center">使用者代碼/名稱</th>
                            <th class="text-center">電腦名稱</th>
                            <th class="text-center">功能編號</th>
                            <th class="text-center">旗標</th>
                        </tr>
                    </thead>
                </table>
                <!-- /.table end -->
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
    $(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});
</script>