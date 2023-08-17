<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php if($page_name == 'delete') { ?>
					<?=$_LOCATION['function']['name'] ;?>-減期作業
				<?php } else if ($page_name == 'cancel_class') { ?>
					<?=$_LOCATION['function']['name'] ;?>-取消班期
				<?php } ?>
			</div>
			<div class="panel-body">
				<?php include('form.php');?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->


