<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<title></title> 
</head> 
	<body style="font-size: 10pt" leftmargin="6"">

	<table border="1" cellspacing="0" cellpadding="0" align="center" >
		<tr >
			<td  rowspan="2" align=center nowrap style="border-style: outset;font-size:10pt;" width=120><b>班期名稱</b></td>
			<td  rowspan="2" align=center  style="border-style: outset;font-size:10pt;" width=70><b>開課起迄</b></td>
			<td  rowspan="2" align=center nowrap style="border-style: outset;font-size:10pt;" width=80><b>教室</b></td>
			<td  rowspan="2" align=center nowrap style="border-style: outset;font-size:10pt;" width=320><b>開放性意見</b></td>
			<td  colspan="4" align=center nowrap style="border-style: outset;font-size:10pt;" width=120><b>處理方式</b></td>
		</tr>
		<tr >
		<td align=center nowrap style="border-style: outset;font-size:10pt;" width=30><b>立即處理</b></td>
		<td align=center nowrap style="border-style: outset;font-size:10pt;" width=30><b>列入FAQ</b></td>
		<td align=center nowrap style="border-style: outset;font-size:10pt;" width=30><b>研議改善方案</b></td>
		<td align=center nowrap style="border-style: outset;font-size:10pt;" width=30><b>存參</b></td>
		</tr>

		<?php 
			$strClass_name = '';
			for($i=0;$i<count($list);$i++){
		?>
		<?php if ($strClass_name != $list[$i]['qd_class_name']) { ?>
				<tr>
					<td  align=left style="border-style: outset;font-size:10pt;" width=120><?=htmlspecialchars($list[$i]['qd_class_name'],ENT_HTML5|ENT_QUOTES);?></td>
					<td  align=left style="border-style: outset;font-size:10pt;" width=70><?=htmlspecialchars($list[$i]['qd_sdate'],ENT_HTML5|ENT_QUOTES);?>-<?=htmlspecialchars($list[$i]['qd_edate'],ENT_HTML5|ENT_QUOTES);?></td>
					<td nowrap align=left style="border-style: outset;font-size:10pt;"><?=htmlspecialchars($list[$i]['qd_room_name'],ENT_HTML5|ENT_QUOTES);?></td>
					<td  align=left style="border-style: outset;font-size:10pt;" width=320><?=htmlspecialchars($list[$i]['od_content'],ENT_HTML5|ENT_QUOTES);?>(<?=htmlspecialchars($list[$i]['od_count'],ENT_HTML5|ENT_QUOTES);?>人)&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
				</tr>
		<?php } else { ?>
				<tr>
					<td style="border-style: outset;font-size:10pt;" width=120>&nbsp;</td>
					<td style="border-style: outset;font-size:10pt;" width=70>&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
					<td align=left style="border-style: outset;font-size:10pt;" width=320><?=htmlspecialchars($list[$i]['od_content'],ENT_HTML5|ENT_QUOTES);?>(<?=htmlspecialchars($list[$i]['od_count'],ENT_HTML5|ENT_QUOTES);?>人)&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
					<td nowrap style="border-style: outset;font-size:10pt;">&nbsp;</td>
				</tr>
		<?php } ?>
		<?php 
			$strClass_name = $list[$i]['qd_class_name']; 
		}
		?>
	</table>

	</body>
</html>