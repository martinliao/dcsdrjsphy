<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?><span style="color:red">供管理班期、採購班期，有較複雜參訓條件時使用</span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <table class="table table-bordered table-condensed ">
                    <tr>
                        <td class='tdl' colspan="4">
                            <input type="button" class="btn btn-info" value="選取班期" class="button" onclick="showClass('addClass')">
                        </td>
                    </tr>
                </table>
                <form action="<?=$exportCSV;?>" name='typeForm' id='typeForm' method='post'>
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>"/>
                <input type='hidden' id="addClass" name="addClass">
                <table id="AddTable" style="width:60%" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">刪除</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">開課起迄日</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </form>
                <br>
                <div id="btn_import" style=""><a type="button" class="btn btn-info"  onclick="exportCSV()">匯出多班期學員名冊</a></div>
                
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
function showClass(x){
    var myW=window.open('<?=$show_class;?>','selCourse','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
    myW.focus();
}

function selClassOK()
{
    var tableObj = document.getElementById('AddTable');
    var btnObj = document.getElementById('btn_import');

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
            newTd1.align="left";
            newTd1.width="10%";
    var newTd2 = newTr.insertCell();
            newTd2.align="left";
            newTd2.width="10%";
    var newTd3 = newTr.insertCell();
            newTd3.align="left";
            newTd3.width="70%";
    var newTd4 = newTr.insertCell();
            newTd4.align="left";
            newTd4.width="10%";
    var newTd5 = newTr.insertCell();
            newTd5.align="left";
    newTd1.innerHTML='<a href="#" onclick="del_item(this);">刪除</a>';
    newTd2.innerHTML=tmp[0];
    newTd3.innerHTML=tmp[2];
    newTd4.innerHTML=tmp[3]+"<input type='hidden' name='pkey[]' id='pkey[]' value='"+tmp[0]+","+tmp[1]+","+tmp[3]+"'>";
    newTd5.innerHTML=tmp[4];

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

function exportCSV(){
    var obj = document.getElementById('typeForm');
    obj.submit();
}

</script>
