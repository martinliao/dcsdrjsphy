<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <table>
                    <tr>
                        <td>用餐時間</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["use_date"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>年度</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["year"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>班期代碼</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["class_no"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>班期名稱</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["class_name"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>期別</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["term"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>承辦人</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["WORKER_NAME"]:"" ?>"></td>
                    </tr>
                </table>
                <hr size="12px" align="center" width="100%">
                <table>
                    <tr>
                        <td>早餐人數</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["persons_1"]:"" ?>"></td>
                        <td>講師/助教</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["TEACHER_CNT1"]:"" ?>"></td>
                        <td>追加人數</td>
                        <td><input id="add1" value="<?=sizeof($datas) >0 ? $datas[0]["add_persons_1"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>早餐單價</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["AMT1"]. ($datas[0]["TYPE1"] == 1 ?" / 人":" / 桌"):"" ?>"></td>
                        <td>早餐金額</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["amount_1"]:"" ?>"></td>
                    </tr>
                </table>
                <hr size="12px" align="center" width="100%">
                <table>
                    <tr>
                        <td>午餐人數</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["persons_2"]:"" ?>"></td>
                        <td>講師/助教</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["TEACHER_CNT2"]:"" ?>"></td>
                        <td>追加人數</td>
                        <td><input id="add2"  value="<?=sizeof($datas) >0 ? $datas[0]["add_persons_2"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>午餐單價</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["AMT2"]. ($datas[0]["TYPE2"] == 1 ?" / 人":" / 桌"):"" ?>"></td>
                        <td>午餐金額</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["amount_2"]:"" ?>"></td>
                    </tr>
                </table>
                <hr size="12px" align="center" width="100%">
                <table>
                    <tr>
                        <td>晚餐人數</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["persons_3"]:"" ?>"></td>
                        <td>講師/助教</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["TEACHER_CNT3"]:"" ?>"></td>
                        <td>追加人數</td>
                        <td><input id="add3"  value="<?=sizeof($datas) >0 ? $datas[0]["add_persons_3"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>晚餐單價</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["AMT3"]. ($datas[0]["TYPE3"] == 1 ?" / 人":" / 桌"):"" ?>"></td>
                        <td>晚餐金額</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["amount_3"]:"" ?>"></td>
                    </tr>
                </table>
                <hr size="12px" align="center" width="100%">
                <table>
                    <tr>
                        <td>總金額</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["total_amount"]:"" ?>"></td>
                        <td>總人數</td>
                        <td><input disabled value="<?=sizeof($datas) >0 ? $datas[0]["TOT_PERSON"]:"" ?>"></td>
                    </tr>
                    <tr>
                        <td>備註</td>
                        <td><input id="memo" value="<?=sizeof($datas) >0 ? $datas[0]["memo"]:"" ?>"></td>
                    </tr>
                </table>
                <hr size="12px" align="center" width="100%">
                <table>
                    <tr>
                        <td><button  <?=sizeof($datas) >0 ? ($datas[0]["id"]!=""?"":"disabled"):"disabled" ?> class="btn btn-info" onclick='updateData(<?= $datas[0]["id"]?>)'>儲存</button></td>
                        <!-- <td><button class="btn btn-info" onclick='deletefun(<?= $data["id"]?>)'>返回</button></td> -->
                    </tr>
                </table>
               
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script type="text/javascript"> 
    function updateData(id) 
    { 
        ApiGet("eat_management?action=update&id="+id+"&add1="+$("#add1").val()
        +"&add2="+$("#add2").val()
        +"&add3="+$("#add3").val()
        +"&memo="+$("#memo").val(),"update")
    } 
    function ApiGet(url,name){
        $.ajax({
            async: false,
            url: url,
            type: "GET",
            dataType: "json",
            success: function (Jdata) {
                console.log(Jdata);
                if(name == "update"){
                    if(Jdata[0])
                    {
                        alert("儲存成功")
                        location.reload();
                    }
                    else{
                        alert("儲存失敗")
                    }
                }
            }
        });
    }

</script> 