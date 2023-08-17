
<div class="row">
    <div class="col-lg-12">
	    <div class="panel panel-default">
		    <div class="panel-body">	
				<form id="filter-form" role="form" method="POST" action="<?=$send_email?>" enctype="multipart/form-data" target="_blank">
					<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
					<input type="hidden" name="send" value="" />
			    	<div class="col-xs-12">
			    		<label class="control-label" >收件者</label>
						<input class="form-control" type="" name="email" value="<?=$data['email']?>"  data-role="tagsinput">
						<input type="hidden" name="username" value="<?=$data['username']?>">
						<?php if(isset($signatures)): ?>
							<?php foreach ($signatures as $signature): ?>
								<input type="hidden" name="signatures[]" value="<?=$signature?>">
							<?php endforeach ?>
						<?php endif ?>
						<!-- data-role="tagsinput" -->	    		
			    	</div>
			    	<div class="col-xs-12" >
						<label class="control-label">標題</label>
						<input class="form-control" type="" name="title" value="<?=$data['mail_title']?>">
					</div>
					<div class="col-xs-6" >
						<label class="control-label">內文範本</label>
						<select class="form-control" id="mail_content_template">
							<option>請選擇範本</option>
							<?php foreach ($templates['course_content_template'] as $template): ?>
								<option value="<?=$template->id?>"><?=$template->title?></option>
							<?php endforeach ?>									
						</select>
					</div>
					<div class="col-xs-12">
						<label class="control-label">內文內容</label>
						<textarea class="form-control" id="mail_content" name="mail_content"></textarea>
					</div>	
				</form>
			</div>
		</div>
	</div>
</div>

<script src="<?=HTTP_PLUGIN;?>ckeditor_4.14.0_full/ckeditor/ckeditor.js"></script>

<!-- 如果需要 input tags 可以打開 start  -->

<!--  <link rel="stylesheet" type="text/css" href="<?=HTTP_PLUGIN;?>/bootstrap-tagsinput/app.css"> 
 <link rel="stylesheet" type="text/css" href="<?=HTTP_PLUGIN;?>/bootstrap-tagsinput/bootstrap-tagsinput.css"> 
 <script src="<?=HTTP_PLUGIN;?>bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script> 
 <style type="text/css">
	.bootstrap-tagsinput {
		line-height: 2;
	}
</style>  -->

 <!-- 如果需要 input tags 可以打開 end -->

<script type="text/javascript">

// $('#upload_file').delegate("input[name='email_file[]']",'change',function(){
// 	if (this.files[0].size/1024/1024 >= 5){
// 		alert('此檔案為' + Math.round(this.files[0].size/1024/1024) + "MB 已超過限制 5MB");
// 		this.value = '';
// 	}
// });  

$('#mail_content_template').on('change',function(){
	getTemplate(this.value, 'mail_content');
});

$('#course_content_template').on('change',function(){
	getTemplate(this.value, 'course_content');
	//alert(this.value);
});

function getTemplate(id, type){
	console.log(id);
	$.ajax({
	  method: "GET",
	  url: "/base/admin/data/template_list/getTemplate/" + id
	}).done(function(result) {
	    var template = JSON.parse(result);
	    //console.log(template);
	    
	    if (type == "mail_content"){
	    	if(template==null){
	    		CKEDITOR.instances.mail_content.setData("");
	    	}else{
	    		CKEDITOR.instances.mail_content.setData(template.content);
	    	}
	    	
	    }else if (type = "course_content"){
	    	if(template==null){
	    		CKEDITOR.instances.course_content.setData("");
	    	}else{
	    		CKEDITOR.instances.course_content.setData(template.content);
	    	}
	    	
	    }
		
	});	
}

function sendEmail(){
	var check = checkUploadSize();
	if (check == true){
		$("input[name='send']").val("true");
		$("#filter-form").attr("target", null);
		$("#filter-form").submit();		
	}else{
		alert("上傳檔案大小總和超過5MB");
	}

}

function view(){
	var check = checkUploadSize();
	if (check == true){
		$("input[name='send']").val("false");
		$("#filter-form").attr("target", "_blank");
		$("#filter-form").submit();
	}else{
		alert("上傳檔案大小總和超過5MB");
	}
}

function checkUploadSize(){
	var upload_file = $("input[name='email_file[]']");
	var size = 0;
	for (var i = 0; i < upload_file.length; i++) {
		if (upload_file[i].files.length > 0){
			size += upload_file[i].files[0].size;
		}		
	}
	size = (size / 1024) / 1024;
	return (size < 5);
}

$("#filter-form").keydown(function(e) {
  //Enter key
  if (e.which == 13) {
  	e.preventDefault();
    return false;
  }
});

$(function() {
	CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ',lineheight' : 'lineheight');
    CKEDITOR.replace('mail_content', {
        language: 'zh',
        uiColor: '#AADBCB',
    });
    CKEDITOR.replace('course_content', {
        language: 'zh',
        uiColor: '#AADBCB',
    });
});

function controlFile(action){
	var upload_file = $("#upload_file");
	if (action === "insert"){
		var file_element = '<div class="col-xs-2"><input type="file" name="email_file[]"></div>';
		upload_file.append(file_element);
	}else if (action === "delete"){
		upload_file = upload_file.children();
		if (upload_file.length-1 > 0){
			upload_file[upload_file.length-1].remove();
		}
	}
}

</script>