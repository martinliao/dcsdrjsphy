<style>
tbody tr {
    background: #f1f1f1;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php if($mailFlag != '3'){ ?>
                <?=$point_send_msg;?>
                <?php } ?>
                <form name="field_form" id="field_form"  method='post' >
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="seq_no" value="<?=$require_data['seq_no'];?>" />
                    <input type="hidden" name="remark" value="<?=$remark?>"/>
                    <table style="width:100%" >
                        <?php if($mailFlag == '3'){ ?>
                        <tr>
                            <td class="tdr" style="text-align:left">
                                 年度：<?=$require_data['year'];?>
                            </td>
                            <td class="tdr" style="text-align:left">
                                 班期代碼：<?=$require_data['class_no'];?>
                            </td>
                            <td class="tdr" style="text-align:left">
                                期別：<?=$require_data['term'];?>
                            </td>
                            <td class="tdr" style="text-align:left">
                                 班期名稱：<?=$require_data['class_name'];?>
                            </td>
                        </tr>
                        <tr align="center">
                            <td colspan="4" >
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="grid th" style="width:20%">Email帳號</th>
                                            <th class="grid th" style="width:80%">發送內容預覽</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($list as $row){ ?>
                                        <tr>
                                            <td style="text-align: left;" >
                                                <?=$row['email'] ;?>
                                            
                                                    <?=!empty($row['office_email']) && !empty($row['email']) ? ',' : null ?>
                                                    <?=$row['office_email']?>

                                                <input type='hidden' name='chk[]'  value='<?=$row['id'] ;?>'></td>
                                            <td style="text-align: left;" ><?=$row['body'] ;?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td class="tdc" colspan="4" align="center">
                                <?php if($mailFlag == '3'){ ?>
                                <input type="button" name='save' value='寄送' class="button" onclick="chk_save();">
                                <?php } ?>
                                <input type="button" name="back" value="返回" class="button" onclick="go_back();">
                                <input type="hidden" name="status" id="status" value="">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>

    <?php if($mailFlag == '1'){ ?>
        alert('寄送失敗!');
    <?php }else if($mailFlag == '0'){ ?>
        alert('寄送成功!');
    <?php } ?>

    function go_back(){
        document.location = ('<?=$link_cancel;?>');
    }

    function chk_save(){
        obj=document.getElementById('field_form');
        document.getElementById('status').value='send';
        obj.submit();
    }
</script>