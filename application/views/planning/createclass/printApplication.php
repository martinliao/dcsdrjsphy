<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>訓練計劃作業	開班需求建檔-列印申請單</title>

    <style type="text/css">
        body {font-family:"標楷體","Times New Roman";
			  height: 842px;
			  width: 595px;
			 }
		@page WordSection1
		{size:595.3pt 841.9pt;
		mso-page-orientation:portrait;
		margin:1.505cm 1.505cm 1.505cm 1.505cm;
		mso-header-margin:42.55pt;
		mso-footer-margin:49.6pt;
		mso-paper-source:0;
		layout-grid:19.05pt;}
		div.WordSection1
		{page:WordSection1;}
		table {
			table-layout:fixed;
			width:100%;
			border:1px solid #f00;
			word-wrap:break-word;}

		td {overflow:hidden;}
	</style>

</head>
<body>
    <div class=WordSection1>

	<div class='title'>
	<font face="標楷體" size = "5">臺北市政府公務人員訓練處</font></br>
		<br>
	<font face="標楷體" size = "5"><?=$form['year']?>年度行政系列(局處專業)訓練班期需求表</font></div>
	</div>
	<table class="table" width="590" border=1>
		<col width=30>
		<col width=560>
		<tr>
			<td class='tdr'><font face="標楷體">班期名稱</font></td>
			<td class='tdl' colspan='4'><?=$form['class_name']?></td>
			<td class='tdr'><font face="標楷體">終身學習代碼</font></td>
			<td class='tdl' colspan='2'><?=$form['ecpa_class_id']?></td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">訓練期別</font></td>
			<td class='tdl'><?=$form['term']?>期</td>
			<td class='tdr'><font face="標楷體">訓練期程</font></td>
			<td class='tdl' colspan = '2'>每期:<?=$form['range']?>(小時)<br>合計:(小時)</td>
			<td class='tdr'><font face="標楷體">訓練人數</font></td>
			<td class='tdl' >每期：<?=$form['no_persons']?>人
						<br>合計： 人</td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">訓練方式</font></td>
			<td class='tdl' colspan = '6'>
			全天或半天：
			<font style="font-size:20px">
			<?php if($form['class_cate1']=="0"){?>
			■
			<?php } else {?>
			□
			<?php } ?>
			</font>全天、  
			<font style="font-size:20px">
			<?php if($form['class_cate1']=="1"){?>
			■
			<?php } else {?>
			□
			<?php } ?>
			</font>半天
			<br>住班或通勤：
			<font style="font-size:20px">
			<?php if($form['class_cate']=="2"){?>
			■
			<?php } else {?>
			□
			<?php } ?>
			</font> 住班 、 
			<font style="font-size:20px">
			<?php if($form['class_cate']=="1"){?>
			■
			<?php } else {?>
			□
			<?php } ?> 
			</font>通勤
			<br>連續或間斷：
			<font style="font-size:20px">
			<?php if($form['class_cate']=="0"){?>
			■
			<?php } else {?>
			□
			<?php } ?>
			</font> 每日、
			<font style="font-size:20px">
			<?php if($form['class_cate2']=="1"){?>
			■
			<?php } else {?>
			□
			<?php } ?>
			</font>隔日、
			<font style="font-size:20px">
			<?php if($form['class_cate2']=="2"){?>
			■
			<?php } else {?>
			□
			<?php } ?>
			</font>其他：請敘明：
			<?php if($form['class_desc']==""){?>
			_____________________  
			<?php } else {?>
			<u><?=$form['class_desc']?></u>
			<?php } ?>
			<br> 
			</td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">辦班時段</font></td>
			<td class='tdl' colspan = '6'>
			    自<?=date('Y-m-d',strtotime($form['start_date1']))?>至<?=date('Y-m-d',strtotime($form['end_date1']))?>止
				<?php
					if(isset($form['bookingRooms']) && !empty($form['bookingRooms'])){
						$rooms = $form['bookingRooms'];
						$roomtext = "";
							foreach($rooms as $room){
								$roomtext .= "<br>".$room['booking_date']."(".$room['name'].")"." ".$room['room_name'];
							}
						echo $roomtext;
					}
				?>
			<br>註解:<?=$form['segmemo']?>
			</td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">教學方式</font></td>
			<td class='tdl' colspan = '6'>
			<?php 
				$teach_style = array();
				if($form['way1'] == 'Y'){
					$teach_style[] = "講授";
				}
				if($form['way2'] == 'Y'){
					$teach_style[] = "實習";
				}
				if($form['way3'] == 'Y'){
					$teach_style[] = "研討";
				}
				if($form['way4'] == 'Y'){
					$teach_style[] = "習作";
				}
				if($form['way5'] == 'Y'){
					$teach_style[] = "討論";
				}
				if($form['way6'] == 'Y'){
					$teach_style[] = "座談";
				}
				if($form['way7'] == 'Y'){
					$teach_style[] = "演練";
				}
				if($form['way8'] == 'Y'){
					$teach_style[] = "說唱";
				}
				if($form['way9'] == 'Y'){
					$teach_style[] = "表演";
				}
				if($form['way10'] == 'Y'){
					$teach_style[] = "參觀活動";
				}
				if($form['way11'] == 'Y'){
					$teach_style[] = "案例討論";
				}
				if($form['way12'] == 'Y'){
					$teach_style[] = "角色扮演";
				}
				if($form['way13'] == 'Y'){
					$teach_style[] = "實地參觀";
				}
				if($form['way14'] == 'Y'){
					$teach_style[] = "模擬演練";
				}
				if($form['way15'] == 'Y'){
					$teach_style[] = "電腦實機";
				}
				if($form['way16'] == 'Y'){
					$teach_style[] = "視聽教材";
				}
				if($form['way17'] == 'Y'){
					$teach_style[] = "其他(";
				}
				$teach_style = implode('、', $teach_style);
			?>
			<?=$teach_style?>
			</td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">研習目標</font></td>
			<td class='tdl' colspan = '6'><?=$form['obj']?></td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">訓練對象</font></td>
			<td class='tdl' colspan = '6'><?=$form['respondant']?></td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">考核方式</font></td>
			<td class='tdl' colspan = '6'>
			<?php 
				$kltype = array();
				if($form['type1'] == '1'){
					$kltype[] = "測驗";
				}
				if($form['type2'] == '1'){
					$kltype[] = "書面報告";
				}
				if($form['type3'] == '1'){
					$kltype[] = "成果發表";
				}
				if($form['type4'] == '1'){
					$kltype[] = "實作演練";
				}
				if($form['type5'] == '1'){
					$kltype[] = "心得分享";
				}
				if($form['type6'] == '1'){
					$kltype[] = "案例研討";
				}
				if($form['type7'] == '1'){
					$kltype[] = "意見交流";
				}
				if(!empty($form['type8'])){
					$kltype[] = "其他(".$form['type8'].")";
				}

				if(!empty($kltype)) {
					$kltype = implode('、', $kltype);
				}else {
					$kltype = '無';
				}
			?>
			<?=$kltype?>
			</td>
		</tr>
		<tr>
			<td class='tdr' width="80"><font face="標楷體">課程內容</font></td>
			<td class='tdl' colspan = '6'>				
			<table class='grid' border=1>
				<col width=200>
				<col width=280>
				<tr>  
					<td class='tdc' colspan='6'>課目</td>
				</tr>
				<?php
				if(isset($course_name) && !empty($course_name)){
					for($i=0;$i<count($course_name);$i++){
						echo '<tr>';
						echo '<td colspan="6" style="word-wrap:break-word;"><b><font face="標楷體" size="2">';
						echo ($i+1).$course_name[$i]['course_name'];
						echo '</font></b></td></tr>';
					}
				}
				?>
				<tr>
					<td colspan='6'>&nbsp;</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td class='tdr' rowspan="2"><font face="標楷體">混成班期</font></td>
			<td class='tdl' colspan = '6'>
			<?php if($form['is_mixed']=="1"){?>
			■
			<?php }else{ ?>
			□
			<?php } ?>
			是,請續填搭配之(*)臺北e大線上課程
			</td>
		</tr>
		<tr>
			<td class='tdl' colspan = '6'><font face="標楷體">
			<?php if($form['is_mixed']!="1"){?>
			■
			<?php }else{ ?>
			□
			<?php } ?>
			否
			</font></td>
		</tr>
		<tr>
			<td class='tdr' rowspan="<{$data.clen}>"><font face="標楷體" size="1">(*)臺北e大<br>線上課程：
			<br>(https://<br>elearning<br>.taipei/<br>elearn/course<br>/index.php)</font></td>
			<td class='tdl' colspan = '3'>
			課程:
			<?php
				if(isset($form['online_course']) && !empty($form['online_course'])){
					echo $form['online_course'][0]['class_name'];
				}
			?>
			</td>
			<td colspan = '3'>
			時數:
			<?php
				if(isset($form['online_course']) && !empty($form['online_course'])){
					echo $form['online_course'][0]['hours'];
				}
			?>
			</td>
		</tr>
		
		<?php
			if(isset($form['online_course']) && !empty($form['online_course'])){
				for ($i=0;$i<count($form['online_course']);$i++) { 
                    $rows = $i+1;
                    echo '<tr>';
                    echo '<td class="tdl" colspan = "3">';
                    echo '課程:'.$form['online_course'][$i]['class_name'];
                    echo '<td colspan = "3">';
                    echo '時數:'.$form['online_course'][$i]['hours'];
                    echo '</td>';
                    echo '</tr>';
                }
			}
		?>

		<tr>
			<td class='tdr' rowspan="2"><font face="標楷體" size="2">重大政策</font></td>
			<td class='tdl' colspan = '6'>
			<?php
				$mapStr = array();
				if($form['map1'] == '1'){
					$mapStr[] = "A營造永續環境 ";
				}
				if($form['map2'] == '1'){
					$mapStr[] = "B健全都市發展";
				}
				if($form['map3'] == '1'){
					$mapStr[] = "C發展多元文化";
				}
				if($form['map4'] == '1'){
					$mapStr[] = "D優化產業勞動";
				}
				if($form['map5'] == '1'){
					$mapStr[] = "E強化社會支持";
				}
				if($form['map6'] == '1'){
					$mapStr[] = "F打造優質教育";
				}
				if($form['map7'] == '1'){
					$mapStr[] = "G精進健康安全";
				}
				if($form['map8'] == '1'){
					$mapStr[] = "H精實良善治理";
				}
				if($form['map9'] == '1'){
					$mapStr[] = "樂活宜居(45項)";
				}
				if($form['map10'] == '1'){
					$mapStr[] = "友善共融(31項)";
				}
				if($form['map11'] == '1'){
					$mapStr[] = "創新活力(37項)";
				}
				$mapStr = implode('、', $mapStr);
			?>

			<?php if(!empty($mapStr)){?>
			■
			<?php } else{ ?>
			□
			<?php } ?>
			是(請續填類別)
			<br>
			<?=$mapStr?>
			</td>
		</tr>
		<tr>
			<td class='tdl' colspan = '6'><font face="標楷體">
			<?php if(empty($mapStr)){?>
			■
			<?php } else { ?>
			□
			<?php } ?>
			否
			</font></td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體" size="2">政策行銷班期</font></td>
			<td class='tdl' colspan = '6'>
			<?php if($form['policy_class'] == 'Y'){ ?>
			■ 是 □ 否
			<?php } else if($form['policy_class'] == 'N'){ ?>
			□ 是 ■ 否
			<?php } else {?>
			□ 是 □ 否
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體" size="2">環境教育班期</font></td>
			<td class='tdl' colspan = '6'>
			<?php if($form['env_class'] == 'Y'){ ?>
			■ 是 □ 否
			<?php } else if($form['env_class'] == 'N'){ ?>
			□ 是 ■ 否
			<?php } else {?>
			□ 是 □ 否
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體" size="2">特殊情況</font></td>
			<td class='tdl' colspan = '6'>
			<?php if($form['not_hourfee'] == 'Y' && $form['not_location'] == 'Y' && $form['special_status'] == '9'){ ?>
			■ 無須支應講座鐘點費 ■ 上課地點非公訓處 ■ 其它 <?=$form['special_status_other']?> (請敘明)
			<?php } else if($form['not_hourfee'] == 'Y' && $form['not_location'] == 'Y'){ ?>
			■ 無須支應講座鐘點費 ■ 上課地點非公訓處 □ 其它 &nbsp&nbsp&nbsp&nbsp&nbsp(請敘明)
			<?php } else if($form['not_hourfee'] == 'Y' && $form['special_status'] == '9'){ ?>
			■ 無須支應講座鐘點費 □ 上課地點非公訓處 ■ 其它 <?=$form['special_status_other']?> (請敘明)
			<?php } else if($form['not_location'] == 'Y' && $form['special_status'] == '9'){ ?>
			□ 無須支應講座鐘點費 ■ 上課地點非公訓處 ■ 其它 <?=$form['special_status_other']?> (請敘明)
			<?php } else if($form['not_hourfee'] == 'Y'){ ?>
			■ 無須支應講座鐘點費 □ 上課地點非公訓處 □ 其它 &nbsp&nbsp&nbsp&nbsp&nbsp(請敘明)
			<?php } else if($form['not_location'] == 'Y'){ ?>
			□ 無須支應講座鐘點費 ■ 上課地點非公訓處 □ 其它 &nbsp&nbsp&nbsp&nbsp&nbsp(請敘明)
			<?php } else if($form['special_status'] == '9'){ ?>
			□ 無須支應講座鐘點費 □ 上課地點非公訓處 ■ 其它 <?=$form['special_status_other']?> (請敘明)
			<?php } else {?>
			□ 無須支應講座鐘點費 □ 上課地點非公訓處 □ 其它 &nbsp&nbsp&nbsp&nbsp&nbsp(請敘明)
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體">需求機關名稱</font></td>
			<td class='tdl' colspan = '6'><?=$form['req_beaurau_name']?></td>
		</tr>
		<tr>
			<td class='tdr'><font face="標楷體" size="2">班期業務承辦人</font></td>
			<td class='tdl' width=200 colspan = '2'><font style="font-size:15px">姓名：<?=$form['contactor']?><font></td>
			<td class='tdl' width=200 colspan = '2'><font style="font-size:15px">TEL：<?=$form['tel']?><font></td>
			<td class='tdl' width=200 colspan = '2'><font style="font-size:15px">e-mail：<?=$form['contactor_email']?><font></td>
		</tr>
	</table>
	<!--<input type='button' name='printhtml' id='printhtml' value='列印' class='button' onclick='print_page();'>
	<input type='button' name='printhtml' id='printhtml' value='返回' class='button' onclick='history.back(1);'>-->
	</div>
</body>
<script>
function print_page() {   
   window.print();   
 }  

</script>
