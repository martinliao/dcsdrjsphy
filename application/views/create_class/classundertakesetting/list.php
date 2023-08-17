<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline" enctype="multipart/form-data">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <input type="hidden" name="sort" value="" />  

                        <div class="col-xs-12" >
                            <label class="control-label">起訖日</label>
                            <div class="input-daterange input-group" >
                                <input type="text" class="form-control datepicker" name="start_date1" id="test1" value="<?=$filter['start_date1']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i class="fa fa-calendar"></i></span>
                            </div>
                       
                            <div class="input-daterange input-group" >
                                <input type="text" class="form-control datepicker" id="datepicker1" name="end_date1" value="<?=$filter['end_date1']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-xs-12">
                           <label class="control-label">班期代碼</label>
                           <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no']?>">
                           <label class="control-label">班期名稱</label>
                           <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name']?>">
                        </div>

                       <div class="col-xs-12">
                          <div class="form-group">
                          <label class="control-label">季別</label>
                              <?php
                                  $season = [
                                    0 => "請選擇季別",
                                    1 => 1,
                                    2 => 2,
                                    3 => 3,
                                    4 => 4
                                  ];
                                  echo form_dropdown('reason', $season, $filter['reason'], 'class="form-control"   onchange="changeCurrentWeek(this.value);"');
                              ?>
                          </div>
                       </div>

                       <div class="col-xs-12">
                          <label class="control-label">顯示筆數</label>
                          <?php
                              echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                          ?>
                       </div>

                       <div class="col-xs-12">  
                          <input type="checkbox" class="form-control" name="del_flag" value="C" <?= isset($filter['del_flag']) && $filter['del_flag']=='C'?'checked':'';?>>
                          <label class="control-label">查詢所有班期</label>
                       </div>

                       <div class="col-xs-12">
                          <input type="submit" name="" value="查詢" class="btn btn-info">
                       </div>

                    </form>
               </div>
                  
               <form id="list-form" method="post">
                   <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                   <input type="hidden" name="plan_status" id="plan_status" value="" />
                   <table class="table table-bordered table-condensed table-hover">
                   <?php if(count($list['data'])!=0){?>

                       <thead>
                           <tr>
                               <th>列序</th>
                               <th>系列</th>
                      <?php 
                      $fields = [
                          "year" => "年度",
                          "class_no" => "班期代碼",
                          "class_name" => "班期名稱",
                          "term" => "期別",
                          "apply_s_date" => "報名起日",
                          "apply_e_date" => "報名迄日",
                          "apply_s_date2" => "二次報名起日",
                          "apply_e_date2" => "二次報名迄日",
                          "contactor" => "承辦聯絡人",
                          "description" => "承辦單位",
                          "tel" => "承辦電話"
                          //"undertake_remark" => "備註"                                                   
                      ];
                     ?>
                  <?php foreach($fields as $key => $value) :?>
                    <th class="sorting<?=($filter['sort']==$key.' asc')?'_asc':'';?><?=($filter['sort']==$key.' desc')?'_desc':'';?>" data-field="<?=$key?>"><?=$value?></th>
                  <?php endforeach ?>
                  <th>備註<input type="button" class="btn btn-info" id="all-fun" type="button" onclick="allFun(this)" value="展開"></th>
                  <th>退費日期起訖日</th>
                  <th>延期轉班起訖日</th>
                  <th>修改</th>
                </tr>      
              </thead>
              <tbody>
              <?php $count = 0; ?>
              <?php foreach ($list["data"] as $row) : ?>
                <tr>
                  <!-- <td class="text-center"><input type="checkbox" name="rowid[]"  value="<?=$row->id;?>"></td> -->
                  <td width="2%"> <?=++$count;?> </td>
                  <td width="2%"> <?=$row->style;?> </td>                  
                  <td width="2%"> <?=$row->year;?> </td>
                  <td width="5%"> <?=$row->class_no;?> </td>
                  <td width="15%"> <?=$row->class_name;?> </td>
                  <td width="2%"> <?=$row->term;?> </td>
                  <td width="5%"> <?=$row->apply_s_date_format;?> </td>
                  <td width="5%"> <?=$row->apply_e_date_format;?> </td>
                  <td width="5%"> <?=$row->apply_s_date2_format;?> </td>
                  <td width="5%"> <?=$row->apply_e_date2_format;?> </td>
                  <td width="5%"> <?=$row->contactor;?> </td>
                  <td width="10%"> <?=$row->description;?> </td>
                  <td width="10%"> <?=$row->tel;?> </td>
                  <td width="10%"><button class="btn btn-info" type="button" onclick="showFun('<?=htmlspecialchars($row->id,ENT_HTML5|ENT_QUOTES);?>')">詳細內容</button><br><div class="push-content" style="display: none;" id="detail_<?=htmlspecialchars($row->id,ENT_HTML5|ENT_QUOTES);?>"><?=htmlspecialchars_decode($row->undertake_remark);?></div></td>
                  <td width="5%" align="center" valign="center"> 
                    <?php 
                  if($row->ref_s_date_format != ""){
                    echo $row->ref_s_date_format."<br>至<br>".$row->ref_e_date_format;
                  } ?> </td>
                  <td width="5%" align="center" valign="center"> <?php 
                  if($row->ext_s_date_format != ""){
                    echo $row->ext_s_date_format."<br>至<br>".$row->ext_e_date_format;
                  } ?> </td>
                  <td width="2%"> 
                    <a href="<?=base_url()."/create_class/class_undertake_setting/edit/".htmlspecialchars($row->seq_no, ENT_HTML5|ENT_QUOTES)."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES).""; ?>" class="btn btn-outline btn-warning btn-xs btn-toggle <?=htmlspecialchars($row->edit_disable, ENT_HTML5|ENT_QUOTES)?>"><i class="fa fa-pencil fa-lg"></i></a>
                  </td>
                </tr>
              <?php endforeach ?>
              <?php }else{?>
                  <span style="color:red;float:center">查無資料</span>
              <?php } ?>
              </tbody>
            </table>
          </form>
          <div class="col-lg-8 text-right">
            <?=$this->pagination->create_links();?>
          </div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });
  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });

  /*var d = new Date();
  var month = d.getMonth()+1;
  var day = d.getDate();

  
  if(month>=10&&month<=12){
      var test1=1;
      var test2=10;
      var end_date1=12;
      var end_date2=31;
      var output1 = d.getFullYear() + '-' + test2 + '-' + test1;
      var output2 = d.getFullYear() + '-' + end_date1 + '-' + end_date2;
      document.getElementById('test1').value=output1;
      document.getElementById('datepicker1').value=output2;
    }
    if(month>=1&&month<=3){
      var test1=1;
      var test2=1;
      var end_date1=3;
      var end_date2=31;
      var output1 = d.getFullYear() + '-' + test2 + '-' + test1;
      var output2 = d.getFullYear() + '-' + end_date1 + '-' + end_date2;
      document.getElementById('test1').value=output1;
      document.getElementById('datepicker1').value=output2;
    }
    if(month>=4&&month<=6){
      var test1=30;
      var test2=4;
      var end_date1=6;
      var end_date2=30;
      var output1 = d.getFullYear() + '-' + test2 + '-' + test1;
      var output2 = d.getFullYear() + '-' + end_date1 + '-' + end_date2;
      document.getElementById('test1').value=output1;
      document.getElementById('datepicker1').value=output2;
    }
    if(month>=7&&month<=9){
      var test1=31;
      var test2=7;
      var end_date1=9;
      var end_date2=30;
      var output1 = d.getFullYear() + '-' + test2 + '-' + test1;
      var output2 = d.getFullYear() + '-' + end_date1 + '-' + end_date2;
      document.getElementById('test1').value=output1;
      document.getElementById('datepicker1').value=output2;
    }


*/
});
//2021 Roger 將季別的訖日全改為年底，以防查不到跨季課程
function changeCurrentWeek(d)
{
  if(d<=1){
    sDate='01-01';
    eDate='12-31';
  }else if(d==2){
      sDate='04-01';
    	eDate='12-31';
  }else if(d==3){
    	sDate='07-01';
    	eDate='12-31';
  }else if(d==4){
    sDate='10-01';
    eDate='12-31';
  }
  var today=new Date();
  var y=today.getFullYear();
  document.getElementById('test1').value=y+'-'+sDate ;
  document.getElementById('datepicker1').value=y+'-'+eDate ;
}
</script>

<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
$(document).ready(function() {
$('#filter-form select').change(function(){
    $('#filter-form').submit();
});
});

function allFun(obj)
{
    if(obj.value == '展開'){
        $(".push-content").show();
        obj.value = '收合';
    } else if(obj.value == '收合'){
        $(".push-content").hide();
        obj.value = '展開';
    }
}

function showFun(id)
{
    if($("#detail_"+id).is(':hidden')){
        // $("#content").show();
        $("#detail_"+id).show();
    } else {
        $("#detail_"+id).hide();
    }
} 
</script>