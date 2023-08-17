<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>班期錄取名冊</title>
<link type="text/css" rel="stylesheet" href="<?=base_url("static/css/master.css")?>"/>
</head>
<body>

<center><div style="color:black;font-size:120%;" ><b><?="{$require->year}年度 {$require->class_name} 第{$require->term}期";?></b></div></center>
<br>
<div class='page_info'>

</div>
<table width="768" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td bgcolor="#eeeeee">
			<table width="100%" border="0" cellspacing="1" cellpadding="3">
				<tr align="center" style=" background-color : #5D7B9D; color:#ffffff; ">
					<td>服務單位</td>
					<td>姓名</font></td>
					<td>學號</font></td>
				</tr>
                <?php foreach($enrolls as $key => $enroll): ?>
                <?php $col = (($key+1)%2 == 0) ? 'bgcolor="#dcdcdc"' : 'bgcolor="#ffffff"';?>
				<tr <?=$col;?>>
					<td align="left"><?=$enroll->bc_name; ?></td>
					<td align="left"><?=$enroll->user_name; ?></td>
					<td align="center"><?=$enroll->st_no; ?></td>
				</tr>
                <?php endforeach ?>				
			</table>
		</td>
	</tr>	
</table>

</body>
</html>
