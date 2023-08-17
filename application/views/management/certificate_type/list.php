<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">證書名稱</label>
                                <input type="text" class="form-control" name="type_title_name" value="<?=$filter['type_title_name'];?>">
                                <button class="btn btn-info btn-sm">搜尋</button>
                                <a class="btn btn-info btn-sm" href="<?=base_url("management/certificate_type")?>">清除</a>
                            </div>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <a class="btn btn-info btn-sm" href="<?=base_url("management/certificate_type/type_add")?>">新增中文書證版型</a>
                            <a class="btn btn-info btn-sm" href="<?=base_url("management/certificate_type/en_type_add")?>">新增英文書證版型</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                
                <form method="POST" onsubmit="return confirmSettingQrcodeTime()">
                    <div class="text-right">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <label>台北通QRcode限1年有效期</label>
                    <select name="qRcodeTimeisOneYear">
                        <option value="N" <?=$qRcodeTimeisOneYear == 'N' ? 'selected' : ''?> >否</option>
                        <option value="Y" <?=$qRcodeTimeisOneYear == 'Y' ? 'selected' : ''?> >是</option>
                    </select>
                    <button name="action" value="setQRcodeTime">設定</button>
                    </div>
                </form>
                              
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#8CBBFF">
                            <th class="text-center">證書名稱</th>
                            <th class="text-center">功能</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row){ ?>
                        <tr>
                            <td><?=htmlspecialchars($row['title'], ENT_QUOTES);?></td>
                            <td>
                                <center>
                                    <a href="<?=$row['detail'];?>" class="btn btn-info">維護</a>
                                    &nbsp;
                                    <a class="btn btn-danger" onclick="checkDelete('<?=htmlspecialchars($row['title'], ENT_QUOTES);?>','<?=$row['id'];?>')">刪除</a>
                                    <!-- <a href="<?=$row['del'];?>" class="btn btn-info">刪除</a> -->
                                </center>
                            </td>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table>
                <form>
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
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
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

/*
  function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
  }
*/
  function checkDelete(title,id){
    var msg = "確定要刪除 <font style='color:red'>" + title + "</font> 嗎";
    // 因為 bk_confirm yes function 沒辦法傳遞函數暫時使用全域變數的方式
    flash_id = id;
    bk_confirm(0, msg, 'center', deleteClassInfo, cancel);
  }

  function cancel(){

  }

  function deleteClassInfo(){
    location.href = "<?=base_url();?>management/certificate_type/type_del/" + flash_id;
  }

  function confirmSettingQrcodeTime(value)
  {
    value = ($("select[name=qRcodeTimeisOneYear]").val() == 'Y') ?  '是' : '否';
    return confirm('內容出現原值為<?=$qRcodeTimeisOneYear == 'Y' ? '是' : '否'?>, 要改為' + value + '嗎?');
  }
</script>