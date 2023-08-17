<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=$_LOCATION['function']['name'] ;?>
				<!-- test button -->
				<button type="button" class="btn btn-success btn-sm" id="tambah0" data-seq_no=<?= $form['seq_no']?> data-toggle="modal" data-target="#booking_room">
					新預約教室
				</button>
				<!-- <button type="button" class="btn btn-success btn-sm" id="tambah0" data-seq_no=<?= $form['seq_no']?> data-toggle="modal" data-target="#booking_room">
					新預約教室
				</button> -->
			</div>
			<div class="panel-body">
				<?php include('form.inc.php');?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<!-- 擴充開始 -->
<?php include('core/modal.inc.php');?>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", () => {
		/** Call Createclass/init with bootstrap-modal-plugin */
		require(['jquery',"core/log","mod_Createclass/init", 'mod_bootstrapbase/bootstrap'], function($, log, createclass) { 
			log.setConfig({"level":"trace"}); 
			createclass.init();
		});
	});
</script>