<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form name='typeForm' id='typeForm' method='post' class="form-inline">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <!-- /.table head -->
                            <table id="AddTable" class="table table-bordered table-condensed table-hover">
                                <thead>
                                    <tr bgcolor="#8CBBFF">
                                        <th class="text-center" style="width: 5%">功能</th>
                                        <th class="text-center" style="width: 5%">年度</th>
                                        <th class="text-center">班期名稱</th>
                                        <th class="text-center">期別</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <label class="control-label">設定頒獎日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" size="4" name="print_year" > 年
                                <input type="text" size="4" name="print_month" > 月
                                <input type="text" size="4" name="print_day" > 日
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <input class="btn btn-info" type="button" value="列印績優名冊 (PDF)" onclick="exportPDF()">
                            <input class="btn btn-info" type="button" value="列印績優名冊(HTML)" onclick="exportHTML()">
                            <input class="btn btn-info" type="button" value="顯示發文單位" onclick="showBrueau()">
                        </div>
                    </div>
                </form>
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">請輸入關鍵字:</label>
                            <input type="text" name="class_name" value="<?=$filter['class_name'];?>" class="form-control">
                            <button class="btn btn-info btn-sm">搜尋</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#8CBBFF">
                            <th class="text-center">功能</th>                        
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row){ ?>
                        <tr class="text-center">
                            <?php $value = $row['year'] . "::" .$row['class_no'] . "::" . $row['class_name']."::" . $row['term']; ?>
                            <td style="width: 10%"><input id="Arrsel" class="btn btn-info" name="Arrsel" type="button" value="選取" onclick="popupOK(this, '<?=$value;?>')"></td>                        
                            <td style="width: 5%"><?=$row['year'];?></td>
                            <td><?=$row['class_no'];?></td>
                            <td><?=$row['class_name'];?></td>
                            <td><?=$row['term'];?></td>
                        </tr>
                        <?php } ?>
                        <input type='hidden' id="addClass" name="addClass">
                    </tbody>
                </table>
                <div class="row ">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>

$(document).ready(function(){
    $("#test1").datepicker();
        $('#test2').click(function(){
            $("#test1").focus();
        });
    }
);
function popupOK(x, value){
  document.getElementById("addClass").value = value;
  selClassOK();
}
function selClassOK()
{
    var tableObj = document.getElementById('AddTable');
    tableObj.style.display = "";

    var obj = document.all.addClass;
    var tmp = obj.value.split("::");
    obj.value = "";
    val_obj=document.getElementsByName('pkey[]');
    for(i=0;i<val_obj.length;i++){
        val = tmp[0]+","+tmp[1]+","+tmp[3]
        if(val==val_obj[i].value){
            alert("已重複!");
            return;
        }
    }

    var num = document.getElementById("AddTable").rows.length;
    var tableObj = document.getElementById('AddTable');
    var newTr = tableObj.insertRow();
    var newTd1 = newTr.insertCell();
            newTd1.align="center";
            newTd1.width="3%";
    var newTd2 = newTr.insertCell();
            newTd2.align="center";
            newTd2.width="3%";
    var newTd3 = newTr.insertCell();
            newTd3.align="center";
            newTd3.width="40%";
    var newTd4 = newTr.insertCell();
            newTd4.align="center";
            newTd4.width="10%";

    newTd1.innerHTML='<a class="btn btn-info" onclick="del_item(this);">刪除</a>';
    newTd2.innerHTML=tmp[0];
    newTd3.innerHTML=tmp[2];
    newTd4.innerHTML=tmp[3]+"<input type='hidden' name='pkey[]' id='pkey[]' value='"+tmp[0]+","+tmp[1]+","+tmp[3]+"'>";
    

}

function del_item(obj){
    var tdItm=obj.parentElement;
    var trItm=tdItm.parentNode;
    var row1 = trItm.rowIndex
    //先取得目前的row數
    var num = document.getElementById("AddTable").rows.length-1;
    //防止把標題跟原本的第一個刪除XD
    if(num >0)
    {
        //刪除最後一個
        document.getElementById("AddTable").deleteRow(row1);
    }
}

function exportPDF(){
    obj = document.getElementById("typeForm");
    var myW = window.open ("", "SubWindow", "height=800, width=1024, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, status=no");
    myW.focus();
    obj.action = '<?=$link_seq_pdf;?>';
    obj.target = "SubWindow";
    obj.submit();
}

function exportHTML(){
    obj = document.getElementById("typeForm");
    var myW=window.open ("", "SubWindow", "height=800, width=1024, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, status=no");
    myW.focus();
    obj.action = '<?=$link_seq_pdf;?>?output_html';
    obj.target = "SubWindow";
    obj.submit();
}

function showBrueau(){
    obj = document.getElementById("typeForm");
    var myW=window.open ("", "SubWindow", "height=800, width=1024, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, status=no");
    myW.focus();
    obj.action = '<?=$link_bureau;?>';
    obj.target = "SubWindow";
    obj.submit();
}
</script>