<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 課表複製
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10%">天數</th>
                                <th style="width: 30%">選擇日期</th>
                                <th style="width: 60%">選擇教室</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $count = 1;?>
                        <?php $index = 0; ?>
                        <?php foreach ($date_list as $row) { ?>
                            <tr>
                                <td><?=$count?></td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="<?php echo $index;$index++;?>" name="use_date_<?=$row['use_date'];?>" value="" />
                                        <span class="input-group-addon" style="cursor: pointer;" id="<?php echo $index;$index++;?>" onclick="myMsg(this)"><i
                                            class="fa fa-calendar"></i></span>
                                       
                                    </div>
                                </td>
                                
                                <td>
                                    <input type="hidden" id="room_<?=$row['use_date'];?>" name="room_<?=$row['use_date'];?>" value=""></input>
                                    <input type="text" id="room_name_<?=$row['use_date'];?>" value="" style="width:53%;display:inline" disabled></input>
                                    <input type="button" class="btn btn-xs btn-primary" onclick="showRoom('<?=$row['use_date'];?>')" value="查詢" style="margin-left: 10px">
                                </td>
                            </tr>
                        <?php $count++; } ?>
                        </tbody>
                    </table>
                    <input type="hidden" id="mode" name="mode" value="">
                    <input type="button" value="設定完成" onclick="setUp()" />
                </form>
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
function myMsg(myObj){
    $(document).ready(function() {
        var b=Number(myObj.id);
        var a=b-1;
        //document.write(b);
        $('#'+a).datepicker();
        $('#'+b).click(function(){
            $('#'+a).focus();
        });
    }); 
}

function setUp(){
    var obj = document.getElementById('list-form');
    document.getElementById('mode').value = 'copy';
    obj.submit();
}

function showRoom(course_date){
    var key = 'use_date_'+course_date;
    var room_key = 'room_'+course_date;
    var room_name_key = 'room_name_'+course_date;
    var tmp = document.getElementsByName(key)[0].value;
    if (tmp!="")
    {
        myW=window.open('../../co_room_popup.php?mode=2&field1='+room_key+'&field2='+room_name_key+'&course_date='+tmp,'show_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=640');
        myW.focus();
    }
    else
    {
        alert("請先選擇日期");
    }
}

</script>
