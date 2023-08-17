
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>產出學員成績整批檔案下載</title>
</head>
<body style='width:964px'>
	<div class='title' style="color:green;font-size:150%;width:100%">產出學員成績整批檔案下載</div>
	<div class='status_list_table'>
	    <form method='post' name='query_form'>
			<table class="grid" style='width:100%'>
				<tr >
		  			<th bgcolor="#E9EEF4" style='width:20%'>下載</th>
		  			<th bgcolor="#E9EEF4" style='width:40%'>建立日期</th>
		  			<th bgcolor="#E9EEF4" style='width:40%'>檔案名稱</th>
				</tr>
				<?php foreach ($fileAry as $key => $row) {?>
					<tr>
						<td class="Row1"><a href="<?= $row['PATH']; ?>">下載檔案</a></td>
						<td class="Row1"><?= $row['DATE']; ?></td>
						<td class="Row1"><?= $row['NAME']; ?></td>
					</tr>
				<?php } ?>
			  	<tr>
					<td colspan="4" style="background-color:#dcdcdc;">
						<input type="button" name='btnSearch' id="btnSearch" value="關閉" onclick="window.close();" class='button'/>
		  			</td>
		  		</tr>
			</table>
		</form>
	</div>
</body>
</html>