<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>發文單位</title>
</head>
<body>
  <div class='title' style="color:green;font-size:150%;width:100%">發文單位</div>
	<div id="Season_Schedule_List">
		<?php if(empty($buNames)){ ?>
			<div class='page_info' style='color:red;font-size:100%;'>查無資料</div>
		<?php }else{ ?>
			<p class="grid">
                <?=$buNames;?>
	        </p>
		<?php } ?>
	</div>
  </div>
</body>
<script>
</script>