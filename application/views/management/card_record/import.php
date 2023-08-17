<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="data-form" role="form" class="form-inline" method="post" action="<?=$link_import?>" enctype="multipart/form-data">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash()?>" />
                    <input type="hidden" name="import" value="import">

                    <input type="file" name="myfile" class="button" style="float: left" accept=".csv">
                    <input type="submit" value="上傳" class="button">
                    <a href="<?=HTTP_EXAMPLE_FILE."card_record.csv"?>" target="_blank">CSV格式下載</a>
                </form>
                <span style="color:red">
                    <p>檔案限制:</p>
                    <p>1、刷到(退)時間請輸入六位數字。</p>
                    <p>2、存檔類型：.csv</p>
                    <p>3、存檔檔名：card_record</p>
                </span>
                <a href="<?=base_url("management/card_record/?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">返回</a>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>

</script>