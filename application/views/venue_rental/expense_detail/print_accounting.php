
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>會計明細表</title>
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
		<td align="right"><font face="標楷體" size="4">場地費</font></td>
		<td align="right"><font face="標楷體" size="4">服務費</font></td>
		<td align="right"><font face="標楷體" size="4">伙食費</font></td>
		<td align="right"><font face="標楷體" size="4">數量</font></td>
		<td align="center"><font face="標楷體" size="4">備註</font></td>
  </tr>
	<tr>
		<td colspan="9"><hr></td>
	</tr>
	<?php
  $amt1 = 0;
  $amt2 = 0;
  $amt3 = 0;
	for ($i=0;$i<count($room_list);$i++) {
    echo '<tr>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['ROOM_NAME'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_S'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_E'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['USE_NAME'] . '</font></td>';

	echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['price_a_sum']) . '</font></td>';
	echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['price_b_sum']) . '</font></td>';

    echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['price_c_sum']) . '</font></td>';

    if ($room_list[$i]['cat_id']=='04')
    {
	    echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['date_num']) . '</font></td>';
	    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['groupnote'] . '</font></td>';
    	echo '</tr>';
	    $amt1 = $amt1 + ($room_list[$i]['price_a_sum'] );
	    $amt2 = $amt2 + ($room_list[$i]['price_b_sum'] );
	    $amt3 = $amt3 + ($room_list[$i]['price_c_sum'] );
	}
	else if ($room_list[$i]['cat_id']=='01' || $room_list[$i]['cat_id']=='06')
	{
	  	echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['num']*$room_list[$i]['date_num']) . '</font></td>';
	  	echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['groupnote'] . '</font></td>';
    	echo '</tr>';
		$amt1 = $amt1 + ($room_list[$i]['price_a_sum'] );
	    $amt2 = $amt2 + ($room_list[$i]['price_b_sum'] );
	    $amt3 = $amt3 + ($room_list[$i]['price_c_sum'] );
	}
	else
	{
	  	echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['num']*$room_list[$i]['date_num']) . '</font></td>';
	  	echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['groupnote'] . '</font></td>';
    	echo '</tr>';
	  	$amt1 = $amt1 + ($room_list[$i]['price_a_sum'] );
	    $amt2 = $amt2 + ($room_list[$i]['price_b_sum'] );
	    $amt3 = $amt3 + ($room_list[$i]['price_c_sum'] );
	}
  }
  ?>
	<tr>
		<td colspan="9"><hr></td>
	</tr>
	<?php
  echo '<tr height="40">';
  echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>合計：</b></td>';
  echo '<td align="right"><font face="標楷體" size="3">' . number_format($amt1) . '</font></td>';
  echo '<td align="right"><font face="標楷體" size="3">' . number_format($amt2) . '</font></td>';
  echo '<td align="right"><font face="標楷體" size="3">' . number_format($amt3) . '</font></td>';
  echo '<td align="right"><font face="標楷體" size="3"></font></td>';
  echo '<td align="center"><font face="標楷體" size="3"></font></td>';
  echo '</tr>';

  echo '<tr height="40">';
  echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>代辦事項金額：</b>' . $applicant['memo'] . '</td>';
  echo '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($applicant['other_expense']) . '</font></td>';
  echo '<td align="right"><font face="標楷體" size="3"></font></td>';
  echo '<td align="center"><font face="標楷體" size="3"></font></td>';
  echo '</tr>';

  echo '<tr height="40">';
  echo '<td align="left" colspan="4"><font face="標楷體" size="3"><b>金額總計：</b></td>';
  echo '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amt1+$amt2+$amt3+$applicant['other_expense']) . '</font></td>';
  echo '<td align="right"><font face="標楷體" size="3"></font></td>';
  echo '<td align="center"><font face="標楷體" size="3"></font></td>';
  echo '</tr>';

  ?>
</table>


</body>
</html>

<script>
window.print();
</script>
