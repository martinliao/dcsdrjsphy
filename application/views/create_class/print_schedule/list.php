<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="col-xs-12">
                        <div class="form-group row">
                            <label class="control-label">年度:</label>
                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label">班期代碼</label>
                            <input type="text" class="form-control" name="query_class_no" value="<?=$filter['query_class_no'];?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label">班期名稱</label>
                            <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                        </div>
                        <button class="btn btn-info btn-sm">查詢</button>
                        <div class="row">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>

                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">

                <!--<label>選擇列印型態:</label>
                <select id="tmp_seq" name="tmp_seq" onchange="open_item(this.value)">
  					<option value='0' selected>一般課表</option>
  					<option value='1'>完整課表</option>
				</select>
                <div class="form-group">
                    <label class="control-label">課表類別(完整課表才可選)</label>
                    <select id="item_id" name="item_id" disabled>
                        <option value="">請選擇</option>
                        <?php
                            for($i=0;$i<count($choices['item_id']);$i++){
                                echo '<option value="'.$choices['item_id'][$i]['tmp_seq'].'">'.$choices['item_id'][$i]['title'].'</option>';
                            }
                        ?>
                    </select>
                </div>-->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">承辦班期注意事項</th>
                            <th class="text-center">功能</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row) {?>
                        <tr>
                            <td class="text-center"><?=$row['year']?></td>
                            <td class="text-center"><?=$row['class_no']?></td>
                            <td class="text-center"><?=$row['term']?></td>
                            <td><?=$row['class_name']?></td>
                            <?php $note="";
                                  $text="";
                            if(!empty($row['note'])){
                                $note = "/base/admin/print_class_note.php?year=".$row['year']."&class_no=".$row['class_no']."&term=".$row['term'];
                                $text='注意事項';
                            }else{
                                $text="";
                            }
                            ?>
                            <td class="text-center"><a href="<?=$note?>" ><?=$text?></a></td>

                            <td class="text-center"><a href="<?=$row['link_detail']?>"  target="_blank" onclick="return check(this);" id="<?php echo $row['seq_no'];?>">查詢</a></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script>

function noteFun(year,class_no,term){
    alert(year);
    var url = "http://172.16.10.29/8D_note.php?year="+ year + "&class_no=" + class_no + "&term=" + term;
    window.open(url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=150");
}



function check(obj){
    tmp_seq = document.getElementById('tmp_seq').value;
    item_id = document.getElementById('item_id').value;
    
    if (tmp_seq=="1" && item_id==""){
        alert("請選擇課表類別"); 
        return false;    
    }
    var b=obj.id;
    if(tmp_seq==1&&item_id!=null){
        var href=document.getElementById(b).getAttribute("href"); 
        var test=document.getElementById(b).href=href+"&tmp_seq=1"; 
    }
}


function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

function open_item(val){
  obj = document.getElementById('item_id');
  if(val==0){
        obj.disabled = true;
        obj.value = "";
        //var href=document.getElementById("link").getAttribute("href"); 
        //var test=document.getElementById("link").href=href+"&tmp_seq=0"; 
	}else{
	    obj.disabled = false;
	    obj.value = "";
        //var href=document.getElementById("link").getAttribute("href"); 
        //var test=document.getElementById("link").href=href+"&tmp_seq=1"; 
	}
}
</script>