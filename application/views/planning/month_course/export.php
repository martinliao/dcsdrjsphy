<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading" >
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="require_query_form">
                    <table class="table table-hover table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th  class="text-center" colspan="12">臺北市政府公務人員<?=$show[0]['show_year']?>年<?=$show[0]['month_start']?>月~<?=$show[0]['month_end']?>月訓練處行政班期開班調整彙整表</th>
                            </tr>
                            <tr>
                                <th class="text-center" rowspan="3">班期名稱</th>
                                <th class="text-center" rowspan="3">報名人數</th>
                                <th class="text-center" rowspan="3">預定人數</th>
                                <th class="text-center" rowspan="3">預定期數</th>
                                <th class="text-center" rowspan="3" style="color:red">實際期數</th>
                                <th class="text-center" rowspan="3" style="color:red">實際人數</th>
                                <th class="text-center" colspan="6" style="color:red">調整情形</th>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="2" style="color:red">期數</th>
                                <th class="text-center" colspan="2" style="color:red">人數</th>
                                <th class="text-center" rowspan="2" style="color:red">說明</th>
                                <th class="text-center" rowspan="2" style="color:red">承辦人</th>
                            </tr>
                            <tr>
                                <th class="text-center" rowspan="1" style="color:red">本次增減</th>
                                <th class="text-center" rowspan="1" style="color:red">確定開班二次報名</th>
                                <th class="text-center" rowspan="1" style="color:red">本次增減</th>
                                <th class="text-center" rowspan="1" style="color:red">二次報名基本人數</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="color:green">總計:</th>
                                <th style="color:green"><?=$maxsignup?></th>
                                <th style="color:green"><?=$maxpeople?></th>
                                <th style="color:green"><?=$maxterm?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php if(count($list)!=0){?>
                            <?php 
                                $course=array();
                                $i=0;
                                foreach ($list as $row ) {
                                   $course[$row['sc_name']][$i]['class_name']=$row['class_name'];
                                   $course[$row['sc_name']][$i]['final_a_count']=$row['final_a_count'];
                                   $course[$row['sc_name']][$i]['sum_people']=$row['sum_people'];
                                   $course[$row['sc_name']][$i]['max_term']=$row['max_term'];
                                   $i++;
                                }
                                //var_dump($course);
                            ?>

                            <?php foreach($bureauCount as $key => $value) {?>
                            <tr>

                                <th style="color:green"><?=$value['description']?></th>
                                <th style="color:green"><?=$value['people']?></th>
                                <th style="color:green"><?=$value['expect_total']?></th>
                                <th style="color:green"><?=$value['term_total']?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php foreach($course[$value['description']] as $temp){?>
                            <tr>
                                <td><?=$temp['class_name']?></td>
                                <td><?=$temp['final_a_count']?></td>
                                <td><?=$temp['sum_people']?></td>
                                <td><?=$temp['max_term']?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php }?>
                            <?php }?>

                            <!--<?php foreach($list as $row) {?>
                            <tr>
                                <td><?=$row['sc_name']?></td>
                                <td><?=$row['class_name']?></td>
                                <td><?=$row['final_a_count']?></td>
                                <td><?=$row['sum_people']?></td>
                                <td><?=$row['max_term']?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?=$row['name']?></td>
                            </tr>
                            <?php }?>-->
                            
                            <?php }?>
                        </tbody>
                    </table>        
                </div>
                <button class="btn btn-info" id="btnprint" onclick="printScreen();">列印</button>
            </div>
        </div>
        <!-- /.panel -->
    </div>
</div>
<script type="text/javascript">
function printScreen()
{
        //document.getElementById('btnprint').style.display= "none";
        document.getElementById('btnprint').style.visibility = 'hidden'; 
        
        //document.title.style.visibility="hidden";
        var head_str = "<html><head><title></title></head><body>"; //先生成頭部
        var foot_str = "</body></html>"; //生成尾部
        var older = document.body.innerHTML;
        var new_str1 = document.getElementById('require_query_form').innerHTML;
        var old_str = document.body.innerHTML; //獲得原本頁面的程式碼
        //document.getElementById('header').style.display = 'none';
        //document.getElementById('footer').style.display = 'none';
        document.title = "　";
        //document.write(document.URL)="　";

        document.querySelector('footer').style = 'display: none'

        document.body.innerHTML = head_str + new_str1 + foot_str; //構建新網頁
        //document.title = "";
        //document.url="";
        //document.body.innerHTML =  new_str1 + new_str2;
        window.print(); //列印剛才新建的網頁
        document.body.innerHTML = older; //將網頁還
        document.getElementById('btnprint').style.visibility = 'visible'; 
     
        return false;

}
</script>

