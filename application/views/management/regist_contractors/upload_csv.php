<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <div class="panel-body">
            <?php if (validation_errors()) { ?>
			<div class="alert alert-danger">
			    <button class="close" data-dismiss="alert" type="button">×</button>
			    <?=validation_errors();?>
			</div>
			<?php } ?>
                <a class="btn btn-success" target="_block" href="<?=base_url('files/example_files/aa_ch4_samp.csv');?>">
                    下載CSV範例
                </a>
                <form id="data-form" role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />


                    <!-- Tab panes -->
                    <div>
                        <?php if(!empty($form['massage'])) {?>
                        <?=$form['massage'];?>
                        <?php }?>
                    </div>
                    <div class="tab-content" style="padding: 15px;">
		                <label class="control-label">匯入CSV檔案(第一欄文字勿刪除，請存CSV檔)</label>
		                <div class="file-block">
		                    <input type="hidden" name="file" value="<?=$form['file'];?>" >
		                    <input type="file" name="upload" onchange="changeFile(this);" accept=".csv">
		                </div>
		                <?=form_error('file');?>
                        <p class="help-block">檔案大小限制 4MB，只能上傳CSV檔。</p>
                    </div>

                    <div class="form-group text-left">
                        <button class="btn btn-default" title="Save">上傳</button>
                        <a class="btn btn-default" href="<?=$link_cancel;?>" title="回上一頁">回上一頁</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script>
$(function() {
    $('#tab_data a:first').tab('show');
    $('#tab_locale a:first').tab('show');
});

var changeFile = function(input)
{
    var $file_block = $(input).closest('.file-block');
    var filename = $(input).val();
    var fileSize = 0;
    var SizeLimit = 4096;  //上傳上限，單位:kb
    var ext = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
    if (input.files && input.files[0] && (ext == "csv" )) {
        if (navigator.userAgent.match("MSIE")) {  //FOR IE
            var img = new Image();
            img.onload = function(){
                fileSize = this.fileSize;
            }
            img.src = input.value;
        } else {  //FOR Firefox,Chrome
            fileSize = input.files.item(0).size;
        }
        fileSize =Math.floor(fileSize / 1000);
        if (fileSize <= SizeLimit) {
            // var reader = new FileReader();
            // reader.onload = function (e) {
                // var $img = $('<img class="img-rounded">').attr('src', e.target.result);
                // $image_block.find('div').html($img);
            // }
            // reader.readAsDataURL(input.files[0]);

            // $image_block.find('a.close').show();
            $file_block.find('[type=hidden]').val(filename);
        } else {
            bk_alert(4, '您所選擇的檔案大小為 ' + fileSize + ' KB<br>已超過上傳上限 ' + SizeLimit + ' KB<br>不允許上傳！', 4, 'center');
        }
    } else {
        //bk_alert(1, '只能上傳圖片檔', 'topCenter', 3)
        bk_alert(4, '只能上傳csv格式', 4, 'center');
    }
}
</script>
