<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>研習記錄表</title>

<style type="text/css">
	table {
	border: 1px solid #000;
	border-collapse: collapse;
	border-spacing: 0;
	FONT-SIZE:12px;
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
			【上課日期：<?=date_format(date_create($class_info->start_date1),'m/d')?>-<?=date_format(date_create($class_info->end_date1),'m/d')?>】
		</b>
	</div>
</center>
<table cellspacing="0" cellpadding="0" width="640" align="center">
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

		<?php if($learn->yn_sel == 5): ?>
			<td style="font-size:110%;" align="center">未報到</td>
			<td style="font-size:110%;" align="center"></td>
			<td style="font-size:110%;" align="center"></td>
			<td style="font-size:110%;" align="center">-</td>
		<?php else: ?>
			<td style="font-size:110%;" align="center"><?=$learn->va_code_text?></td>
			<td style="font-size:110%;" align="center"><?=$learn->vacation_date?></td>
			<td style="font-size:110%;" align="center"><?=$learn->time?></td>
			<td style="font-size:110%;" align="center"><?=$learn->hours?></td>
		<?php endif ?>
		
		<td style="font-size:110%;" align="center">
			<?php 
			
			$remark = "";
			if($learn->yn_sel == 4){
				$remark .= '退訓';
			}

		    if (!empty($learn->memo) && $learn->va_sn == $learn->v_count) {
		    	$remark .= "({$learn->memo})";
		   	}

			if ($learn->retirement === "0"){
				$remark .= "(退休)";
			}
			?>

			<?=$remark?>
		</td>
	</tr>
<?php endforeach ?> 
</table>

</div>


<!----------------------------->
<!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD-->
<!----------------------------->
</body>

</html>