<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 課表複製
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">

                <div class="form-group col-xs-6">
                        <label class="control-label">請輸入欲複製的課表年度(同代碼<?=htmlspecialchars($class_no, ENT_HTML5|ENT_QUOTES)?>)</label>
                        <input class="form-control" id="copyyear" name="copyyear" value="">
                </div>
                <div class="form-group col-xs-6">
                    <label class="control-label">請輸入欲複製的課表期別(同代碼<?=htmlspecialchars($class_no, ENT_HTML5|ENT_QUOTES)?>)</label>
                    <input class="form-control" id="copyterm" name="copyterm" value="">
                </div>

                <div class="form-group col-xs-12">
                    <center>
                    <input type="button" class="btn btn-xs btn-primary" onclick="confirmFun()" value="確定">
                    <input type="button" class="btn btn-xs btn-primary" onclick="cancelFun()" value="取消" style="margin-left: 10px;background:red">
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmFun(){
        var copyyear = document.getElementById('copyyear').value;
        var copyterm = document.getElementById('copyterm').value;


        var reg = /^[1-9][0-9]*$/;
        if(!reg.test(copyyear)){
            alert('請輸入正確年度');
            return false;
        }

        if(!reg.test(copyterm)){
            alert('請輸入正確期別');
            return false;
        }

        window.opener.copyScheduleNew(copyyear,copyterm);
        window.close();
    }

    function cancelFun(){
        window.close();
    }
</script>