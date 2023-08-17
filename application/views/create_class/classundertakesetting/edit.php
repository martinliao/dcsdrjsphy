<?=validation_errors();?>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
      </div>
      <div class="panel-body" >
        <?php include('form.php');?>
      </div>
    </div>
  </div>
</div>

