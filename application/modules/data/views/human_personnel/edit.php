<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>人事資料維護-更新</title>
<link rel="stylesheet" type="text/css" href="css/master.css"/>
</head>
<body>
  <div class='title' style="color:green;font-size:150%;width:100%">人事資料維護-更新</div>
<div class='insert_block'>
  <form  name='applicant_insert_form' method='post' class='insert_form' id='applicant_insert_form' >
  	<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
	  <table width="100%" class="table" >
	    <tr>
	      <th bgcolor="#DCDCDC" style="font-size:100%">局處</th>
		  <td>
			<input type="text" name="gname" id="gname" SIZE=30 value="<?=$user_data['gname'];?>"></td>
		  </td>
	      <th bgcolor="#DCDCDC"  style="font-size:100%"></th>
	      <td></td>
	    </tr>
	    <tr>
	      <th bgcolor="#DCDCDC" style="font-size:100%">承辦人</th>
	      <td><input type="text" name="name" id="name" SIZE=30 value="<?=$user_data['name'];?>"></td>
	      <th bgcolor="#DCDCDC"  style="font-size:100%">性別</th>
	      <td>
		  <select id="gender" name="gender">
			<?php if ($gender=='F') {
				echo '<option value="M">男<option value="F" selected>女';
			} elseif (($gender=='M') ) {
				echo '<option value="M" selected>男<option value="F">女';
			} else {
				echo '<option value="M">男<option value="F">女';
			}?>
		  </select>
		  </td>
	    </tr>
	    <tr>
	      <th bgcolor="#DCDCDC"  style="font-size:100%"><input type="button" value="查詢" onclick="showtitle('tmp_tid','0')" class="button">職稱</th>
		  <td>
			<input type="hidden" name="tmp_tid" id="tmp_tid" value="">
			<input type="hidden" name="title" id="title" value="<?=$user_data['title'];?>">
			<input type="text" name="t_name" id="t_name" value="<?=$user_data['job'];?>" size="20" readonly>
		  </td>
	      <th bgcolor="#DCDCDC"  style="font-size:100%;color: red">Email-1(研習通知)</th>
	      <td><input type="text" name="email" id="email" SIZE=30 value="<?=$user_data['email'];?>"></td>
	    </tr>
	    <tr>
	      <th style="font-size:100%"></th>
	      <td></td>
	      <th bgcolor="#DCDCDC"  style="font-size:100%;color: red">Email-2(勤惰通知)</th>
	      <td><input type="text" name="email2" id="email2" SIZE=30 value="<?=$user_data['email2'];?>"></td>
	    </tr>
	   <tr>
	      <th bgcolor="#DCDCDC"  style="font-size:100%">公司電話[分機]</th>
	      <td><input type="text" name="office_tel" id="office_tel" SIZE=15 value="<?=$user_data['office_tel'];?>">[
		  <input type="text" name="office_tel_ext" id="office_tel_ext" SIZE=5 value="<?=$user_data['office_tel_ext'];?>">]</td>
	      <th bgcolor="#DCDCDC"  style="font-size:100%">公司傳真</th>
	      <td><input type="text" name="office_fax" id="office_fax" SIZE=30 value="<?=$user_data['office_fax'];?>"></td>
	    </tr>
			  <tr>
				  <td colspan="4" style="background-color:#DCDCDC;">
	    			<input type='button' value='儲存'  class='button' onclick='SaveForm()' />
	    			<input type='button' value='取消'  class='button' onclick='	window.close();' />
	    			<input type="hidden" name="mode" id="mode" value="" >
				  </td>
				</tr>


	  </table>
</form>

</div>
</body>
</html>

<script>
function showtitle(x,y){
  var myW = window.open('<?=base_url('data/human_personnel/co_title');?>','seltitle','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}
function seltitleOK(){
  var obj = document.all.tmp_tid;
  var tmp = obj.value.split("::");
  document.all.title.value = tmp[0];
  document.all.t_name.value = tmp[1];
  obj.value = "";
}
function SaveForm(){
  obj = document.getElementById("applicant_insert_form");
  document.all.mode.value = "save";
  obj.submit();
}
</script>