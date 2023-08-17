<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 
                <?php if($is_edap && ($_LOCATION['id']==619 || $_LOCATION['id']==637)){ ?>
                    <?php echo '28B 學員基本資料';?>
                <?php } else { ?>
                    <?=$_LOCATION['name'];?>
                <?php } ?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <input type="hidden" name="post" value="post" />
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">身分證字號</label>
                                <input type="text" class="form-control" name="idno" value="<?=$filter['idno'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">姓名</label>
                                <input type="text" class="form-control" name="name" value="<?=$filter['name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">局處名稱</label>
                                <input type="text" class="form-control" name="bname" value="<?=$filter['bname'];?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">身分別：離職</label>
                                <input type="checkbox" class="form-control" name="departure" value="Y" <?= isset($filter['departure']) && $filter['departure']=='Y'?'checked':'';?>>
                                <font color='red'>(已調離本府，系統不再介接)</font>
                            </div>
                            <div class="form-group">
                                <label class="control-label">退休</label>
                                <input type="checkbox" class="form-control" name="retirement" value="Y" <?= isset($filter['retirement']) && $filter['retirement']=='Y'?'checked':'';?>>
                                <font color='red'>(介接人事資訊系統已退休人員)</font>
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <input class="btn btn-info" type="button" value="查詢" class="button" style="font-size: 20px;line-height: 1.5;padding: 5px 10px;border-radius: 3px;height:42px" onclick="doQuery()">
                            <input class="btn btn-info" type="button" value="清除" class="button" style="font-size: 20px;line-height: 1.5;padding: 5px 10px;border-radius: 3px;height:42px" onclick="doClear()">
                            <input class="btn btn-info" type="button" value="新增" class="button" style="font-size: 20px;line-height: 1.5;padding: 5px 10px;border-radius: 3px;height:42px" onclick="doAdd()">
                            <a class="btn btn-info" type="button" value="批次新增匯入" class="button" style="font-size: 20px;line-height: 1.5;padding: 5px 10px;border-radius: 3px;height:42px" href="<?=base_url("data/student_manger/batch_import");?>">批次新增匯入</a>
                            <a class="btn btn-info" type="button" value="離職待確認" class="button" style="font-size: 20px;line-height: 1.5;padding: 5px 10px;border-radius: 3px;height:42px" href="<?=base_url("data/student_manger/download_resign_excel");?>">離職待確認</a>
                            <a class="btn btn-info" type="button" value="在職人員" class="button" style="font-size: 20px;line-height: 1.5;padding: 5px 10px;border-radius: 3px;height:42px" onclick="downloadlist()">在職清單</a>
                            <div class="form-group">
                                <label class="control-label">在職清單日期</label>
                                <input type="text" class="form-control datepicker" id="download_date" name="download_date" value="<?=date('Y-m-d'); ?>"/>
                            </div>                        
                        </div>
                        <div class="col-xs-6" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                    </form>
                    <form id="actQuery" method="POST" action="<?=$url_add;?>">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <input type="hidden" name="pid" value="" />
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead style="background-color: #8CBBFF">
                            <tr>
                                <th>功能</th>
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th  data-field="idno" >身分證</th>
                                <th  data-field="name" >姓名</th>
                                <th  data-field="bureau_name" >局處名稱</th>
                                <th  data-field="out_gov_name" >外機關名稱</th>
                                <th  data-field="birthday" >出生日期</th>
                                <th  data-field="job_title_name" >職稱</th>
                                <th  data-field="co_empdb_poftel" >公司電話</th>
                                <th  data-field="office_fax" >公司傳真</th>
                                <th  data-field="email" >Email</th>
                                <th  data-field="office_eamil" >公司Email</th>
                                <th  data-field="departure" >離職</th>
                                <th  data-field="retirement" >退休</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_edit'])) { ?>
                                    <a type="button" class="btn btn-default btn-xs btn-toggle" href="<?=$row['link_edit'];?>">
                                        明細
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_record'])) { ?>
                                    <a type="button" class="btn btn-default btn-xs btn-toggle" href="<?=$row['link_record'];?>" target="_blank">
                                        上課紀錄
                                    </a>
                                    <?php } ?>
                                    <a type="button" class="btn btn-default btn-xs btn-toggle" href="<?=$row['link_log'];?>" target="_blank">
                                        修改紀錄
                                    </a>
                                    <!-- <?php if (isset($row['link_delete'])) { ?>
                                    <button type="button" class="btn btn-default btn-xs" onclick="ajaxDelete(this, '確認要刪除「<?=$row['name'];?>」?', '<?=$row['link_delete'];?>')">
                                        刪除
                                    </button>
                                    <?php } ?> -->
                                </td>
                                <?php if (isset($row['link_record'])) { ?>
                                <td></td>
                                <?php } else { ?>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['id'];?>"></td>
                                <?php } ?>
                                <td><?=$row['idno'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['bureau_name'];?></td>
                                <td><?=$row['out_gov_name'];?></td>
                                <td><?=$row['birthday'];?></td>
                                <td><?=$row['job_title_name'];?></td>
                                <td><?=$row['co_empdb_poftel'];?></td>
                                <td><?=$row['office_fax'];?></td>
                                <td><?=$row['email'];?></td>
                                <td><?=$row['office_email'];?></td>
                                <td><?=($row['departure'] == '0')?'<span style="color:red">是</span>':'否';?></td>
                                <td><?=($row['retirement'] == '0')?'<span style="color:red">是</span>':'否';?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </form>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
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
<script>
$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});

function doQuery(){
  obj = document.getElementById("filter-form");
  obj.submit();
}

function doClear(){
  document.all.idno.value = "";
  document.all.name.value = "";
  document.all.bname.value = "";
  document.all.departure.value = "";
  document.all.retirement.value = "";
}

function doAdd(){
obj = document.getElementById("actQuery");
objfrom = document.getElementById("filter-form");
obj.pid.value = objfrom.idno.value;
 obj = document.getElementById("actQuery");
 if (obj.pid.value == "")
 {
    alert("請先輸入正確的身分證字號!");
    objfrom.idno.focus();
 } else {
    if ( obj.pid.value.length!=10) {
        if (!confirm('身分證字號長度非10碼，繼續?')) {
            objfrom.idno.focus();
            return false;
        }
    }
    obj.pid.value = obj.pid.value.toUpperCase();
    obj.submit();
 }
}

// var ajaxDelete = function(obj, msg, url) {
//     var data = {'<?=$csrf['name'];?>': '<?=$csrf['hash'];?>'};
//     var yesfunc = function() {
//         $.ajax({
//             url: url,
//             // data: $('#sentToBack').serialize(),
//             data: data,
//             type:"POST",
//             dataType:'json',
//             success: function(response){
//                 var sec = 4;
//                 var layout = 'topCenter';
//                 if (response.status == true) {
//                     var type = 2  // succeed

//                     // remove item
//                     $(obj).closest('tr').remove();
//                 } else {
//                     var type = 4 // error
//                 }
//                 bk_alert(type, response.message, sec, layout);
//             },
//             error:function(xhr, ajaxOptions, thrownError){
//                 bk_alert(4, xhr.status+' '+thrownError, 4, 'topCenter');
//             }
//         });

//     }

//     var nofunc = function() {
//         // bk_alert(4, 'ok', 4, 'center');
//     }
//     bk_confirm(3, msg, 'center', yesfunc, nofunc);
// }

function downloadlist(){
  //alert($('#download_date').val());
  var dl_date = $('#download_date').val().replace(/-/g, "");
  window.location.href = "./student_manger/download_incumbency_excel/"+dl_date;
}
</script>
