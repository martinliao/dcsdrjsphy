<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>課程表</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('static/css/master.css')?>">
</head>
<body style="width:620px">
    <?php foreach($schedules as $schedule): ?>
	<div id="require_query_form" style="margin-left:40px;">
        <div align="center" style="font-family:'標楷體'">
            <font size="4"><b>臺北市政府公務人員訓練處　　　課程表</b></font>
        </div>
        <div align="center" style="font-family:'標楷體'">
            <font size="4"><b><?=$schedule['require']->year?> 年度　<?=$schedule['require']->class_name?>　第<?=$schedule['require']->term?>期</b></font>
        </div>
        <div>
        <font size="4">
            <b>
                <div style="float:left;font-family:'標楷體'"><?=$schedule['require']->class_no?></div>
                <div style="float:right;font-family:'標楷體'">
                    <?php if($schedule['muti_room'] == false): ?>
                        <?="上課地點：".$schedule['room_name']?>
                    <?php endif ?>
                </div>
            </b>
        </font>
        </div>
        <?php if(!empty($schedule['online'])): ?>
        <div style="font-family:'標楷體';"><font size="4"><b>線上課程表</b></font></div>
            <table class='grid2' width="600px" > 
                <thead>
                    <tr>
                        
                        <th class="grid2 th" width="60px">起日</th>
                        <th class="grid2 th" width="60px">迄日</th>
                        <th class="grid2 th" width="300px">線上課程名稱</th>
                        <th class="grid2 th" width="120px">講座名稱</th>
                        <th class="grid2 th" width="100px">上課地點</th>
                    
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($schedule['online'] as $online): ?>
                        <tr>
                            <td class="grid2 td" ><?=$online->start_date_format; ?></td>
                            <td class="grid2 td" ><?=$online->end_date_format; ?></td>
                            <td class="grid2 td" ><?=$online->class_name; ?></td>
                            <td class="grid2 td" ><?=$online->teacher_name; ?></td>
                            <td class="grid2 td" ><?=$online->place; ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <br>
        <?php endif ?>

        <div style="font-family:'標楷體';"><font size="4"><b>實體課程表</b></font></div>
        <table class="grid2" width="600px">
            <thead>
                <tr>
                    <th class="grid2 th" width="60px">日期</th>
                    <th class="grid2 th" width="60px">星期</th>
                    <th class="grid2 th" width="110px">時間</th>
                    <th class="grid2 th" width="150px">課程</th>
                    <th class="grid2 th" width="120px">講座</th>
                    <?php if($schedule['muti_room']): ?>
                        <th class="grid2 th" width="100px">上課地點</th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php $last_date = "";?>
                <?php foreach($schedule['phy'] as $phy): ?>
                <tr>
                    <?php 
                        $use_date = new DateTime($phy->use_date);
                    ?>
                    
                    <td class="grid2 td">
                        <?php if($last_date != $use_date->format("m/d")): ?>
                            <?=$use_date->format("m/d")?>
                        <?php endif ?>
                    </td>
                    <td class="grid2 td">
                        <?php if($last_date != $use_date->format("m/d")): ?>
                            <?=get_chinese_weekday($phy->use_date)?>
                        <?php endif ?>
                    </td>
                    <td class="grid2 td"><?=substr($phy->from_time, 0, 2).":".substr($phy->from_time, 2, 2)?>~<?=substr($phy->to_time, 0, 2).":".substr($phy->to_time, 2, 2)?></td>
                    <td class="grid2 td" style="text-align: left"><?=$phy->description?></td>
                    <td class="grid2 td"><?=join("<br>", $phy->teacher)?><br></td>
                    <?php if($schedule['muti_room']): ?>
                 
                    <td class="grid2 td"><?=$phy->room_sname?></td>
                    <?php endif ?>
                    <?php 
                        $last_date = $use_date->format("m/d");
                    ?>
                </tr>
                <?php endforeach ?>
                <tr>
                    <td class="grid2 td" style="text-align:left;border-top: 2px solid black" colspan="6">
                        一、承辦人：<?=$schedule['require']->worker_name?>(分機 <?=$schedule['require']->worker_sub_phone?>)、代理人：<?=$schedule['require']->agent_name?>(分機 <?=$schedule['require']->agent_sub_phone?>)。<br>
                        二、研習人數 <?=$schedule['require']->search_count?>人；研習總時數 <?=$schedule['require']->range_real + $schedule['require']->range_internet?>小時。
                    </td>
                </tr>
                </tbody>
        </table>
	</div>
    <br>
    <?php endforeach ?>
<script>
	
function printScreen(){
	
var value = document.getElementById('require_query_form').innerHTML;


var printPage = window.open('','printPage','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=1024');	
printPage.document.open();
printPage.document.write("<HTML><head><title>課程表</title><link rel='stylesheet' type='text/css' href='css/master.css'/></head><BODY onload='window.print();'>");
printPage.document.write("<PRE>");
printPage.document.write(value);
printPage.document.write("</PRE>");
printPage.document.close("</BODY></HTML>");
}

</script>

</body>
</html>