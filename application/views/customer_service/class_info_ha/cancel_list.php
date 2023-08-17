<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>取消參訓清單</title>
<link rel="stylesheet" type="text/css" href="<?=base_url("static/css/master.css")?>"/>  
</head>
<body> 
<center><div style="font-size:150%;"><b>臺北市政府公務人員訓練處 取消參訓清單</b></div></center>
<center><div><?=$require->class_name; ?></div></center>
<br>
<table class='grid'>
    <thead>
        <tr>
        <th class='grid th' style='width:10%'>學號</th>
        <th class='grid th' style='width:10%'>服務單位</th>
        <th class='grid th' style='width:10%'>職稱</th>
        <th class='grid th' style='width:10%'>姓名</th>
        <th class='grid th' style='width:10%'>性別</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($cancel_list as $cancel): ?>
        <tr>
            <td><?=$cancel->st_no?></td>
            <td><?=$cancel->bureau_name?></td>
            <td><?=$cancel->title?></td>
            <td><?=$cancel->user_name?></td>
            <td><?=$cancel->sex?></td>
        </tr>
        <?php endforeach ?>
    </tobdy>
</table>

<script>
    
</script>