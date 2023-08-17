<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" value="<?=$require->year?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$require->class_no?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$require->class_name?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$require->term?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label" >退訓標準(小時):</label>
                                <input type="text" class="form-control" style="color:red; font-size: 16px;" value="大於<?=($require->retreat_standard)?>小時" disabled>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- /.table head -->
                <form id="data-form" role="form" class="form-inline" action="" method="POST">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" >
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">退訓<input type="checkbox" id="chkall" onclick="checkAll(this,'check');"></th>
                            <th class="text-center">學號</th>
                            <th class="text-center">局處名稱</th>
                            <th class="text-center">身分證ID</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">職稱</th>
                            <th class="text-center">請假時數</th>
                            <th class="text-center">備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($retreats as $key => $retreat): ?>
                        <tr>
                            <td><input type="checkbox" name="retreats[<?=$retreat->id?>][yn_sel]" class="selectAll" value="<?=$retreat->yn_sel?>" 
                                <?=($retreat->yn_sel == 4) ? 'checked' : ''?> ></td>
                            <td><?=$retreat->st_no?></td>
                            <td><?=$retreat->be_name?></td>
                            <td><?=$retreat->id?></td>
                            <td><?=$retreat->name?></td>
                            <td><?=$retreat->job_title?></td>
                            <td><?=$retreat->hours?></td>
                            <td><input type="text" name="retreats[<?=$retreat->id?>][memo]" value="<?=$retreat->memo?>"></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
function checkAll(id,check){
    if($("#chkall").prop("checked")){
        $(".selectAll").each(function(){
            $(this).prop("checked",true);
        })
    } else {
        $(".selectAll").each(function(){
            $(this).prop("checked",false);
        })
    }
}
</script>