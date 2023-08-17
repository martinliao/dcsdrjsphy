<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=$_LOCATION['function']['name'] ;?>
			</div>
			<div class="panel-body">
				<table width="99%">
				  <tr>

				    <td bgcolor="#eeeeee">

				      <table class="table table-bordered table-condensed" width="100%">
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">年度</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['year'];
				          ?>
				          </td>
								</tr>
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">班期代碼</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['class_no'];
				          ?>
				          </td>
								</tr>
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">期別</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['term'];
				          ?>
				          </td>
								</tr>
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">名稱</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['class_name'];
				          ?>
				          </td>
								</tr>
							</table>
				<form id="actQuery" method="POST" >
				<table width="100%" >
					<tr>
					<td bgcolor="#eeeeee">
					<table width="100%" class="table table-bordered table-striped table-condensed" id='show_table'>
						<tr>
						  <td align="center" bgcolor="#5D7B9D" width="40"><font color="#ffffff">序號</font></td>
						  <td align="center" bgcolor="#5D7B9D" width="80" style="display:none;"><font color="#ffffff">優先順序</font></td>
				          <td align="center" bgcolor="#5D7B9D" width="110"><font color="#ffffff">身分證字號</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">出生年月</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">局處</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">身障需求註記</font></td>
				        </tr>
    					<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
				        <?php for($i=1;$i<11;$i++) { ?>
				        <tr>
				        	<td align="center" ><?=$i;?></td>
				        	<td align="center" style="display:none;" ><input type="text" id="order_<?=$i;?>" name="selNO[]" size="3" maxlength="3" value="<?=$class['max_order'];?>"</td>
				        	<td align="center" ><input type="text" id="selID_<?=$i;?>" name="selID[]" size="10" maxlength="10" value="" onblur="check_ID(this)"></td>
				        	<td id="name" align="center" ></td>
				        	<td id="birthday" align="center" ></td>
				        	<td id="beaurau_name" align="center" ></td>
				        	<td id="title" align="center" ></td>
				        	<td id="phydisabled" align="center" ></td>
				        </tr>
				        <?php $class['max_order']++; } ?>
					</table>
					<div>
						<button id="btn_save" class="btn btn-default" onclick="do_enrollment()" disabled="disabled" title="確定">確定</button>
						<a class="btn btn-default" href="<?=$link_cancel;?>" title="返回">返回</a>
						<a class="btn btn-default" href="<?=$import;?>" title="匯入" <?=($class['isend'] == 'Y')?'disabled':''?>>匯入</a>
						<!-- <input type="button" name="import" value="匯入" class="button" > -->
					</div>
					</td>
					</tr>
				</table>
				</form>
				<?php if(!empty($regist_list)) { ?>
				<form id="edit_form" method="POST" >
				<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
				<input type="hidden" name="year" value="<?=$class['year'];?>" />
				<input type="hidden" name="class_no" value="<?=$class['class_no'];?>" />
				<input type="hidden" name="term" value="<?=$class['term'];?>" />

				<table width="100%" >
					<tr>
					<td bgcolor="#eeeeee">
					<table width="100%" class="table table-bordered table-striped table-condensed" id='show_table_1'>
						<tr>
							<td align="center" bgcolor="#5D7B9D" width="40"><font color="#ffffff"></font></td>
							<td align="center" bgcolor="#5D7B9D" width="40"><font color="#ffffff">序號</font></td>
							<td align="center" bgcolor="#5D7B9D" width="80"><font color="#ffffff">優先順序</font></td>
				        	<td align="center" bgcolor="#5D7B9D" width="110"><font color="#ffffff">身分證字號</font></td>
				        	<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
				        	<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">出生年月</font></td>
				        	<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">局處/外機關單位</font></td>
				        	<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>
				        	<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">身障需求註記</font></td>
				        </tr>
				        <?php $seq = '1'; ?>
				        <?php foreach($regist_list as $row) { ?>
				        <tr>
				        	<td align="center" ><a class="btn btn-default" onclick="btn_del('<?=$row['id'];?>')">取消報名</a></td>
				        	<td align="center" ><?=$seq;?></td>
				        	<td align="center" ><input type="text" name="chkNO[]" size="3" maxlength="3" value="<?=$row['insert_order'];?>"</td>
				        	<td align="center" ><?=$row['id'];?></td>
				        	<input type="hidden" name="chkID[]" value="<?=$row['id'];?>" >
				        	<td id="name" align="center" ><?=$row['name'];?></td>
				        	<td id="birthday" align="center" ><?=$row['birthday'];?></td>
				        	<td id="beaurau_name" align="center" ><?=$row['beaurau_name'];?></td>
				        	<td id="title" align="center" ><?=$row['title'];?></td>
				        	<td id="phydisabled" align="center" ><?=$row['phydisabled'];?></td>
				        </tr>
				        <?php $seq++; ?>
				        <?php } ?>
					</table>
					<div>
						<a class="btn btn-default" onclick="btn_edit()" title="修改順序">修改順序</a>
						<a class="btn btn-default" onclick="all_cancel()" title="全部取消報名">全部取消報名</a>
					</div>
					</td>
					</tr>
				</table>
				</form>
				<?php } ?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>

function check_ID(obj)
{
	var tdItm=obj.parentElement;
	var trItm=tdItm.parentNode;
	var row1 = trItm.rowIndex
 	id=obj.value;
 	if(id=='') {
	  	obj.value='';
	  	trItm.cells[3].innerHTML = '';
		trItm.cells[4].innerHTML = '';
		trItm.cells[5].innerHTML = '';
		trItm.cells[6].innerHTML = '';
		trItm.cells[7].innerHTML = '';
 		return;
 	}
	obj.value = id.toUpperCase();
	id = obj.value;

	var url = '<?=base_url('management/regist_contractors/ajax/check_person');?>';

    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'id': id,
        'year': '<?=$class['year'];?>',
        'class_no': '<?=$class['class_no'];?>',
        'term': '<?=$class['term'];?>',
    }
    //console.log(data);
    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        console.log(response);
                        trItm.cells[3].innerHTML = response.person.name;
						trItm.cells[4].innerHTML = response.person.birthday;
						trItm.cells[5].innerHTML = response.person.bureau;
						trItm.cells[6].innerHTML = response.person.job_title_name;
						trItm.cells[7].innerHTML = '<a class="btn btn-outline btn-link btn-xs btn-toggle" onclick=chgDis("' + response.person.phy_url + '") ><font color="blue"><u>'+ response.person.phydisabled +'</u></font></a>';
						document.getElementById("btn_save").disabled = false;
                    } else {
                    	alert(response.msg);
                    	console.log(response);
                    	obj.value='';
                    	trItm.cells[3].innerHTML = '';
						trItm.cells[4].innerHTML = '';
						trItm.cells[5].innerHTML = '';
						trItm.cells[6].innerHTML = '';
						trItm.cells[7].innerHTML = '';
                    	document.getElementById("btn_save").disabled = true;
                    }
                }

    });
}

function all_cancel()
{
    var yesfunc = function() {
    	var $form = $('#edit_form');
        var url = '<?=base_url('management/regist_contractors/ajax/all_cancel');?>';

	    $.ajax({
	        url: url,
	        data: $form.serialize(),
	        type: "POST",
	        dataType: 'json',
	        success: function(response){
	                    if (response.status) {
                            var func_1 = function() {
	                    		location.reload();
	                    	}
	                    	bk_confirm_2(0, '刪除成功', 'center', func_1);
                        } else {

                        }
	                }

	    });
    }

    var nofunc = function() {
        // bk_alert(4, 'ok', 4, 'center');
    }

	bk_confirm(3, '是否確定刪除?', 'center', yesfunc, nofunc);
}

function btn_edit()
{
	var $form = $('#edit_form');
	var url = '<?=base_url('management/regist_contractors/ajax/regist_edit');?>';

    $.ajax({
        url: url,
        data: $form.serialize(),
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                    	var yesfunc = function() {
                    		location.reload();
                    	}
                    	bk_confirm_2(0, '修改成功', 'center', yesfunc);
                    } else {

                    }
                }

    });
}

function btn_del(id)
{

	var yesfunc = function() {
        var url = '<?=base_url('management/regist_contractors/ajax/regist_del');?>';

	    var data = {
	        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
	        'id': id,
	        'year': '<?=$class['year'];?>',
	        'class_no': '<?=$class['class_no'];?>',
	        'term': '<?=$class['term'];?>',
	    }

	    $.ajax({
	        url: url,
	        data: data,
	        type: "POST",
	        dataType: 'json',
	        success: function(response){
	                    if (response.status) {
                            var func_1 = function() {
	                    		location.reload();
	                    	}
	                    	bk_confirm_2(0, '刪除成功', 'center', func_1);
                        } else {

                        }
	                }

	    });
    }

    var nofunc = function() {
        // bk_alert(4, 'ok', 4, 'center');
    }

	bk_confirm(3, '是否確定刪除?', 'center', yesfunc, nofunc);

}

function chgDis(phy_url){
  var h=160;
  var w=300;
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);

  var myW=window.open(phy_url,'chgDis','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width='+w+', height='+h+',top='+top+', left='+left);
  myW.focus();
}

function do_enrollment()
{
	 $('#actQuery').submit();
}
</script>