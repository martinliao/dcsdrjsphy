
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>檔案上傳</title>
</head>
<body>
<div>
    <?php if(!empty($form['massage'])) {?>
    <?=$form['massage'];?>
    <?php }?>
</div>
<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
角色類別:
<select name='group_id' id='group_id'>
	<?php foreach($choices['group'] as $key => $row){ ?>
	<option value="<?=$key;?>" ><?=$row;?></option>
	<?php } ?>

</select>
<input type="hidden" name="import" value="import">
<input type="file" name="courseSetupfile" class='button' accept=".csv">
<input type="submit" value="上傳" class='button'>
</form>
</body>
