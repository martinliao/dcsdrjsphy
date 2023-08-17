<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                    <form id="actSave" method="post" action="<?=base_url('other_work/tv_wall_set')?>">
                    <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>" class="form-inline">
                        <table class="table table-hover table-condensed table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="9">輪播圖片上傳</th>
                                </tr>
                                <tr>
                                    <th class="text-center">圖片</th>
                                    <th class="text-center">開始日期</th>
                                    <th class="text-center">結束日期</th>
                                    <th class="text-center">開始時間</th>
                                    <th class="text-center">結束時間</th>
                                    <th class="text-center" colspan="4">功能列</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i=0; $cnt=count($list);$k=0;?>
                            <?php foreach($list as $row){  ?>
                            <?php $photoName=explode('/',$row['file_path']);?>
                                <tr class="text-center">
                                    <td><img src="<?=base_url('./files/upload_tv_wall/'.$photoName[2]);?>" width="50" height="50"></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" size="10" class="form-control datepicker" id="start_date_<?=$row['id']?>" name="start_date_<?=$row['id']?>"  value="<?=substr($row['start_date'],0,10)?>" >
                                            <span class="input-group-addon" style="cursor: pointer;" id="start_date_cald<?=$row['id']?>" onclick="dateFocus1(this);"><i
                                                    class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" size="10" class="form-control datepicker" id="end_date_<?=$row['id']?>" name="end_date_<?=$row['id']?>" value="<?=substr($row['end_date'],0,10)?>" >
                                            <span class="input-group-addon" style="cursor: pointer;" id="end_date_cald<?=$row['id']?>" onclick="dateFocus2(this);"><i
                                                    class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td><input type="text" size="8" name="start_time_<?=$row['id']?>" id="start_time_<?=$row['id']?>" value="<?=$row['start_time']?>" class="form-control"></td>
                                    <td><input type="text" size="8" name="end_time_<?=$row['id']?>" id="end_time_<?=$row['id']?>"  value="<?=$row['end_time']?>"  class="form-control"></td>
                                    
                                    <td><input type="button" id="<?=$row['id']?>" onclick="return save_time(this)" value="儲存"></td>
                                    <td><input type="button"  id="<?=$row['id']?>" onclick="del_item(this)" value="刪除"></td>

                                    <?php if($i==0){?>
                                    <td><input type="button" id="<?=$row['order_id']?>" onclick="downFun(this)" value="下移"></td>
                                    <td></td>
                                    <?php }else if($i==$cnt-1){?>
                                    <td><input type="button" id="<?=$row['order_id']?>" onclick="upFun(this)" value="上移"></td>
                                    <td></td>
                                    <?php }else{?>
                                        <td><input type="button" id="<?=$row['order_id']?>" onclick="upFun(this)" value="上移"></td>
                                        <td><input type="button" id="<?=$row['order_id']?>" onclick="downFun(this)"  value="下移"></td>
                                    <?php }?>
                                </tr>
                            <?php $i++; }?>
                            </tbody>
                        </table>
                            <input type="hidden" name="mode" id="mode" value="">
                            <input type="hidden" name="item_id" id="item_id" value="">
                            <input type="hidden" name="order_id" id="order_id" value="">
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <form id="filter-form" role="form" class="form-inline" enctype="multipart/form-data" method="post" action="<?=base_url('other_work/tv_wall_set/uploadPhoto')?>">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <div class="row">
                                <div class="col-xs-12">
                                    <label class="control-label">上傳圖片:</label>
                                    <input type="file" name="photo" class="form-control">
                                    <button class="btn btn-info">上傳</button>
                                </div>
                            </div>
                        </form>
                        <form id="savsSet" method="post" action="<?=base_url('other_work/tv_wall_set/saveSet')?>">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <input type="hidden" name="mode2" id="mode2" value="" />
                        <div class="row">
                                <div class="col-xs-12">
                                    <label class="control-label">輪播頻率:</label>
                                    <input type="text" class="form-inline" id="frequency" name="frequency" value="<?=isset($setup_list[0]['frequency'])?$setup_list[0]['frequency']:''?>">
                                    <span style="font-family:Microsoft JhengHei;">秒內更換一次圖片</span>
                                    <button class="btn btn-info" onclick="return save();">輪播頻率儲存</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <label class="control-label">跑馬燈文字:</label>
                                    <textarea class="form-inline" id="marquee" name="marquee" cols="50" rows="10" wrap="soft"><?=isset($setup_list[0]['marquee'])?$setup_list[0]['marquee']:''?></textarea>
                                    <button class="btn btn-info" onclick="return save_marquee();">跑馬燈文字儲存</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
function upFun(obj){
    var id=obj.id;
	document.getElementById("mode").value = 'up';
	document.getElementById("order_id").value = id;
	obj = document.getElementById("actSave");
	obj.submit();
}

function downFun(obj){
    var id=obj.id;
	document.getElementById("mode").value = 'down';
	document.getElementById("order_id").value = id;
	obj = document.getElementById("actSave");
	obj.submit();
}
function del_item(obj){
    var id=obj.id;
	if(confirm('是否確認刪除')){
		document.getElementById("mode").value = 'del';
		document.getElementById("item_id").value = id;
		obj = document.getElementById("actSave");
		obj.submit();
	} else {
		return false;
	}	
}
function save_time(obj){
    var id=obj.id;

    if(jQuery("#start_date_"+id).val()=="") {
	    alert('請輸入開始日期');
	    return false;
	} 
	 
	if(jQuery("#end_date_"+id).val()=="") {
	    alert('請輸入結束日期');
	    return false;
    } 

	if(jQuery("#start_time_"+id).val()==""){
	    alert('請輸入開始時間');
	    return false;
	} 
	 
	if(jQuery("#end_time_"+id).val()=="") {
	    alert('請輸入結束時間');
	    return false;
	}
    
    document.getElementById("mode").value = 'savetime';
    document.getElementById("item_id").value = id;
	obj = document.getElementById("actSave");
	obj.submit();
}
function save() {
	if(""==jQuery("#frequency").val()) {
	    alert('請輸入輪播頻率');
	    return false;
	} 

	document.getElementById("mode2").value = 'savepar';
	obj = document.getElementById("savsSet");
	obj.submit();
}

function save_marquee() {
	if(""==jQuery("#marquee").val()) {
	    alert('請輸入跑馬燈文字');
	    return false;
	} 

	document.getElementById("mode2").value = 'saveMarquee';
	obj = document.getElementById("savsSet");
	obj.submit();
}



function dateFocus1(myObj)
{
    var id2=myObj.id;
    var id1=id2.replace('cald','');
    $(document).ready(function() {
        $('#'+id1).datepicker();
        $('#'+id2).click(function(){
            $('#'+id1).focus();
        });
    });
}
function dateFocus2(myObj)
{
    var id2=myObj.id;
    var id1=id2.replace('cald','');
    $(document).ready(function() {
        $('#'+id1).datepicker();
        $('#'+id2).click(function(){
            $('#'+id1).focus();
        });
    });
}
</script>