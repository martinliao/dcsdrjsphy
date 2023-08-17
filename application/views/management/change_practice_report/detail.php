<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr class="text-center">
                            <td>發文單位</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requires as $require): ?>
                        <tr class="text-center">
                            <td><span><font color="red"><?=$require['class_name']?><font><span>(第<?=$require['term']?>期)</td>
                        </tr>
                        <?php endforeach ?>
                        <tr>
                        <td>
                        <?=join(",", $bc_names)?>
                        </td>
                        </tr>
                    </tbody>
                </table>
				<!-- /.table end -->
                <button class="btn btn-info" onclick="history.back(-1)" >回上頁</button>
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