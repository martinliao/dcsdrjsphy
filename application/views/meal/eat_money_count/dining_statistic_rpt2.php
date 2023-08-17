<?php
//------------------------------------------------------------------------------------
// include "init.inc.php";
//$db->debug = true;
// set_time_limit(0);
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
$d1 = $_REQUEST['d1'];
$d2 = $_REQUEST['d2'];

//$d1 = "2011-10-31";
//$d2 = "2011-11-06";
$where = "between date(".$this->db->escape(addslashes($d1)).") and date(".$this->db->escape(addslashes($d2)).")";

// sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','05');  
//exit;
//------------------------------------------------------------------------------------

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>用餐人數統計表</title>
	<link type="text/css" rel="stylesheet" href="css/master.css"/>
</head>
<style type="text/css">
<!--
table {
  border: 1px solid #000;
  border-collapse: collapse;
  border-spacing: 0;
  FONT-SIZE:12px;
}
tr, td {
  border: 1px solid #000;
}
-->
</style>
<body>
<div id="printTable">
<center><div style="font-size:150%;"><b>用餐人數計費統計表</b></div></center>

<?php
//條件
echo "<center><div>從 ".htmlspecialchars($d1, ENT_HTML5|ENT_QUOTES)." 至 ".htmlspecialchars($d2, ENT_HTML5|ENT_QUOTES)." </div></center>";

//開始
//echo '<table border="1" cellspacing="0" cellpadding="0" style="FONT-SIZE:12px;">';
echo '<center><table border="1" cellspacing="0" cellpadding="0">';

//表頭
//--------------------------------------------------------------------------------------------------
$sql = "select a.*, b.no_persons, b.ROOM_CODE, nvl(c.NAME,a.worker) as worker_name " . 
       "from ( " . 
       " select distinct year, class_no, term, class_name, worker from dining_student " . 
       " where use_date {$where} " . 
       ") a " . 
       "left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term " .
       "left join view_all_account c on a.worker = c.personal_id " .
       "order by a.year, a.class_no, a.term";
$rs = $this->db->query($sql);
$rs=QueryToArray($rs);
//外借班(有早午晚或結訓餐的才列)
$sql = "select b.name as worker_name, a.* " .
       "from ( " .
       " select * from appinfo where appi_id in (select distinct appi_id from room_use where use_period in ('11','12','13','14') and appi_id is not null and use_date {$where} ) " . 
       ") a " .
       "left join view_all_account b on a.cre_user = b.username " . 
       "order by a.appi_id";
$rsA = $this->db->query($sql);
$rsA=QueryToArray($rsA);
echo '<tr>';
  echo '<td nowrap align="center"><div style="width:60px">承辦人</div></td>';
  echo '<td align="center"><div style="width:60px"></div></td>';
  
  for ($i=0; $i < sizeof($rsA); $i++) { 
    $fields=$rsA[$i];
    echo '<td nowrap align="center" valign="top" width="80" colspan="3">' . $fields['WORKER_NAME'] . '</td>';
  }
  if (sizeof($rsA) < 5){
    $space_cell = 5 - sizeof($rsA); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td nowrap width="80" colspan="3"></td>';
    }
  }                                                      
echo '</tr>';

// $rs->MoveFirst();
// $rsA->MoveFirst();
echo '<tr>';
  echo '<td align="center">班期</td>';
  echo '<td align="center"></td>';
  
  for ($i=0; $i < sizeof($rsA); $i++) { 
    $fields=$rsA[$i];
    echo '<td align="center" valign="top" rowspan="2">';
    echo '<div style="height:100px;layout-flow:vertical-ideographic;text-align:left">' . $fields['APP_REASON'] . '</div>';
    echo '</td>';
    echo '<td align="center" valign="top" rowspan="3">';
    echo '<div style="height:100px;layout-flow:vertical-ideographic;text-align:left">學員及長官餐桌 數 合 計</div>';
    echo '</td>';
    echo '<td align="center" valign="top" rowspan="3">';
    echo '<div style="height:100px;layout-flow:vertical-ideographic;text-align:left">請款桌數</div>';
    echo '</td>';

  }    
  if (sizeof($rsA) < 5){
    $space_cell = 5 - sizeof($rsA); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td rowspan="2" colspan="3"></td>';
    }
  }
echo '</tr>';

// $rs->MoveFirst();
// $rsA->MoveFirst();
echo '<tr>';
  echo '<td align="center">調訓人數</td>';
  echo '<td align="center"></td>';
  
echo '</tr>';

// $rs->MoveFirst();
// $rsA->MoveFirst();
echo '<tr>';
  echo '<td align="center">桌次</td>';
  echo '<td align="center"></td>';

  for ($i=0; $i < sizeof($rsA); $i++) { 
    $fields=$rsA[$i];
    echo '<td align="center" ></td>';
  }
  if (sizeof($rsA) < 15){
    $space_cell = 5 - sizeof($rsA); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td align="center" colspan="3"></td>';
    }
  }                                            
echo '</tr>';
//--------------------------------------------------------------------------------------------------

//週期資料
//--------------------------------------------------------------------------------------------------
$totAll1 = 0;
$totAll2 = 0;
$totAll3 = 0;
$weekNM = array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
$sql = "select A.*, date_format(data_dt,'%m/%d') as dt_nm, dayofweek(data_dt) - 1 as week_nm from " . 
       "( select date(".$this->db->escape(addslashes($d1)).") as data_dt from dual " . 
       "union select date(".$this->db->escape(addslashes($d1)).") + INTERVAL 1 DAY from dual " .
       "union select date(".$this->db->escape(addslashes($d1)).") + INTERVAL 2 DAY from dual " .
       "union select date(".$this->db->escape(addslashes($d1)).") + INTERVAL 3 DAY from dual " .
       "union select date(".$this->db->escape(addslashes($d1)).") + INTERVAL 4 DAY from dual " .
       "union select date(".$this->db->escape(addslashes($d1)).") + INTERVAL 5 DAY from dual " .
       "union select date(".$this->db->escape(addslashes($d1)).") + INTERVAL 6 DAY from dual " .
       ") A order by A.data_dt";
$rs1 = $this->db->query($sql);
$rs1=QueryToArray($rs1);

for ($k=0; $k < sizeof($rs1); $k++) { 
  $d1=$rs1[$k];
$query_dt = $d1['data_dt'];
$sql = "
select a.*, b.no_persons, b.room_code
,a1.m_cnt, a1.l_cnt, a1.d_cnt
,replace(c1.m_name,',','<br>') as m_name, c1.m_teach_cnt
,replace(c2.l_name,',','<br>') as l_name, c2.l_teach_cnt
,replace(c3.d_name,',','<br>') as d_name, c3.d_teach_cnt
from
(
 select distinct year, class_no, term, class_name, worker from dining_student 
 where use_date {$where}
) a
left join 
(
 select year, class_no, term, class_name
 ,(nvl(persons_1,0) + nvl(add_persons_1,0)) as m_cnt
 ,(nvl(persons_2,0) + nvl(add_persons_2,0)) as l_cnt
 ,(nvl(persons_3,0) + nvl(add_persons_3,0)) as d_cnt
 from dining_student 
 where use_date = date('{$query_dt}')
) a1 on a.year = a1.year and a.class_no = a1.class_no and a.term = a1.term
left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term
left join
(
 select year, class_no, term, use_date, name as m_NAME, count(*) as m_teach_cnt
 from dining_teacher
 where use_date = date('{$query_dt}') and dining_type = 'A'
 group by year, class_no, term, use_date
) c1 on a.year = c1.year and a.class_no = c1.class_no and a.term = c1.term
left join
(
 select year, class_no, term, use_date, name as l_NAME, count(*) as l_teach_cnt
 from dining_teacher
 where use_date = date('{$query_dt}') and dining_type = 'B'
 group by year, class_no, term, use_date
) c2 on a.year = c2.year and a.class_no = c2.class_no and a.term = c2.term
left join
(
 select year, class_no, term, use_date, name as d_NAME, count(*) as d_teach_cnt
 from dining_teacher
 where use_date = date('{$query_dt}') and dining_type = 'C'
 group by year, class_no, term, use_date
) c3 on a.year = c3.year and a.class_no = c3.class_no and a.term = c3.term
order by a.year, a.class_no, a.term
";
$rs2 = $this->db->query($sql);
$rs2=QueryToArray($rs2);

echo '<tr>';
  echo '<td align="center" rowspan="3">' . $d1['dt_nm'] . '<br>' . $weekNM[$d1['week_nm']] . '</td>';
  echo '<td align="center" height="40" >早餐</td>';
  
  // $rsA->MoveFirst();
  for ($i=0; $i < sizeof($rsA); $i++) { 
    $fields=$rsA[$i];
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
    
  }
  if (sizeof($rsA) < 15){
    $space_cell = 5 - sizeof($rsA); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td align="center"></td>';
      echo '<td align="center"></td>';
      echo '<td align="center"></td>';
    }
  }    
echo '</tr>'; 

  

echo '<tr>';
  echo '<td align="center" height="40" >午餐</td>';
  
  // $rsA->MoveFirst();
  for ($i=0; $i < sizeof($rsA); $i++) { 
    $fields=$rsA[$i];
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
  }
  if (sizeof($rsA) < 15){
    $space_cell = 5 - sizeof($rsA); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td align="center"></td>';
      echo '<td align="center"></td>';
      echo '<td align="center"></td>';
    }
  }     
echo '</tr>';



echo '<tr>';
  echo '<td align="center" height="40" >晚餐</td>';
  
  // $rsA->MoveFirst();
  for ($i=0; $i < sizeof($rsA); $i++) { 
    $fields=$rsA[$i];
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
  }
  if (sizeof($rsA) < 15){
    $space_cell = 5 - sizeof($rsA); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td align="center"></td>';
      echo '<td align="center"></td>';
      echo '<td align="center"></td>';
    }
  }        
echo '</tr>';  


}
//--------------------------------------------------------------------------------------------------

//加總
//--------------------------------------------------------------------------------------------------
// $rs->MoveFirst();
echo '<tr>';
  echo '<td align="center">備註欄</td>';
  echo '<td align="center"></td>';
  
  
//update by jay
$sql="
select A.appi_id, B.COUNTBY, B.PRICE3, B.NUM, B.AMT from appinfo A 
LEFT JOIN ( select countby, price3, appi_id, sum(num) as num, sum(amt) as amt, use_period 
from ( select c.countby, a.appi_id, a.num, b.price_c as price3, a.use_period, (nvl(a.num,0) * nvl(b.price_c,0)) as amt from room_use a 
left join venue_time b on a.room_id = b.room_id and a.use_period = b.price_t
left join classroom c on a.room_id = c.room_id 
where a.appi_id is not null  and a.use_date {$where} )sd 
group by appi_id, use_period, countby, price3 ) B ON A.APPI_ID = B.APPI_ID AND B.USE_PERIOD = '11'
 where A.appi_id in ( select distinct appi_id from room_use where use_period in ('11','12','13','14') 
 and appi_id is not null  and use_date {$where} ) ORDER BY A.APPI_ID
";
/*
$sql=<<<tag
select A.appi_id, B.AMT from appinfo A
LEFT JOIN
(
  select appi_id, sum(num) as num, sum(amt) as amt, use_period from
  (
    select a.appi_id, a.num, b.price3, a.use_period, (nvl(a.num,0) * nvl(b.price3,0)) as amt from room_use a
    left join classroom_timeprice b on a.room_id = b.room_id and a.use_period = b.usetime
    where a.appi_id is not null
    and a.use_date {$where}
  )
  group by appi_id, use_period
) B ON A.APPI_ID = B.APPI_ID AND B.USE_PERIOD = '11' 
where A.appi_id in 
(
  select distinct appi_id from room_use where use_period in ('11','12','13','14') and appi_id is not null 
  and use_date {$where}
)
ORDER BY A.APPI_ID
tag;
*/
$rsA1 = $this->db->query($sql);
$rsA1=QueryToArray($rsA1);
for ($j=0; $j < sizeof($rsA1); $j++) { 
  $fields=$rsA1[$j];

  echo '<td nowrap align="center" colspan="2">早餐';
  
  //add by jay
  echo $fields['PRICE3'];
  if ($fields['PRICE3'] != ""){
    echo "元";
  } 
  if ($fields['COUNTBY'] == "1"){
    echo "/人";
  }
  if ($fields['COUNTBY'] == "2"){
    echo "/桌";
  }
  if ($fields['COUNTBY'] == "3"){
    echo "/場地";
  }
  
  echo '</td>';  
  //echo '<td nowrap align="center">' . $fields['AMT'] . '</td>';
  echo '<td nowrap align="center">' . $fields['NUM'] . '</td>';
  
}
if (sizeof($rsA1) < 5){
  $space_cell = 5 - sizeof($rsA1); 
  for ($i=0;$i<$space_cell;$i++){
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';      
  }
}

echo '</tr>';

// $rs->MoveFirst();
echo '<tr>';
  echo '<td align="center"></td>';
  echo '<td align="center"></td>';
  
  
$sql="
select A.appi_id, B.COUNTBY, B.PRICE3, B.NUM, B.AMT from appinfo A 
LEFT JOIN ( select countby, price3, appi_id, sum(num) as num, sum(amt) as amt, use_period 
from ( select c.countby, a.appi_id, a.num, b.price_c as price3, a.use_period, (nvl(a.num,0) * nvl(b.price_c,0)) as amt from room_use a 
left join venue_time b on a.room_id = b.room_id and a.use_period = b.price_t
left join classroom c on a.room_id = c.room_id where a.appi_id is not null  and a.use_date {$where} ) as zz
 group by appi_id, use_period, countby, price3 ) B ON A.APPI_ID = B.APPI_ID AND B.USE_PERIOD = '12' 
 where A.appi_id in ( select distinct appi_id from room_use where use_period in ('11','12','13','14') 
 and appi_id is not null and use_date {$where} ) ORDER BY A.APPI_ID

";
$rsA1 = $this->db->query($sql);
$rsA1=QueryToArray($rsA1);
for ($j=0; $j < sizeof($rsA1); $j++) { 
  $fields=$rsA1[$j];
  echo '<td nowrap align="center" colspan="2" >午餐';  
  
  //add by jay
  echo $fields['PRICE3'];
  if ($fields['PRICE3'] != ""){
    echo "元";
  } 
  if ($fields['COUNTBY'] == "1"){
    echo "/人";
  }
  if ($fields['COUNTBY'] == "2"){
    echo "/桌";
  }
  if ($fields['COUNTBY'] == "3"){
    echo "/場地";
  }
    
  echo '</td>';  
  //echo '<td nowrap align="center">' . $fields['AMT'] . '</td>';
  echo '<td nowrap align="center">' . $fields['NUM'] . '</td>';
  
}
if (sizeof($rsA1) < 5){
  $space_cell = 5 - sizeof($rsA1); 
  for ($i=0;$i<$space_cell;$i++){
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
  }
}                       

echo '</tr>';

// $rs->MoveFirst();
echo '<tr>';
  echo '<td align="center"></td>';
  echo '<td align="center"></td>';
  

$sql="
select A.appi_id, B.COUNTBY, B.PRICE3, B.NUM, B.AMT from appinfo A 
LEFT JOIN ( select countby, price3, appi_id, sum(num) as num, sum(amt) as amt, use_period 
from ( select c.countby, a.appi_id, a.num, b.price_c as price3, a.use_period, (nvl(a.num,0) * nvl(b.price_c,0)) as amt from room_use a 
left join venue_time b on a.room_id = b.room_id and a.use_period = b.price_t 
left join classroom c on a.room_id = c.room_id where a.appi_id is not null and a.use_date {$where} )  as zz
group by appi_id, use_period, countby, price3 ) B ON A.APPI_ID = B.APPI_ID AND B.USE_PERIOD = '13' 
where A.appi_id in ( select distinct appi_id from room_use where use_period in ('11','12','13','14')
 and appi_id is not null and use_date {$where}) ORDER BY A.APPI_ID

";
$rsA1 = $this->db->query($sql);
$rsA1=QueryToArray($rsA1);
for ($j=0; $j < sizeof($rsA1); $j++) { 
  $fields=$rsA1[$j];
  echo '<td nowrap align="center" colspan="2">晚餐';  
  
  //add by jay
  echo $fields['PRICE3'];
  if ($fields['PRICE3'] != ""){
    echo "元";
  } 
  if ($fields['COUNTBY'] == "1"){
    echo "/人";
  }
  if ($fields['COUNTBY'] == "2"){
    echo "/桌";
  }
  if ($fields['COUNTBY'] == "3"){
    echo "/場地";
  }
  
  echo '</td>';  
  //echo '<td nowrap align="center">' . $fields['AMT'] . '</td>';
  echo '<td nowrap align="center">' . $fields['NUM'] . '</td>';
  
}
if (sizeof($rsA1) < 5){
  $space_cell = 5 - sizeof($rsA1); 
  for ($i=0;$i<$space_cell;$i++){
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
  }
}      

echo '</tr>';
//--------------------------------------------------------------------------------------------------

//末列1
//--------------------------------------------------------------------------------------------------
// $rs->MoveFirst();
echo '<tr>';
  echo '<td align="center">&nbsp;</td>';
  echo '<td align="center"></td>';
  
  
$sql="
select A.appi_id, B.COUNTBY, B.PRICE3, B.NUM, B.AMT from appinfo A 
LEFT JOIN ( select countby, price3, appi_id, sum(num) as num, sum(amt) as amt, use_period 
from ( select c.countby, a.appi_id, a.num, b.price_c as price3, a.use_period, (nvl(a.num,0) * nvl(b.price_c,0)) as amt from room_use a 
left join venue_time b on a.room_id = b.room_id and a.use_period = b.price_t 
left join classroom c on a.room_id = c.room_id where a.appi_id is not null and a.use_date {$where} )  as zz
group by appi_id, use_period, countby, price3 ) B ON A.APPI_ID = B.APPI_ID AND B.USE_PERIOD = '14' 
where A.appi_id in ( select distinct appi_id from room_use where use_period in ('11','12','13','14')
 and appi_id is not null and use_date {$where}) ORDER BY A.APPI_ID

";
$rsA1 = $this->db->query($sql);
$rsA1=QueryToArray($rsA1);
for ($j=0; $j < sizeof($rsA1); $j++) { 
  $fields=$rsA1[$j];
  echo '<td nowrap align="center" colspan="2">結訓餐';
    
  //add by jay
  echo $fields['PRICE3'];
  if ($fields['PRICE3'] != ""){
    echo "元";
  } 
  if ($fields['COUNTBY'] == "1"){
    echo "/人";
  }
  if ($fields['COUNTBY'] == "2"){
    echo "/桌";
  }
  if ($fields['COUNTBY'] == "3"){
    echo "/場地";
  }
  
  echo '</td>';  
  //echo '<td nowrap align="center">' . $fields['AMT'] . '</td>';
  echo '<td nowrap align="center">' . $fields['NUM'] . '</td>';
  
}
if (sizeof($rsA1) < 5){
  $space_cell = 5 - sizeof($rsA1); 
  for ($i=0;$i<$space_cell;$i++){
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
  }
}             

echo '</tr>';
//--------------------------------------------------------------------------------------------------

//末列2
//--------------------------------------------------------------------------------------------------
echo '<tr>';
  echo '<td align="center"></td>';
  echo '<td align="center"></td>';
  
$sql="
select A.appi_id, B.AMT from appinfo A 
LEFT JOIN ( select appi_id, sum(num) as num, sum(amt) as amt from 
( select a.appi_id, a.num, b.price_c as price3, a.use_period, (nvl(a.num,0) * nvl(b.price_c,0)) as amt from room_use a 
left join venue_time b on a.room_id = b.room_id and a.use_period = b.price_t where a.appi_id is not null 
and a.use_period in ('11','12','13','14')  and a.use_date {$where} ) as zz group by appi_id ) B ON A.APPI_ID = B.APPI_ID 
where A.appi_id in ( select distinct appi_id from room_use where use_period in ('11','12','13','14') 
and appi_id is not null and use_date {$where})  ORDER BY A.APPI_ID

";
$rsA1 = $this->db->query($sql);
$rsA1=QueryToArray($rsA1);
for ($j=0; $j < sizeof($rsA1); $j++) { 
  $fields=$rsA1[$j];
  echo '<td nowrap align="center" colspan="2">合計</td>';  
  echo '<td nowrap align="center">' . $fields['AMT'] . '</td>';
  
}
if (sizeof($rsA1) < 5){
  $space_cell = 5 - sizeof($rsA1); 
  for ($i=0;$i<$space_cell;$i++){
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
    echo '<td>&nbsp;</td>';
  }
}   

echo '</tr>';
//--------------------------------------------------------------------------------------------------


//結束
echo '</table></center>';
?>
</div>
<center><input type="button" value="列印" onclick="print()"></center>
</body>
</html>
<script type="text/javascript">

  function print(){
    printData("printTable");
  }

</script>