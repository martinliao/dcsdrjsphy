
<div class="row">
   <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
        </div>
            <!-- /.panel-heading -->
        <div class="panel-body">
          <div class="row">
            <form id="filter-form" role="form" class="form-inline" enctype="multipart/form-data">
              <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
              <input type="hidden" name="sort" value="" />
              <div class="col-xs-5" >
                <div class="form-group">
                  <label class="control-label">班期名稱</label>
                  <input type="text" class="form-control" name="title" value="">
                  <label>上傳檔案</label>
                  <input type="file" class="form-control" name="class_info_file" accept=".pdf">
                </div>
              </div>             
              <div class="col-xs-4" >
                <label class="control-label">開放日期~結束日期</label>
                <div class="input-daterange input-group" id="datepicker" >
                  <input type="text" class="form-control" name="start_date" id="datepicker1" value=""/>
                  <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                            class="fa fa-calendar"></i></span>
                  <span class="input-group-addon">to</span>
                  <input type="text" class="form-control" name="end_date" id="test1" value=""/>
                  <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                            class="fa fa-calendar"></i></span>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="form-group">
                  <label class="control-label">顯示筆數</label>
                  <?php
                      echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                  ?>
                  <input type="submit" name="" value="查詢" class="btn btn-info">
                  <input type="button" value="新增" onclick="add()" class="btn btn-primary">
                  <span style="color: red "><br>【備註：只限PDF檔，最大：5MB】</span>
                </div>
              </div>
            </form>
          </div>

          <form id="list-form" method="post">
            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
            <input type="hidden" name="plan_status" id="plan_status" value="" />
            <table class="table table-bordered table-condensed table-hover">
              <thead>
                <tr>
                  <!-- <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th> -->
                  <th>列序</th>
                  <?php 
                      $fields = [
                          "title" => "名稱", 
                          "fname" => "檔名", 
                          "start_date" => "開放日期", 
                          "end_date" => "結束日期", 
                          "cre_date" => "上傳日", 
                          "cre_user" => "上傳者", 
                          "upd_date" => "更新日", 
                          "upd_user" => "更新者"
                      ];
                  ?>
                  <?php foreach($fields as $key => $value) :?>
                    <th class="sorting<?=($filter['sort']==$key.' asc')?'_asc':'';?><?=($filter['sort']==$key.' desc')?'_desc':'';?>" data-field="<?=$key?>"><?=$value?></th>
                  <?php endforeach ?>
                  <th>URL</th>
                  <th></th>
                </tr>      
              </thead>
              <tbody>
              <?php $count = 0; ?>
              <?php foreach ($list["data"] as $row) : ?>
                <tr>
                  <!-- <td class="text-center"><input type="checkbox" name="rowid[]"  value="<?=$row->id;?>"></td> -->
                  <td> <?=++$count;?> </td>
                  <td> <?=$row->title;?> </td>                  
                  <td> <?=$row->fname;?> </td>
                  <td> <?=$row->start_date;?> </td>
                  <td> <?=$row->end_date;?> </td>
                  <td> <?=$row->cre_date;?> </td>
                  <td> <?=$row->cre_user;?> </td>
                  <td> <?=$row->upd_date;?> </td>
                  <td> <?=$row->upd_user;?> </td>
                  <td style="width: 35px;" class="text-center"><a href="<?=base_url();?>create_class/class_info/show/<?=$row->id;?>"><?=base_url();?>create_class/class_info/show/<?=$row->id;?></a></td>
                  <td class="text-center">
                    <a href="<?=base_url();?>create_class/class_info/edit/<?=$row->id?>" class="btn btn-primary">編輯</a>
                    <a class="btn btn-danger" onclick="checkDelete('<?=$row->title?>','<?=$row->id?>')">刪除</a>
                  </td>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          </form>
          <div class="row">

            <div class="col-lg-8 text-right">
              <?=$this->pagination->create_links();?>
            </div>
          </div>
        </div>
        <!-- /.panel-body -->
    </div>
        <!-- /.panel -->
  </div>
    <!-- /.col-lg-12 -->
</div>

<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>

<script type="text/javascript">
  var flash_id = "";
  function confirmFun(){
    var obj = document.getElementById('list-form');
    var plan_status = document.getElementById('class_status').value;
    document.getElementById('plan_status').value = plan_status;

    obj.submit();
  }  

  function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
  }

  function add(){
    var check = check_add();
    console.log(check);
    if (check){
      var obj = document.getElementById('filter-form');
      obj.action = "<?=base_url();?>create_class/class_info/store";
      obj.method = "POST";
      obj.submit();      
    }
  }

  function check_add(){
    var fields = {
      "title" : "班級名稱", 
      "class_info_file" : "檔案", 
      "start_date" : "開課日期(起)", 
      "end_date" : "開課日期(訖)"
    };

    var stop = false;
    Object.keys(fields).map(function(item,key){
      if (stop == true) return true;
      var field = document.getElementsByName(item)[0];
      if (field.value == ""){
        alert("請補上" + fields[item]);
        stop = true;
      }
    });
    return !stop;
  }

  function checkDelete(title,id){
    var msg = "確定要刪除 <font style='color:red'>" + title + "</font> 嗎";
    // 因為 bk_confirm yes function 沒辦法傳遞函數暫時使用全域變數的方式
    flash_id = id;
    bk_confirm(0, msg, 'center', deleteClassInfo, cancel);
  }

  function cancel(){

  }

  function deleteClassInfo(){
    location.href = "<?=base_url();?>create_class/class_info/delete/" + flash_id;
  }
$(document).ready(function() {
    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
        $("#datepicker1").focus();
    });
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });
});
</script>