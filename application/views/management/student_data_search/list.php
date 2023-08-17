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
                        <div class="form-group">
                            <label class="control-label">期別</label>
                            <input type="text" class="form-control" name="term" value="<?=$filter['term'];?>">
                        </div>
                        <label> <input type="checkbox" class="form-control" id="allQuery" value="1" name="allQueryChecked" <?= isset($filter['allQueryChecked']) && $filter['allQueryChecked']=='1'?'checked':'';?>>選取所有班期</label>

                        <!--<button class="btn btn-info btn-sm" onclick="serch();">查詢</button>-->
                        <button class="btn btn-info btn-sm">查詢</button>

                        <!--<input id="allQueryChecked" name="allQueryChecked" type="hidden" value="0"> -->
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
                            <th class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                            <th class="sorting<?=($filter['sort']=='term asc')?'_asc':'';?><?=($filter['sort']=='term desc')?'_desc':'';?>" data-field="term" >期別</th>
                            <th class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach ($list as $row) { ?>
                            <tr>
                                <td width="15%"><a href="<?=$row['link_regist'];?>" ><?=$row['class_no'];?></a></td>
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

function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
/*if(allQueryChecked.value =='1')
{
    $("#allQuery").prop("checked",true);
}else{
    $("#allQuery").prop("checked",false);
}*/
/*function serch(){
//  if($("#allQuery").attr("checked")){
  if(document.getElementById("allQuery").checked==true) {
     allQueryChecked.value ='1';
  }else{
     allQueryChecked.value ='0';
  }
}*/


</script>