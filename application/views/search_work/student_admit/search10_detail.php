<?php
// include "init.inc.php";
//$db->debug = true;
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
//初始INI_FUN
// ini_fun(basename(__FILE__));

//查詢
$year       = $_GET['year'];
$class_no = $_GET['class_no'];
$term       = $_GET['term'];
$type = $_GET['type'];

if($type=="") $type ='3';
if($type=='3') {
	$type_name = "錄取名冊";
	$type_filed = "錄取人數";
	$type_condi = " yn_sel NOT IN ('2', '6', '7') ";
}
else	
{	
	$type_name = "未錄取名冊";
	$type_filed = "未錄取人數";
	$type_condi = " yn_sel IN ('2') ";
}

$sql = "select o.*,v.name,nvl(og.ou_gov ,BC.name) AS description
				from online_app o 
				left join BS_user v on o.id=v.idno 
				LEFT JOIN bureau BC ON BC.bureau_id=v.bureau_id 
				LEFT outer JOIN out_gov og on v.idno = og.ID
				where o.year=".$this->db->escape(addslashes($year))." and o.class_no=".$this->db->escape(addslashes($class_no))." and o.term=".$this->db->escape(addslashes($term))."  and {$type_condi}";
	$rsAll = $this->db->query($sql);
	$rsAll = QueryToArray($rsAll);
	$page_size = 30;
	$total_query_records = sizeof($rsAll);
	$total_page = ceil($total_query_records / $page_size);
	$cur_page   = ($_GET['p'] == '') ? 1 : intval($_GET['p']);
	$rdsBegIdx = 1;
	$rdsEndIdx = $page_size;
	if ($cur_page <= 0)
	{
		$cur_page = 0;
		$rdsEndIdx = $total_query_records;
	}
	else if ($cur_page > $total_page)
	{
		$cur_page = 1;
		$rdsBegIdx = (($cur_page-1)*$page_size) ;
		$rdsEndIdx = $page_size;
	}
	else if ($cur_page <= $total_page)
	{
		$rdsBegIdx = (($cur_page-1)*$page_size ) +1 ;
		$rdsEndIdx = $cur_page*$page_size;		
	}
	if ($total_query_records==0){
	  $cur_page = 0;
	}
	
	$p1 = (($cur_page==1) || ($cur_page==0)) ? "disabled" : 'onclick="page(-1)"';
	$p2 = (($cur_page==1) || ($cur_page==0)) ? "disabled" : 'onclick="page(-2)"';
	$p3 = (($cur_page==$total_page) || ($cur_page==0)) ? "disabled" : 'onclick="page(-3)"';
	$p4 = (($cur_page==$total_page) || ($cur_page==0)) ? "disabled" : 'onclick="page(-4)"';
	
	// $sql = "SELECT * FROM (SELECT Z.* FROM (" . $sql . ") Z limit " . $rdsBegIdx . "," . $rdsEndIdx . ")a";  
	$sql = "SELECT * FROM (SELECT Z.* FROM (" . $sql . ") Z )a";  
	$rs = $this->db->query($sql);
	$rs = QueryToArray($rs);
	// sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','01');  

//------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>名冊</title>
	<script type="text/javascript" src="calendar/js/mootools.js"></script>
	<script type="text/javascript" src="calendar/js/calendar.js"></script>    
	<link rel="stylesheet" type="text/css" href="calendar/css/calendar.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="calendar/css/alternate.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="css/master.css"/>
</head>
<body>

<form id="actQuery" method="get" action="/base/admin/search_work/student_admit?act=detail">
    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year, ENT_HTML5|ENT_QUOTES);?>" />
    <input type="hidden" name="class_no" value="<?php echo htmlspecialchars($class_no, ENT_HTML5|ENT_QUOTES);?>" />
    <input type="hidden" name="term" value="<?php echo htmlspecialchars($term, ENT_HTML5|ENT_QUOTES);?>" />
    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type, ENT_HTML5|ENT_QUOTES);?>" />

<div style="color:green;font-size:150%;"><b><? echo $type_name; ?></b></div>
<!-- <div class='page_info'>
<?php
echo "第 " . $cur_page . " 頁, 共 " . $total_page . " 頁";
?>
</div> -->
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#eeeeee">
  
      <table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr>
				  <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">服務單位</font></td>
				  <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">學號</font></td>
				  <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
				</tr>
        <?php
		for ($i=0; $i < sizeof($rs); $i++) { 
			$fields=$rs[$i];
			//$col = ($col == '#ffffff') ? '#dcdcdc' : '#ffffff';
			$col= '#ffffff';
          	echo "<tr>";
          	echo '<td align="center" bgcolor="' . $col . '">' . $fields['description'] . '</td>';
          	echo '<td align="center" bgcolor="' . $col . '">' . $fields['st_no'] . '</td>';
          	echo '<td align="center" bgcolor="' . $col . '">' . $fields['name'] . '</td>';
          	echo "</tr>";
        }
        ?>     
			</table>

		</td>
	</tr>
</table>
<div>
<!-- <?php
echo '<input type="button" value="首頁" class="button" ' . $p1 . '>' . "\n";
echo '<input type="button" value="上頁" class="button" ' . $p2 . '>' . "\n";
echo '<input type="button" value="下頁" class="button" ' . $p3 . '>' . "\n";
echo '<input type="button" value="末頁" class="button" ' . $p4 . '>' . "\n";
echo '<input type="hidden" name="p" id="p" value="' . $cur_page . '">';
?> -->
</div>

</form>

</body>
</html>

<script>
$( document ).ready(function() {
    $("#menu-toggle").click();
	$(".navbar").css("display", "none");
    $(".page-header").css("display", "none");
    $(".footer").css("display", "none");
});

function page(n){
  <?php
	echo "var cur_page = " . $cur_page . ";"; 
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

	obj.p.value = tmp;
    obj.submit();
}
</script>