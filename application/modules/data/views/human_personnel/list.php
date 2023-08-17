<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">

                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>局處名稱</th>
                                <th>帳號</th>
                                <th>承辦人姓名</th>
                                <th>性別</th>
                                <th>職稱</th>
                                <th>公司電話[分機]</th>
                                <th>公司傳真</th>
                                <th style="color: red;">Email-1(研習通知)</th>
                                <th style="color: red;">Email-2(勤惰通知)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=$user_data['name'];?></td>
                                <td><?=$user_data['username'];?></td>
                                <td><?=$user_data['co_usrnick'];?></td>
                                <?php if($user_data['gender'] == 'M'){ ?>
                                    <td>男</td>
                                <?php }else if($user_data['gender'] == 'F'){ ?>
                                    <td>女</td>
                                <?php }else{ ?>
                                    <td></td>
                                <?php } ?>
                                <td><?=$user_data['job_title_name'];?></td>
                                <td><?=$user_data['office_tel'];?></td>
                                <td><?=$user_data['office_fax'];?></td>
                                <td><?=$user_data['email'];?></td>
                                <td><?=$user_data['email2'];?></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="9">
                                    <a type="button" class="btn btn-default" onclick="go_detail('<?=$user_data['edit'];?>');">
                                        修改
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script>
function go_detail(url) {
    var myW=window.open(url, 'personnel_detail', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=400,width=1000');
    myW.focus();
}
</script>
