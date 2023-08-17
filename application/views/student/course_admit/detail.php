<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>課程表</title>
<!-- <link rel="stylesheet" type="text/css" href="css/master.css"/> -->
</head>
<body>
<div align="center">
	<div id="output" style='width:968px;'>
		<div align="center"><b>臺北市政府公務人員訓練處　　　課程表</b></div>
		<div align="center"><b><?=$require->year?>年度　<?=$require->class_name?>　第<?=$require->term?>期</b></div>
		<table style="width:968px;" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left"><?=$require->class_no?></td>
				<td>
					<?php if ($muti_room  == false): ?>
						<div align="right">
                            <b>上課地點：<?=$room_name?></b>
						</div>
					<?php endif ?>
				</td>
			</tr>	
		</table>		
		<table style="width:968px;" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td bgcolor="#CCCCCC">		
					<table border="0" cellspacing="1" cellpadding="3" width="100%" id="tab_1" >
						<tr style=" background-color : #5D7B9D; color:#ffffff;">
							<td width='100' align='center'>日期</td>
							<td width='100' align='center'>星期</td>
							<td width='150' align='center'>時間</td>
							<td width='350' align='center'>課程</td>
							<td width='200' align='center'>講座</td>
							<?php if ($muti_room): ?>
							<td  width='200' align='center'>上課地點</td>
							<?php endif ?>
						</tr>

						<tbody>
                        <?php 
                            $col = "";
                             foreach($phy_schedule as $schedule): 
                             	if($schedule->display < 0){
                             		continue;
                             	}
                                $col = ($col == 'bgcolor="#ffffff"') ? 'bgcolor="#dcdcdc"' : 'bgcolor="#ffffff"';
                                $schedule->week = get_chinese_weekday($schedule->use_date);
                            ?>
                            <tr <?=$col; ?>>
                                <td align="center"><?=$schedule->use_date ?></td>
                                <td align="center"><?=$schedule->week?></td>
                                <td align="center"><?=$schedule->from_time."~".$schedule->to_time?></td>
                                <td align="center"><?=$schedule->description?></td>
                                <td align="center"><?=$schedule->teacher_name; ?></td>
                                <?php if ($muti_room): ?>
                                <td align="center"><?=$schedule->room_name?></th>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
						</tbody>
					</table>
				</td>
			</tr>	
		</table>
	</div>
</div>

<table style="width:968px;" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td bgcolor="#FFFFFF" align="left">		
		<?=$require->class_content?>
		</td>
	</tr>	
</table>
	
<p>&nbsp;</p>

<div align="center">	
	<div align="center" >		
		<div align="center"><b>臺北市政府公務人員訓練處　　　研習人員名冊</b></div>
		<div align="center"><b><?=$require->year?>年度　<?=$require->class_name?>　第<?=$require->term?>期</b></div>
		<table style="width:968px;" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td bgcolor="#CCCCCC">		
					<table border="0" cellspacing="1" cellpadding="3" width="100%" id="tab_2" >
						<thead>
						<tr style=" background-color : #5D7B9D; color:#ffffff;">
						<th width='150' align='center'>學號</th>
						<th width='350' align='center'>服務單位</th>
						<th width='250' align='center'>職稱</th>
						<th width='250' align='center'>姓名</th>
						<th width='100' align='center'>性別</th>
						</tr>
						</thead>
						<tbody>

                        <?php  
                             $col = "";
                             foreach($students as $key => $student): 
                                $col = ($col == 'bgcolor="#ffffff"') ? 'bgcolor="#dcdcdc"' : 'bgcolor="#ffffff"';
                                ?>
                                <tr <?=$col; ?>>
                                    <td align="center"><?=$student->st_no; ?></td>
                                    <td align="center"><?=$student->bureau_name; ?></td>
                                    <td align="center"><?=$student->title; ?></td>
                                    <td align="center"><?=hiddenName($student->name); ?></td>
                                    <td  align="center"><?=$student->sex?></td>
                                </tr>
						<?php endforeach ?>

						</tbody>
					</table>
				</td>
			</tr>	
		</table>					
	</div>	
</div>
</body>
</html>