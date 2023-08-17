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

function isBanDon($db, $year, $class_no, $term, $date){
    
        $sql = "SELECT * FROM dining_info WHERE year = ? AND class_no = ? AND term = ? AND use_date = ? AND is_bandon = 'Y'";
    // }else{
        // $sql = "SELECT * FROM arrival WHERE year = ? AND class_no = ? AND term = ? AND course_date = ? AND arrival_time = '便當'";
    // }
    $query = $db->query($sql, [$year, $class_no, $term, $date]);
    return ($query->row() == null) ? false : true;
}

$d1 = $this->input->get_post('d1');
$d2 = $this->input->get_post('d2');

//$d1 = "2011-10-31";
//$d2 = "2011-11-06";
$where = "between date(".$this->db->escape(addslashes($d1)).") and date(".$this->db->escape(addslashes($d2)).")";

//sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','05');  
//exit;
//------------------------------------------------------------------------------------

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>用餐人數統計表</title>
  <script src="<?php echo base_url(HTTP_PLUGIN . 'jquery-1.12.4.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url('static/css/master.css')?>">
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
$normal_mode = '1';
if(isset($_GET['mode']) && $_GET['mode'] == 'save'){
  $normal_mode = '1';

  
  foreach ($_GET as $key => $value) {
    if($key != 'mode' && $key != 'd1' && $key != 'd2'){
      $key = addslashes($key);
      $tmp_key = explode('_',$key);

      if(count($tmp_key) == 5){
        $sql_check = sprintf("SELECT count(1) cnt FROM dining_info WHERE year = '%s' AND class_no = '%s' AND term = '%s' AND use_date = '%s'",addslashes($tmp_key[1]),addslashes($tmp_key[2]),addslashes($tmp_key[3]),addslashes($tmp_key[4]));
        $rs_check = $this->db->query($sql_check);
        $cnt_check = QueryToArray($rs_check)[0];

        if($cnt_check['cnt'] > 0){
          if($tmp_key[0] == 'totime'){
            $sql_upd = sprintf("UPDATE dining_info SET to_time = '%s' WHERE year = '%s' AND class_no = '%s' AND term = '%s' AND use_date = '%s'",addslashes($value),addslashes($tmp_key[1]),addslashes($tmp_key[2]),addslashes($tmp_key[3]),addslashes($tmp_key[4]));
            $this->db->query($sql_upd);
          } else if($tmp_key[0] == 'lcnt'){
            $sql_upd = sprintf("UPDATE dining_info SET dining_count = '%s' WHERE year = '%s' AND class_no = '%s' AND term = '%s' AND use_date = '%s'",addslashes($value),addslashes($tmp_key[1]),addslashes($tmp_key[2]),addslashes($tmp_key[3]),addslashes($tmp_key[4]));
            $this->db->query($sql_upd);
          }
        } else {
          if($tmp_key[0] == 'totime'){
            $sql_insert = sprintf("INSERT INTO dining_info(year,class_no,term,to_time,dining_count,use_date) VALUES('%s','%s','%s','%s','%s','%s')",addslashes($tmp_key[1]),addslashes($tmp_key[2]),addslashes($tmp_key[3]),addslashes($value),0,addslashes($tmp_key[4]));
            $this->db->query($sql_insert);
          } else if($tmp_key[0] == 'lcnt'){
            $sql_insert = sprintf("INSERT INTO dining_info(year,class_no,term,to_time,dining_count,use_date) VALUES('%s','%s','%s','%s','%s','%s')",addslashes($tmp_key[1]),addslashes($tmp_key[2]),addslashes($tmp_key[3]),'',addslashes($value),addslashes($tmp_key[4]));
            $this->db->query($sql_insert);
          }
        }
      }
    }
  }
} else if(isset($_GET['mode']) && $_GET['mode'] == 'recovery'){
  $normal_mode = '1';
  foreach ($_GET as $key => $value) {
    if($key != 'mode' && $key != 'd1' && $key != 'd2'){
      $key = $this->db->escape(addslashes($key));
      $tmp_key = explode('_',$key);

      if(count($tmp_key) == 5){
        $sql_del = sprintf("DELETE FROM dining_info WHERE year = '%s' AND class_no = '%s' AND term = '%s' AND use_date = '%s'",addslashes($tmp_key[1]),addslashes($tmp_key[2]),addslashes($tmp_key[3]),addslashes($tmp_key[4]));
        $this->db->query($sql_del);
      }
    }
  }
} else if(isset($_GET['mode']) && $_GET['mode'] == 'edit'){
  $normal_mode = '0';
}

//條件
$url = base_url('meal/eat_money_count?d1=').htmlspecialchars($d1, ENT_HTML5|ENT_QUOTES)."&d2=". htmlspecialchars($d2, ENT_HTML5|ENT_QUOTES)."&action=print2";
echo "<form id='sendq' action='".$url."' method='get'><center><div>從 ".htmlspecialchars($d1, ENT_HTML5|ENT_QUOTES)." 至 ".htmlspecialchars($d2, ENT_HTML5|ENT_QUOTES)." </div></center>";

//開始
//echo '<table border="1" cellspacing="0" cellpadding="0" style="FONT-SIZE:12px;">';
echo '<center><table border="1" cellspacing="0" cellpadding="0">';

//表頭
//--------------------------------------------------------------------------------------------------
$sql = "select a.*,b.seq_no, 
(select count('x') from online_app p where yn_sel NOT IN ('2','6','7') and p.year=a.year and p.class_no=a.class_no and p.term=a.term) as no_persons
, b.room_code, nvl(c.NAME,a.worker) as worker_name " . 
       "from ( " . 
       " select distinct year, class_no, term, class_name, worker from dining_student " . 
       " where use_date {$where} " . 
       ") a " . 
       "left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term " .
       "left join view_all_account c on a.worker = c.personal_id " .
	   "where IFNULL(b.is_cancel, '0') = '0' " .
       "order by a.year, a.class_no, a.term";
$rs = $this->db->query($sql);
//echo "sql:".$sql;
//外借班(有早午晚或結訓餐的才列)
$sql = "select b.name as worker_name, a.* " .
       "from ( " .
       " select * from appinfo where appi_id in (select distinct appi_id from room_use where use_period in ('11','12','13','14') and appi_id is not null and use_date {$where} ) " . 
       ") a " .
       "left join view_all_account b on a.cre_user = b.username " . 
       "order by a.appi_id";
$rsA = $this->db->query($sql);

echo '<tr>';
  echo '<td nowrap align="center" width="40"><div style="width:60px">承辦人</div></td>';
  echo '<td align="center" width="40"><div style="width:60px"></div></td>';
  $rs=QueryToArray($rs);
  for ($i=0; $i < sizeof($rs); $i++) { 
    $fields=$rs[$i];
    echo '<td nowrap align="center" valign="top" width="50">' . $fields['worker_name'] . '</td>';
  }

  if (sizeof($rs)< 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" rowspan="3" width="30"><div style="width:1em;">長官暨講座</div></td>';
  echo '<td align="center" rowspan="3" width="40"><div style="width:1em;">請款人數</div></td>';                                                    
echo '</tr>';

// $rs->MoveFirst();
// $rsA->MoveFirst();
echo '<tr>';
  echo '<td align="center">班期</td>';
  echo '<td align="center"></td>';
  for ($i=0; $i < sizeof($rs); $i++) { 
    $fields=$rs[$i];
    echo '<td align="center" valign="top">';
    if($normal_mode == '0'){
      $course_url = base_url('create_class/print_schedule/print/').htmlspecialchars($fields['seq_no'], ENT_HTML5|ENT_QUOTES).'?&rows=10&query_year='.htmlspecialchars($fields['year'], ENT_HTML5|ENT_QUOTES).'&query_class_no='.htmlspecialchars($fields['class_no'], ENT_HTML5|ENT_QUOTES).'&query_class_name='.htmlspecialchars($fields['class_name'], ENT_HTML5|ENT_QUOTES);
      echo '<div style="height:150px;layout-flow:vertical-ideographic;text-align:left"><a target="_blank" href="'.$course_url.'">' . htmlspecialchars($fields['class_name'], ENT_HTML5|ENT_QUOTES) . '第' . htmlspecialchars($fields['term'], ENT_HTML5|ENT_QUOTES) . '期</a></div>';
    } else if($normal_mode == '1') {
      echo '<div style="height:150px;layout-flow:vertical-ideographic;text-align:left">' . htmlspecialchars($fields['class_name'], ENT_HTML5|ENT_QUOTES) . '第' . htmlspecialchars($fields['term'], ENT_HTML5|ENT_QUOTES). '期</div>';
    }
   
    echo '</td>';
  }

  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }   
echo '</tr>';

// $rs->MoveFirst();
// $rsA->MoveFirst();
echo '<tr>';
  echo '<td align="center">調訓人數</td>';
  echo '<td align="center"></td>';
  for ($i=0; $i < sizeof($rs); $i++) { 
    $fields=$rs[$i];
    echo '<td align="center" valign="top">' . $fields['no_persons'] . '</td>';
  }

  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }     
echo '</tr>';

// $rs->MoveFirst();
// $rsA->MoveFirst();
echo '<tr>';
  echo '<td align="center">桌次</td>';
  echo '<td align="center"></td>';
  for ($i=0; $i < sizeof($rs); $i++) { 
    $fields=$rs[$i];
    echo '<td align="center" valign="top"></td>';
  }

  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30"></td>';
  echo '<td align="center" width="40"></td>';
                                           
echo '</tr>';
//--------------------------------------------------------------------------------------------------

//週期資料
//--------------------------------------------------------------------------------------------------
$totAll1 = 0;
$totAll2 = 0;
$totAll3 = 0;
$year=intval(substr($d1,0,4))-1911;
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

for ($i=0; $i < sizeof($rs1); $i++) { 
  $d1=$rs1[$i];
$query_dt = $d1['data_dt'];
$sql = "
select distinct a.*, b.no_persons, b.room_code, b.is_cancel, cr.sname
,a1.m_cnt, a1.l_cnt, a1.d_cnt, p.to_time
,replace(c1.m_name,',','<br>') as m_name, c1.m_teach_cnt
,replace(c2.l_name,',','、') as l_name, c2.l_teach_cnt
,replace(c3.d_name,',','<br>') as d_name, c3.d_teach_cnt
from
(
 select distinct year, class_no, term, class_name, worker from dining_student 
 where use_date {$where}
)a
left join 
(
 select year, class_no, term, class_name
 ,(nvl(persons_1,0) + nvl(add_persons_1,0)) as m_cnt
 ,(nvl(persons_2,0) + nvl(add_persons_2,0)) as l_cnt
 ,(nvl(persons_3,0) + nvl(add_persons_3,0)) as d_cnt
 from dining_student 
 where use_date = date(".$this->db->escape(addslashes($query_dt)).")
)a1 on a.year = a1.year and a.class_no = a1.class_no and a.term = a1.term
left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term
left join 
(
select R.year, R.class_id, R.term, use_date, min(R.use_period) as min_use_period from room_use R where R.use_id!='O00003'
 group by R.year, R.class_id, R.term, use_date having R.use_date = date(".$this->db->escape(addslashes($query_dt)).") 
)rr on a.year = rr.year and a.class_no = rr.class_id and a.term = rr.term   
LEFT JOIN room_use ru on ru.year = ru.year and ru.class_id = rr.class_id and ru.term = rr.term and ru.use_period=rr.min_use_period and  ru.use_date = date(".$this->db->escape(addslashes($query_dt)).")
LEFT JOIN classroom cr on cr.room_id = ru.room_id
left join
(
 select year, class_no, term, use_date,GROUP_CONCAT(name) as m_NAME, count(*) as m_teach_cnt
 from dining_teacher
 where use_date = date(".$this->db->escape(addslashes($query_dt)).") and dining_type = 'A'
 group by year, class_no, term, use_date
)c1 on a.year = c1.year and a.class_no = c1.class_no and a.term = c1.term
left join
(
 select year, class_no, term, use_date, GROUP_CONCAT(name) as l_name, count(*) as l_teach_cnt
 from dining_teacher
 where use_date = date(".$this->db->escape(addslashes($query_dt)).") and dining_type = 'B'
 group by year, class_no, term, use_date
)c2 on a.year = c2.year and a.class_no = c2.class_no and a.term = c2.term
left join
(
 select year, class_no, term, use_date, GROUP_CONCAT(name) as d_NAME, count(*) as d_teach_cnt
 from dining_teacher
 where use_date = date(".$this->db->escape(addslashes($query_dt)).") and dining_type = 'C'
 group by year, class_no, term, use_date
)c3 on a.year = c3.year and a.class_no = c3.class_no and a.term = c3.term
left join
(
 select * from (select year, class_no, term, case when '1130' between from_time and to_time then 'Y' end is_lunch , to_time  from periodtime where year='{$year}' 
 and course_date=date(".$this->db->escape(addslashes($query_dt))."))df where is_lunch = 'Y'
)p on a.year = p.year and a.class_no = p.class_no and a.term = p.term
where IFNULL(b.is_cancel,'0')='0' order by a.year, a.class_no, a.term
";

$rs2 = $this->db->query($sql);
$rs2=QueryToArray($rs2);


/*
echo '<tr>';
  echo '<td align="center" rowspan="6">' . $d1['DT_NM'] . '<br>' . $weekNM[$d1['WEEK_NM']] . '</td>';
  echo '<td align="center" height="40" rowspan="2">早餐</td>';
  $rs2->MoveFirst();  
  while($d2 = $rs2->FetchRow()){
    echo '<td align="center" valign="top">';
    echo $d2['M_NAME'] . "<br>";
    echo '</td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">';
  echo '</td>';
  echo '<td align="center" width="40">&nbsp;';
  echo '</td>';
   
echo '</tr>'; 
echo '<tr>';
  $tot1 = 0;
  $tot2 = 0;
  $rs2->MoveFirst();  
  while($d2 = $rs2->FetchRow()){
    echo '<td align="center" valign="top">';
    if($d2['M_CNT']!=0){echo $d2['M_CNT'] . "<br>";}
    echo '</td>';
    if($d2['M_TEACH_CNT']!=""){$tot1 = $tot1 + $d2['M_TEACH_CNT'];}
    if($d2['M_CNT']!=""){$tot2 = $tot2 + $d2['M_CNT'];}
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  $totAll1 = $totAll1 + $tot1 + $tot2; 
  echo '<td align="center" width="30">';
  if ($tot1>0){echo $tot1;}
  echo '</td>';
  echo '<td align="center" width="40">';
  echo $tot1 + $tot2;
  echo '</td>';
  
echo '</tr>';   
*/
echo '<tr>';
  echo '<td align="center" rowspan="4">' . $d1['dt_nm'] . '<br>' . $weekNM[$d1['week_nm']] . '</td>';
  echo '<td align="center" height="40" rowspan="4">午餐</td>';
  // $rs2->MoveFirst();
  $year_tmp = '';
  $classno_tmp = '';
  $term_tmp = '';
   for ($j=0; $j < sizeof($rs2); $j++) { 
    $d2=$rs2[$j];
    if($d2['year'] == $year_tmp && $d2['class_no'] == $classno_tmp && $d2['term'] == $term_tmp){
      continue;
    } else {
      $year_tmp = $d2['year'];
      $classno_tmp = $d2['class_no'];
      $term_tmp = $d2['term'];
    }

    $isBanDon = isBanDon($this->db, $d2['year'], $d2['class_no'], $d2['term'], $query_dt);

    $td_color = ($isBanDon) ? 'background-color: yellow' : '';

    echo '<td align="center" valign="top" style="'.$td_color.'">';
  	/*教室名稱簡化:
        電腦教室->電
        國際會議廳->國
        大禮堂->大
        教室->[空白]
  	*/
  	$d2['sname']=str_replace("電腦教室", "電", $d2['sname']);
  	$d2['sname']=str_replace("國際會議廳", "國", $d2['sname']);
  	$d2['sname']=str_replace("大禮堂", "大", $d2['sname']);
  	$d2['sname']=str_replace("教室", "", $d2['sname']);

    if(!empty($d2['to_time'])){
      echo $d2['l_name'];
    }
    
    echo '</td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($j=0;$j<$space_cell;$j++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">';
  echo '</td>';
  echo '<td align="center" width="40">&nbsp;';
  echo '</td>';
     
echo '</tr>';

echo '<tr>';
// $rs2->MoveFirst();
  $year_tmp = '';
  $classno_tmp = '';
  $term_tmp = '';  
  for ($j=0; $j < sizeof($rs2); $j++) { 
    $d2=$rs2[$j];
    if($d2['year'] == $year_tmp && $d2['class_no'] == $classno_tmp && $d2['term'] == $term_tmp){
      continue;
    } else {
      $year_tmp = $d2['year'];
      $classno_tmp = $d2['class_no'];
      $term_tmp = $d2['term'];
    }

    $isBanDon = isBanDon($this->db, $d2['year'], $d2['class_no'], $d2['term'], $query_dt);

    $td_color = ($isBanDon) ? 'background-color: yellow' : '';

    echo '<td align="center" valign="top" style="'.$td_color.'">';
    /*教室名稱簡化:
        電腦教室->電
        國際會議廳->國
        大禮堂->大
        教室->[空白]
    */
    $d2['sname']=str_replace("電腦教室", "電", $d2['sname']);
    $d2['sname']=str_replace("國際會議廳", "國", $d2['sname']);
    $d2['sname']=str_replace("大禮堂", "大", $d2['sname']);
    $d2['sname']=str_replace("教室", "", $d2['sname']);

    if(!empty($d2['to_time'])){
      echo $d2['sname'];
    }
    
    echo '</td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($j=0;$j<$space_cell;$j++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">';
  echo '</td>';
  echo '<td align="center" width="40">&nbsp;';
  echo '</td>';
     
echo '</tr>';


  // $rs2->MoveFirst();
  $year_tmp = '';
  $classno_tmp = '';
  $term_tmp = '';    
  for ($j=0; $j < sizeof($rs2); $j++) { 
    $d2=$rs2[$j];
    if($d2['year'] == $year_tmp && $d2['class_no'] == $classno_tmp && $d2['term'] == $term_tmp){
      continue;
    } else {
      $year_tmp = $d2['year'];
      $classno_tmp = $d2['class_no'];
      $term_tmp = $d2['term'];
    }

    
    if ($d2['sname']!="" && !empty($d2['to_time'])) {
        $time_key = 'totime_'.$d2['year'].'_'.$d2['class_no'].'_'.$d2['term'].'_'.$query_dt;

        $sql_info = sprintf("SELECT to_time FROM dining_info WHERE year = '%s' AND class_no = '%s' AND term = '%s' AND use_date = '%s'",$d2['year'],$d2['class_no'],$d2['term'],$query_dt);
        $rs_info = $this->db->query($sql_info);
        $rs_info=QueryToArray($rs_info);

        if(sizeof($rs_info)!=0){
            $totime_info = $rs_info[0];
        }else{
            $totime_info = $rs_info;
        }

        $isBanDon = isBanDon($this->db, $d2['year'], $d2['class_no'], $d2['term'], $query_dt);

        $td_color = ($isBanDon) ? 'background-color: yellow' : '';

        if(!empty($totime_info) && $totime_info['to_time'] != substr($d2['to_time'],0,2).":".substr($d2['to_time'],2,2)){
            $td_color = "background-color: #FF8888";
        }
        
        echo '<td align="center" valign="top" style="'.$td_color.'">';


        if(!empty($totime_info)){
            $time_value = $totime_info['to_time'];
        } else {
            $time_value = substr($d2['to_time'],0,2).":".substr($d2['to_time'],2,2);
        }

        if($normal_mode == '0'){
            echo '<input type="text" id="'.$time_key.'" name="'.$time_key.'" value="'.$time_value.'" size="1">';
        } else if($normal_mode == '1') {
            echo $time_value;
        }
      
	} else {
        echo '<td align="center" valign="top">';  
        echo '&nbsp';
    }
    echo '</td>';
  }

  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($j=0;$j<$space_cell;$j++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">';
  echo '</td>';
  echo '<td align="center" width="40">&nbsp;';
  echo '</td>';
     
echo '</tr>';

echo '<tr>';
  $tot1 = 0;
  $tot2 = 0;
  // $rs2->MoveFirst();
  $year_tmp = '';
  $classno_tmp = '';
  $term_tmp = '';    
  for ($j=0; $j < sizeof($rs2); $j++) { 
    $d2=$rs2[$j];
    if($d2['year'] == $year_tmp && $d2['class_no'] == $classno_tmp && $d2['term'] == $term_tmp){
      continue;
    } else {
      $year_tmp = $d2['year'];
      $classno_tmp = $d2['class_no'];
      $term_tmp = $d2['term'];
    }

    
    if($d2['l_cnt']!=0 && !empty($d2['to_time'])){
        $lcnt_key = 'lcnt_'.$d2['year'].'_'.$d2['class_no'].'_'.$d2['term'].'_'.$query_dt;

        $sql_info = sprintf("SELECT dining_count FROM dining_info WHERE year = '%s' AND class_no = '%s' AND term = '%s' AND use_date = '%s'",$d2['year'],$d2['class_no'],$d2['term'],$query_dt);
        // $sql_info = sprintf("SELECT count(*) as dining_count FROM online_app WHERE yn_sel IN ('1', '3', '8') and year= '%s' AND class_no = '%s' AND term = '%s' ",$d2['year'],$d2['class_no'],$d2['term']);
        $rs_info = $this->db->query($sql_info);
        $rs_info=QueryToArray($rs_info);
        if(sizeof($rs_info)!=0){
            $lcnt_info = $rs_info[0];
        }else{
            $lcnt_info = $rs_info;
        }
      

        $isBanDon = isBanDon($this->db, $d2['year'], $d2['class_no'], $d2['term'], $query_dt);

        $td_color = ($isBanDon) ? 'background-color: yellow' : '';

        if (!empty($lcnt_info) && $lcnt_info['dining_count'] != $d2['l_cnt']){
          $td_color = 'background-color: #FF8888';
        }
          
        echo '<td align="center" valign="top" style="'.$td_color.'">';


        if(!empty($lcnt_info)){
            $lcnt_value = $lcnt_info['dining_count'];
            $d2['l_cnt'] = $lcnt_info['dining_count'];
        } else {
            $lcnt_value = $d2['l_cnt'];
        }
         
        if($normal_mode == '0'){
            echo '<input type="text" id="'.$lcnt_key.'" name="'.$lcnt_key.'" value="'.$lcnt_value.'" size="1"><br>';
        } else if($normal_mode == '1') {
            echo $lcnt_value.'<br>';
        }

        
            $bandonText = ($isBanDon) ? "取消便當" : "便當"; 
            echo "<button type=\"button\" onclick=\"actionBanDon('{$d2['year']}', '{$d2['class_no']}', '{$d2['term']}', '{$query_dt}')\">{$bandonText}</button>";
        // }

        echo '</td>';
    }else{
        echo '<td align="center" valign="top"></td>';
    }
    
    if($d2['l_teach_cnt']!="" && $d2['l_name'] != '教務組'){$tot1 = $tot1 + $d2['l_teach_cnt'];}
    if($d2['l_cnt']!=""){$tot2 = $tot2 + $d2['l_cnt'];}
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($j=0;$j<$space_cell;$j++){
      echo '<td width="50"></td>';
    }
  }
  $totAll2 = $totAll2 + $tot1 + $tot2; 
  echo '<td align="center" width="30">';
  if ($tot1>0){echo $tot1;}
  echo '</td>';
  echo '<td align="center" width="40">';
  echo $tot1 + $tot2;
  echo '</td>';
      
echo '</tr>';   

/*
echo '<tr>';
  echo '<td align="center" height="40" rowspan="2">晚餐</td>';
  $rs2->MoveFirst();  
  while($d2 = $rs2->FetchRow()){
    echo '<td align="center" valign="top">';
    echo $d2['D_NAME'] . "<br>";
    echo '</td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">';
  echo '</td>';
  echo '<td align="center" width="40">&nbsp;';
  echo '</td>';
        
echo '</tr>';  
echo '<tr>';
  $tot1 = 0;
  $tot2 = 0;
  $rs2->MoveFirst();  
  while($d2 = $rs2->FetchRow()){
    echo '<td align="center" valign="top">';
    if($d2['D_CNT']!=0){echo $d2['D_CNT'] . "<br>";}
    echo '</td>';
    if($d2['D_TEACH_CNT']!=""){$tot1 = $tot1 + $d2['D_TEACH_CNT'];}
    if($d2['D_CNT']!=""){$tot2 = $tot2 + $d2['D_CNT'];}
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  $totAll3 = $totAll3 + $tot1 + $tot2; 
  echo '<td align="center" width="30">';
  if ($tot1>0){echo $tot1;}
  echo '</td>';
  echo '<td align="center" width="40">';
  echo $tot1 + $tot2;
  echo '</td>';
        
echo '</tr>'; 
*/
}

//--------------------------------------------------------------------------------------------------

//加總
//--------------------------------------------------------------------------------------------------
/*
$rs->MoveFirst();
echo '<tr>';
  echo '<td align="center">備註欄</td>';
  echo '<td align="center"></td>';
  while($fields = $rs->FetchRow()){
    echo '<td align="center" valign="top"></td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">早餐</td>';
  echo '<td align="center" width="40">';
  echo $totAll1;
  echo '</td>';
   
echo '</tr>';
*/
// $rs->MoveFirst();
echo '<tr>';
  echo '<td align="center"></td>';
  echo '<td align="center"></td>';
  for ($i=0; $i < sizeof($rs); $i++) { 
    $fields=$rs[$i];
    echo '<td align="center" valign="top"></td>';
  }

  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">午餐</td>';
  echo '<td align="center" width="40">';
  echo $totAll2;
  echo '</td>'; 
  

echo '</tr>';

/*
$rs->MoveFirst();
echo '<tr>';
  echo '<td align="center"></td>';
  echo '<td align="center"></td>';
  while($fields = $rs->FetchRow()){
    echo '<td align="center" valign="top"></td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">晚餐</td>';
  echo '<td align="center" width="40">';
  echo $totAll3;
  echo '</td>';


echo '</tr>';
*/
//--------------------------------------------------------------------------------------------------

//末列1
//--------------------------------------------------------------------------------------------------
// $rs->MoveFirst();
echo '<tr>';
  echo '<td align="center">&nbsp;</td>';
  echo '<td align="center"></td>';
  for ($i=0; $i < sizeof($rs); $i++) { 
    $fields=$rs[$i];
    echo '<td align="center" valign="top"></td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30"></td>';
  echo '<td align="center" width="40"></td>';
  

echo '</tr>';
//--------------------------------------------------------------------------------------------------

//末列2
//--------------------------------------------------------------------------------------------------
/*
$sql = "select nvl(sum(tot),0) from ( " . 
       "select nvl(amount_1,0) + nvl(amount_2,0) + nvl(amount_3,0) as tot from dining_student " . 
       "where use_date {$where} " .
       ")";
*/	   
// $sql = "select nvl(sum(tot),0) from ( " . 
//        "select nvl(amount_2,0) as tot from dining_student " . 
//        "where use_date {$where} " .
//        ")";

$sql = "select add_val1 from code_table where type_id = '25' and item_id = 'B'";       
$unit_price = QueryToArray($this->db->query($sql))[0]['add_val1'];

$totAmt = $unit_price * $totAll2;

// $rs->MoveFirst();
echo '<tr>';
  echo '<td align="center">上課教室</td>';
  echo '<td align="center"></td>';
  for ($i=0; $i < sizeof($rs); $i++) { 
    $fields=$rs[$i];
    echo '<td align="center" valign="top">' . $fields['room_code'] . '</td>';
  }
  if (sizeof($rs) < 15){
    $space_cell = 15 - sizeof($rs); 
    for ($i=0;$i<$space_cell;$i++){
      echo '<td width="50"></td>';
    }
  }
  echo '<td align="center" width="30">金額</td>';
  echo '<td align="center" width="40">';
  echo $totAmt;
  echo '</td>';

echo '</tr>';
//--------------------------------------------------------------------------------------------------


//結束
if($normal_mode == '1'){
  $url = base_url('meal/eat_money_count/exportXlsx?start_date=').htmlspecialchars($_REQUEST['d1'], ENT_HTML5|ENT_QUOTES).'&end_date='.htmlspecialchars($_REQUEST['d2'], ENT_HTML5|ENT_QUOTES);
  echo '</table><br>
        <input type="hidden" id="mode" name="mode" value="">
        <input type="hidden" name="d1" value="'.htmlspecialchars($_REQUEST['d1'], ENT_HTML5|ENT_QUOTES).'">
        <input type="hidden" name="d2" value="'.htmlspecialchars($_REQUEST['d2'], ENT_HTML5|ENT_QUOTES).'">
        <input type="hidden" name="action" value="print2">
        <input type="button" value="編輯" onclick="editFun()">
        <a href="'.$url.'"><input type="button" value="匯出 Excel"></a>
      </center></form>';
} else if($normal_mode == '0'){
  echo '</table><br>
        <input type="hidden" id="mode" name="mode" value="">
        <input type="hidden" name="d1" value="'.htmlspecialchars($_REQUEST['d1'], ENT_HTML5|ENT_QUOTES).'">
        <input type="hidden" name="d2" value="'.htmlspecialchars($_REQUEST['d2'], ENT_HTML5|ENT_QUOTES).'">
        <input type="hidden" name="action" value="print2">
        <input type="button" value="復原" onclick="recoveryFun()">
        <input type="button" value="儲存" onclick="sendFun()">
      </center></form>';
}
//<center><input type="button" value="列印" onclick="print()"></center>
?>
</div>

<form id="bandon" method="POST" action="<?=base_url('/meal/eat_money_count/bandon')?>">
  <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
  <input type="hidden" name="bandonYear">
  <input type="hidden" name="bandonClass_no">
  <input type="hidden" name="bandonTerm">
  <input type="hidden" name="bandonCourse_date">
</form>

</body>
</html>

<script type="text/javascript">
  function sendFun() {
    var obj =  document.getElementById('sendq');
    document.getElementById('mode').value = 'save';

    obj.submit();
  }

  function recoveryFun(){
    var obj =  document.getElementById('sendq');
    document.getElementById('mode').value = 'recovery';

    obj.submit();
  }

  function editFun(){
    var obj =  document.getElementById('sendq');
    document.getElementById('mode').value = 'edit';

    obj.submit();
  }
  function print(){
    printData("printTable");
  }

  function actionBanDon(year, class_no, term, course_date)
  {
    $("input[name=bandonYear]").val(year);
    $("input[name=bandonClass_no]").val(class_no);
    $("input[name=bandonTerm]").val(term);
    $("input[name=bandonCourse_date]").val(course_date);
    $("#bandon").submit();
  }
</script>