<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="sort" value="" />
                    <div class="col-xs-12">
                        <div class="form-group row">
                            <label class="control-label">年度</label>
                            <?php
                                echo form_dropdown('year', $choices['year'], $filter['year'], 'class="form-control"');
                            ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label">班期代碼</label>
                            <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no'];?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label">班期名稱</label>
                            <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name'];?>">
                        </div>
                        <!--<label> <input type="checkbox" class="form-control" id="allQuery" value="<?=$filter['allQueryChecked'];?>">查詢所有研習人員名冊</label>-->
                        <label> <input type="checkbox" class="form-control" id="allQuery" value="1" name="allQueryChecked" <?= isset($filter['allQueryChecked']) && $filter['allQueryChecked']=='1'?'checked':'';?>>查詢所有研習人員名冊</label>

                        <button class="btn btn-info btn-sm">查詢</button>
                        <!--<input id="allQueryChecked" name="allQueryChecked" type="hidden" value="0"> -->
                        <div class="row">
                            <label class="control-label"> <input type="checkbox" class="form-control" id="ShowTel">顯示電話</label>
                            <label class="control-label"> <input type="checkbox" class="form-control" id="csv" >下載CSV</label>
                        </div>
                        <div class="row">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#5D7B9D"  style="color:white";>
                            <th>班期代碼</th>
                            <th>期別</th>
                            <th>班期名稱</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td style="width:15%"><a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>);"><?=$row['class_no'];?></a></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['class_name'];?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </form>
                <div class="row ">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
                </form>
            </div>
            <!-- /.table end -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>

function select(seq_no){
    if (seq_no == undefined){
        alert('找無此資料');
    }
    if(document.getElementById("ShowTel").checked==true) {
        var ShowTel ='1';
    }else{
        var ShowTel ='0';
    }
    if(document.getElementById("csv").checked==true) {
        var csv ='1';
        window.location.href='<?=base_url('management/print_student_list/csv?csv=1&ShowTel=');?>'+ShowTel+'&seq_no='+seq_no;
        $("body").unblock();  //取消load畫面
        return false;
    }else{

        var csv ='0';
        var url ='<?=base_url('management/print_student_list/show/');?>'+seq_no+'?csv=0&ShowTel='+ShowTel;
        window.open(url);
        //window.location.href= url;
    }
}
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
/*
if(allQueryChecked.value =='1')
{
    $("#allQuery").prop("checked",true);
}else{
    $("#allQuery").prop("checked",false);
}
function serch(){
//  if($("#allQuery").attr("checked")){
  if(document.getElementById("allQuery").checked==true) {
     allQueryChecked.value ='1';
  }else{
     allQueryChecked.value ='0';
  }
}*/

</script>