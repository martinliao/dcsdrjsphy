<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <div class="panel-body">
                <form action="<?=base_url('planning/set_worker/import')?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash()?>" />
                    <input type="hidden" name="import" value="import">
                    <input type="file" name="myfile" class="button" style="float: left">
                    <input type="submit" value="上傳" class="button">
                    <a href="../../files/example_files/set_worker.csv" target="_blank">下載範例檔</a>
                </form>
            </div>
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


