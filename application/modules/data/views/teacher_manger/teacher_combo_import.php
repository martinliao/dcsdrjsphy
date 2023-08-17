<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=$_LOCATION['function']['name'] ;?>
			</div>
			<div class="panel-body">
				<form id="actSave" method="POST" action="<?=$import_save;?>" enctype="multipart/form-data">
				<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
				<div style="color:green;font-size:200%;"><b>匯入講座基本資料</b></div>

				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr>
				    <td bgcolor="#eeeeee">
				        <table border="0" cellspacing="1" cellpadding="1" width="100%">
				  			<tr>
								<td width="150" align="center" bgcolor="#dcdcdc">上傳CSV檔案</td>
								<td align="left" bgcolor="#ffffff">
								<input type="file" name="aCSV" id="aCSV" class="button" size="30"><br>
								<input type="hidden" name="doAction" id="doAction" class="button" value="import">
								<input type="submit" value="確定匯入" class="button">
								<input type="button" value="返回" class="button" onclick="location.href='<?=$import_cancel;?>'">
								</td>
								<td>
								<a href="<?=base_url('files/example_files/teacher_combo_import_example.csv');?>" target="_blank">下載格式檔</a> <font color="red">(附註：CSV格式檔的第一行欄位說明請勿刪除或異動)</font>
								<br>身分別填數字代碼：1.個人 2.公司行號 3.外國人 4.無身分證
								<br>學歷填數字代碼：20.國(初)中以下 30.高中(職) 40.專科 50.大學 60.碩士 70.博士
								<br>講師或助教代碼：1.講師 2.助教
								<br>聘請類別代碼：I.內聘 O.外聘 P.國外 Q.主辦或訓練機關學校 R.學生
								<br>生日格式：1978/01/02
								<br>市內電話格式：02-1234567，手機格式：0930-123456
								</td>
				            </td>
				          </tr>
				         </table>
				    </td>
				  </tr>
				</table>
				</form>
				<?php if(!empty($echo_msg)){?>
				<?=$echo_msg;?>
				<?php }?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>


