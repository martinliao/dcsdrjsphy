
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>選取職稱</title>
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
  echo "var cur_page = " . $filter['bureau_page'] . ";";
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
  obj.bureau_page.value = tmp;
  obj.submit();
}
function popupOK(x){

  window.opener.document.getElementById("tmp_tid").value = x.value;
  window.opener.seltitleOK();
  window.close();
}
</script>

<form id="actQuery" role="form">
<div id="list1">
請輸入關鍵字
<input type="text" name="bureau_q" id="bureau_q" value="<?=$filter['bureau_q'];?>">

<input type="button" value="查詢" class="button" style="FONT-SIZE:12px;" onclick="doQuery()">
<?php
echo '<input type="hidden" name="bureau_page" id="bureau_page" value="' . $filter['bureau_page'] . '">';
?>
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#909397">

      <table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td colspan="4" bgcolor="#dcdcdc">
      		  <span style="font:0.8em Verdana, Arial, Helvetica, sans-serif;">
      		  <?php
      		  echo "頁次:" . $filter['bureau_page'] . "/" . $total_page;
            ?>
            </span>
      		  <?php
            $p1 = (($filter['bureau_page']==1) || ($filter['bureau_page']==0)) ? "disabled" : 'onclick="page(-1)"';
            $p2 = (($filter['bureau_page']==1) || ($filter['bureau_page']==0)) ? "disabled" : 'onclick="page(-2)"';
            $p3 = (($filter['bureau_page']==$total_page) || ($filter['bureau_page']==0)) ? "disabled" : 'onclick="page(-3)"';
            $p4 = (($filter['bureau_page']==$total_page) || ($filter['bureau_page']==0)) ? "disabled" : 'onclick="page(-4)"';
      		  echo '<input type="button" value="首頁" class="button" style="FONT-SIZE:12px;" ' . $p1 . '>';
      		  echo '<input type="button" value="上頁" class="button" style="FONT-SIZE:12px;" ' . $p2 . '>';
      			echo '<input type="button" value="下頁" class="button" style="FONT-SIZE:12px;" ' . $p3 . '>';
      			echo '<input type="button" value="末頁" class="button" style="FONT-SIZE:12px;" ' . $p4 . '>';
            ?>
          </td>
        </tr>
				<tr>
				  <td width="50" align="center" bgcolor="#E9EEF4">選取</td>
					<td width="130" align="center" bgcolor="#E9EEF4">職稱代碼</td>
					<td align="center" bgcolor="#E9EEF4">職稱名稱</td>
        </tr>
        <?php
        foreach($title_list as $fields){
          echo "<tr>";
          echo '<td align="center" bgcolor="#ffffff">';
          $value = $fields['item_id'] . "::" . $fields['name'];
          echo '<input id="Arrsel" name="Arrsel" type="radio" value="' . $value . '" onclick="popupOK(this)">';
          echo '</td>';
          echo '<td align="center" bgcolor="#ffffff">' . $fields['item_id'] . '</td>';
          echo '<td align="left" bgcolor="#ffffff">' . $fields['name'] . '</td>';
          echo "</tr>";
        }
        ?>
        <tr>
          <td colspan="4" align="center" height="3"></td>
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
