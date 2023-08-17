
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>案件統計表</title>
	<link type="text/css" rel="stylesheet" />
</head>
<body>

<table width="100%">
  <tr>
    <td align="center">
      <font face="標楷體" size="5">臺北市政府公務人員訓練處場地使用情形明細表</font>
    </td>
  </tr>
</table>

<table width="100%">
	<tr>
		<td colspan="9"><hr></td>
	</tr>
  <tr>
    <td align="center"><font face="標楷體" size="3">場次</font></td>
    <td align="center"><font face="標楷體" size="3">單位及活動</font></td>
		<td align="right"><font face="標楷體" size="3">場地費</font></td>
		<td align="right"><font face="標楷體" size="3">服務費</font></td>
		<td align="right"><font face="標楷體" size="3">伙食費</font></td>
		<td align="right"><font face="標楷體" size="3">其它</font></td>
		<td align="right"><font face="標楷體" size="3">合計</font></td>
		<td align="right"><font face="標楷體" size="3">人天數</font></td>
		<td align="center"><font face="標楷體" size="3">收據號碼</font></td>
  </tr>
	<tr>
		<td colspan="9"><hr></td>
	</tr>
	<?php
	$sno = 1;
	for ($i=0;$i<count($list);$i++) {
    echo '<tr>';
    echo '<td align="left" valign="top"><font face="標楷體" size="3">' . $sno . '</font></td>';
    echo '<td align="left" valign="top"><font face="標楷體" size="3">';
    echo $list[$i]['APP_SDATE'] . "~" . $list[$i]['APP_EDATE'] . "<br>";
    echo $list[$i]['APP_NAME'] . "<br>";
    echo $list[$i]['APP_REASON'] . "<br>";
    echo $list[$i]['CONTACT_NAME'] . "  " . $list[$i]['TEL'] . "<br>";
    echo '</font></td>';
    echo '<td align="right" valign="top"><font face="標楷體" size="3">' . number_format($list[$i]['price_a']) . '</font></td>';
    echo '<td align="right" valign="top"><font face="標楷體" size="3">' . number_format($list[$i]['price_b']) . '</font></td>';
    echo '<td align="right" valign="top"><font face="標楷體" size="3">' . number_format($list[$i]['price_c']) . '</font></td>';
    echo '<td align="right" valign="top"><font face="標楷體" size="3">' . number_format($list[$i]['OTHER_EXPENSE']) . '</font></td>';
    echo '<td align="right" valign="top"><font face="標楷體" size="3">' . number_format($list[$i]['TOTAL_EXPENSE']) . '</font></td>';
    echo '<td align="right" valign="top"><font face="標楷體" size="3">' . number_format($list[$i]['PDS']) . '</font></td>';
    echo '<td align="center" valign="top"><font face="標楷體" size="3">' . $list[$i]['BILLNO'] . '</font></td>';
    echo '</tr>';
	  echo '<tr>';
		echo '<td colspan="9"><hr></td>';
	  echo '</tr>';
    $sno = $sno + 1;
	}
  ?>
</table>

</body>
</html>

<script>
window.print();
</script>
