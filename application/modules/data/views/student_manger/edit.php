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
				<?php include('form.php');?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

