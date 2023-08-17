<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=$_LOCATION['function']['name'] ;?>
				<a class="btn btn-success btn-sm" href="<?= base_url()?>Createclass/edit/<?= $form['seq_no']?>" class="href">轉到-新預約教室</a>
			</div>
			<div class="panel-body">
				<?php include('form.php');?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

