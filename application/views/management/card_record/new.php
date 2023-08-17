<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" method="post" action="">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">人工簽到數:</label>
                            
                            <input class="form-control" name="hand_people_num" value=<?=$num[0]['hpn']?>>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">身障人數:</label>
                            <input class="form-control" name="disabled_people_num" value=<?=$num[0]['dpn']?>>
                        </div>
                        <div class="col-xs-12">
                            <p style="color:red">＊若是按下服務員填報完成，就不能再次進行填寫</p>
                        </div>
                        <input type="hidden" name="enable" id="enable" value="">
                        <div class="col-xs-12">
                            <button class="btn btn-info" onclick="selectAction(1);">儲存設定</button>
                            <button class="btn btn-info" onclick="selectAction(2);">服務員填報完成</button>
                            <a href="<?=base_url("management/card_record")?>" class="btn btn-info">返回</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>
function selectAction($number){
    if($number==1){
        document.getElementById("filter-form").action = "<?=$link_new;?>";
        document.getElementById("enable").value=0;
        document.filter-form.submit();
        
    }
    if($number==2){
        document.getElementById("filter-form").action = "<?=$link_new;?>";
        document.getElementById("enable").value=1;
        //document.filter-form.submit();
        alert('修改完成');
        
    }
}


$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });
  $("#test3").datepicker();
  $('#test4').click(function(){
    $("#test3").focus();
  });


  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });
});

</script>