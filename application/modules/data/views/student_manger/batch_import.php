<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
			<?php if($is_edap && ($_LOCATION['function']['name']=='2D 學員基本資料' || $_LOCATION['function']['name']=='23D 學員基本資料' || $_LOCATION['name']=='2D 學員基本資料' || $_LOCATION['name']=='23D 學員基本資料')){ ?>
                <?php echo '28B 學員基本資料';?>
            <?php } else { ?>
                <?=$_LOCATION['function']['name'] ;?>
            <?php } ?>
			</div>
			<div class="panel-body">
				<form id=fform name=fform method=post enctype="multipart/form-data">
				<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <table class="table table-bordered table-condensed table-hover">
					<tr>
						<td class="tdr" style="width:20%;font-size:100%;text-align:center">上傳CSV檔案</td>
						<td style="width:20%;font-size:100%;">
							<input style='font-size:14px;' type=file name=file id=file accept=".csv"/><br><br>
							<input type=button onclick=checkFile() value="確定匯入" style='font-size:14px;'/><input type=button onclick=goback() style='font-size:14px;'value="返回" />
						</td>
						<td style="font-size:100%;text-align:left;background-color:#E6E6E6">
							<p><a href="<?=base_url('files/example_files/import.csv');?>"><b>下載格式檔</b></a><font color=red>(附註：CSV格式檔的第一行欄位說明請勿刪除或異動)</font></p>
							<p>填寫說明：</p>
							<p>1.性別(格式：M、F)</p>
							<p>2.出生日期格式：YYYY/MM/DD</p>
							<p>3.機關代碼查詢網址：<a href="https://svrorg.dgpa.gov.tw/UC3/UC3-2/UC3-2-01-001.aspx">https://svrorg.dgpa.gov.tw/cpacode/UC3/UC3-2/UC3-2-01-001.aspx</a>(如為外機關請填：D0004)</p>
							<p>4.外機關名稱全銜 註：查無機關代碼者必填本欄，例如”xx股份有限公司”</p>
							<p>5.學歷請填代碼： 20.國(初)中以下  30.高中(職) 40.專科  50.大學 60.碩士 70.博士</p>
							<p>6.現職區分請填代碼： 1.簡任主管,2簡任非主管,3荐任主管,4荐任非主管,5委任主管,6委任非主管,7警察消防主管,8警察消防非主管,9約聘僱人員,10技工工友,11.其他</p>
							<p>7.公司電話格式：02-12345678</p>
							<p>8.公司傳真格式：02-12345678</p>
							<p>9.公司傳真格式：0912-345-678</p>
							<p><font style="color:red;font-size:26px">✭注意：請分次匯入(每筆60人)</font></p>
						</td>
					</tr>
				</table>
				</form>
				<input type=hidden id=feedback name=feedback value="" />
				<?php if(!empty($echo_msg)){?>
				<?=$echo_msg;?>
				<?php }?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>

<script>

//$(document).ready(function()
//{
//    var table = $('#itable').DataTable();
//}
function checkFile()
{
	var f = document.getElementById("file");
	var fileName;
	// var size=document.getElementById("file1").files.item(0).size;
	if(f.value=="")
	{
		alert('請選擇檔案');
		return false;
	}
	else
	{
		fileName = f.value;
		var extIndex = f.value.lastIndexOf('.');
		if (extIndex != -1)
		{
		    //fileName = file.name.substr(0, extIndex);
		    fileName = fileName.substr(extIndex+1, fileName.length);
		    if("csv"!=fileName)
		    {
		    	alert('限定csv格式檔案');
		    	return false;
		    }
		    else
		    {
		    	 document.fform.submit();
		    }
		}
		else
		{
			return false;
		}
	}
}

function goback()
{
	window.location.href="<?=$link_cancel;?>";
}

function comfirmTo(errorf)
{
	if(errorf>0)
	{
		alert('資料有誤，請重新檢查後上傳');
		return false;
	}

	if(!confirm("按下開始匯入後，若資料有誤將導致單筆匯入失敗，請確定"))
	{
		return false;
	}
	//console.log($('#itable'));
	var table = document.getElementById('itable');
	var rowLength = table.rows.length;
	var ajaxCallback = "";

	for(var i=1; i<rowLength; i+=1)
	{
		var is_id_number ='1';
		var row = table.rows[i];
		var _idno = row.cells[1+1].innerHTML;
		_idno = _idno.toUpperCase();
	//	var error_msg = '';
		if("可新增"==row.cells[0].innerHTML)
		{
			is_id_number ='1';
		}
		else if("覆蓋原有資料"==row.cells[0].innerHTML) {
			_idno = row.cells[1+1].getElementsByTagName('input')[0].value;
			is_id_number ='2';
		}
		else{
			is_id_number ='0';
			_idno = row.cells[1+1].getElementsByTagName('input')[0].value;
			_idno = (row.cells[1+1].firstChild.value);
		}
		//console.log(_idno);
		$.ajax( {
			async: false,
			type: "post",
			dataType: 'json',
		  	url: "<?=base_url('data/student_manger/ajax/import');?>",
		  	data: { '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
		  			name: row.cells[0+1].innerHTML, //姓名
		  			idno: _idno, //字號
		  			gender: row.cells[2+1].innerHTML, //生日
		  			bday:row.cells[3+1].innerHTML, //性別
		  			email: row.cells[4+1].innerHTML, //信箱
		  			gemail: row.cells[5+1].innerHTML, //公司信箱
		  			gname: row.cells[6+1].innerHTML, //局處名稱
		  			goname: row.cells[7+1].innerHTML, //外機關名
		  			edu: row.cells[8+1].innerHTML, //學歷
		  			pjob: row.cells[9+1].innerHTML, //現職
		  			gphone: row.cells[10+1].innerHTML, //公司電話
		  			gfax: row.cells[11+1].innerHTML, //公司傳真
		  			job : row.cells[12+1].innerHTML, //職稱
		  			cell_phone : row.cells[13+1].innerHTML,
		  			is_id_number : is_id_number
		  			},

		  	success: function(response)
		  	{
		  		//$("#feedback").val($("#feedback").val()+response+"\n");
		  		if(response.message != ''){
		  			ajaxCallback += (response.message +"\n");
		  			console.log(response.message +"\n");
		  		}
		  		
			}
		})
		//var cellLength = row.cells.length;
		//for(var y=0; y<cellLength; y+=1)
		//{
			//var cell = row.cells[y];
			//console.log(cell.innerHTML);
		//}
	}
	alert(ajaxCallback);
	window.location.href="<?=$link_cancel;?>";
}

function upd(obj) {
	obj.parentNode.parentNode.style.backgroundColor='#A6FFA6';
	obj.parentNode.innerHTML="覆蓋原有資料";
	//alert(obj.parentNode);
}
</script>


