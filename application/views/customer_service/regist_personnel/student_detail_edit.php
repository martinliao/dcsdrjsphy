
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>學員個人資料</title>
<script src="<?=HTTP_PLUGIN;?>jquery-1.12.4.min.js"></script>
</head>
<body>
  <div class='title' style="color:green;font-size:150%;width:100%">學員個人資料</div>
<div class='insert_block'>
  <form name='applicant_insert_form' method='post' id='applicant_insert_form' >
  	<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
	  <table class="table" >
	    <tr>
	      <th bgcolor="#DCDCDC" style="font-size:100%">姓名</th>
	      <td ><input type="hidden" name="name" id="name" value="<?= $student_edit['name'];?>" ><?= $student_edit['name'];?></td>
	      <th bgcolor="#DCDCDC"  style="font-size:100%">職稱</th>
	      <td>
	      	<input type="hidden" name="tmp_tid" id="tmp_tid" value=""><input type="hidden" name="title" id="title" value="<?= $student_edit['title']; ?>"><input type="text" name="t_name" id="t_name" value="<?= $student_edit['t_name'];?>" size="20" readonly>
					<input type="button" value="查詢職稱" onclick=showtitle("tmp_tid","0") class="button">
	      </td>
	    </tr>

	    <tr>
	      <th bgcolor="#DCDCDC"  style="font-size:100%">私人Email</th>
	      <td><input type="text" name="email" id="email" value="<?= $student_edit['email'];?>" ></td>
	      <th bgcolor="#DCDCDC"  style="font-size:100%">公司電話</th>
	      <td><input type="text" name="office_tel" id="office_tel" value="<?= $student_edit['office_tel'];?>" ></td>
	    </tr>
	    <tr>
	      <th bgcolor="#DCDCDC"  style="font-size:100%">原局處名稱</th>
	      <td>
						<?= $student_edit['b_name_old'];?>
	      </td>
	       <th bgcolor="#DCDCDC"  style="font-size:100%">新局處名稱</th>
	      <td>
	      		<input type="hidden" name="bid" id="bid" value="<?= $student_edit['bid_new']; ?>"><input type="text" name="bname" id="bname" value="<?= $student_edit['bname_new'];?>" size="30" readonly>
	      </td>
	    </tr>

			  <tr>
				  <td colspan="4" style="background-color:#DCDCDC;">
	    			<input type='button' value='儲存'  class='button' onclick='SaveForm()' />
	    			<input type='button' value='取消'  class='button' onclick='	window.close();' />
	    			<input type="hidden" name="mode" id="mode" value="" >
	    			<input type="hidden" name="row" id="row" value="<?= $student_edit['row']; ?>" >
	    			<input type="hidden" name="id" id="id" value="<?= $student_edit['id']; ?>" >
				  </td>
				</tr>


	  </table>
	  <h2 style="color: red">※學員「公務信箱」係介接WebHR人力資源管理系統，如有誤請逕至該系統修正。</h2>
</form>

</div>
</body>
</html>
<script>
function SaveForm(){
  obj = document.getElementById("applicant_insert_form");

  var $form = $('#applicant_insert_form');
	var url = '<?=base_url('customer_service/regist_personnel/ajax/student_edit');?>';

    $.ajax({
        url: url,
        data: $form.serialize(),
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                    	alert('新增成功!');

                    	window.opener.again_show(<?=$student_edit['row'];?>,'<?= $student_edit['idno']; ?>');
                    	window.close();
                    } else {

                    }
                }

    });
}
function showbeaurau(){
  var myW = window.open('<?=base_url('customer_service/regist_personnel/bureau');?>','selbeaurau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}
function selbeaurauOK(){
  var obj = document.all.tmp_bid;
  var tmp = obj.value.split("::");
  document.all.bid.value = tmp[0];
  document.all.bname.value = tmp[1];
  obj.value = "";
}

function showtitle(x,y){
  var myW=window.open('<?=base_url('customer_service/regist_personnel/co_title');?>','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}
function seltitleOK(){
  var obj = document.all.tmp_tid;
  var tmp = obj.value.split("::");
  document.all.title.value = tmp[0];
  document.all.t_name.value = tmp[1];
  obj.value = "";
}

</script>