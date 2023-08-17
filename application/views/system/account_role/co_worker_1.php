
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>選取承辦人</title>
	<link type="text/css" rel="stylesheet" href="css/master.css"/>
</head>
<body>

<script>
function doQuery(){
  obj = document.getElementById("actQuery");
  obj.submit();
}
function page(n){
  <?php
	echo "var worker_page = " . $filter['worker_page'] . ";";
	echo "var total_page = " . $total_page . ";";
  ?>
	var obj = null;
	obj = document.getElementById("actQuery");
	if ((typeof(obj) != "object") || (obj == null)) return false;
	var tmp = 0;
	switch(n){
		case -1:
			tmp = 1;
			break;
		case -2:
			tmp = (worker_page-1);
			break;
		case -3:
			tmp = (worker_page+1);
			break;
		case -4:
			tmp = total_page;
			break;
		default:
			var p = parseInt(n, 10);

			if (p >= 0 && p <= total_page){
				tmp = p;
			}
			break;
	}
	obj.worker_page.value = tmp;
  obj.submit();
}
function popupOK(x){

  window.opener.document.getElementById("worker").value = x.value;

  window.opener.selWorkerOK();

  window.close();
}
</script>

<form id="actQuery" role="form">
<div id="list1">
<table width="99%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="15%">
局處名稱：
</td>
<td width="35%" align="left">
<input type="text" name="key" id="key" value="<?=$filter['key'];?>">
</td>
<td width="15%">
帳號：
</td>
<td width="35%" align="left">
<input type="text" name="key1" id="key1" value="<?=$filter['key1'];?>">
</td>
</tr>
<tr>
<td width="15%">
身分證：
</td>
<td width="35%" align="left">
<input type="text" name="key2" id="key2" value="<?=$filter['key2'];?>">
</td>

<td width="15%">
姓名：
</td>
<td width="35%" align="left">
<input type="text" name="key3" id="key3" value="<?=$filter['key3'];?>">
</td>
</tr>
<tr>
	<td  align="center" colspan="4">
		<input type="button" value="查詢" class="button" style="FONT-SIZE:18px;" onclick="doQuery()">
	<td>
</tr>
<tr>
	<td  align="center" colspan="4">
		&nbsp;
	<td>
</tr>
</table>

<?php
echo '<input type="hidden" name="worker_page" id="worker_page" value="' . $filter['worker_page'] . '">';
?>
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#909397">

      <table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td colspan="5" bgcolor="#dcdcdc">
      		  <span style="font:0.8em Verdana, Arial, Helvetica, sans-serif;">
      		  <?php
      		  echo "頁次:" . $filter['worker_page'] . "/" . $total_page;
            ?>
            </span>
      		<?php
      		$p1 = (($filter['worker_page']==1) || ($filter['worker_page']==0)) ? "disabled" : 'onclick="page(-1)"';
          $p2 = (($filter['worker_page']==1) || ($filter['worker_page']==0)) ? "disabled" : 'onclick="page(-2)"';
          $p3 = (($filter['worker_page']==$total_page) || ($filter['worker_page']==0)) ? "disabled" : 'onclick="page(-3)"';
          $p4 = (($filter['worker_page']==$total_page) || ($filter['worker_page']==0)) ? "disabled" : 'onclick="page(-4)"';
      		echo '<input type="button" value="首頁" class="button" style="FONT-SIZE:12px;" ' . $p1 . '>';
      		echo '<input type="button" value="上頁" class="button" style="FONT-SIZE:12px;" ' . $p2 . '>';
      		echo '<input type="button" value="下頁" class="button" style="FONT-SIZE:12px;" ' . $p3 . '>';
      		echo '<input type="button" value="末頁" class="button" style="FONT-SIZE:12px;" ' . $p4 . '>';
            ?>
          </td>
        </tr>
				<tr>
				  <td width="50" align="center" bgcolor="#E9EEF4">選取</td>
					<td width="200" align="center" bgcolor="#E9EEF4">局處名稱</td>
					<td width="80" align="center" bgcolor="#E9EEF4">姓名</td>
					<td width="130" align="center" bgcolor="#E9EEF4">身分證</td>
					<td width="130" align="center" bgcolor="#E9EEF4">帳號</td>


        </tr>
        <?php
        foreach($worker_list as $fields){
          echo "<tr>";
          echo '<td align="center" bgcolor="#ffffff">';
          $value = $fields['b_name'] . "::" . $fields['username']."::".$fields['idno'] . "::" . $fields['name'];
          echo '<input id="Arrsel" name="Arrsel" type="radio" value="' . $value . '" onclick="popupOK(this)">';
          echo '</td>';
          echo '<td align="center" bgcolor="#ffffff">' . $fields['b_name'] . '</td>';
          echo '<td align="left" bgcolor="#ffffff">' . $fields['name'] . '</td>';
          echo '<td align="center" bgcolor="#ffffff">' . $fields['idno'] . '</td>';
          echo '<td align="center" bgcolor="#ffffff">' . $fields['username'] . '</td>';


          echo "</tr>";
        }
        ?>
        <tr>
          <td colspan="5" align="center" height="3"></td>
        </tr>
				<tr>
					<td colspan="5" align="center" bgcolor="#dcdcdc">
      			<input type="button" value="取消" class="button" style="FONT-SIZE:12px;" onclick="window.close()">
          </td>
        </tr>
			</table>

		</td>
	</tr>
</table>
</div>

</form>

</body>
</html>
