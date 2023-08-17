<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>

            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row"style="margin-bottom:2%">
                        <div class="col-xs-12">
                            <a style="margin-right:3%" href="<?=base_url('dcsdindex')?>">回首頁</a>
                            <!--<a style="margin-right:3%" href="<?=$refresh?>">查堂與抽查紀錄</a>-->
                            <a style="margin-right:3%" href="<?=$management?>">簽到退管理</a>
                            <a style="margin-right:3%" href="<?=$seat?>" target=_blank>座位表</a>
                        </div>
                    </div>
                    <form role="form" id="filter" class="form-inline" method="post" action="">
                    <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>">
                <table class="table  table-condensed table-hover">
                    <thead>
                        <tr>
                            <th colspan="6">臺北市政府公務人員訓練處 <?=$course_info[0]['year']?>年度<?=$course_info[0]['class_name']?>第<?=$course_info[0]['term']?>期 查堂與抽查紀錄表</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th class="text-center">日期</th>
                            <th class="text-center">時間(請輸入6碼數字)</th>
                            <th class="text-center">實到人數</th>
                            <th class="text-center">查堂抽查人員</th>
                            <th class="text-center">備註</th>
                            <th class="text-center">刪除</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" id="datepicker1" name="patrol_date_show" value="<?=$patrol_date_show;?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                <!--<input type="hidden" class="form-control"  name="patrol_date" value="<?=$patrol_date?>">-->

                            </div>
                            </td>
                            <td>
                            <div class="input-group" >
                                <input type="text"  class="form-control"name="patrol_time_show"value="<?=$use_time?>">
                                
                            </div>
                            </td>
                            <td><input type="text" class="form-control" size="2" name="real_number" value="0"></td>
                            <td><input type="text" class="form-control" size="3" value="<?=$name?>" disabled>
                                <input type="hidden" class="form-control" name="patrol_person" value="<?=$name?>">
                                <input type="hidden" class="form-control" name="seq_no" value="<?=$seq_no?>">
                            </td>
                            <td><input type="text" class="form-control" name="note"></td>
                            <td>-</td>
                        </tr>
                        <input type="hidden" name="mode" id="mode" value="">
                        <input type="hidden" name="item_id" id="item_id" value="">

                        <?php foreach($list as $row) {?>
                        <tr class="text-center">
                            <td><?=$row['patrol_date']?></td>
                            <td></td>
                            <td><?=$row['real_number']?></td>
                            <td><?=$row['patrol_person']?></td>
                            <td><?=$row['note']?></td>
                            <td><button  onclick='return save(1,<?=$row["id"]?>);'>刪除</button></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
                    <button onclick="return save(2);" class="btn btn-primary" style="float:right">儲存</button>
                    <button onclick="return back();" class="btn btn-default" style="float:right;margin-right:1%;" >返回</button>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>
function del(obj)
{
    var id=obj.id;
    alert(id);
}
function back()
{
    document.getElementById("filter").action = "<?=base_url("management/card_record/?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>";
   
}
function save(number,id)
{   
    //alert(id);
    if(number==2){
        //document.getElementById("filter").action = "<?=base_url("management/card_record/patrol/".$seq_no."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>";
        document.getElementById("filter").action = "<?=$save?>";
    }
    if(number==1){
		document.getElementById("mode").value = 'del';
        document.getElementById("item_id").value = id;
        document.getElementById("filter").action = "<?=base_url("management/card_record/patrol/".$seq_no."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>";
        //document.filter-form.submit();

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