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
                            <td colspan="9">
                                年度：<?=$require->year?>&nbsp;&nbsp;&nbsp;&nbsp;班期代碼:<?=$require->class_no?>&nbsp;&nbsp;&nbsp;&nbsp;班期名稱：<?=$require->class_name?>&nbsp;&nbsp;&nbsp;&nbsp;期別：<?=$require->term?>&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <th>上傳時間</th>
                            <th>檔案</th>    
                            <th>上傳者</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($mail_files)){ ?>
                            <tr>
                                <td><font color="red">查無資料</font></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php }else{ ?>
                            <?php foreach($mail_files as $file): ?>
                            <tr>
                                <td><?=$file->cre_date?></td>
                                <td><a href="<?=base_url("{$file->file_path}")?>"><?=basename($file->file_path)?></a></td>
                                <td><?=$file->name?></td>
                            </tr>
                            <?php endforeach ?>
                        <?php }?>
                    </tbody>
                </table>
                <!-- /.table end -->
                <button class="btn btn-info btn-sm" onclick="history.back(-1)" >回上頁</button>
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