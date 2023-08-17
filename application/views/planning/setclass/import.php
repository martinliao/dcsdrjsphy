<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <div class="panel-body">
                <form action="<?=base_url("planning/setclass/add/?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."");?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash()?>" />
                    <input type="hidden" name="import" value="import">
                    <input type="file" name="myfile" class="button" style="float: left">
                    <input type="submit" value="上傳" class="button">
                    <?php if($user_bureau == '379680000A'){ ?>
                    <a href="../../../files/example_files/require_dev_class_samp.csv" target="_blank">下載範例檔</a>
                    <?php } else { ?>
                    <a href="../../../files/example_files/require_class_samp.csv" target="_blank">下載範例檔</a>
                    <?php } ?>
                    <font style="color: red">【範例檔：請儲存為系統預設檔名】</font>
                </form>
            </div>
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


