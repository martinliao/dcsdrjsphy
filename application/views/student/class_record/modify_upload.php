<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <form id="filter-form" role="form" class="form-inline" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>">
                <p><?=htmlspecialchars($title,ENT_HTML5|ENT_QUOTES)?>上傳異動表</p>
                <input type="file" name="files" id="files" class="form-control"><font color="red">*檔案限PDF、JPG、PNG格式，檔案大小10MB內，上傳檔案保存60天</font>
                <br>
                <button type="button" class="btn btn-info btn-sm" onclick="sendFun()">送出</button>
                <button class="btn btn-info btn-sm" onclick="closeFun()">取消</button>
            </form>
        </div>
    </div>
</div>

<script>
    function closeFun(){
        window.close();
    }

    function sendFun(){
        var fileInput = $('#files').get(0).files[0];

        if(fileInput){
            $("#filter-form").submit();
        } else {
            alert("請上傳異動表");
            return false;
        }
    }
</script>