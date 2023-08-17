<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <form method="POST" action="<?=base_url("customer_service/change_practice_report/detail?year={$require->year}&class_no={$require->class_no}&term={$require->term}")?>" name="modify_form" id="modify_form">
            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
            <div class="panel-body">
                <div class="row">
                   
                        <!-- /.table head -->
                        <table class="table table-bordered table-condensed table-hover">
                            <thead>
                                <tr class="text-center">
                                    <td colspan="9">
                                        年度：<?=$require->year?>&nbsp;&nbsp;&nbsp;&nbsp;班期代碼:<?=$require->class_no?>&nbsp;&nbsp;&nbsp;&nbsp;班期名稱：<?=$require->class_name?>&nbsp;&nbsp;&nbsp;&nbsp;期別：<?=$require->term?>&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <th><input type="checkbox" name="emails[]" id="checkAll" />Email給人事</th>
                                    <th>局處</th>
                                    <th>承辦人</th>
                                    <th>暱稱</th>
                                    <th>email</th>
                                    <th>電話</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($has as $ha): ?>
                                <tr>
                                    <td><input type="checkbox" name="mail[]" value="<?=$ha->email?>" /></td>
                                    <td><?=$ha->bc_name?></td>
                                    <td><?=$ha->name?></td>
                                    <td><?=$ha->co_usrnick?></td>
                                    <td><?=$ha->email?></td>
                                    <td><?=$ha->office_tel?></td>                                    
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    
                </div>
                <!-- /.table end -->
                <button class="btn btn-info btn-sm" onclick="check_submit()">確定</button>
                </form>
                <button class="btn btn-info btn-sm" onclick="go_back()">返回</button>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
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
function go_back(){
    obj=document.getElementById("modify_form");
    obj.action='<?=$link_refresh;?>';
    obj.submit();
}   
function check_submit(){
    var Formobj=document.getElementById("modify_form");
    var obj=document.getElementsByName("mail[]");
    var len = obj.length;
    var checked = false;
    for (i = 0; i < len; i++){
        if (obj[i].checked == true){
            checked = true;
            break;
        }
    } 
    if(checked==false) {
        alert("請勾選收件者!");
    }else{
        Formobj.submit();
    }
}
// .select all head //
$(function(){
    $('#checkAll').change(function() {
        //get all checkbox which want to change
        var checkboxes = $(this).closest('form').find('input[name="mail[]"]:checkbox');
        if($(this).is(':checked')) {
            checkboxes.prop('checked', 'checked');
        } else {
            checkboxes.removeAttr('checked');
        }
    });
    $('input[name="mail[]"]').change(function(){
      checkOrRemoveCheckAll();
    });
});
function checkOrRemoveCheckAll(){
if($('input[name="mail[]"]:checked').length == $('input[name="mail[]"]').length)
    {
        $('#checkAll').prop("checked", "checked");
    }
    else
    {
        $('#checkAll').removeAttr("checked");
    }
}
// .select all end //
</script>


