
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <?php echo $pagetitle; ?>
          </div><!-- /.col -->
          <div class="col-sm-6">
						<?php echo $breadcrumb; ?>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
	<section class="content">
		<div class="container-fluid">
			<p>
				Display my content passed from the controller
			</p>
			<? if ( isset($foo) ) : ?>
				<div class="data">Our data: <b>foo === <?= $foo; ?></b></div>
			<? endif; ?>
		</div>
	</section>
</div>