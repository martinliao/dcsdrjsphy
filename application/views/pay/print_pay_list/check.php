<!DOCTYPE html>
<html>
<head>
	<style>
	body
	{
		font-family:標楷體;
		font-size:14px;
	}
	</style>
	<title></title>
</head>
<body style="background-color: #ECF0F5">
	<div style="background-color: #e7e7e7; margin: 0px;">
		<div style="text-align:center; font-size: 18px">
			臺北市政府公務人員訓練處 請款清冊(流水號<?=$hour_traffic_tax->app_seq?>)
		</div>
		<div style="text-align:center; font-size: 16px">
			<?=$hour_traffic_tax->year?>年 <?=$hour_traffic_tax->class_name?> 第<?=$hour_traffic_tax->term?>期
		</div>
		<div style="text-align:center; font-size: 14px;width:30%;float:left;">
			查詢日期：<?=$filter['sdate']?>~<?=$filter['edate']?>
		</div>
		<div style="text-align:center; font-size: 14px;width:40%;float:left;">
			開課日期：<?=$hour_traffic_tax->sdate.'~'.$hour_traffic_tax->edate?>
		</div>
		<div style="text-align:center; font-size: 16px;width:30%;float:left;">
			類別：<?=$hour_traffic_tax->class_type?>
		</div>	
		<div >
			<table style="width:100%;border-collapse:collapse;" border="1">
				<thead>
					<th>上課日期</th>
					<th>姓名/公司<br>ID/編號</th>
					<th>銀行/郵局分行<br>帳號(帳戶名稱)</th>
					<th>地址<br>email</th>
					<th>時數</th>
					<th>單價</th>
					<th>鐘點費</th>
					<th>交通費</th>
					<th>合計</th>
					<th>備註</th>
				</thead>
				<tbody>
					<tr>
						<td><?=$hour_traffic_tax->u_date?></td>
						<td><?=$hour_traffic_tax->teacher_name?><br><?=$hour_traffic_tax->teacher_id?></td>
						<td>
							<?=$hour_traffic_tax->bank_name?><br><?=$hour_traffic_tax->teacher_account?>(<?=$hour_traffic_tax->teacher_acct_name?>)<br>
							<strong><font style="font-size:16px;"><?=($hour_traffic_tax->remark == '無') ? '' : $hour_traffic_tax->remark ?></font></strong>
						</td>
						<td><?=$hour_traffic_tax->teacher_addr?><br><?=$hour_traffic_tax->email?></td>
						<td><?=$hour_traffic_tax->hrs?></td>
						<td style="text-align:right"><?=number_format($hour_traffic_tax->unit_hour_fee)?></td>
						<td style="text-align:right"><?=number_format($hour_traffic_tax->hour_fee)?></td>
						<td style="text-align:right"><?=number_format($hour_traffic_tax->traffic_fee)?></td>
						<td style="text-align:right"><?=number_format($hour_traffic_tax->subtotal)?></td>
						<td><?=$hour_traffic_tax->description?></td>
					</tr>	
					<tr>
						<td style="text-align:right" colspan="6">總計</td>
						<td style="text-align:right"><?=number_format($hour_traffic_tax->hour_fee)?></td>
						<td style="text-align:right"><?=number_format($hour_traffic_tax->traffic_fee)?></td>
						<td style="text-align:right"><?=number_format($hour_traffic_tax->subtotal)?></td>
						<td style="text-align:right"></td>
					</tr>				
				</tbody>
			</table>
		</div>	
		<!--
		<div style="font-size: 14px;">
		有關本處講座鐘點費、交通費支給相關注意事項：<br>
		1、 配合二代健保補充保險費扣取作業，當週講師鐘點費超過24,000元(含)以上，須扣取補充保費1.91%。<br>
		2、 倘搭乘公務車授課，依規定不得支領交通費。<br>
		3、 配合本府政風處107年4月17日北市政三字第1076000125號函示略以：內、外聘之講師、助教係受本處遴聘、具<br>
		&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp 領鐘點費及受託執行教學工作者，宜予類推適用「臺北市政府公務員廉政倫理規範」第5點規定，避免要求、<br>
		&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp 期約或收受利害關係者所為之餽贈，以彰本府廉能法紀政府形象。<br>			
		</div>
		-->
		<!-- 2021-07-02 1100630創意提案會議要求修正 -->
		<!--
		<div style="font-size: 14px;">
		有關本處講座鐘點費、交通費支給相關注意事項：<br>
		1、 配合二代健保補充保險費扣取作業，當週講師鐘點費超過24,000元(含)以上，須扣取補充保費1.91%。<br>
		2、 請提供個人匯款帳號（勿提供公司帳戶）；倘搭乘公務車授課，依規定不得支領交通費。<br>
		3、 依「臺北市政府公務員廉政倫理規範」規定，本處遴聘人員應避免要求、期約或收受利害關係者所為之餽贈。<br>		
		</div>
		-->
		<!-- 2021-07-15 1100630創意提案會議要求再修正 -->
		<div style="font-size: 154x;">
		<br>
		<spen>有關本處講座鐘點費、交通費支給相關注意事項：</spen><br>		
		<spen>1、以上為講座個人匯款帳號（非公司帳號），款項及金額正確 </spen>
			<form style="margin:0px;display: inline" method="POST" onsubmit="return check()">
				<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />	
				<input type="hidden" name="seq" value="<?=$taxkey['seq'];?>" />		
				<button>確認送出 submit</button>
 			</form>
		</br>
		<spen>&nbsp;&nbsp;&nbsp;&nbsp;(若資料須更新，請告知承辦人)</spen><br>
		<spen>&nbsp;&nbsp;&nbsp;&nbsp;Please confirm the information listed above, and click on "submit". </spen><br>
		<spen>&nbsp;&nbsp;&nbsp;&nbsp;If you need to update your personal information, please inform us.</spen><br>
		<spen>2、當周講座鐘點費超過26,400元(含)以上，須扣二代健保補充保費2.11%。倘搭乘公務車授課，依規定不得支領交通費。</spen><br>
		<!--<spen style="background-color:#FFFF00">3、依「臺北市政府公務員廉政倫理規範」規定，本處遴聘人員應避免要求、期約或收受利害關係者所為之餽贈。</spen><br>-->
		</div>
	</div>
	<!-- 2021-07-05 1100630創意提案會議要求再修正 -->
	<!--
	<div style="color:blue">
		※請講座確認請款資料是否正確，如有誤，請先提供正確、最新資料予班期承辦人更正，以維護權益。<br>
		※確認無誤後，請點選[確認送出]按鍵。<br>
		(Please confirm the above information，and hit "send")
	</div>
	<form method="POST" onsubmit="return check()">
		<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />	
		<input type="hidden" name="seq" value="<?=$taxkey['seq'];?>" />		
		<button style="font-size: 20px; margin-top: 20px;">確認送出 send</button>
	</form>
	-->
</body>
<script type="text/javascript">
	function check(){
		return confirm('確定要送出嗎?');
	}
</script>

</html>

