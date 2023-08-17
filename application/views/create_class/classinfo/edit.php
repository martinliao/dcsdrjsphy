<div class="row">

  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
      </div>
      <div class="panel-body">
        <form method="POST">
          <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
          <div class="row">
            <div class="col-xs-12" >
              <div class="form-group">
                <label class="control-label">班期名稱</label>
                <input type="text" class="form-control" name="title" value="<?=$classinfo['title']?>">
                <label>檔案</label>
                <div>
                  <a  href="<?=base_url();?>create_class/class_info/show/<?=$classinfo['id'];?>"><?=base_url();?>create_class/class_info/show/<?=$classinfo['id'];?></a>
                </div>
              </div>
            </div>             
            <div class="col-xs-12" >
              <label class="control-label">開課日期</label>
              <div class="input-daterange input-group" id="datepicker" style="width: 30%;">
                <input type="text" class="form-control datepicker" name="start_date" value="<?=$classinfo['start_date']?>"/>
                <span class="input-group-addon">to</span>
                <input type="text" class="form-control datepicker" id="datepicker1" name="end_date"  value="<?=$classinfo['end_date']?>"/>
                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
              </div>
            </div> 
          </div>
          <input type="submit" name="" class="btn btn-primary" value="修改">
        </form>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
    $("#datepicker1").datepicker();
        $('#datepicker2').click(function(){
    $("#datepicker1").focus();
    });
});
</script>