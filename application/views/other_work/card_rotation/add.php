<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" method="post" Enctype="Multipart/Form-Data">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style="background-color: #DCDCDC ">名稱：</label>
                            <input type="text" class="form-control" id="name" name="name" value="" style="width:400px">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style="background-color: #DCDCDC ">網址：</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style="background-color: #DCDCDC ">檔案上傳：</label>
                            <input type="file" class="form-control" name="userfile">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <a href="<?=base_url('other_work/card_rotation/') ?>"><input type="button" class="btn btn-info" value="回上頁"></a>
                            <input type="button" class="btn btn-info" value="儲存" onclick="sendFun()">
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function sendFun(){
    if($('#name').val()=="" && $('#query_class_no').val()=="") {
        alert("請輸入代碼或名稱");
        return false;
    } else {
        var obj = document.getElementById('filter-form');
        obj.submit();
    }
}
</script>