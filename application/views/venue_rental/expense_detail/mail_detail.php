<?php
// include "init.inc.php";
// include "common.php";
// //$db->debug = true;
// sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','05');

// //接值
// //------------------------------------------------------------------------------------
// $seqno = $_REQUEST['seqno'];
// //------------------------------------------------------------------------------------

// //查詢1
// //------------------------------------------------------------------------------------
// $sql = "SELECT B.EMAIL, B.APP_NAME, A.APP_DATE, C.APP_SDATE, C.APP_EDATE, A.APP_REASON, A.memo, A.other_expense, A.TOTAL_EXPENSE FROM APPINFO A " .
//        "LEFT JOIN APPLICANT B ON A.APP_ID = B.APP_ID " .
//        "LEFT JOIN (SELECT to_char(MIN(USE_DATE),'yyyy/mm/dd') AS APP_SDATE, to_char(MAX(USE_DATE),'yyyy/mm/dd') AS APP_EDATE, APPI_ID FROM ROOM_USE WHERE APPI_ID IS NOT NULL GROUP BY APPI_ID) C ON A.APPI_ID = C.APPI_ID " .
//        "WHERE A.APPI_ID = '{$seqno}'";
// $rs1 = db_excute($sql);
// $applicant = $rs1->FetchRow();
// //------------------------------------------------------------------------------------

// //查詢2
// //------------------------------------------------------------------------------------
// $sql = "SELECT A.*, B2.IS_PUBLIC, C.COUNTBY, C.NAME AS ROOM_NAME, D.DESCRIPTION AS CAT_NAME, E.memo AS USE_NAME, F.PRICE1, F.PRICE2, F.PRICE3 FROM " .
//        "( " .
//        "SELECT to_char(MIN(USE_DATE),'yyyy/mm/dd') AS APP_DATE_S, to_char(MAX(USE_DATE),'yyyy/mm/dd') AS APP_DATE_E, APPI_ID, CAT_ID, ROOM_ID, USE_PERIOD, UNIT, NUM, " .
//        "SUM(EXPENSE) AS EXPENSE, DISCOUNT, GROUPNUM, GROUPNOTE, SUM(WEEKEND) AS WEEKEND " .
//        "FROM (SELECT a.*, CASE WHEN TO_CHAR(USE_DATE,'D') IN ('1','7') THEN 1 END AS WEEKEND FROM ROOM_USE a) " .
//        "WHERE APPI_ID = '{$seqno}' GROUP BY " .
//        "APPI_ID, CAT_ID, ROOM_ID, USE_PERIOD, UNIT, NUM, DISCOUNT, GROUPNUM, GROUPNOTE " .
//        ") A " .
//        "LEFT JOIN APPINFO B1 ON A.APPI_ID = B1.APPI_ID " .
//        "LEFT JOIN APPLICANT B2 ON B1.APP_ID = B2.APP_ID " .
//        "LEFT JOIN CLASSROOM C ON A.ROOM_ID = C.ROOM_ID " .
//        "LEFT JOIN CODE_TABLE D ON C.CAT_ID = D.ITEM_ID AND D.TYPE_ID = '20' " .
//        "LEFT JOIN CODE_TABLE E ON A.USE_PERIOD = E.ITEM_ID AND E.TYPE_ID = '31' " .
//        "LEFT JOIN CLASSROOM_TIMEPRICE F ON A.ROOM_ID = F.ROOM_ID AND A.USE_PERIOD = F.USETIME " .
//        "ORDER BY CAT_ID, ROOM_ID, APP_DATE_S, USE_PERIOD";
// $rs2 = db_excute($sql);
//------------------------------------------------------------------------------------

//內容
//------------------------------------------------------------------------------------
$body = '';
$body .= '<table width="100%">';
$body .= '  <tr>';
$body .= '    <td align="center">';
$body .= '      <font face="標楷體" size="5">'.$applicant['APP_NAME'].'場地使用費明細表</font>';
$body .= '    </td>';
$body .= '  </tr>';
$body .= '  <tr height="30">';
$body .= '    <td align="right">';
$body .= '      <font face="標楷體" size="3">'.$applicant['APP_SDATE'].'-'.$applicant['APP_EDATE'].'</font>';
$body .= '    </td>';
$body .= '  </tr>';
$body .= '</table>';
$body .= '<table width="100%">';
$body .= '  <tr>';
$body .= '    <td align="center"><font face="標楷體" size="4">項目</font></td>';
$body .= '    <td align="center"><font face="標楷體" size="4">起日</font></td>';
$body .= '		<td align="center"><font face="標楷體" size="4">迄日</font></td>';
$body .= '		<td align="center"><font face="標楷體" size="4">時段</font></td>';
$body .= '		<td align="right"><font face="標楷體" size="4">單價</font></td>';
$body .= '		<td align="right"><font face="標楷體" size="4">數量</font></td>';
$body .= '		<td align="right"><font face="標楷體" size="4">金額</font></td>';
$body .= '		<td align="center"><font face="標楷體" size="4">備註</font></td>';
$body .= '  </tr>';
$body .= '	<tr>';
$body .= '		<td colspan="8"><hr></td>';
$body .= '	</tr>';

	$catChk = "";
  $amt = 0;
  $amtS = 0;
  $amtAll = 0;
	for ($i=0;$i<count($room_list);$i++) {

    $amt = $room_list[$i]['price_a'] + $room_list[$i]['price_b'] + $room_list[$i]['price_c'];
    $amtAll = $amtAll + $room_list[$i]['EXPENSE'];

    if ($catChk==""){
      $catChk = $room_list[$i]['cat_id'];
    }
    if ($catChk!=$room_list[$i]['cat_id'])
    {
      $body .= '<tr height="40">';
      $body .= '<td align="left" colspan="4"><font face="標楷體" size="3"><b>小計：</b></td>';
      $body .= '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amtS) . '</font></td>';
      $body .= '<td align="right"><font face="標楷體" size="3"></font></td>';
      $body .= '</tr>';
      $catChk = $room_list[$i]['cat_id'];
      $amtS = 0;
    }
    $amtS = $amtS + $room_list[$i]['EXPENSE'];

    $body .= '<tr>';
    $body .= '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['ROOM_NAME'] . '</font></td>';
    $body .= '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_S'] . '</font></td>';
    $body .= '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['APP_DATE_E'] . '</font></td>';
    $body .= '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['USE_NAME'] . '</font></td>';
    $body .= '<td align="right"><font face="標楷體" size="3">' . number_format($amt) . '</font></td>';
    $body .= '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['date_num']) . '</font></td>';
    $body .= '<td align="right"><font face="標楷體" size="3">' . number_format($room_list[$i]['EXPENSE']) . '</font></td>';
    $body .= '<td align="center"><font face="標楷體" size="3">' . $room_list[$i]['groupnote'] . '</font></td>';
    $body .= '</tr>';
	}
  $body .= '<tr height="40">';
  $body .= '<td align="left" colspan="4"><font face="標楷體" size="3"><b>小計：</b></td>';
  $body .= '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amtS) . '</font></td>';
  $body .= '<td align="right"><font face="標楷體" size="3"></font></td>';
  $body .= '</tr>';

	$body .= '<tr>';
	$body .= '<td colspan="8"><hr></td>';
	$body .= '</tr>';

  $body .= '<tr height="40">';
  $body .= '<td align="left" colspan="4"><font face="標楷體" size="3"><b>合計：</b></td>';
  $body .= '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($amtAll) . '</font></td>';
  $body .= '<td align="right"><font face="標楷體" size="3"></font></td>';
  $body .= '</tr>';

  $body .= '<tr height="40">';
  $body .= '<td align="left" colspan="4"><font face="標楷體" size="3"><b>代辦事項金額：</b>' . $applicant['memo'] . '</td>';
  $body .= '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($applicant['other_expense']) . '</font></td>';
  $body .= '<td align="center"><font face="標楷體" size="3"></font></td>';
  $body .= '</tr>';

  $body .= '<tr height="40">';
  $body .= '<td align="left" colspan="4"><font face="標楷體" size="3"><b>金額總計：</b></td>';
  $body .= '<td align="right" colspan="3"><font face="標楷體" size="3">' . number_format($applicant['total_expense']) . '</font></td>';
  $body .= '<td align="center"><font face="標楷體" size="3"></font></td>';
  $body .= '</tr>';

  $body .= '</table>';
//------------------------------------------------------------------------------------
jd($body);
//Mail
//------------------------------------------------------------------------------------
//$body = mb_convert_encoding(addslashes("123"),"BIG5","UTF-8");
$title = "場地使用費用明細表";
//$emailaddress = "jayni@cybersoft4u.com";
$emailaddress = $applicant['EMAIL'];
// $cc = "";
// $mail_flag = send_mail(mb_convert_encoding(addslashes($body),"BIG5","UTF-8"), $title, "", $emailaddress, $cc, "", "1",'');
//------------------------------------------------------------------------------------
?>

<!-- <script>
alert("發送完成")
window.close();
</script> -->