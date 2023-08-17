
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>身障需求註記</title>

</head>
<body>

<script>
<?php if($set_phydisabled == 'Y') { ?>
	var set_id = "<?=$phydisabled_id;?>";
	var parentForm = window.opener.document.forms.actQuery;
	var parentControls = parentForm.elements['selID[]'];

	for(i=0;i<10;i++){
		if(parentControls[i].value == set_id){
			var obj=parentControls[i];
		}
	}
	// console.log(parentControls);
	window.opener.check_ID(obj);
 	window.close();
<?php } ?>
function getID(x) {
  var parentForm = window.opener.document.forms.actQuery;
  var parentControls = parentForm.elements['selID[]'];
  alert (parentControls[x].value);
}

function popupOK(x){
  obj = document.getElementById("actAmt");
  obj.submit();
}

function checkSel(obj)
{
	if (obj.value == '其他障別')
	{
		document.all.other_memo.style.display = "";
		document.all.explain.style.display = "";
		document.all.other_memo.focus();
	}
	else
	{
		document.all.other_memo.style.display = "none";
		document.all.explain.style.display = "none";
	}
}

</script>

<form id="actAmt" method="post" action="<?=$link_save_phydisabled;?>">
	<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#909397">

      <table width="100%" border="0" cellspacing="1" cellpadding="1" style="FONT-SIZE:12px;">
				<tr>
				  <td align="center" bgcolor="#E9EEF4">身障需求註記</td>
					<td align="left" bgcolor="#ffffff">
			<select name='memo' onchange="checkSel(this);">
			<?php
			if ($memo =="")
				echo "<option value='' selected>無</option>";
			else
				echo "<option value=''>無</option>";

			if ($memo =="視障")
				echo "<option value='視障' selected>視障</option>";
			else
				echo "<option value='視障'>視障</option>";

			if ($memo =="聽障")
				echo "<option value='聽障' selected>聽障</option>";
			else
				echo "<option value='聽障'>聽障</option>";

			if ($memo =="肢障")
				echo "<option value='肢障' selected>肢障</option>";
			else
				echo "<option value='肢障'>肢障</option>";

			if ($memo_status =='0') {
				echo "<option value='其他障別' selected>其他障別</option></select>";
				echo '</td><tr><td align="center" bgcolor="#E9EEF4"><span id="explain" style="display:" >說明</span></td><td><input type="text" id="other_memo" name="other_memo" maxlength="25" style="display:" value="' . $memo   . '" >';
			} else	{
				echo "<option value='其他障別'>其他障別</option></select>";
				echo '</td><tr><td align="center" bgcolor="#E9EEF4"><span id="explain" style="display:none" >說明</span></td><td><input type="text" id="other_memo" name="other_memo" maxlength="25" style="display:none" value="" >';
			}
            ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center" height="3"></td>
        </tr>
				<tr>
					<td colspan="3" align="center" bgcolor="#dcdcdc">
					  <input type="button" value="確定" class="button" style="FONT-SIZE:12px;" onclick="popupOK()">
      			<input type="button" value="取消" class="button" style="FONT-SIZE:12px;" onclick="window.close()">
          </td>
        </tr>
			</table>

		</td>
	</tr>
</table>
</form>

</body>
</html>