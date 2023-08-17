<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                匯入名單
            </div>
            <div class="panel-body">
                <form id="sForm" action="<?=base_url("management/OutsideSign/importStudent/{$id}");?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash()?>" />
                    請輸入身分證字號：<input type='text' id='idno' name='idno' value="">
                    <br>
                    <font style="margin-left: 4%">請輸入姓名：</font><input type='text' id='name' name='name' value="">
                    <input type='button' value="新增" onclick="addFun()">
                    <br>
                    <br>
                    <input type="hidden" name="id" value="<?=$id?>">
                    <input type="hidden" id="import" name="import" value="Y">
                    <input type="file" name="myfile" class="button" style="float: left">
                    <input type="submit" value="上傳" class="button">
                    <a href="../../../files/example_files/importStudent.csv" target="_blank">下載範例檔</a>
                    <font style="color: red">【範例檔：請儲存為系統預設檔名】</font>
                </form>
                <br>
                <a class="btn btn-info" id="back" href="<?=$link_home?>">回首頁</a>
            </div>
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script type="text/javascript">
    function addFun() {
        var obj = document.getElementById('sForm');
        var idno = document.getElementById('idno').value.trim();
        var name = document.getElementById('name').value.trim();

        if(idno == ''){
            alert('身分證字號不能為空');
            return false;
        }

        if(name == ''){
            alert('姓名不能為空');
            return false;
        }

        document.getElementById('import').value = 'N';
        obj.submit();
    }

</script>
