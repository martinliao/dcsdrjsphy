<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <input tpye="text" class="form-control" value="<?=$require['year'];?>" disabled>
                                <label class="control-label">班期代碼:</label>
                                <input tpye="text" class="form-control" value="<?=$require['class_no'];?>" disabled>
                                <label class="control-label">班期名稱:</label>
                                <input tpye="text" class="form-control" value="<?=$require['class_name'];?>" disabled>
                                <input type="hidden" name="year" id="year" value="<?=$require['year'];?>">
                                <input type="hidden" name="class_no" id="class_no" value="<?=$require['class_no'];?>">
                                <input type="hidden" name="term" id="term" value="<?=$require['term'];?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">原期別:</label>
                            <input tpye="text" class="form-control" value="<?=$require['term'];?>" disabled>
                            <label class="control-label">轉班類型:</label>
                            <select name='change_type' id='change_type' disabled>
                                <option value='1' >同局處同班</option>
                                <option value='2' >同局處不同班</option>
          		            </select>
                            <input  type="radio" class="form-group" name="rdo_type" value="single" checked="checked" onclick='document.getElementById("new_multi_term").disabled=true;document.getElementById("change_type").disabled=true;document.getElementById("new_term").disabled=false;document.getElementById("btnSave").disabled=true;'>單期
                            <?php
                                echo form_dropdown('new_term', $require['all_term'], '', 'class="form-control" id="new_term"');
                            ?>
                            &emsp;
                            <input  type="radio" class="form-group" name="rdo_type" value="multi" onclick='document.getElementById("new_term").disabled=true;document.getElementById("new_multi_term").disabled=false;document.getElementById("change_type").disabled=false;document.getElementById("btnSave").disabled=false;'>多期
                            <?php
                                echo form_dropdown('new_multi_term', $require['all_term'], '', 'class="form-control" id="new_multi_term" disabled ');
                            ?>

                            <input type='button' name="btnSave" id="btnSave" value="加入" onclick="add_chk();"  disabled/>
                            <div class="form-group">
                            	<table id="add_title" border="2" style="width: 100px;"><tr><td>期別</td></tr></table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <a class="btn btn-info" onclick="ck_val()" >確認</a>
                            <a href="<?=$link_cancel;?>" class="btn btn-info">回上頁</a>
                        </div>
                    </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">換期<input type="checkbox" id="chkall" class="form-group"></th>
                            <th class="text-center">學號</th>
                            <th class="text-center">局處名稱</th>
                            <th class="text-center">學員ID</th>
                            <th class="text-center">姓名</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($require_list){ ?>
                        <?php foreach($require_list as $row){ ?>
                        <tr>
                            <td width="60px" ><input type='checkbox' name='ck[]' value='<?=$row['id'];?>' ></td>
                            <td><?=$row['st_no'];?></td>
                            <td><?=$row['beaurau_name'];?></td>
                            <td><?=$row['id'];?></td>
                            <td><?=$row['name'];?></td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>

    var $form_list = $('#filter-form');
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

    function del_item(obj){
        //先取得目前的row數
        var tdItm=obj.parentElement;
        var trItm=tdItm.parentNode;
        var row1 = trItm.rowIndex
        document.getElementById("add_title").deleteRow(row1);
    }

    function add_chk()
    {
        var term = document.getElementById("new_multi_term").value;
        obj=document.getElementsByName("new_multi_term_array[]");
        for(i=0;i< document.getElementsByName("new_multi_term_array[]").length;i++ ){

            if(obj[i].value==term)
            {
                alert("此期別重覆加入!");
                return;
            }
        }

        var num = document.getElementById("add_title").rows.length;
        var tableObj = document.getElementById('add_title');
        var newTr = tableObj.insertRow();
        var newTd1 = newTr.insertCell();
                newTd1.align="left";
                newTd1.width="40%";
        var newTd2 = newTr.insertCell();
                newTd2.align="left";
                newTd2.width="60%";

      newTd1.innerHTML='<a href="#" onclick="del_item(this)">刪除</a>';
      newTd2.innerHTML=term+"<input type='hidden' name='new_multi_term_array[]' id='new_multi_term_array[]' value='"+term+"'/>";

    }

    function ck_val(){

       obj=document.getElementById("filter-form");

       var rdo = document.getElementsByName("rdo_type");
       for (i=0;i<rdo.length;i++) {
           if (rdo[i].checked) {
               rdo_val=rdo[i].value;
               break;
           }
       }

       if(rdo_val=="multi")
       {
         ado=document.getElementsByName("new_multi_term_array[]");
         if(ado.length==0){
                alert("請於多期加入期別!");
                return;
            }
       }
       flag=false;
       var $form_list = $('#filter-form');
       $form_list.find('tbody [type=checkbox]').each(function(){
              if($(this).prop("checked")==true)
              {
                flag=true;
              }

        });
       if(flag==false){
            alert("請勾選學員!");
            return;
        }

        var $form = $('#filter-form');
        var url = '<?=base_url('management/bb_1/ajax/do_transfer');?>';

        $.ajax({
            url: url,
            data: $form.serialize(),
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            if(response.msg){
                                alert(response.msg);
                            }
                            alert("轉班完成!");
                            window.location = "<?=$link_refresh;?>";
                        } else {
                            // console.log(response);
                        }
                    }

        });
    }

</script>
