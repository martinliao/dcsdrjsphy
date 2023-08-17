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
	$rs = $this->db->query("select * from `require` where YEAR = '{$year}' and CLASS_NO = '{$class_no}' and TERM = '{$term}'");
	if($rs){
		$row = $rs->FetchRow();
		return $row['START_DATE1'] . ' ~ ' . $row['END_DATE1'];
	}
	else return '';
}

function HT_class_type($year, $class_no, $term){
	global $db;
	$item_id = $db->GetOne("select HT_CLASS_TYPE from `require` where YEAR = '{$year}' and CLASS_NO = '{$class_no}' and TERM = '{$term}'");
	$cname = $db->GetOne("select DESCRIPTION from code_table where TYPE_ID = '07' and ITEM_ID = '{$item_id}'");
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
$outputHTML='';
$yearStr='';
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
//echo "sql:".$sql;
//sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','06');

$total_subtotal = 0;
if ($rs)
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
        "{$where1} ) a";
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
  for ($j=0; $j < sizeof($rs2); $j++) {
    $data= $rs2[$j];
    $total_subtotal =$total_subtotal + $data['subtotal'];
  }
}

$momey = str_pad($total_subtotal, 9 , '*', STR_PAD_LEFT);
//------------------------------------------------------------------------------------

$outputHTML .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
@page {
    margin: 15mm 5mm 5mm 5mm;  
} 
</style>
</head>
<body>
<table width="700" border="0" align="center"><tr><td height="50" align="center" >';

// <h1><span style="border-bottom: 3px double black">臺北市政府公務人員訓練處</span></h1></td></tr></table>
// <table width="700" align="center" border="0" cellpadding="0" cellspacing="0" bordercolor="#990000">
//   <tr>
//     <td>
// 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
//       <tr>
//         <td width="86%">
// 		<table width="100%" border="0" cellspacing="0" cellpadding="0">
//           <tr>
//             <td>
// 			<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
//               <tr>
//                 <td height="40" align="center" nowrap>傳票編號</td>
//                 <td style="width:45px;">&nbsp;</td>
//               </tr>
//               <tr>
//                 <td height="40" align="center" nowrap>付款憑單<br />
//                   編　　號</td>
//                 <td style="width:45px;">&nbsp;</td>
//               </tr>
//             </table></td>
//             <td align="center">
//               <h1><u>黏 貼 憑 證 用 紙</u></h1></td>
//           </tr>
//         </table>
//           <table width="100%" border="0" cellspacing="0" cellpadding="0">
//             <tr>
//               <td><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
//                 <tr>
//                   <td rowspan="3" align="center" style="width:70px;">憑　證<br /><br />
//                     編　號</td>
//                   <td height="26" align="center" style="width:85px;">預算年度</td>
//                   <td align="center" style="width:80px;">'.$yearStr.'</td>
//                   <td colspan="9" align="center">金　　　　額</td>
//                   <td align="center">用　途　說　明</td>
//                 </tr>
//                 <tr>
//                   <td height="26" align="center">預算</td>
//                   <td align="center">科　　目</td>
//                   <td width="4%" rowspan="2" align="center">億</td>
//                   <td width="4%" rowspan="2" align="center">千<br />
//                     萬</td>
//                   <td width="4%" rowspan="2" align="center">百<br />
//                     萬</td>
//                   <td width="4%" rowspan="2" align="center">十<br />
//                     萬</td>
//                   <td width="4%" rowspan="2" align="center">萬</td>
//                   <td width="4%" rowspan="2" align="center">千</td>
//                   <td width="4%" rowspan="2" align="center">百</td>
//                   <td width="4%" rowspan="2" align="center">十</td>
//                   <td width="4%" rowspan="2" align="center">元</td>
//                   <td rowspan="3" valign="top">鐘點費及交通費<br><'.$paper_app_seq.'></td>
//                 </tr>

//                 <tr>
//                   <td height="28" align="center" style="width:70px;">工作計畫</td>
//                   <td align="center">用　途　別</td>
//                   </tr>
//                 <tr align="center">
//                   <td height="36">&nbsp;</td>
//                   <td align="center">在職訓練</td>
//                   <td align="center">業務費</td>
//                   <td>'.($momey[0] == '*' ? '&nbsp;' : $momey[0]).'</td>
//                   <td>'.($momey[1] == '*' ? '&nbsp;' : $momey[1]).'</td>
//                   <td>'.($momey[2] == '*' ? '&nbsp;' : $momey[2]).'</td>
//                   <td>'.($momey[3] == '*' ? '&nbsp;' : $momey[3]).'</td>
//                   <td>'.($momey[4] == '*' ? '&nbsp;' : $momey[4]).'</td>
//                   <td>'.($momey[5] == '*' ? '&nbsp;' : $momey[5]).'</td>
//                   <td>'.($momey[6] == '*' ? '&nbsp;' : $momey[6]).'</td>
//                   <td>'.($momey[7] == '*' ? '&nbsp;' : $momey[7]).'</td>
//                   <td>'.($momey[8] == '*' ? '&nbsp;' : $momey[8]).'</td>
//                   </tr>
//               </table></td>
//             </tr>
//           </table></td>
//         <td width="14%" align="right" valign="bottom"><table width="95%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
//           <tr>
//             <td height="30" align="center">附件</td>
//           </tr>
//           <tr height="138">
//            <td align="left">
// 		   <font style="font-size:14px;">
// 		    發　票&nbsp;&nbsp;&nbsp;&nbsp;張<br />
//             收　據&nbsp;&nbsp;&nbsp;&nbsp;張<br />
//             請購單&nbsp;&nbsp;&nbsp;&nbsp;張<br />
//             請修單&nbsp;&nbsp;&nbsp;&nbsp;張<br />
//             驗收報告&nbsp;&nbsp;張<br />
//             合約書&nbsp;&nbsp;&nbsp;&nbsp;份<br />
//             其他文件&nbsp;&nbsp;張<br />
// 			</font>
// 			<font style="font-size:10px;"> (需註明文件名稱)</font>
// 			課表
// 			<br><br>
//               </td>
//           </tr>
//         </table></td>
//       </tr>
//     </table>
//       <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
//         <tr>
//           <td width="25%" height="38" align="center">經　辦　單　位</td>
//           <td width="25%" align="center">申請、使用單位<br />
//             （驗收或證明、保管）</td>
//           <td width="25%" align="center">會　計　單　位</td>
//           <td width="25%" align="center">機&nbsp;&nbsp;&nbsp;關&nbsp;&nbsp;&nbsp;長&nbsp;&nbsp;&nbsp;官<br />
//             或&nbsp;授&nbsp;權&nbsp;代&nbsp;簽&nbsp;人</td>
//         </tr>
//         <tr>
//           <td height="100">&nbsp;</td>
//           <td height="10">&nbsp;</td>
//           <td height="100">&nbsp;</td>
//           <td height="100">&nbsp;</td>
//         </tr>
//       </table>
//   </tr>
// </table>

//查詢
//------------------------------------------------------------------------------------
// 載入縣市區的對照表
$cityArr = array();
$rs_city = $this->db->query("select CITY, CITY_NAME from co_city");
$rs_city = QueryToArray($rs_city);
if($rs_city)
for ($i=0; $i < sizeof($rs_city); $i++) {
  $row_city=$rs_city[$i];
  $cityArr[$row_city['CITY']] = $row_city['CITY_NAME'];
} 

$subcityArr = array();
$rs_subcity = $this->db->query("select CITY, SUBCITY, SUBCITY_NAME from co_subcity");
$rs_subcity = QueryToArray($rs_subcity);
if($rs_subcity)
for ($i=0; $i < sizeof($rs_subcity); $i++) {
  $row_subcity=$rs_subcity[$i];
  $k = "{$row_subcity['CITY']}-{$row_subcity['SUBCITY']}";
  $subcityArr[$k] = $row_subcity['SUBCITY_NAME'];
} 

$d1 = trim($sess_start_date);
$d2 = trim($sess_end_date);

$mtList   = trim($mtList);
$year     = '';
$class_no = '';
$term     = '';
$paper_app_seq = trim($paper_app_seq);
$is_status_ok = trim($is_status_ok);

$where1 = "and a.use_date between date('{$d1}') and date('{$d2}')";
if ($year!="" && $class_no!="" && $term!="" && $mtList ==''){ // custom by chiahua補上mtList == ''
  $where2 = "and a.year = '{$year}' and a.class_no = '{$class_no}' and a.term = '{$term}'";
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
  $where2 = "and (a.year, a.class_no, a.term) in ({$where2})";

  $show_app_seq = $arryValue[3];
}

$where3 = "";
if($paper_app_seq != ''){
	$where3 = " and a.seq in ( select seq from hour_app where app_seq = '{$paper_app_seq}' )";
}
else{

}

$sql = "select year, class_no, class_name, term from hour_traffic_tax a " .
       "where 1=1 {$where1} {$where2} {$where3} " .
       "group by year, class_no, class_name, term order by year, class_no, term";

// 來自13C查看清冊
$from_13C = (strpos($_SERVER['HTTP_REFERER'], 'pay08.php') ? true : false);

if($from_13C == true){
	$sql = "select year, class_no, class_name, term from hour_traffic_tax a " .
		   "where 1=1 {$where3} " .
		   "group by year, class_no, class_name, term order by year, class_no, term";
}
//echo 'sql:'.$sql;
$rs = $this->db->query($sql);
$rs = QueryToArray($rs);
//sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','06');
//------------------------------------------------------------------------------------


//PDF
//------------------------------------------------------------------------------------
/*
include ('fpdf1/chinese-unicode.php');
$pdf = new PDF_Unicode();
$pdf->SetAutoPageBreak("on","20");
$pdf->AddUniCNShwFont('uni');
$pdf->SetFont('uni','',12);
$pdf->Open();
$pdf->AddPage("L");
$pdf->SetMargins(15,5,15,10);

$title="臺北市政府公務人員訓練處    ";
$title.= "請款清冊";

//表頭
$pdf->SetFontSize(14);
$pdf->Cell(280,10,$title,0,1,'C');

$pdf->SetFontSize(8);
$pdf->Cell(20,10,"上課日期",1,0,'C');
$pdf->Cell(30,10,"姓名/公司\nID/編號",1,0,'C');
$pdf->Cell(30,10,"銀行分號\n帳號",1,0,'C');
$pdf->Cell(40,10,"地址\ne-mail",1,0,'C');
$pdf->Cell(10,10,"時數",1,0,'C');
$pdf->Cell(20,10,"單價",1,0,'C');
$pdf->Cell(20,10,"鐘點費",1,0,'C');
$pdf->Cell(30,10,"交通費\n(火車莒光)",1,0,'C');
$pdf->Cell(20,10,"合計",1,0,'C');
$pdf->Cell(20,10,"簽章",1,0,'C');
$pdf->Cell(20,10,"備註",1,1,'C');


while($fields = $rs->FetchRow()){
	$pdf->Cell(20,10,$fields['USE_DATE'],1,0,'C');
	$pdf->Cell(30,10,$fields['TEACHER_NAME'],1,0,'C');

	$tmp = $fields['TEACHER_BANK_ID'] . " " . $fields['TEACHER_ACCOUNT'];
	$pdf->Cell(30,10,$tmp,1,0,'C');

	$pdf->Cell(40,10,$fields['TEACHER_ADDR'],1,0,'C');
	$pdf->Cell(10,10,$fields['HRS'],1,0,'C');
	$pdf->Cell(20,10,$fields['UNIT_HOUR_FEE'],1,0,'C');
	$pdf->Cell(20,10,$fields['HOUR_FEE'],1,0,'C');
	$pdf->Cell(30,10,$fields['TRAFFIC_FEE'],1,0,'C');
	$pdf->Cell(20,10,$fields['SUBTOTAL'],1,0,'C');
	$pdf->Cell(20,10,"",1,0,'C');
	$pdf->Cell(20,10,$fields['MODIFYED'],1,1,'C');
}

$pdf->Output();
*/
//------------------------------------------------------------------------------------
  $outputHTML .= '<table width="100%">';
  $outputHTML .= '<tr>';
  $outputHTML .= '<td align="center">';
  $outputHTML .= '<font style="font-size:18px;">臺北市政府公務人員訓練處    請款清冊'. ($show_app_seq != "" ? "(流水號".htmlspecialchars($show_app_seq, ENT_HTML5|ENT_QUOTES).")" : ($paper_app_seq != "" ? "(流水號".htmlspecialchars($paper_app_seq, ENT_HTML5|ENT_QUOTES).")" : "")) .'</font><br>';
  $outputHTML .= '</td>';
  $outputHTML .= '</tr>';
  $outputHTML .= '</table>';
if ($rs)
for ($i=0; $i < sizeof($rs); $i++) { 
  $fields=$rs[$i];
    
  /*
    $sql = "select * from hour_traffic_tax a where status = '待確認' " .
          "and year = '{$fields['YEAR']}' and class_no = '{$fields['CLASS_NO']}' and term = '{$fields['TERM']}' " .
          "{$where1} AND seq NOT IN (SELECT seq FROM hour_app) order  by USE_DATE ";

    if($from_13C == true){
      $sql = "select * from hour_traffic_tax a where status = '待確認' " .
            "and year = '{$fields['YEAR']}' and class_no = '{$fields['CLASS_NO']}' and term = '{$fields['TERM']}' " .
            "{$where3}  order  by USE_DATE ";
    }
  */
  $sql = "select a.seq, p.course_date, min(p.from_time) from_time, t.teacher as teacher, t.name, t.account_name as acct_name
  from hour_traffic_tax a join periodtime p on a.year=p.year and a.term=p.term and a.class_no=p.class_no and a.use_date=p.course_date 
  join room_use cr on a.year=cr.year and a.term=cr.term and a.class_no=cr.class_id and a.teacher_id=cr.teacher_id and cr.use_date = p.course_date and cr.use_period=p.id and cr.room_id=p.room_id 
  join teacher t on t.idno=cr.teacher_id and a.teacher_id = t.idno and t.teacher=cr.isteacher and a.isteacher = t.teacher
  where a.status = '待確認' and a.year = '{$fields['year']}' and a.class_no = '{$fields['class_no']}' and a.term = '{$fields['term']}' 
  {$where1} AND a.seq NOT IN (SELECT seq FROM hour_app) group by  a.seq, p.course_date, t.teacher, t.name	order by p.course_date, from_time, t.teacher desc, t.name ";

  if($from_13C == true){
  $sql = "select a.seq, p.course_date, min(p.from_time) from_time, t.teacher as teacher, t.name, t.account_name as acct_name
  from hour_traffic_tax a join periodtime p on a.year=p.year and a.term=p.term and a.class_no=p.class_no and a.use_date=p.course_date 
  join room_use cr on a.year=cr.year and a.term=cr.term and a.class_no=cr.class_id and a.teacher_id=cr.teacher_id and cr.use_date = p.course_date and cr.use_period=p.id and cr.room_id=p.room_id 
  join teacher t on t.idno=cr.teacher_id and a.teacher_id = t.idno and t.teacher=cr.isteacher and a.isteacher = t.teacher
      where a.status = '待確認' and a.year = '{$fields['year']}' and a.class_no = '{$fields['class_no']}' and a.term = '{$fields['term']}' 
    {$where3}  group by  a.seq, p.course_date, t.teacher, t.name order by p.course_date, from_time, t.teacher desc, t.name ";
  }

  //#47552 實體系統-13A及13B已產生流水號的請款清冊、憑證資料沒有by流水號：增加請款流水號的判斷
  //#mis28223 實體系統-13A、13B合併在一個流水號，產出的格式有誤：加入年、班期、期別過濾
  if($paper_app_seq){
  $sql = "select a.seq, p.course_date, min(p.from_time) from_time, t.teacher as teacher, t.name, t.account_name as acct_name
  from hour_traffic_tax a join periodtime p on a.year=p.year and a.term=p.term and a.class_no=p.class_no and a.use_date=p.course_date 
  join room_use cr on a.year=cr.year and a.term=cr.term and a.class_no=cr.class_id and a.teacher_id=cr.teacher_id and cr.use_date = p.course_date and cr.use_period=p.id and cr.room_id=p.room_id 
  join teacher t on t.idno=cr.teacher_id and a.teacher_id = t.idno and t.teacher=cr.isteacher and a.isteacher = t.teacher
  where a.seq in (select seq from hour_app where app_seq = '{$paper_app_seq}')
  and a.year = '{$fields['year']}'
  and a.class_no = '{$fields['class_no']}'
  and a.term = '{$fields['term']}'
  and a.status != '已設定為不請款'
  group by  a.seq, p.course_date, t.teacher, t.name, t.account_name
  order by p.course_date, from_time, t.teacher desc, t.name, t.account_name";
  }

  //echo $sql;
  $title = $fields['year'] . "年 " . htmlspecialchars($fields['class_name'], ENT_HTML5|ENT_QUOTES) . " 第" . htmlspecialchars($fields['term'], ENT_HTML5|ENT_QUOTES) . "期(流水號：" . htmlspecialchars($paper_app_seq, ENT_HTML5|ENT_QUOTES) . ")";
  
  $rs2 = $this->db->query($sql);
  $rs2 = QueryToArray($rs2);

  $classdate='';
    $funsql = $this->db->query("select * from `require` where YEAR = '{$fields['year']}' and CLASS_NO = '{$fields['class_no']}' and TERM = '{$fields['term']}'");
    $funsql = QueryToArray($funsql);
    if($funsql){
        $classdate = substr($funsql[0]['start_date1'],0,10) . ' ~ ' . substr($funsql[0]['end_date1'],0,10);	
    }
    else $classdate = '';

  $classtype='';
    $funitem_id = QueryToArray($this->db->query("select ht_class_type from `require` where YEAR = '{$fields['year']}' and CLASS_NO = '{$fields['class_no']}' and TERM = '{$fields['term']}'"));
    if(sizeof($funitem_id)==0){
      $funitem_id='';
    }else{
      $funitem_id=$funitem_id[0]['ht_class_type'];
    };
    $classtype = QueryToArray($this->db->query("select description from code_table where TYPE_ID = '07' and ITEM_ID = '{$funitem_id}'"));
    if(sizeof($classtype)==0){
      $classtype='';
    }else{
      $classtype=$classtype[0]['description'];
    };


  $outputHTML .= '<table width="100%" border="0">';
  $outputHTML .= '<tr>';
  $outputHTML .= '<td align="center" colspan="3">';
  $outputHTML .= '<font style="font-size:16px;">' . htmlspecialchars($fields['year'], ENT_HTML5|ENT_QUOTES) . "年 " . htmlspecialchars($fields['class_name'], ENT_HTML5|ENT_QUOTES) . ' 第' . htmlspecialchars($fields['term'], ENT_HTML5|ENT_QUOTES) . '期</font>';
  $outputHTML .= '</td>';
  $outputHTML .= '</tr>';
  $outputHTML .= '<tr>';
  $outputHTML .= '<td align="left">';
  $outputHTML .= '<font style="font-size:14px;">查詢日期：'.htmlspecialchars($d1, ENT_HTML5|ENT_QUOTES) . ' ~ ' .htmlspecialchars($d2, ENT_HTML5|ENT_QUOTES).'</font>';
  $outputHTML .= '</td>';
  $outputHTML .= '<td align="center"><font style="font-size:14px;">開課日期：'.htmlspecialchars($classdate, ENT_HTML5|ENT_QUOTES);
  $outputHTML .= '</td>';
  $outputHTML .= '<td align="right">';
  $outputHTML .= '<font style="font-size:14px;">類別：'. htmlspecialchars($classtype, ENT_HTML5|ENT_QUOTES) .'</font>';
  $outputHTML .= '</td>';
  $outputHTML .= '</tr>';
  $outputHTML .= '</table>';

  $outputHTML .= '<table border="0" cellspacing="0" cellpadding="0" width="100%"  >';
  $outputHTML .= '<tr>';
  $outputHTML .= '<td >';

  $outputHTML .= '<table border="1" bordercolor="#000000" cellspacing="0" cellpadding="2" width="100%" >';
  $outputHTML .= '<tr >';
  $outputHTML .= '<td style="width:70px;" align="center" valign="middle"><font style="font-size:13px;">上課日期</font></td>';
  $outputHTML .= '<td style="width:70px;" align="center"><font style="font-size:13px;">姓名/公司<br>ID/編號</font></td>';
  $outputHTML .= '<td style="width:250px;" align="center"><font style="font-size:13px;">銀行/郵局分行<br>帳號(帳戶名稱)</font></td>';
  $outputHTML .= '<td style="width:165px;" align="center" valign="middle"><font style="font-size:13px;">地址<br>email</font></td>';
  $outputHTML .= '<td style="width:35px;" align="center" valign="middle"><font style="font-size:13px;">時數</font></td>';
  $outputHTML .= '<td style="width:40px;" align="center" valign="middle"><font style="font-size:13px;">單價</font></td>';
  $outputHTML .= '<td style="width:50px;" align="center" valign="middle"><font style="font-size:13px;">鐘點費</font></td>';
  $outputHTML .= '<td style="width:50px;" align="center" valign="middle"><font style="font-size:13px;">交通費</font></td>';
  $outputHTML .= '<td style="width:50px;" align="center" valign="middle"><font style="font-size:13px;">合計</font></td>';
  $outputHTML .= '<td style="width:180px;" align="center" valign="middle"><font style="font-size:13px;">資料確認請V</font></td>'; // custom by chiahua 加大欄位
  $outputHTML .= '<td style="width:30px;" align="center" valign="middle"><font style="font-size:13px;">備註</font></td>';
  // $outputHTML .= '<td style="width:30px;" align="center" valign="middle"><font style="font-size:13px;">已閱讀✔</font></td>';
  $outputHTML .= '</tr>';
  $total_hour = 0;
  $total_traffic = 0;
  $total_subtotal = 0;

  for ($j=0; $j < sizeof($rs2); $j++) { 
    $seq_data=$rs2[$j];
    $sql = "select a.*,(case when t.ID_TYPE='3' then f.fid else t.id end) as tid, t.rpno from hour_traffic_tax a join teacher t on t.idno=a.teacher_id left join fid f on f.id = t.id where seq = '{$seq_data['seq']}'"; 
    $rs1 = $this->db->query($sql);
    $rs1 = QueryToArray($rs1);
    $data = $rs1[0];
    $tid=$data['tid'];	// 身分證(外國人:居留證編號)
    if($data['remark'] == '無'){
      $remark = '';
    } else {
      $remark = $data['remark'];
    }
    $tb_rs = $this->db->query("select name, bank_account as ACCOUNT,bank_code as BANKID,route as ADDR,account_name as ACCT_NAME,county as CITY,district as SUBCITY from teacher where IDno = '{$data['teacher_id']}' and teacher = '{$data['isteacher']}'");
    //echo "sql:".$sql;
    $tb_rs = QueryToArray($tb_rs);					
    $tb_row = $tb_rs[0];
    // custom (b) by chiahua 如果狀態不是"請款確認"和"市庫支票"時，要再重新抓取講師的資料
    if($data['status'] != '請款確認' && $data['status'] != '市庫支票'){
      $teacher_name = $tb_row['name']; // 姓名
      $tb_account = $tb_row['ACCOUNT']; // 銀行帳號
      $tb_BANKID = $tb_row['BANKID']; // 銀行代碼
      $teacher_acct_name = $tb_row['ACCT_NAME']; // 帳戶名稱
      $city = $cityArr[$tb_row['CITY']]; // 縣市
      $subcity = $subcityArr[$tb_row['CITY'] . '-'. $tb_row['SUBCITY']]; // 區
      $teacher_addr = $tb_row['ADDR']; // 地址
    
      $temp_tb_type = QueryToArray($this->db->query("select MEMO from code_table where TYPE_ID=14 and ITEM_ID = '{$tb_BANKID}'"));
      
      //再找出類型是銀行還是郵局
      if(sizeof($temp_tb_type) != 0) {
        $tb_type = $temp_tb_type[0]['MEMO'];
      }
      else {
        $tb_type = "";
      }
    
      // 更新請款的銀行帳戶資料
      $bank_sql ="update hour_traffic_tax set TEACHER_NAME = '{$teacher_name}', TEACHER_BANK_TYPE = '{$tb_type}' , TEACHER_BANK_ID = '{$tb_BANKID}', TEACHER_ACCOUNT = '{$tb_account}', TEACHER_ACCT_NAME = '{$teacher_acct_name}', TEACHER_ADDR = '{$city}{$subcity}{$teacher_addr}' where TEACHER_ID = '{$data['teacher_id']}' and YEAR = '{$fields['year']}' and CLASS_NO = '{$fields['class_no']}' and TERM = '{$fields['term']}'";
      //echo "bank_sql:".$bank_sql;
      $this->db->query($bank_sql);

      if($this->db->Affected_Rows() > 0){
        $data['TEACHER_BANK_ID'] = $tb_BANKID;
        $data['TEACHER_ACCOUNT'] = $tb_account;
        $data['TEACHER_NAME']    = $teacher_name;
        $data['TEACHER_ACCT_NAME']= $teacher_acct_name;
        $data['TEACHER_ADDR']    = $city . $subcity . $teacher_addr;
      }
    }
    //$data['TEACHER_ADDR']=$cityArr[$tb_row['CITY']].$subcityArr[$tb_row['CITY'] . '-'. $tb_row['SUBCITY']].$tb_row['ADDR'];
    // custom (e) by chiahua 如果狀態不是"請款確認"時，要再重新抓取講師的資料

    // custom (b) by chiahua 重新抓取鐘點費，更新成最新狀態
    if (trim($data['status']) == "" || trim($data['status']) == "待確認"){

    // custom (b) by chiahua 重新抓取課程的鐘點費類別，避免班期基本資料中的鐘點費類別有異動時，會抓不到對應的資料
    $get_ht_class_type = QueryToArray($this->db->query("select ht_class_type from `require` where YEAR = {$fields['year']} and CLASS_NO = '{$fields['class_no']}' and TERM = '{$fields['term']}'"))[0]['ht_class_type'];

    $this->db->query("update hour_traffic_tax set ht_class_type = '{$get_ht_class_type}' where YEAR = {$fields['year']} and CLASS_NO = '{$fields['class_no']}' and TERM = '{$fields['term']}' and TEACHER_ID = '{$data['teacher_id']}' and USE_DATE = '{$data['use_date']}' " . (trim($data['status']) == "" ? " and STATUS is null" : " and STATUS = '{$data['status']}'"));
    $data['ht_class_type'] = $get_ht_class_type;

    // custom (e) by chiahua 重新抓取課程的鐘點費類別，避免班期基本資料中的鐘點費類別有異動時，會抓不到對應的資料

    $count_fee = QueryToArray($this->db->query("select count(*) as cnt from hour_fee where CLASS_TYPE_ID = '{$data['ht_class_type']}' and teacher_type_ID = '{$data['t_source']}' and ASSISTANT_TYPE_ID ".(trim($data['a_source']) == '' ? 'is null' : "='{$data['a_source']}'")." and TYPE = '".($data['isteacher'] == 'Y' ? 1 : 2)."'"))[0]['cnt'];
    if($count_fee == 1){
      $rs_fee = $this->db->query("select * from hour_fee where CLASS_TYPE_ID = '{$data['ht_class_type']}' and teacher_type_ID = '{$data['t_source']}' and ASSISTANT_TYPE_ID ".(trim($data['a_source']) == '' ? 'is null' : "='{$data['a_source']}'")." and TYPE = '".($data['isteacher'] == 'Y' ? 1 : 2)."'");
      $rs_fee = QueryToArray($rs_fee);
      $row_fee = $rs_fee[0];

      // 單價和鐘點費和交通費都沒有被手動更新過才要自動更新
      if($data['unit_hour_fee_is_changed'] == 'N')
        $data['unit_hour_fee']	= $row_fee['hour_fee'];
      if($data['hour_fee_is_changed'] == 'N')
        $data['hour_fee']		= $row_fee['hour_fee'] * $data['hrs'];
      if($data['traffic_fee_is_changed'] == 'N')
        $data['traffic_fee']	= $row_fee['traffic_fee'];

      // custom by chiahua
      $data['traffic_fee'] = ($data['traffic_fee'] == "-1" ? 0 : $data['traffic_fee']);

      $data['SUBTOTAL']		= $data['hour_fee'] + $data['traffic_fee']; // 合計 = 鐘點費+交通費

      $this->db->query("update hour_traffic_tax set UNIT_HOUR_FEE = {$data['unit_hour_fee']}, HOUR_FEE = {$data['hour_fee']}, SUBTOTAL = {$data['subtotal']} where SEQ = '{$data['seq']}'");

    }
    }
    // custom (e) by chiahua 重新抓取鐘點費，更新成最新狀態
    $outputHTML .= '<tr>';
    $outputHTML .= '<td  align="center"><font style="font-size:13px;">' . htmlspecialchars(substr($data['use_date'],0,10), ENT_HTML5|ENT_QUOTES) . '</font></td>';
    $outputHTML .= '<td  align="left"><font style="font-size:13px;">' . htmlspecialchars($data['teacher_name'])  . '<br>' . ($data['rpno']!=''?htmlspecialchars($data['rpno'], ENT_HTML5|ENT_QUOTES):htmlspecialchars($data['teacher_id'], ENT_HTML5|ENT_QUOTES))  . '</font></td>';
    // custom by chiahua 顯示銀行郵局的中文名稱
    $temp_bank_name = QueryToArray($this->db->query("select description from code_table where TYPE_ID=14 and ITEM_ID = '{$data['teacher_bank_id']}'"));
    //再找出類型是銀行還是郵局
    if(sizeof($temp_bank_name) != 0) {
      $bank_name = $temp_bank_name[0]['description'];
    }
    else {
      $bank_name = "";
    }

    $outputHTML .= '<td  align="left"><font style="font-size:13px;">' . htmlspecialchars($bank_name, ENT_HTML5|ENT_QUOTES)  . '<br>' . $data['teacher_account'].'('.$data['teacher_acct_name'] . ')</font><br><strong><font style="font-size:16px;">' .htmlspecialchars($remark, ENT_HTML5|ENT_QUOTES). '</font></strong></td>';
    
    $email = QueryToArray($this->db->query("select EMAIL from teacher where idno = '{$data['teacher_id']}'"))[0]['EMAIL'];
    $outputHTML .= '<td  align="left"><font style="font-size:12px;">' . htmlspecialchars($data['teacher_addr'], ENT_HTML5|ENT_QUOTES) . '<br>'. htmlspecialchars($email, ENT_HTML5|ENT_QUOTES). '</font></td>';
    $outputHTML .= '<td  align="center"><font style="font-size:13px;">' . htmlspecialchars($data['hrs'], ENT_HTML5|ENT_QUOTES)  . '</font></td>';
    $outputHTML .= '<td  align="right"><font style="font-size:13px;">' . number_format($data['unit_hour_fee'])  . '</font></td>';
    $outputHTML .= '<td  align="right"><font style="font-size:13px;">' . number_format($data['hour_fee'])  . '</font></td>';

    $showTrafficFee = $data['traffic_fee'];
    $outputHTML .= '<td  align="right"><font style="font-size:13px;">' . ($showTrafficFee <=0 ? 0 : number_format($showTrafficFee))  . '</font></td>';
    $outputHTML .= '<td  align="right"><font style="font-size:13px;">' . number_format($data['subtotal'])  . '</font></td>';
    // $outputHTML .= '<td  align="center"><font style="font-size:13px;"><br><br><br><br></font></td>';
    // custom by chiahua 備註欄顯示聘請類別
    if($data['isteacher']=='Y')
    $getDesc = QueryToArray($this->db->query("select description from code_table where ITEM_ID = '{$data['t_source']}'"))[0]['description'];
    elseif($data['isteacher']=='N')
    $getDesc = QueryToArray($this->db->query("select description from code_table where ITEM_ID = '{$data['a_source']}'"))[0]['description'];
    $ischeck = ($data['ischeck'] == 'Y') ? '已確認' : '';
    $outputHTML .= '<td  align="center"><font style="font-size:13px;">'.htmlspecialchars($ischeck, ENT_HTML5|ENT_QUOTES).'</td>';
    $outputHTML .= '<td  align="center"><font style="font-size:13px;">'.htmlspecialchars($getDesc, ENT_HTML5|ENT_QUOTES).'</font></td>';
    $total_hour = $total_hour + $data['hour_fee'];
    $total_traffic =$total_traffic + ($data['traffic_fee'] <=0 ? 0 : $data['traffic_fee']);
    $total_subtotal =$total_subtotal + $data['subtotal'];
    $outputHTML .= '</tr>';
  }

  $outputHTML .= '<tr style="line-height:14px;">';
  $outputHTML .= '<td  align="right" colspan="6"><font style="font-size:13px;">總計</font></td>';
  $outputHTML .= '<td  align="right"><font style="font-size:13px;">' . number_format($total_hour) . '</font></td>';
  $outputHTML .= '<td  align="right"><font style="font-size:13px;">' . number_format($total_traffic) . '</font></td>';
  $outputHTML .= '<td  align="right"><font style="font-size:13px;">' . number_format($total_subtotal) . '</font></td>';
  $outputHTML .= '<td  align="left" colspan="3">&nbsp;</td>';
  $outputHTML .= '</tr>';
  $outputHTML .= '</table>';

  $outputHTML .= '</td>';
  $outputHTML .= '</tr>';
  $outputHTML .= '<tr><td align="left">';
  //$outputHTML .= '<font face="標楷體" size="10">※當日講師鐘點費超過5,000元(含)以上，須扣取二代健保補充保費1.91％。</font>';
  //$outputHTML .= '<font style="font-size:14px;">※配合二代健保補充保險費扣取作業，當日講師鐘點費超過20,008元(含)以上，須扣取補充保費1.91％。</font>';21,009
  //$outputHTML .= '<font style="font-size:14px;">※配合二代健保補充保險費扣取作業，當日講師鐘點費超過21,009元(含)以上，須扣取補充保費1.91％。</font>';
  /* //2021-07-02 1100630創意提案會議要求修正，註解
  $outputHTML .= '<table style="font-size:14px;"><tr><td colspan="2">有關本處講座鐘點費、交通費支給相關注意事項：</td></tr>
                  <tr><td align="left">1、  配合二代健保補充保險費扣取作業，當週講師鐘點費超過24,000元(含)以上，須扣取補充保費1.91%。</td></tr>
                  <tr><td align="left">2、  倘搭乘公務車授課，依規定不得支領交通費。</td></tr>
                  <tr><td align="left">3、  配合本府政風處107年4月17日北市政三字第1076000125號函示略以：內、外聘之講師、助教係受本處遴聘、具<br>&nbsp&nbsp&nbsp&nbsp領鐘點費及受託執行教學工作者，宜予類推適用「臺北市政府公務員廉政倫理規範」第5點規定，避免要求、<br>&nbsp&nbsp&nbsp&nbsp期約或收受利害關係者所為之餽贈，以彰本府廉能法紀政府形象。</td></tr>';
  */
  //2021-07-02 1100630創意提案會議要求修正
  /*
  $outputHTML .= '<table style="font-size:14px;"><tr><td colspan="2">有關本處講座鐘點費、交通費支給相關注意事項：</td></tr>
                  <tr><td align="left">1、  配合二代健保補充保險費扣取作業，當週講師鐘點費超過24,000元(含)以上，須扣取補充保費1.91%。</td></tr>
                  <tr><td align="left">2、  請提供個人匯款帳號（勿提供公司帳戶）；倘搭乘公務車授課，依規定不得支領交通費。</td></tr>
                  <tr><td align="left">3、  依「臺北市政府公務員廉政倫理規範」規定，本處遴聘人員應避免要求、期約或收受利害關係者所為之餽贈。</td></tr>';
                  $outputHTML .= '</table>';
  */
  //2021-07-15 1100630創意提案會議要求再修正
  $outputHTML .= '<table style="font-size:14px;"><tr><td colspan="2">有關本處講座鐘點費、交通費支給相關注意事項：</td></tr>
                  <tr><td align="left">1、  以上為講座個人匯款帳號（非公司帳號），款項及金額正確。</td></tr>
                  <tr><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;(若資料須更新，請告知承辦人)</td></tr>
                  <tr><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please confirm the information listed above, and click on "submit". </td></tr>
                  <tr><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If you need to update your personal information, please inform us.</td></tr>
                  <tr><td align="left">2、  當周講座鐘點費超過26,400元(含)以上，須扣二代健保補充保費2.11%。倘搭乘公務車授課，依規定不得支領</td></tr>
                  <tr><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;交通費。</td></tr>';
  $outputHTML .= '</table>';

  $outputHTML .= '<br><br>';
  $outputHTML .= '<script>';
  $outputHTML .= 'document.title = "'.htmlspecialchars($title, ENT_HTML5|ENT_QUOTES).'"';
  $outputHTML .= '</script>';
  $outputHTML .= '</body>';
  $outputHTML .= '</html>';
}
echo $outputHTML;
?>

<script type="text/javascript">
  $(document).ready(function() {
    $("#menu-toggle").click();
    $(".navbar").css("display", "none");
    $(".page-header").css("display", "none");
    $(".footer").css("display", "none");
  });
</script>
