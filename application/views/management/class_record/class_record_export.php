<?php
// sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','08');

$outputcsv=stripslashes($outputcsv);

if (ctype_alnum($_POST['out_filename'])){

	$filename =date('Y-m-d')."-".str_replace(array("\r", "\n"), "", urlencode(iconv( "UTF-8", "Big5" , $_POST["out_filename"]))).".xls";
	header("Content-disposition:filename=$filename");
	header("Content-type: application/vnd.ms-excel';charset=utf-8");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo "
		<html>
		<meta http-equiv=\"Content-Type\" content=\"application/x-excel; charset=utf-8\">
		<body><table border=1>".$outputcsv."</table></body><html>";

}
//readfile($filename);

?>