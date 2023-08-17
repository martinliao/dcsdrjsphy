
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>選取銀行</title>
</head>
<body>

<script>
function doQuery(){
  obj = document.getElementById("actQuery");
  obj.submit();
}
function page(n){
  <?php
	echo "var cur_page = " . $filter['search_page'] . ";";
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
			tmp = (cur_page-1);
			break;
		case -3:
			tmp = (cur_page+1);
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
	obj.search_page.value = tmp;
  obj.submit();
}
function popupOK(x){
  <?php
  echo 'var obj1 = window.opener.document.getElementById("aBank");';
  echo 'var obj2 = window.opener.document.getElementById("aBankName");';
  echo 'var obj3 = window.opener.document.getElementById("aAccount");';
  ?>
  var tmp = x.value.split("::");
  if (typeof(obj1) != "undefined")
  {
    obj1.value = tmp[0];
	if (tmp[0]=='0000')
	{
		obj3.value = tmp[1];
	}
  }
  if (typeof(obj2) != "undefined")
  {
    obj2.value = tmp[0]+'  '+tmp[1];
  }
  window.close();
}
</script>

<form id="actQuery" role="form">
<div id="list1">
請輸入關鍵字
<input type="text" name="key" id="key" value="<?=$filter['key'];?>">
<input type="button" value="查詢" class="button" style="FONT-SIZE:12px;" onclick="doQuery()">
<?php
echo '<input type="hidden" name="search_page" id="search_page" value="' . $filter['search_page'] . '">';
?>
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#909397">

      <table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td colspan="3" bgcolor="#dcdcdc">
      		  <span style="font:0.8em Verdana, Arial, Helvetica, sans-serif;">
      		  <?php
      		  echo "頁次:" . $filter['search_page'] . "/" . $total_page;
            ?>
            </span>
      		  <?php
            $p1 = (($filter['search_page']==1) || ($filter['search_page']==0)) ? "disabled" : 'onclick="page(-1)"';
            $p2 = (($filter['search_page']==1) || ($filter['search_page']==0)) ? "disabled" : 'onclick="page(-2)"';
            $p3 = (($filter['search_page']==$total_page) || ($filter['search_page']==0)) ? "disabled" : 'onclick="page(-3)"';
            $p4 = (($filter['search_page']==$total_page) || ($filter['search_page']==0)) ? "disabled" : 'onclick="page(-4)"';
      		  echo '<input type="button" value="首頁" class="button" style="FONT-SIZE:12px;" ' . $p1 . '>';
      		  echo '<input type="button" value="上頁" class="button" style="FONT-SIZE:12px;" ' . $p2 . '>';
      			echo '<input type="button" value="下頁" class="button" style="FONT-SIZE:12px;" ' . $p3 . '>';
      			echo '<input type="button" value="末頁" class="button" style="FONT-SIZE:12px;" ' . $p4 . '>';
            ?>
          </td>
        </tr>
				<tr>
				  <td width="50" align="center" bgcolor="#E9EEF4">選取</td>
					<td width="130" align="center" bgcolor="#E9EEF4">銀行代碼</td>
					<td align="center" bgcolor="#E9EEF4">銀行名稱</td>
        </tr>
        <?php
        foreach($list as $fields){
          echo "<tr>";
          echo '<td align="center" bgcolor="#ffffff">';
          $value = $fields['item_id'] . "::" . $fields['name'];
          echo '<input id="Arrsel" name="Arrsel" type="radio" value="' . $value . '" onclick="popupOK(this)">';
          //echo '<input id="Arrsel" name="Arrsel" type="checkbox" value="' . $fields['item_id'] . '">';
          //echo '<input id="Arrcap" name="Arrcap" type="hidden" value="' . $fields['name'] . '">';
          echo '</td>';
          echo '<td align="center" bgcolor="#ffffff">' . $fields['item_id'] . '</td>';
          echo '<td align="left" bgcolor="#ffffff">' . $fields['name'] . '</td>';
          echo "</tr>";
        }
        ?>
        <tr>
          <td colspan="3" align="center" height="3"></td>
        </tr>
				<tr>
					<td colspan="3" align="center" bgcolor="#dcdcdc">
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