
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>申請表</title>
	<link type="text/css" rel="stylesheet" href="css/master.css"/>
</head>
<body>

<table width="100%">
  <tr>
    <td align="center">
      <font face="標楷體" size="5">臺北市政府公務人員訓練處場地使用費申請表</font>
    </td>
  </tr>
</table>

<br>
<table>
  <tr height="30">
    <td align="left"><font face="標楷體" size="3">申請編號：</font></td>
    <td align="left" width="200">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['appi_id'];
      ?>
      </font>
    </td>
		<td align="left"><font face="標楷體" size="3">申請日期：</font></td>
		<td align="left">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['cre_date'];
      ?>
      </font>
    </td>
  </tr>
  <tr height="30">
    <td align="left"><font face="標楷體" size="3">單位名稱：</font></td>
    <td align="left">
      <font face="標楷體" colspan="3" size="3">
      <?php
        echo $applicant['APP_NAME'];
      ?>
      </font>
    </td>
  </tr>
</table>
<table>
  <tr height="28">
    <td align="left"><font face="標楷體" size="3">活動名稱暨內容說明：</font></td>
    <td align="left">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['app_reason'];
      ?>
      </font>
    </td>
  </tr>
</table>

<br>
<table>
  <tr height="30">
    <td align="left"><font face="標楷體" size="3">聯絡人姓名：</font></td>
    <td align="left" width="200">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['CONTACT_NAME'];
      ?>
      </font>
    </td>
		<td align="left"><font face="標楷體" size="3">聯絡電話：</font></td>
		<td align="left">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['TEL'];
      ?>
      </font>
    </td>
  </tr>
  <tr height="30">
    <td align="left"><font face="標楷體" size="3">傳真號碼：</font></td>
    <td align="left">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['FAX'];
      ?>
      </font>
    </td>
		<td align="left"><font face="標楷體" size="3">電子信箱：</font></td>
		<td align="left">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['EMAIL'];
      ?>
      </font>
    </td>
  </tr>
  <tr height="30">
    <td align="left"><font face="標楷體" size="3">通訊地址：</font></td>
    <td align="left">
      <font face="標楷體" colspan="3" size="3">
      <?php
        echo $applicant['ZONE'] . $applicant['ADDR'];
      ?>
      </font>
    </td>
  </tr>
</table>
<table>
  <tr height="28">
    <td align="left"><font face="標楷體" size="3">其它代辦事項：</font></td>
    <td align="left">
      <font face="標楷體" size="3">
      <?php
        echo $applicant['memo'];
      ?>
      </font>
    </td>
  </tr>
</table>

<br>
<table width="100%">
  <tr>
    <td align="center"><font face="標楷體" size="3"><u>項目</u></font></td>
    <td align="center"><font face="標楷體" size="3"><u>使用起日</u></font></td>
		<td align="center"><font face="標楷體" size="3"><u>使用迄日</u></font></td>
		<td align="center"><font face="標楷體" size="3"><u>租借類別</u></font></td>
		<td align="center"><font face="標楷體" size="3"><u>使用場地</u></font></td>
		<td align="center"><font face="標楷體" size="3"><u>數量</u></font></td>
		<td align="center"><font face="標楷體" size="3"><u>單位</u></font></td>
		<td align="center"><font face="標楷體" size="3"><u>使用時段</u></font></td>
		<td align="center"><font face="標楷體" size="3"><u>週六日天數</u></font></td>
		<td align="right"><font face="標楷體" size="3"><u>預估金額</u></font></td>
  </tr>
	<?php
  $sno = 1;
	$amt =0;
	for ($i=0;$i<count($room_list);$i++) {
    echo '<tr>';
    echo '<td align="right"><font face="標楷體" size="3">' . $sno . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_S'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_E'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['CAT_NAME'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['ROOM_NAME'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['num'] . '</font></td>';

    if ($room_list[$i]['COUNTBY']=="1"){
      $COUNT_NAME = "人";
    }
    if ($room_list[$i]['COUNTBY']=="2"){
      $COUNT_NAME = "桌";
    }
    if ($room_list[$i]['COUNTBY']=="3"){
      $COUNT_NAME = "場地";
    }
    echo '<td align="center"><font face="標楷體" size="3">' . $COUNT_NAME . '</font></td>';

    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['USE_NAME'] . '</font></td>';
    echo '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['WEEKEND'] . '</font></td>';

    if ($room_list[$i]['cat_id']=='04')
    {
      echo '<td align="right"><font face="標楷體" size="3">' . number_format(($room_list[$i]['price_a']+$room_list[$i]['price_b']+$room_list[$i]['price_c'])*$room_list[$i]['NUM']) . '</font></td>';
      $amt = $amt + ($room_list[$i]['price_a']+$room_list[$i]['price_b']+$room_list[$i]['price_c'])*$room_list[$i]['num'];
    }
    else
    {
      echo '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['EXPENSE']) . '</font></td>';
      $amt = $amt + $room_list[$i]['EXPENSE'];
    }
    echo '</tr>';
    $sno = $sno + 1;
	}
  ?>
</table>

<br>
<table>
  <tr>
    <td align="left"><font face="標楷體" size="3">註：</font></td>
    <td align="left"><font face="標楷體" size="3">若使用類別為「場地」，則使用時段A表上午8時至12時；B表下午13時至17時；C表夜間18時至22時‧</font></td>
  </tr>
</table>

<br>
<table>
  <tr>
    <td align="left"><font face="標楷體" size="3">代辦事項金額：</font></td>
    <td align="left">
      <font face="標楷體" size="3">
      <?php
        echo number_format($applicant['other_expense']) . "元整";
      ?>
      </font>
    </td>
  </tr>
  <tr>
    <td align="left"><font face="標楷體" size="3">預估金額總計：</font></td>
    <td align="left">
      <font face="標楷體" size="3">
      <?php
        echo number_format($amt+$applicant['other_expense']) . "元整";
      ?>
      </font>
    </td>
  </tr>
</table>

</body>
</html>

<script>
window.print();
</script>
