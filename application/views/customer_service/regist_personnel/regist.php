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
						<tr>
							<td width="120" align="center" bgcolor="#dcdcdc">配當人數</td>
							<td align="left" bgcolor="#ffffff">
							<?php
							$tmpString = '';
							if ($beaurau_persons['currentNo']<$beaurau_persons['persons']) {
							    $tmpString = '，還差'.($beaurau_persons['persons']-$beaurau_persons['currentNo']).'人';
							}
					        echo '最小人數: '.$beaurau_persons['persons'].'人， 最大人數: '.$beaurau_persons['persons_2'].'人， 本機關已報名(選員)人數: '.$beaurau_persons['currentNo'].'人'.$tmpString;
					        ?>
					        </td>
						</tr>
						<tr>
							<td width="120" align="center" bgcolor="#dcdcdc">備註</td>
							<td align="left" bgcolor="#ffffff">
								<font color="blue">
							一、本系統功能僅提供人事報名機關所屬人員。報名時務請輸入正確之身分證字號，系統即自動帶出屬員資料。</font><br>
							<font color="red">二、報名時如顯示【無此身分證號】，表示該員為新進人員或未曾至本處研習，請點選【確定】後新增該員基本資料，同時確認該員資料是否已輸入《WebHR人力資源管理系統》。</font><br><font color="blue">
							三、本系統學員個人資料係介接自《WebHR人力資源管理系統》，點按【修改個資】發現資料有誤時，請務必至《WebHR人力資源管理系統》修正，俾便學員收到研習通知Email。<br>
							四、參訓同仁如為身障者(或特殊需求)，請於身障需求註記欄勾選，俾提供適當協助。</font><br>
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
						  <td align="center" bgcolor="#5D7B9D" width="80"><font color="#ffffff">優先順序</font></td>
				          <td align="center" bgcolor="#5D7B9D" width="110"><font color="#ffffff">身分證字號</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">出生年月</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">局處</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">身障需求註記</font></td>
				          <td align="center" bgcolor="#5D7B9D" width="100"><font color="#ffffff">修改</font></td>
				        </tr>
    					<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
				        <?php for($i=1;$i<11;$i++) { ?>
				        <tr>
				        	<td align="center" ><?=$i;?></td>
				        	<td align="center" ><input type="text" id="order_<?=$i;?>" name="selNO[]" size="3" maxlength="3" value="<?=$class['max_order'];?>"</td>
							<td align="center" ><input type="text" id="selID_<?=$i;?>" name="selID[]" size="10" maxlength="10" value="" onblur="check_ID_new(this)"></td>
				        	<td id="name" align="center" ></td>
				        	<td id="birthday" align="center" ></td>
				        	<td id="beaurau_name" align="center" ></td>
				        	<td id="title" align="center" ></td>
				        	<td id="phydisabled" align="center" ></td>
				        	<td align="center" ><input type="button" value="修改個資" class="button" onclick="edit_data(<?=$i;?>)" ></td>
				        </tr>
				        <?php $class['max_order']++; } ?>
					</table>
					<div>
						<button id="btn_save" class="btn btn-default" <?=($class['canapp']=='N')?'style="display:none"':'';?> onclick="do_enrollment()" disabled="disabled" title="確定">確定</button>
						<a class="btn btn-default" href="<?=$link_cancel;?>" title="返回">返回</a>
						<a class="btn btn-default" href="<?=$import;?>" <?=($class['canapp']=='N')?'style="display:none"':'';?> title="匯入" <?=($class['isend'] == 'Y')?'disabled':''?>>匯入</a>
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
						<a class="btn btn-default" onclick="output_csv()" title="匯出">匯出</a>
						<a class="btn btn-default" onclick="output_pdf()" title="列印">列印</a>
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

function output_csv(){

	var myW = window.open('<?=$regist_csv;?>','student_detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=400,width=800');

}

function output_pdf(){

	var myW = window.open('<?=$regist_pdf;?>','student_detail');

}

function edit_data(row){
	var ids = document.getElementsByName("selID[]");
	var obj=ids[row-1].value;

	if(obj==""){
		alert("請輸入身分證!");
		return;
	}
	var myW = window.open('<?=$student_detail_edit;?>id=' + obj +'&row='+row,'student_detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=400,width=800');
	myW.focus();
}

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

	var url = '<?=base_url('customer_service/regist_personnel/ajax/check_person');?>';

    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'id': id,
        'year': '<?=$class['year'];?>',
        'class_no': '<?=$class['class_no'];?>',
        'term': '<?=$class['term'];?>',
        'row': row1,
    }

    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        // console.log(response.person);
                        <?php if($class['canapp']=='N'){ ?>
                        	alert('非報名期間不允許報名');
                        <?php } ?>
                        trItm.cells[3].innerHTML = response.person.name;
						trItm.cells[4].innerHTML = response.person.birthday;
						trItm.cells[5].innerHTML = response.person.bureau;
						trItm.cells[6].innerHTML = response.person.job_title_name;
						trItm.cells[7].innerHTML = '<a class="btn btn-outline btn-link btn-xs btn-toggle" onclick=chgDis("' + response.person.phy_url + '") ><font color="blue"><u>'+ response.person.phydisabled +'</u></font></a>';
						document.getElementById("btn_save").disabled = false;
                    } else {
                    	alert(response.msg);
                    	obj.value='';
                    	trItm.cells[3].innerHTML = '';
						trItm.cells[4].innerHTML = '';
						trItm.cells[5].innerHTML = '';
						trItm.cells[6].innerHTML = '';
						trItm.cells[7].innerHTML = '';
                    	document.getElementById("btn_save").disabled = true;
                    	if(response.url){
                    		var myW=window.open(response.url ,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=400,width=800');
							myW.focus();
                    	}
                    }
                }

    });
}

function check_ID_new(obj)
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

	var url = '<?=base_url('customer_service/regist_personnel/ajax/check_person');?>';

    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'id': id,
        'year': '<?=$class['year'];?>',
        'class_no': '<?=$class['class_no'];?>',
        'term': '<?=$class['term'];?>',
        'row': row1,
    }

    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        // console.log(response.person);
                        <?php if($class['canapp']=='N'){ ?>
                        	alert('非報名期間不允許報名');
                        <?php } ?>
                        trItm.cells[3].innerHTML = response.person.name;
						trItm.cells[4].innerHTML = response.person.birthday;
						trItm.cells[5].innerHTML = response.person.bureau;
						trItm.cells[6].innerHTML = response.person.job_title_name;
						trItm.cells[7].innerHTML = '<a class="btn btn-outline btn-link btn-xs btn-toggle" onclick=chgDis("' + response.person.phy_url + '") ><font color="blue"><u>'+ response.person.phydisabled +'</u></font></a>';
						document.getElementById("btn_save").disabled = false;
                    } else {
                    	alert(response.msg);
                    	obj.value='';
                    	trItm.cells[3].innerHTML = '';
						trItm.cells[4].innerHTML = '';
						trItm.cells[5].innerHTML = '';
						trItm.cells[6].innerHTML = '';
						trItm.cells[7].innerHTML = '';
                    	document.getElementById("btn_save").disabled = true;
                    	if(response.url){
							var tempForm = document.createElement("form");     
							tempForm.id="tempForm1";     
							tempForm.method="post";     
							tempForm.action=response.url; 
							tempForm.target='add_data';     

							var hideInput = document.createElement("input");     
							hideInput.type="hidden";     
							hideInput.name= "pid";
							hideInput.value= id;   
							tempForm.appendChild(hideInput); 

							var hideInput = document.createElement("input");     
							hideInput.type="hidden";     
							hideInput.name= "row_id";
							hideInput.value= response.row_id;   
							tempForm.appendChild(hideInput); 
							
							var hideInput = document.createElement("input");     
							hideInput.type="hidden";     
							hideInput.name= '<?=$csrf["name"];?>';
							hideInput.value= '<?=$csrf["hash"];?>';   
							tempForm.appendChild(hideInput); 
							
							tempForm.addEventListener("onsubmit",function(){ 
								var myW=window.open('about:blank' ,'add_data','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=400,width=800');
								myW.focus();
							 });   

							document.body.appendChild(tempForm);   
							
							tempForm.submit();   
						
							document.body.removeChild(tempForm); 
                    	}
                    }
                }

    });
}

function all_cancel()
{
    var yesfunc = function() {
    	var $form = $('#edit_form');
        var url = '<?=base_url('customer_service/regist_personnel/ajax/all_cancel');?>';

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
	var url = '<?=base_url('customer_service/regist_personnel/ajax/regist_edit');?>';

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
        var url = '<?=base_url('customer_service/regist_personnel/ajax/regist_del');?>';

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

function again_show(row,id)
{
	var tab=document.getElementById("show_table");
	var trItm=tab.rows[row];
	// console.log(trItm);
	var url = '<?=base_url('customer_service/regist_personnel/ajax/check_person');?>';
	var idno=document.getElementById("selID_"+ row);
	idno.value = id;

    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'id': id,
        'year': '<?=$class['year'];?>',
        'class_no': '<?=$class['class_no'];?>',
        'term': '<?=$class['term'];?>',
        'row': row,
    }

    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                        // console.log(response.person);
                        <?php if($class['canapp']=='N'){ ?>
                        	alert('非報名期間不允許報名');
                        <?php } ?>
                        trItm.cells[3].innerHTML = response.person.name;
						trItm.cells[4].innerHTML = response.person.birthday;
						trItm.cells[5].innerHTML = response.person.bureau;
						trItm.cells[6].innerHTML = response.person.job_title_name;
						trItm.cells[7].innerHTML = '<a class="btn btn-outline btn-link btn-xs btn-toggle" onclick=chgDis("' + response.person.phy_url + '") ><font color="blue"><u>'+ response.person.phydisabled +'</u></font></a>';
						document.getElementById("btn_save").disabled = false;
                    } else {
                    	alert(response.msg);
                    	obj.value='';
                    	trItm.cells[3].innerHTML = '';
						trItm.cells[4].innerHTML = '';
						trItm.cells[5].innerHTML = '';
						trItm.cells[6].innerHTML = '';
						trItm.cells[7].innerHTML = '';
                    	document.getElementById("btn_save").disabled = true;
                    	if(response.url){
                    		var myW=window.open(response.url ,'','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=400,width=800');
							myW.focus();
                    	}
                    }
                }

    });
}
</script>