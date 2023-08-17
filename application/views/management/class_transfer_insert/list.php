<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="query_form_type" role="form" class="form-inline" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">原始班期:</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <?php
                                echo form_dropdown('year', $choices['year'], $form['year'], 'class="form-control" id="year_before"');
                            ?>
                            <label class="control-label">班期代碼與班期名稱:</label>
                            <input type="text" class="form-control" name='class_no_before' id='class_no_before' value="<?=$form['class_no'];?>" align="middle">
                            <input type='hidden' id="addClass_before" name="addClass_before">
                            <input type="text" class="form-control" name='class_name_before' id='class_name_before' value="<?=$form['class_name'];?>" align="middle">
                            <a class="btn btn-info btn-sm" onclick="showClass('addClass_before')" >查詢班期</a>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:3%">
                        <div class="col-xs-12">
                            <label class="control-label">選擇修課狀態:</label>
                            <?php
                                echo form_dropdown('yn_sel', $choices['yn_sel'], $form['yn_sel'], 'class="form-control"');
                            ?>
                            <label class="control-label">期別:</label>
                            <input type="text" name='term_before' id='term_before' value="<?=$form['term_before'];?>" class="form-control">
                            <label class="control-label">請輸入要報名的學員ID:</label>
                            <input type="text" name='studentid' id='studentid' value="<?=$form['studentid'];?>" class="form-control">
                            <a class="btn btn-info btn-sm" onclick="query_type_query()" >查詢學員</a>
                        </div>
                    </div>
                    <?php if($query_type != ''){ ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">轉入班期:</label>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:3%">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <?php
                                echo form_dropdown('year_move', $choices['year'], $form['year_move'], 'class="form-control" id="year_move"');
                            ?>
                            <label class="control-label">班期代碼與班期名稱:</label>
                            <input type="text" class="form-control" name='class_no_move' id='class_no_move' value="" align="middle" readonly>
                            <input type="text" class="form-control" name='class_name_move' id='class_name_move' value="" align="middle" readonly>
                            <input type='hidden' id="addClass_move" name="addClass_move">
                            <a class="btn btn-info btn-sm" onclick="showClass2('addClass_move')" >查詢班期</a>
                            <label class="control-label">期別:</label>
                            <input type="text" id="term_move" name="term_move" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" id="chkTable" name="chkTable" style="margin-bottom:10px">指定學員:</label>
                            <table class="table table-bordered table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">序號    <input type="checkbox" id="chkall"></th>
                                        <th class="text-center">身分證字號</th>
                                        <th class="text-center">姓名</th>
                                        <th class="text-center">局處</th>
                                        <th class="text-center">職稱</th>
                                        <th class="text-center">狀態</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($stud_list)){ $count_row = '1';?>
                                <?php foreach($stud_list as $row){ ?>
                                <tr>
                                <td class="text-center"><?=$count_row;?>   <input type="checkbox" id="chk" name="chk[]" value="<?=$row['id'];?>,<?=$row['beaurau_id'];?>"></td>
                                <td><?=$row['id'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['beaurau_name'];?></td>
                                <td><?=$row['title'];?></td>
                                <td><?=$choices['yn_sel'][$row['yn_sel']];?></td>
                                </tr>
                                <?php $count_row++; } ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-center">
                        <input type="hidden" name="oldterm" id="oldterm" value="<?=$form['term_before'];?>">
                        <input type="hidden" name="oldyear" id="oldyear" value="<?=$form['year'];?>">
                        <input type="hidden" name="oldclassno" id="oldclassno" value="<?=$form['class_no'];?>">
                        <a class="btn btn-info" onclick="do_save()" >確認</a>
                    </div>
                    <?php } ?>
                </form>
                <!-- /.table head -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>

var $form_list = $('#query_form_type');
$form_list.find('#chkall').click(function(){
    var checked = $(this).prop('checked');
    $form_list.find('tbody [type=checkbox]').each(function(){
        $(this).prop('checked', checked);
        if (checked == true) {
            $(this).closest('tr').addClass('active');
        } else {
            $(this).closest('tr').removeClass('active');
        }
    });
});
$form_list.find('tbody [type=checkbox]').click(function(){
    var checked = $(this).prop('checked');
    if (checked == true) {
        $(this).closest('tr').addClass('active');
    } else {
        $(this).closest('tr').removeClass('active');
    }
});

function query_type_query() {
    if(document.getElementById('year_before').value==""){
        alert("請輸入原始年度!");
        return false;
    }

    if(document.getElementById('term_before').value==""){
        alert("請輸入原始期別!");
        return false;
    }

    if(document.getElementById('class_no_before').value==""){
        alert("請輸入原始班期代碼!");
        return false;
    }
    obj=document.getElementById('query_form_type');
    obj.submit();
}

function showClass(x) {
  var myW=window.open('<?=$co_class_url;?>','selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}

function showClass2(x) {
  var myW=window.open('<?=$co_class_url2;?>','selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
  myW.focus();
}

function selClassOK() {
  var tmp = $('#addClass_before').val().split("::");

  $('#class_no_before').val(tmp[0]);
  $('#class_name_before').val(tmp[1]);
  $('#addClass_before').value = "";
}

function selClassOK2() {
    var tmp = $('#addClass_move').val().split("::");

    $('#class_no_move').val(tmp[0]);
    $('#class_name_move').val(tmp[1]);
    $('#addClass_move').value = "";
}

function chk_save() {

    if(document.getElementById('year_move').value==""){
        alert("請輸入轉入年度!");
        return false;
    }

    if(document.getElementById('term_move').value==""){
        alert("請輸入轉入期別!");
        return false;
    }

    if(document.getElementById('class_no_move').value==""){
        alert("請輸入轉入班期代碼!");
        return false;
    }

    return true;
}

function do_save() {
    obj=document.getElementById('query_form_type');
    var $chk_list = $('#query_form_type');
    var flag = false;
    $chk_list.find('tbody [type=checkbox]').each(function() {
        if (this.checked) {
            flag = true;
            // console.log(this);
        }
    });

    if (flag==false) {
        alert("請勾選人員!");
        return false;
    }

    flag = chk_save();

    if (flag === true) {

        var $form = $('#query_form_type');
        var url = '<?=base_url('management/class_transfer_insert/ajax/do_transfer');?>';

        $.ajax({
            url: url,
            data: $form.serialize(),
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            // console.log(response);
                            alert("轉入成功");
                            window.location = "<?=$link_refresh;?>";
                        } else {
                            // console.log(response);
                        }
                    }

        });

    }

}

</script>