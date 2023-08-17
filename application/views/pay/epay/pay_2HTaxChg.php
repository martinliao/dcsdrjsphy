<?php
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
// include "init.inc.php";
//$db->debug = true;

//接值
//------------------------------------------------------------------------------------
$key = $this->input->get('key');
$p_2htax = empty($this->input->get('p_2htax')) ? "" : $this->input->get('p_2htax');
$doAction = isset($_GET['doAction'])?$_GET['doAction']:"";
//------------------------------------------------------------------------------------

$sql = "select date_format(bill_date,'%Y-%m-%d') as bill_date, teacher_id, hour_fee from hour_bill where id = ".$this->db->escape(addslashes($key))."";
$rs = $this->db->query($sql);
$fields = QueryToArray($rs)[0];
$bdat = $fields['bill_date'];
$hfee = $fields['hour_fee'];
$tid = $fields['teacher_id'];

//儲存
//------------------------------------------------------------------------------------
if ($doAction=="save"){
/*
  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
  echo "<script>";
  echo 'alert("key:'.$key.',tax:'.$p_2htax.'")';
  echo "</script>";
*/  
  $sql = "Delete From is_2htax Where " . 
         "date_format(bill_date,'%Y-%m-%d') = ".$this->db->escape(addslashes($bdat))." and " . 
         "teacher_id = ".$this->db->escape(addslashes($tid))." " ;
  $this->db->query($sql); 
		 
  // 免扣二代健保
  if ($p_2htax == 'y')
  {
    // mark need'nt pay tax by recording data to is_2htax
	$sql = "Insert Into is_2htax  " . 
         "(teacher_id, bill_date) " .
		 "Values " .
		 "(".$this->db->escape(addslashes($tid)).", " .
         "date(".$this->db->escape(addslashes($bdat)).")) ";
	$this->db->query($sql); 
	
	// upadte h_tax and h_tax_rate to 0
    $sql = "UPDATE hour_bill SET " . 
          "h_tax = 0, h_tax_rate = 0 " . 
          "WHERE id = ".$this->db->escape(addslashes($key))."";
	$this->db->query($sql); 
  } else {
  
	// upadte hour_fee and h_tax_rate to 0
    $sql = "select * from co_tax where h_tax is not null";
	$rs = $this->db->query($sql);
	$fields = QueryToArray($rs)[0];
	$htax = $fields['H_TAX'];
	$htax_rate = $fields['H_TAX_RATE'];

	
	if ($hfee > $htax) {
	    // upadte h_tax and h_tax_rate
	    $h_tax = $htax_rate * $hfee;
		$sql = "UPDATE hour_bill SET " . 
			   "h_tax = " . $this->db->escape(addslashes($h_tax)) . ", h_tax_rate = " . $this->db->escape(addslashes($htax_rate)) .
               " WHERE id = ".$this->db->escape(addslashes($key))."";
	    $this->db->query($sql); 
	}
	
  }
  
  //sys_log_insert_3(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','02','稅');    
 
  echo "<script>";
  // echo 'obj = window.opener.document.getElementById("actQuery");';
  // echo 'obj.submit();';
  echo "alert('更新成功！');";
  echo "window.opener.location.reload(false);";
  echo "window.close();";
  echo "</script>";
  exit;
}
//------------------------------------------------------------------------------------

//查詢
//------------------------------------------------------------------------------------

$cnt = QueryToArray($this->db->query("select count(*) as cnt from is_2htax where date_format(bill_date,'%Y-%m-%d')=".$this->db->escape(addslashes($bdat))." and teacher_id=".$this->db->escape(addslashes($tid)).""))[0]['cnt'];
if ($cnt>0)
	$chkstr = "Checked";
else	
	$chkstr = "";
//------------------------------------------------------------------------------------
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>免扣二代健保費</title>
	<link type="text/css" rel="stylesheet" href="css/master.css"/>
</head>
<body>

<script>
function popupOK(x){
  //if (document.all.f_amt.value==""){
  //  alert("稅率不可空白");
  //  document.all.f_amt.focus();    
  //  return false;    
  //}
  document.all.doAction.value = "save";
  obj = document.getElementById("actAmt");
  obj.submit();  
}
</script>
<!-- <?php print_r($fields)?> -->
<form id="actAmt" method="GET" action="/base/admin/pay/epay/detail">
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#909397">
  
      <table width="100%" border="0" cellspacing="1" cellpadding="1" style="FONT-SIZE:12px;">
				<tr>
				  <td align="center" bgcolor="#E9EEF4">免扣二代健保</td>
					<td align="left" bgcolor="#ffffff">
            <?php
            // $amt = $fields['tax_rate'];
            echo '<input type="checkbox" name="p_2htax" id="p_2htax"  value="y"' . htmlspecialchars($chkstr, ENT_HTML5|ENT_QUOTES) . '>';
            ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center" height="3"></td>
        </tr>        
				<tr>
					<td colspan="3" align="center" bgcolor="#dcdcdc">
					  <input type="button" value="確定" class="button" style="FONT-SIZE:12px;" onclick="popupOK()">
      			<input type="button" value="取消" class="button" style="FONT-SIZE:12px;" onclick="window.close()">
          </td>
        </tr>        
			</table>

		</td>
	</tr>
</table>
<input type="hidden" id="act" name="act" value="hrate">
<input type="hidden" id="doAction" name="doAction" value="">
<?php
echo '<input type="hidden" id="key" name="key" value="' . htmlspecialchars($key, ENT_HTML5|ENT_QUOTES) . '">';
?>

</form>

</body>
</html>