<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>研習記錄表</title>

<style type="text/css">
	table {
	border: 1px solid #000;
	border-collapse: collapse;
	border-spacing: 0;
	font-size:12px;
	}
	tr, td {
	border: 1px solid #000;
	}
</style>	

</head>
<body>
<center>
	<div style="font-size:120%;">
		<b>
			臺北市政府公務人員訓練處          研習紀錄表
			<br>
			<?=$class_info->year?> 年度 <?=$class_info->class_name?> 第 <?=$class_info->term?> 期
			<br>
		</b>
	</div>
	<div align="right" style="font-size:110%;">
		<b>
			【上課日期：<?=$coursedate_list?>】
		</b>
	</div>
</center>
<table cellspacing="0" cellpadding="0" width="100%" align="center">
 <tr >
  <td style="font-size:110%;" height="30"  align="center" width="50">學號</td>
  <td style="font-size:110%;" align="center" width="150">局處名稱</td>
  <td style="font-size:110%;" align="center" width="80">姓名</td>
  <td style="font-size:110%;" align="center" width="80">缺席情形</td>
  <td style="font-size:110%;" align="center" width="80">請假日期</td>
  <td style="font-size:110%;" align="center" width="80">請假時間</td>
  <td style="font-size:110%;" align="center" width="50">時數</td>
  <td style="font-size:110%;" align="center" width="70">備註</td>

 </tr>
<?php $last_st_no="";?>
<?php $last_description="";?>
<?php $last="";?>
<?php foreach($learns as $learn):?>

	<tr>
		<?php if ($last_st_no != $learn->st_no):?>
			<td style="font-size:110%;" height=47 align="center">
					<?=$learn->st_no?>
					<?php $last_st_no = $learn->st_no?>
			</td>
			<td style="font-size:110%;" align="center">
					<?=$learn->description?>
			</td>
			<td style="font-size:110%;" align="center">
					<?=$learn->name?>			
			</td>
		<?php else: ?>
			<td style="font-size:110%;" height=47 align="center"></td>
			<td style="font-size:110%;" align="center"></td>
			<td style="font-size:110%;" align="center"></td>			
		<?php endif?>

		
		<td style="font-size:110%;" align="center"><?=$learn->va_code_text?></td>
		<td style="font-size:110%;" align="center"><?=$learn->vacation_date?></td>
		<td style="font-size:110%;" align="center"><?=$learn->time?></td>
		<td style="font-size:110%;" align="center"><?=$learn->hours?></td>
		
		
		<td style="font-size:110%;" align="center">
			<?php 
			
			$remark = "";
			if($learn->yn_sel == 4){
				$remark .= '退訓';
			} else if($learn->yn_sel == 5){
				$remark .= '未報到';
			}
			?>

			<?=$remark?>
		</td>
	</tr>
<?php endforeach ?> 
</table>

</div>
<br>
<div align='left'><b><font size='3'>說明：<br><br>1.缺席情形欄<br><font style='margin-left:20px'>■「請假」：係已完成線上請假者。</font><br><font style='margin-left:20px'>■「未請假」：係未完成線上請假者。<br><br>2.備註欄<br><font style='margin-left:20px'>■「未報到」：係為應參加研習卻未參訓人員。</font><br><font style='margin-left:20px'>■「退訓」：係為已報到參加研習，但缺課時數逾該班期退訓標準人員。</font><br><br><font style='margin-left:20px'>為撙節訓練資源，請貴機關多加配合，避免類似情形發生。</font></font></font> <b></div>
