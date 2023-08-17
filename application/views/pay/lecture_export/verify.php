<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <input hidden id='suid' name='uid' value="<?=htmlspecialchars($_GET['uid'], ENT_HTML5|ENT_QUOTES);?>">
                    <input hidden id='act' name='act' value="update">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">姓名:</label>
                            <input type="text" id='name' name='name' value="<?=$datas['name']?>" class="form-control">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">身分證:</label>
                            <input type="text" id='id' name='id' value="<?=$datas['idno']?>" class="form-control">
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">所得人代號:</label>
                            <input type="text" id='code' name='code' value="<?=$datas['code']?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <a id="Search" class="btn btn-info btn-sm">儲存</a>
                            <a href="<?=base_url('pay/lecture_export/')?>" class="btn btn-info btn-sm">回上頁</a>
                        </div>
                    </div>     
                </form>
                <!-- /.table head -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script type="text/javascript">
if("<?php echo ($result); ?>" != "0"){
    alert("<?php echo ($result); ?>");
}
$(document).ready(function() {

    $('#Search').click(function(){
        $( "#filter-form" ).submit();
    });

});
</script>