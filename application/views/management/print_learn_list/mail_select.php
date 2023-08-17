<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 寄送研習紀錄
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">

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

                <form id="list-form" method="post" action="<?=$link_confirm;?>">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

                    <?php if($who != 10) :?>
                        <table class="table table-bordered table-condensed table-hover">
                            <thead>
                                <?php foreach ($list_header as $header) :?>
                                    <th><?=$header?></th>
                                <?php endforeach ?>
                                <th><input type="checkbox" name="all" id="all" onclick="chk_all(this.checked)">是否Email給<?=$email_who?></th>
                            </thead>
                            <tbody>
                                <?php switch($who) :
                                        case '1': ?>
                        
                                        <?php break; ?>                                 
                                    <?php case '2': ?>
                                        
                                        <?php break; ?>
                                    <?php case '3': ?>
                                        <?php foreach ($list as $row) :?>
                                            <tr>
                                                <td><?=$row->name;?></td>
                                                <td><?=$row->bureau_name;?></td>
                                                <td><?=$row->office_tel;?></td>
                                                <td><?=$row->email2;?></td>
                                                <td><input type="checkbox" name="email[]" value="<?=$row->email2.','.$row->username;?>"></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <?php break; ?>                                
                                    <?php case '4': ?>
                                        # code...
                                        <?php break; ?>  
                                    <?php case '8': ?>
                                        
                                        <?php break; ?>                                       
                                    <?php case '9': ?>
                                    
                                        <?php break; ?>  
                                    <?php case '6': ?>
                                        <?php break; ?>                                                                                         
                                <?php endswitch; ?>
                            </tbody>
                        </table>
                    <?php else:?>
                        

                    <?php endif ?>
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
