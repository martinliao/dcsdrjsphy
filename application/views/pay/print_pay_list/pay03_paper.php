<?php
//include "init.inc.php";
//$db->debug = true;
//if($_SERVER['HTTP_REFERER'] != "http://{$_SERVER['HTTP_HOST']}/pay03.php")
//die(1);
function QueryToArray($query){
  $arrAll = array();
if($query->num_rows() > 0)
{
foreach($query->result_array() as $row)
{
  array_push($arrAll,$row);
}
}
return $arrAll;
}
function getClassDate1($year, $class_no, $term){
	global $db;
	$rs = db_excute("select * from REQUIRE where YEAR = '{$year}' and CLASS_NO = '{$class_no}' and TERM = '{$term}'");
	if($rs){
		$row = $rs->FetchRow();
		return $row['START_DATE1'] . ' ~ ' . $row['END_DATE1'];
	}
	else return '';
}

function HT_class_type($year, $class_no, $term){
	global $db;
	$item_id = $db->GetOne("select HT_CLASS_TYPE from REQUIRE where YEAR = '{$year}' and CLASS_NO = '{$class_no}' and TERM = '{$term}'");
	$cname = $db->GetOne("select DESCRIPTION from CODE_TABLE where TYPE_ID = '07' and ITEM_ID = '{$item_id}'");
	return $cname;
}

//查詢
//------------------------------------------------------------------------------------
$d1 = trim($sess_start_date);
$d2 = trim($sess_end_date);

$mtList   = trim($mtList);
$year     = '';
$class_no = '';
$term     = '';
$paper_app_seq = trim($paper_app_seq);
$is_status_ok = trim($is_status_ok);

$where1 = "and use_date between date('{$d1}') and date('{$d2}')";
if ($year!="" && $class_no!="" && $term!="" && $mtList ==''){ // custom by chiahua補上mtList == ''
  $where2 = "and year = '{$year}' and class_no = '{$class_no}' and term = '{$term}'";
}
else{
  $where2 = "";
  $arry = explode(",,",$mtList);
  for ($x=0;$x<count($arry);$x++){
    if ($arry[$x]!="")
    {
      $arryValue = explode("::",$arry[$x]);
      if ($where2==""){
        $where2 .= "select '{$arryValue[0]}','{$arryValue[1]}','{$arryValue[2]}' from dual ";
      }
      else{
        $where2 .= "union all select '{$arryValue[0]}','{$arryValue[1]}','{$arryValue[2]}' from dual ";
      }
    }
  }
  $where2 = "and (year, class_no, term) in ({$where2})";
}

$sql = "select year, class_no, class_name, term from hour_traffic_tax " .
       "where 1=1 {$where1} {$where2} " .
       "group by year, class_no, class_name, term order by year, class_no, term";
$rs = $this->db->query($sql);
$rs = QueryToArray($rs);
$yearStr='';
$outputHTML='';
$total_subtotal = 0;
for ($i=0; $i < sizeof($rs); $i++) {
  $fields=$rs[$i]; 
  //#47552 實體系統-13A及13B已產生流水號的請款清冊、憑證資料沒有by流水號：增加請款流水號的判斷
  if($paper_app_seq){
    $sql = "select * from hour_traffic_tax a where seq in (select seq from hour_app where app_seq = '{$paper_app_seq}') and year = '{$fields['year']}' and class_no = '{$fields['class_no']}' and term = '{$fields['term']}' and status != '已設定為不請款' ";
    $sql1 = "select  year(use_date) ystr from (select min(use_date) use_date from hour_traffic_tax a where seq in (select seq from hour_app where app_seq = '{$paper_app_seq}') and year = '{$fields['year']}' and class_no = '{$fields['class_no']}' and term = '{$fields['term']}' and status != '已設定為不請款' )b";
  }
  else {
  $sql = "select * from hour_traffic_tax a where status = '待確認' " .
        "and year = '{$fields['year']}' and class_no = '{$fields['class_no']}' and term = '{$fields['term']}' " .
        "{$where1}  order  by USE_DATE ";
  $sql1 = "select  year(use_date) ystr from (select min(use_date) use_date from hour_traffic_tax a where status = '待確認' " .
        "and year = '{$fields['year']}' and class_no = '{$fields['class_no']}' and term = '{$fields['term']}' " .
        "{$where1} )";
  } 		 
  //echo "sql1:".$sql1;
  $rs3 = $this->db->query($sql1);
  $rs3 = QueryToArray($rs3);
  if ($rs3)
  {
  $ydata = $rs3[0];
  $yearStr = $ydata['ystr']-1911;
  }	
  else {
  $yearStr = "";
  }

  $rs2 = $this->db->query($sql);
  $rs2 = QueryToArray($rs2);
  for ($i=0; $i < sizeof($rs2); $i++) {
    $data=$rs2[$i]; 
    $total_subtotal =$total_subtotal + $data['subtotal'];
  }
}

$momey = str_pad($total_subtotal, 9 , '*', STR_PAD_LEFT);
//------------------------------------------------------------------------------------

echo $outputHTML .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> </title>
<style tyle="text/css">
body
 {
	font-family:標楷體;
	font-size:14px;
 }
</style>
</head>
<body>
<table width="700" border="0" align="center"><tr><td height="50" align="center" >
<h1><span style="border-bottom: 3px double black">臺北市政府公務人員訓練處</span></h1></td></tr></table>
<table width="700" align="center" border="0" cellpadding="0" cellspacing="0" bordercolor="#990000">
  <tr>
    <td>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="86%">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
			<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
              <tr>
                <td height="40" align="center" nowrap>傳票編號</td>
                <td style="width:45px;">&nbsp;</td>
              </tr>
              <tr>
                <td height="40" align="center" nowrap>付款憑單<br />
                  編　　號</td>
                <td style="width:45px;">&nbsp;</td>
              </tr>
            </table></td>
            <td align="center">
              <h1><u>黏 貼 憑 證 用 紙</u></h1></td>
          </tr>
        </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
                <tr>
                  <td rowspan="3" align="center" style="width:70px;">憑　證<br /><br />
                    編　號</td>
                  <td height="26" align="center" style="width:85px;">預算年度</td>
                  <td align="center" style="width:80px;">'.$yearStr.'</td>
                  <td colspan="9" align="center">金　　　　額</td>
                  <td align="center">用　途　說　明</td>
                </tr>
                <tr>
                  <td height="26" align="center">預算</td>
                  <td align="center">科　　目</td>
                  <td width="4%" rowspan="2" align="center">億</td>
                  <td width="4%" rowspan="2" align="center">千<br />
                    萬</td>
                  <td width="4%" rowspan="2" align="center">百<br />
                    萬</td>
                  <td width="4%" rowspan="2" align="center">十<br />
                    萬</td>
                  <td width="4%" rowspan="2" align="center">萬</td>
                  <td width="4%" rowspan="2" align="center">千</td>
                  <td width="4%" rowspan="2" align="center">百</td>
                  <td width="4%" rowspan="2" align="center">十</td>
                  <td width="4%" rowspan="2" align="center">元</td>
                  <td rowspan="3" valign="top">鐘點費及交通費<br><'.$paper_app_seq.'></td>
                </tr>

                <tr>
                  <td height="28" align="center" style="width:70px;">工作計畫</td>
                  <td align="center">用　途　別</td>
                  </tr>
                <tr align="center">
                  <td height="100">&nbsp;</td>
                  <td align="center">在職訓練</td>
                  <td align="center">業務費</td>
                  <td>'.($momey[0] == '*' ? '&nbsp;' : $momey[0]).'</td>
                  <td>'.($momey[1] == '*' ? '&nbsp;' : $momey[1]).'</td>
                  <td>'.($momey[2] == '*' ? '&nbsp;' : $momey[2]).'</td>
                  <td>'.($momey[3] == '*' ? '&nbsp;' : $momey[3]).'</td>
                  <td>'.($momey[4] == '*' ? '&nbsp;' : $momey[4]).'</td>
                  <td>'.($momey[5] == '*' ? '&nbsp;' : $momey[5]).'</td>
                  <td>'.($momey[6] == '*' ? '&nbsp;' : $momey[6]).'</td>
                  <td>'.($momey[7] == '*' ? '&nbsp;' : $momey[7]).'</td>
                  <td>'.($momey[8] == '*' ? '&nbsp;' : $momey[8]).'</td>
                  </tr>
              </table></td>
            </tr>
          </table></td>
        <td width="14%" align="right" valign="bottom"><table width="95%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
          <tr>
            <td height="30" align="center">附件</td>
          </tr>
          <tr height=180>
           <td align="left">
		   <font style="font-size:14px;">
		    發　票&nbsp;&nbsp;&nbsp;&nbsp;張<br />
            收　據&nbsp;&nbsp;&nbsp;&nbsp;張<br />
            請購單&nbsp;&nbsp;&nbsp;&nbsp;張<br />
            請修單&nbsp;&nbsp;&nbsp;&nbsp;張<br />
            驗收報告&nbsp;&nbsp;張<br />
            合約書&nbsp;&nbsp;&nbsp;&nbsp;份<br />
            其他文件&nbsp;&nbsp;張<br />
			</font>
			<font style="font-size:10px;"> (需註明文件名稱)</font>
              </td>
          </tr>
        </table></td>
      </tr>
    </table>
      <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
        <tr>
          <td width="25%" height="45" align="center">經　辦　單　位</td>
          <td width="25%" align="center">申請、使用單位<br />
            （驗收或證明、保管）</td>
          <td width="25%" align="center">會　計　單　位</td>
          <td width="25%" align="center">機&nbsp;&nbsp;&nbsp;關&nbsp;&nbsp;&nbsp;長&nbsp;&nbsp;&nbsp;官<br />
            或&nbsp;授&nbsp;權&nbsp;代&nbsp;簽&nbsp;人</td>
        </tr>
        <tr>
          <td height="100">&nbsp;</td>
          <td height="100">&nbsp;</td>
          <td height="100">&nbsp;</td>
          <td height="100">&nbsp;</td>
        </tr>
      </table>
      <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>

';

?>