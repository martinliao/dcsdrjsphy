<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>未選員名單</title>
<link rel="stylesheet" type="text/css" href="<?=base_url("static/css/master.css")?>"/>  
</head>
<body> 
<center><div style="font-size:150%;"><b>未選員名單</b></div></center>
<br>
<table class='grid'>
    <thead>
        <tr>
        <th class='grid th' style='width:10%'>服務單位</th>
        <th class='grid th' style='width:10%'>職稱</th>
        <th class='grid th' style='width:10%'>姓名</th>
        <th class='grid th' style='width:10%'>性別</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($no_records as $no_record): ?>
        <tr>
            <td><?=$no_record->bureau_name?></td>
            <td><?=$no_record->title?></td>
            <td><?=$no_record->user_name?></td>
            <td><?=$no_record->sex?></td>
        </tr>
        <?php endforeach ?>
    </tobdy>
</table>

<script>
    
</script>