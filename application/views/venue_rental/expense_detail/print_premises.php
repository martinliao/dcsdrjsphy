
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>場地使用費用明細表</title>
	<link type="text/css" rel="stylesheet" href="css/master.css"/>
</head>
<body>

<table width="100%">
  <tr>
    <td align="center">
      <font face="標楷體" size="5">
      <?php
        echo $applicant['APP_NAME'] . "場地使用費明細表";
      ?>
      </font>
    </td>
  </tr>
  <tr height="30">
    <td align="right">
      <font face="標楷體" size="3">
      <?php
        if ($applicant['APP_SDATE']!="" && $applicant['APP_EDATE']!=""){
          echo $applicant['APP_SDATE'] ."-" . $applicant['APP_EDATE'];
        }
      ?>
      </font>
    </td>
  </tr>
</table>

<table width="100%">
  <tr>
    <td align="center"><font face="標楷體" size="4">項目</font></td>
    <td align="center"><font face="標楷體" size="4">起日</font></td>
		<td align="center"><font face="標楷體" size="4">迄日</font></td>
		<td align="center"><font face="標楷體" size="4">時段</font></td>
		<td align="right"><font face="標楷體" size="4">單價</font></td>
		<td align="right"><font face="標楷體" size="4">數量</font></td>
		<td align="right"><font face="標楷體" size="4">金額</font></td>
		<td align="center"><font face="標楷體" size="4">備註</font></td>
  </tr>
	<tr>
		<td colspan="8"><hr></td>
	</tr>
	<?php
	$catChk = "";
  $amt = 0;
  $amtS = 0;
  $amtAll = 0;
	for ($i=0;$i<count($room_list);$i++) {

    $amt = $room_list[$i]['price_a'] + $room_list[$i]['price_b'] + $room_list[$i]['price_c'];


    if ($catChk==""){
      $catChk = $room_list[$i]['cat_id'];
    }
    if ($catChk!=$room_list[$i]['cat_id'])
    {
      echo '<tr height="40">';
      echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>小計：</b></td>';
      echo '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amtS) . '</font></td>';
      echo '<td align="right"><font face="標楷體" size="3"></font></td>';
      echo '</tr>';
      $catChk = $room_list[$i]['cat_id'];
      $amtS = 0;
    }
    if ($room_list[$i]['cat_id']=='04')
    {
    	$amtS = $amtS + $room_list[$i]['num']*$amt;
    	$amtAll = $amtAll + $room_list[$i]['num']*$amt;
	}
	else
	{
			$amtS = $amtS + $room_list[$i]['EXPENSE'];
			$amtAll = $amtAll + $room_list[$i]['EXPENSE'];
	}
    echo '<tr>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['ROOM_NAME'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_S'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_E'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['USE_NAME'] . '</font></td>';

	// echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['EXPENSE']/($room_list[$i]['num']*$room_list[$i]['date_num'])) . '</font></td>';
  //echo '<td align="right"><font face="標楷體" size="3">' . number_format($amt) . '</font></td>';
    echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['EXPENSE']/($room_list[$i]['num']*$room_list[$i]['date_num'])) . '</font></td>';
    if ($room_list[$i]['cat_id']=='04')
    {
	    echo '<td align="right"><font face="標楷體" size="3">' . $room_list[$i]['num'] . '</font></td>';
	    echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['num']*$amt) . '</font></td>';
  	}
  	else
  	{
	  	echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['num']*$room_list[$i]['date_num']) . '</font></td>';
		echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['EXPENSE']) . '</font></td>';
    }
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['groupnote'] . '</font></td>';
    echo '</tr>';
	}
  echo '<tr height="40">';
  echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>小計：</b></td>';
  echo '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amtS) . '</font></td>';
  echo '<td align="right"><font face="標楷體" size="3"></font></td>';
  echo '</tr>';
  ?>
	<tr>
		<td colspan="8"><hr></td>
	</tr>
	<?php
  echo '<tr height="40">';
  echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>合計：</b></td>';
  echo '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amtAll) . '</font></td>';
  echo '<td align="right"><font face="標楷體" size="3"></font></td>';
  echo '</tr>';

  echo '<tr height="40">';
  echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>代辦事項金額：</b>' . $applicant['memo'] . '</td>';
  echo '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($applicant['other_expense']) . '</font></td>';
  echo '<td align="center"><font face="標楷體" size="3"></font></td>';
  echo '</tr>';
	$amtAll = $amtAll + $applicant['other_expense'];
  echo '<tr height="40">';
  echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>金額總計：</b></td>';
  echo '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amtAll) . '</font></td>';
  echo '<td align="center"><font face="標楷體" size="3"></font></td>';
  echo '</tr>';

  ?>
</table>


</body>
</html>

<script>
window.print();
</script>
