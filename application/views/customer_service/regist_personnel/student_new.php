
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>學員個人資料</title>
<script src="<?=HTTP_PLUGIN;?>jquery-1.12.4.min.js"></script>
</head>
<body>
	<div class='title' style="color:green;font-size:150%;width:100%">學員個人資料</div>
	<div class='insert_block'>
	<form name='applicant_insert_form' method='post' class='insert_form' id='applicant_insert_form' >
		<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
		<table class="table" >
			<tr>
				<th bgcolor="#DCDCDC" style="font-size:100%" value="">姓名</th>
				<td ><input type="text" name="name" id="name" value="" onblur="check_Name(this)"></td>
				<th bgcolor="#DCDCDC"  style="font-size:100%">職稱</th>
				<td>
					<input type="hidden" name="tmp_tid" id="tmp_tid" value="">
					<input type="hidden" name="title" id="title" value="">
					<input type="text" name="t_name" id="t_name" value="" size="20" readonly>
					<input type="button" value="查詢職稱" onclick=showtitle("tmp_tid","0") class="button">
				</td>
			</tr>

			<tr>
				<th bgcolor="#DCDCDC"  style="font-size:100%" value="" >身分證</th>
				<td><input type="text" name="personal_id" id="personal_id" value="" onblur="check_id(this)" ></td>
				<th bgcolor="#DCDCDC"  style="font-size:100%">性別</th>
				<td>
					<input type="radio" name="gender"  id="gender" value="F">女性
					<input type="radio" name="gender"  id="gender" value="M">男性

				</td>
			</tr>
			<tr>
				<th bgcolor="#DCDCDC"  style="font-size:100%">局處名稱</th>
				<td>
					<input type="hidden" name="tmp_bid" id="tmp_bid" value="">
					<input type="hidden" name="bid" id="bid" value="">
					<input type="text" name="bname" id="bname" value="" size="30" readonly>
					<input type="button" value="查詢局處" onclick=showbeaurau() class="button">
				</td>
				<th bgcolor="#DCDCDC"  style="font-size:100%">Email</th>
				<td><input type="text" name="email" id="email" value="" ></td>
			</tr>

			<tr>
				<th bgcolor="#DCDCDC"  style="font-size:100%">公司電話</th>
				<td><input type="text" name="office_tel" id="office_tel" value="" ></td>
				<th bgcolor="#DCDCDC"  style="font-size:100%">學歷</th>
				<td>
					<select  name="edu_level" id="edu_level" >
						<option value="" >請選擇</option>
						<option value="20" >國(初)中以下</option>
						<option value="30" >高中(職)</option>
						<option value="40" >專科</option>
						<option value="50" >大學</option>
						<option value="60" >碩士</option>
						<option value="70" >博士</option>
					</select>
				</td>

			</tr>

			<tr>
				<th bgcolor="#DCDCDC"  style="font-size:100%">現職區分</th>
				<td>
					<select  name="job_Distinguish" id="job_Distinguish" >
						<option value="11" >其他</option>
						<option value="01" >簡任主管 </option>
						<option value="02" >簡任非主管</option>
						<option value="03" >荐任主管</option>
						<option value="04" >荐任非主管</option>
						<option value="05" >委任主管</option>
						<option value="06" >委任非主管</option>
						<option value="07" >警察消防主管</option>
						<option value="08" >警察消防非主管</option>
						<option value="09" >約聘僱人員</option>
						<option value="10" >技工工友</option>
					</select>
				</td>

			</tr>

			<tr>
				<th bgcolor="#DCDCDC"  style="font-size:100%">生日</th>
				<td>
					<select  name="bir_year" id="bir_year" >
						<?php
							$x=1899; while($x<=intval(date("Y"))){ 	echo '<option value="'.$x.'" ';
							echo '>'.$x.'</option>';	$x++; }
						?>
					</select>
					<select  name="bir_month" id="bir_month" >
						<?php
							$x=0; while($x<12){ 	echo '<option value="'.($x+1).'"';
							echo '>'.($x+1).'</option>';	$x++; }
						?>
					</select>
					<select  name="bir_day" id="bir_day" >
						<?php
							$x=0; while($x<31){ 	echo '<option value="'.($x+1).'"';
							echo '>'.($x+1).'</option>';	$x++; }
						?>
					</select>
				</td>

			</tr>
			<tr>
				<th bgcolor="#DCDCDC"  style="font-size:100%">備註</th>
				<td>
				<font color="red">【以上欄位請務必輸入屬員正確個資】</font>
				</td>

			</tr>
			<tr>
				<td colspan="4" style="background-color:#DCDCDC;">
	    			<input type='button' value='儲存'  class='button' onclick='SaveForm()' />
	    			<input type='button' value='取消'  class='button' onclick='	window.close();' />
	    			<input type="hidden" name="mode" id="mode" value="" >
	    			<input type="hidden" name="row" id="row" value="<? echo $row; ?>" >
	    			<input type="hidden" name="wbid" id="wbid" value="<? echo $beaurauId; ?>" >

				</td>
			</tr>


		</table>
	</form>
</div>

</body>
</html>

<script>

function SaveForm(){
	obj = document.getElementById("applicant_insert_form");
	obj.personal_id.value = myTrim(obj.personal_id.value);

	if (obj.name.value == "")
	{
		alert("請輸入姓名!");
		obj.name.focus();
		return false;
	}
	if (obj.t_name.value == "")
	{
		alert("請輸入職稱!");
		obj.t_name.focus();
		return false;
	}

	if (obj.personal_id.value == "")
	{
		alert("請輸入正確的身分證字號!");
		obj.personal_id.focus();
		return false;
    } else {
	    if (obj.personal_id.value.length!=10) {
		    if (!confirm('身分證字號長度非10碼，繼續?')) {
 		        obj.personal_id.focus();
			    return false;
			}
		}
		obj.personal_id.value = obj.personal_id.value.toUpperCase();

	}

	if (!obj.gender[0].checked && !obj.gender[1].checked)
	{
		alert("請輸入性別!!");
		obj.gender[0].focus();
		return false;
	}
	if (obj.bname.value == "")
	{
		alert("請輸入局處名稱!!");
		obj.bname.focus();
		return false;
	}
	if (obj.email.value == "")
	{
		alert("請輸入Email!!");
		obj.email.focus();
		return false;
	}
	if (obj.office_tel.value == "")
	{
		alert("請輸入公司電話!!");
		obj.office_tel.focus();
		return false;
	}
	if (obj.edu_level.value == "")
	{
		alert("請輸入學歷!!");
		obj.edu_level.focus();
		return false;
	}

	var $form = $('#applicant_insert_form');
	var url = '<?=base_url('customer_service/regist_personnel/ajax/student_new');?>';

    $.ajax({
        url: url,
        data: $form.serialize(),
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                    	alert('新增成功!');

                    	window.opener.again_show(<?=$student_new['row'];?>,obj.personal_id.value);
                    	window.close();
                    } else {

                    }
                }

    });
}
function myTrim(x) {
    return x.replace(/^\s+|\s+$/gm,'');
}


function showbeaurau(){
  var myW = window.open('<?=base_url('customer_service/regist_personnel/bureau');?>','selbeaurau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}

function selbeaurauOK(){
  var obj = document.all.tmp_bid;
  var tmp = obj.value.split("::");
  document.all.bid.value = tmp[0];
  document.all.bname.value = tmp[1];
  obj.value = "";
}

function showtitle(x,y){
  var myW = window.open('<?=base_url('customer_service/regist_personnel/co_title');?>','seltitle','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}

function seltitleOK(){
  var obj = document.all.tmp_tid;
  var tmp = obj.value.split("::");
  document.all.title.value = tmp[0];
  document.all.t_name.value = tmp[1];
  obj.value = "";
}

function check_Name(obj)
{
	var name = obj.value;

	if(name.length != 0){
		var url = '<?=base_url('customer_service/regist_personnel/ajax/check_name');?>';

	    var data = {
	        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
	        'name': name,
	    }

	    $.ajax({
	        url: url,
	        data: data,
	        type: "POST",
	        dataType: 'json',
	        success: function(response){
	                    if (response.status) {

	                    } else {
	                    	alert(response.msg);
	                    }
	                }

	    });
	}
}

function check_id(obj)
{
	var idno = obj.value;

	if(idno.length == 10){
		var url = '<?=base_url('customer_service/regist_personnel/ajax/check_idno');?>';

	    var data = {
	        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
	        'idno': idno,
	    }

	    $.ajax({
	        url: url,
	        data: data,
	        type: "POST",
	        dataType: 'json',
	        success: function(response){
	                    if (response.status) {

	                    } else {
	                    	alert(response.msg);
	                    	obj.value = "";
	                    }
	                }

	    });
	}
}
</script>