<script>

function doSave(){
  obj = document.getElementById("actQuery");
  document.getElementById("doAction").value='update';
  obj.submit();
}

function add_total(obj){
  val = 0
  for(i=0;i<document.getElementsByName("person[]").length;i++)
  {
    val_2=document.getElementsByName("person[]")[i].value;
    val_3=document.getElementsByName("person2[]")[i].value;
    if(val_2==''){
        val_2=0;
    }
        if(val_3!=''){
            if(parseInt(val_2)>parseInt(val_3)){
                alert("上限人數需大於下限人數");
                document.getElementsByName("person[]")[i].value='';
            }
            else{
                    val = parseInt(val) + parseInt(val_2);

        }
    }
    else
    {

        val = parseInt(val) + parseInt(val_2);
    }

  }

  document.getElementById("person_total").value = val;

}

function add2_total(obj){
  val = 0
  for(i=0;i<document.getElementsByName("person2[]").length;i++)
  {
    val_2=document.getElementsByName("person2[]")[i].value;
    val_3=document.getElementsByName("person[]")[i].value;

    if(val_2==''){
        val_2=0;
    }
    if(val_3==''){
        val_3=0;
    }
    if(parseInt(val_2)<parseInt(val_3)){
        alert("上限人數需大於下限人數");
        document.getElementsByName("person2[]")[i].value='';
    }
    else{
        val = parseInt(val) + parseInt(val_2);
    }
  }

  document.getElementById("person2_total").value = val;
}

// function $(_0){
//     return document.getElementById(_0);
// }

function popmenu(tableName,obj){
    if (document.getElementById(tableName).style.display=="none"){
        document.getElementById(tableName).style.display="block";
        document.getElementById(obj).getElementsByTagName("img")[0].src = "<?=HTTP_IMG;?>open.gif";

    }else{
        document.getElementById(tableName).style.display="none";
        document.getElementById(obj).getElementsByTagName("img")[0].src = "<?=HTTP_IMG;?>close.gif";
    }
}

</script>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['function']['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="actQuery" role="form" method="POST" class="form-inline">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:&ensp; <?=$class_data['year'];?> &emsp;</label>
                            <label class="control-label">班期名稱:&ensp;<?=$class_data['class_name'];?>&emsp;</label>
                            <label class="control-label">期別:&ensp;<?=$class_data['term'];?></label>
                        </div>
                    </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">局處代碼</th>
                            <th class="text-center">局處名稱</th>
                            <th class="text-center">人數下限</th>
                            <th class="text-center">人數上限</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_per = 0;
                        $total_per2 = 0;
                        ?>
                        <?php foreach($bureau_list as $bureau){ ?>
                            <tr>
                                <td width="45%" class="text-center">
                                    <a href="javascript:popmenu('tab_<?=$bureau['bureau_id'];?>','item_<?=$bureau['bureau_id'];?>');"  id="item_<?=$bureau['bureau_id'];?>"><img src="<?=HTTP_IMG;?>close.gif" border="0">
                                    <?=$bureau['bureau_id'];?>
                                    </a>
                                </td>
                                <td class="text-center"><?=$bureau['name'];?></td>
                                <td class="text-center">
                                    <input type="text" class="form-control" name="person[]" value="<?=$bureau['persons'];?>" onchange="add_total(this)" >
                                </td>
                                <td class="text-center">
                                    <input type="text" class="form-control" name="person2[]" value="<?=$bureau['persons_2'];?>" onchange="add2_total(this)" >
                                    <input type="hidden" id="hddid" name="hddid[]" value="<?=$bureau['bureau_id'];?>" />
                                </td>
                            </tr>
                            <?php
                            if(empty($bureau['persons'])){
                                $bureau['persons'] = '0';
                            }
                            if(empty($bureau['persons_2'])){
                                $bureau['persons_2'] = '0';
                            }
                            $total_per = $total_per+$bureau['persons'];
                            $total_per2 = $total_per2+$bureau['persons_2'];
                            $tmp_per=0;
                            $tmp_per2=0;
                            ?>

                            <tr style="display:none;" id="tab_<?=$bureau['bureau_id'];?>" >
                                <td colspan="4" >
                                    <table class="table table-bordered table-condensed table-hover">
                                        <?php foreach($bureau['lv4'] as $row){ ?>
                                        <tr>
                                            <td class="text-center"><?=$row['bureau_id'];?></td>
                                            <td class="text-center"><?=$row['name'];?></td>
                                            <td class="text-center">
                                                <input type="text" class="form-control" name="person[]" value="<?=$row['persons'];?>" onchange="add_total(this)" >
                                            </td>
                                            <td class="text-center">
                                                <input type="text" class="form-control" name="person2[]" value="<?=$row['persons_2'];?>" onchange="add2_total(this)" >
                                                <input type="hidden" id="hddid" name="hddid[]" value="<?=$row['bureau_id'];?>" />
                                            </td>
                                        </tr>
                                        <?php
                                        if(empty($row['persons'])){
                                            $row['persons'] = '0';
                                        }
                                        if(empty($row['persons_2'])){
                                            $row['persons_2'] = '0';
                                        }
                                        $total_per = $total_per+$row['persons'];
                                        $total_per2 = $total_per2+$row['persons_2'];
                                        $tmp_per= $tmp_per+$row['persons'];
                                        $tmp_per2 = $tmp_per2+$row['persons_2'];
                                        ?>
                                        <?php } ?>
                                    </table>
                                </td>
                            </tr>
                            <?php if(($tmp_per>0)||($tmp_per2>0)){ ?>
                            <script>
                                popmenu('tab_<?=$bureau['bureau_id'];?>','item_<?=$bureau['bureau_id'];?>');
                            </script>
                            <?php } ?>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td class="text-right" ><label class="control-label">合計:</label></td>
                            <td class="text-center">
                                <input type="text" class="form-control" id="person_total" name="person_total" value="<?=$total_per;?>" >
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control" id="person2_total" name="person2_total" value="<?=$total_per2;?>" >
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-info" id ='determine'>確定</button>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
function determine(){
    document.getElementById("determine").click();
}


</script>