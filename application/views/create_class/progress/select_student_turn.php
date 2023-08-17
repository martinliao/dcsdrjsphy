<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <p><span style="color:red;float:right ">※轉寄成功後，人事人員信箱會同步收到Email副本</span></p>
                <table style='width:100%' class="table table-bordered table-condensed table-hover">
                    <tr>
                    <th class="tdr" style="width:12%;font-size:120%;">年度</th>
                    <td style="width:18%;"><?=$require->year?></td>
                    <th  class="tdr" style="width:15%;font-size:120%;">班期代碼<br>班期名稱</th>
                    <td style="width:25%;"><?=$require->class_no?><br><?=$require->class_name?></td>
                    <th  class="tdr" style="width:15%;font-size:120%;">期別</th>
                    <td style="width:15%">第<?=$require->term?>期</td>
                    </tr>
                </table> 

                <form id="list-form" method="post" action="<?=$link_confirm."#";?>">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="history_back" value="<?=$history_back?>">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <th>狀態</th>
                            <th>學號</th>
                            <th>局處</th>
                            <th>姓名</th>
                            <th>電話</th>
                            <th>E-mail</th>
                            <th><input type="checkbox" name="all" id="all" onclick="chk_all(this.checked)">是否Email給學員</th>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $row) :?>
                                <tr>
                                    <td><?=$row->yn_sel;?></td>                      
                                    <td><?=$row->st_no;?></td>
                                    <td><?=$row->bureau_name;?></td>
                                    <td><?=$row->name;?></td>
                                    <td><?=$row->office_tel;?></td>
                                    <?php 
                                        $email = [$row->email, $row->office_email];
                                        $email = array_filter($email);
                                    ?>
                                    <td><?=join(",", $email);?></td>
                                    <td><input type="checkbox" name="email[]" value="<?=join(",", $email);?>"></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <div class="col-lg-12 text-right">
        <input type="button" class="btn btn-primary" onclick="confirmFun()" title="confirm" value="確定">
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function chk_all(argtype){
    var obj=document.getElementsByName("email[]");
    var len = obj.length;

    for (i = 0; i < len; i++)
    {
        obj[i].checked = argtype;
    } 
}

function confirmFun(){
    var list_form = document.getElementById('list-form');
    list_form.submit();
}

function chkAllBreau(id, argtype){
    var obj = $('#' + id +' input[name="email[]"]');

    $('#' + id + '_row')[0].checked = argtype;
    $('#' + id + '_top')[0].checked = argtype;

    for (i = 0; i < obj.length; i++)
    {
        obj[i].checked = argtype;
    }     
}
</script>
