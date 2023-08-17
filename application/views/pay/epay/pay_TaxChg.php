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
$doAction = isset($_GET['doAction'])?$_GET['doAction']:"";
//------------------------------------------------------------------------------------

//儲存
//------------------------------------------------------------------------------------
if ($doAction=="save"){

  $f_amt = $this->input->get('f_amt');

  $sql = "UPDATE hour_bill SET " . 
         "tax_rate = ".$this->db->escape(addslashes($f_amt)).", " . 
         "tax = hour_fee * ".$this->db->escape(addslashes($f_amt))." " .
         "WHERE id = ".$this->db->escape(addslashes($key))."";
  $this->db->query($sql); 

  $sql = "UPDATE hour_bill SET " . 
         "AFTERTAX = SUBTOTAL - tax " . 
         "WHERE id = ".$this->db->escape(addslashes($key))."";
  $this->db->query($sql); 
	// sys_log_insert_3(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','02','稅');    
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
$sql = "select * from hour_bill where id = ".$this->db->escape(addslashes($key))."";
$rs = $this->db->query($sql);
$fields = QueryToArray($rs)[0];
//------------------------------------------------------------------------------------
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>稅率修改</title>
	<link type="text/css" rel="stylesheet" href="css/master.css"/>
</head>
<body>

<script>
function popupOK(x){
  if (document.all.f_amt.value==""){
    alert("稅率不可空白");
    document.all.f_amt.focus();    
    return false;    
  }
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
				  <td align="center" bgcolor="#E9EEF4">請輸入稅率<br>(例:5%請輸入0.05)</td>
					<td align="left" bgcolor="#ffffff">
            <?php
            $amt = $fields['tax_rate'];
            echo '<input type="text" name="f_amt" id="f_amt" size="6" value="' . $amt . '">';
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
<input type="hidden" id="act" name="act" value="rate">
<input type="hidden" id="doAction" name="doAction" value="">
<?php
echo '<input type="hidden" id="key" name="key" value="' . htmlspecialchars($key, ENT_HTML5|ENT_QUOTES) . '">';
?>

</form>

</body>
</html>