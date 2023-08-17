<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
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
                                        <?php foreach ($list as $row) :?>
                                            <tr>
                                                <td><?=$row->yn_sel;?></td>                      
                                                <td><?=$row->st_no;?></td>
                                                <td><?=$row->bureau_name;?></td>
                                                <td><?=$row->name;?></td>
                                                <td><?=$row->co_empdb_poftel;?></td>
                                                <td>
                                                    <?php 
                                                        $email = [$row->email, $row->office_email];
                                                        $email = array_filter($email);
                                                    ?>
                                                    <?=join(",", $email);?>
                                                </td>
                                                <td><input type="checkbox" name="email[]" value="<?=join(",", $email)?>"></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <?php break; ?>                                 
                                    <?php case '2': ?>
                                        <?php foreach ($list as $row) :?>
                                            <tr>
                                                <td><?=$row->teacher_name;?></td>
                                                <td><?=$row->class_name;?></td>
                                                <td>
                                                <?php 
                                                    $email = [$row->email1, $row->email2];
                                                    $email = array_filter($email);
                                                ?>
                                                <?=join(",", $email);?>                                                
                                                </td>
                                                <td><input type="checkbox" name="signatures[]" value="<?=$row->t_id;?>"></td>
                                                <td><input type="checkbox" name="email[]" value="<?=join(",", $email)?>"></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <?php break; ?>
                                    <?php case '3': ?>
                                        <?php foreach ($list as $row) :?>
                                            <tr>
                                                <td><?=$row->name;?></td>
                                                <td><?=$row->bureau_name;?></td>
                                                <td><?=$row->office_tel;?></td>
                                                <td><?=$row->email;?></td>
                                                <td><input type="checkbox" name="email[]" value="<?=$row->email;?>"></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <?php break; ?>                                
                                    <?php case '4': ?>
                                        # code...
                                        <?php break; ?>  
                                    <?php case '8': ?>
                                        <?php foreach ($list as $row) :?>
                                            <tr>
                                                <td></td>                      
                                                <td></td>
                                                <td><?=$row->bureau_name;?></td>
                                                <td><?=$row->name;?></td>
                                                <td><?=$row->office_tel;?></td>
                                                <td><?=(empty($row->office_email)) ? $row->email : $row->office_email;?></td>
                                                <td><input type="checkbox" name="email[]" value="<?=(empty($row->office_email)) ? $row->email : $row->office_email;?>"></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <?php break; ?>                                       
                                    <?php case '9': ?>
                                        <?php foreach ($list as $row) :?>
                                            <tr>
                                                <td><?=$row->name;?></td>                      
                                                <td><?=$row->bureau_name;?></td>
                                                <td><?=$row->telephone;?></td>
                                                <td>
                                                    <?php 
                                                        $email = [];
                                                        $email = [$row->email, $row->office_email];
                                                        $email = array_filter($email);
                                                    ?>
                                                    <?=join(",", $email)?>
                                                </td>
                                                <td><input type="checkbox" name="email[]" value="<?=join(",", $email)?>"></td>
                                            </tr>
                                        <?php endforeach ?>
                                        <?php break; ?>  
                                    <?php case '6': ?>
                                        <?php break; ?>                                                                                         
                                <?php endswitch; ?>
                            </tbody>
                        </table>
                    <?php else:?>
                        <table class="table table-bordered table-condensed table-hover">
                            <thead></thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox"  id="breau_0_top" value="" onclick="chkAllBreau('breau_0', this.checked)">Email給一級局處</td>
                                    <td><input type="checkbox"  id="breau_1_top" value="" onclick="chkAllBreau('breau_1', this.checked)">Email給二級局處</td>
                                    <td><input type="checkbox"  id="breau_2_top" value="" onclick="chkAllBreau('breau_2', this.checked)">Email給學校</td>
                                </tr>
                            </tbody>
                        </table>

                        <?php foreach ($list as $key => $list_data): ?>
                       <table class="table table-bordered table-condensed table-hover" id="breau_<?=$key?>">
                            <thead>
                                <?php foreach ($list_header as $header) :?>
                                    <th style="width: 25%"><?=$header?></th>
                                <?php endforeach ?>
                                <th><input type="checkbox" id="breau_<?=$key?>_row" onclick="chkAllBreau('breau_<?=$key?>', this.checked)">是否Email給<?=$list_data['who']?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($list_data['data'] as $row): ?>
                                    <tr>
                                    <td><?=$row->bureau_name;?></td>
                                    <td><?=$row->office_tel;?></td>
                                    <td><?=$row->email;?></td>
                                    <td><input type="checkbox" name="email[]" value="<?=$row->email;?>"></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>                            
                        <?php endforeach ?>

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
