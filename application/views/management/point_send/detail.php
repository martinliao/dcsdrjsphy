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
                                <label class="control-label">年度</label>
                                <input type="text" class="form-control" value="<?=$require_data['year'];?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$require_data['class_no'];?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$require_data['term'];?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$require_data['class_name'];?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <a onclick="chk_save();" class="btn btn-info btn-sm">確認</a>
                            <a href="<?=$link_cancel;?>" class="btn btn-info btn-sm">返回</a>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <form id="field_form" method="POST" action="<?=$send_url;?>" role="form">
                    
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-xs-12">
                            <label class="control-label">註解：</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="remark" class="form-control" placeholder="輸入信件寄送文字說明，限500字元內"></textarea>
                        </div>
                    </div>
                     
                    <table class="table table-bordered table-condensed table-hover" id="chkTable" name="chkTable">
                        <thead>
                            <tr>
                                <th>全選<input type='checkbox' name='sel_all' id='checkAll' >學號</th>
                                <th>組別</th>
                                <th>姓名</th>
                                <th>服務單位</th>
                                <th>組別</th>
                                <th>email帳號</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <input type="hidden" name="seq_no" value="<?=$require_data['seq_no'];?>" />
                            <?php foreach($list as $row){ ?>
                            <tr>
                                <td>
                                    <input type='checkbox' name='chk[]' class='checkbox1' value='<?=$row['id'];?>'></input>
                                    <?=$row['st_no'];?>&nbsp;
                                </td>
                                <td><?=$row['group_no'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['beaurau_name'];?></td>
                                <td><?=$row['title_name'];?></td>
                                <td>
                                    <?=$row['email'];?>


                                        <?=!empty($row['office_email']) && !empty($row['email']) ? ',' : null ?>
                                        <?=$row['office_email']?>
                                   
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>                 
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>
    $("#checkAll").change(function(){
        $(".checkbox1").prop('checked', $(this).prop("checked"));
    });

    function chk_save()
    {

        obj=document.getElementById('field_form');
        flag = false;
        $("#chkTable :checkbox").each(function() {
    if(this.checked){
        flag = true;
        }
        });

   if(flag==false) {
    alert("請勾選人員!")
    return;
   }

        obj.submit();

    }
</script>