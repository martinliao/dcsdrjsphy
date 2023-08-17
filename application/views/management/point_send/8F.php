<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>學員成績寄送通知</title>
</head>
<body>
	<style>
	.tabletast {
		border:#000 1px solid;
	}
	.tabletast {
		border-collapse:collapse;
		margin: 3px;
	}
	.tabletast td, tr, th{
		border:#000 1px solid;
		padding: 3px;
	}
	</style>
	<p>親愛的學長，您好：</p>
	<p style="margin-left: 40px;">您參加本處<?=$year;?>年<?=$class_name;?>第<?=$term;?>期研習，總成績如下：</p>
	<table class="tabletast" style="width:100%" border="1">
		<tr>
			
			<td colspan="6" style="text-align: center;">
				<p><b>臺北市政府公務人員訓練處<?=$year;?>年<?=$class_name;?>第<?=$term;?>期總成績</b></p>
				<p><b>&lt;研習期間<?=$start_date;?>起至<?=$end_date;?>止&gt;</b></p>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;"><b>學號</b></td>
			<td style="text-align: center;"><b>服務單位</b></td>
			<td style="text-align: center;"><b>職稱</b></td>
			<td style="text-align: center;"><b>姓名</b></td>
			<td style="text-align: center;"><b>總成績</b></td>
			<td style="text-align: center;"><b>說明</b></td>
		</tr>
		<tr>
			<td style="text-align: center;"><?=$st_no;?></td>
			<td style="text-align: center;"><?=$bureau;?></td>
			<td style="text-align: center;"><?=$title;?></td>
			<td style="text-align: center;"><?=$name;?></td>
			<td style="text-align: center;"><?=$final_score;?></td>
			<td style="text-align: center;"><?=$notpass_desc;?></td>
		</tr>
		<tr>
			
			<td colspan="6">
				<p><?=str_replace("\n", '<br>', $remark)?></p>
			</td>
		</tr>
	</table>
	<br />
	<div style="float: right;">
		公訓處　教務組<br />
		承辦人：<?=$contactor;?><br />
		電話：<?=$tel;?><br />
	</div>
</body>
</html>