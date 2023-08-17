<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading" >
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="require_query_form">
                    <?php foreach ($data as $row1) {?>
                    <div class="row">
                        <div class="col-xs-12 text-center" style="font-family:'標楷體';font-weight:bold;">
                            臺北市政府公務人員訓練處　　課程表
                        </div>
                        <div class="col-xs-12 text-center" style="font-family:'標楷體';font-weight:bold;">
                            <?=$row1['year']?>年度 <?=$row1['class_name']?> 第<?=$row1['term']?>期
                        </div>
                    </div>
                    <?php if (count($onlineCourse) > 0) {?>
                    <div class="col-xs-12" style="font-family:'標楷體'">
                        <?=$row1['class_no']?> 線上課程表
                    </div>
                    <div class="row" id="test2">
                        <div class="col-xs-12">
                            <table class="table table-bordered table-condensed table-hover">
                                <thead>
                                    <tr style="background-color:DarkBlue; color:white;">
                                        <th class="text-center" style="font-family:'標楷體'">起日</th>
                                        <th class="text-center" style="font-family:'標楷體'">迄日</th>
                                        <th class="text-center" style="font-family:'標楷體'">線上課程名稱</th>
                                        <th class="text-center" style="font-family:'標楷體'">講座名稱</th>
                                        <th class="text-center" style="font-family:'標楷體'">上課地點</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($onlineCourse as $row2) {?>
                    
                                    <tr class="text-center">
                                        <td style="font-family:'標楷體'"><?=substr($row2['start_date'], 0, 10)?></td>
                                        <td style="font-family:'標楷體'"><?=substr($row2['end_date'], 0, 10)?></td>
                                        <td style="font-family:'標楷體'"><?=$row2['class_name']?></td>
                                        <td style="font-family:'標楷體'"><?=$row2['teacher_name']?></td>
                                        <td style="font-family:'標楷體'"><?=$row2['place']?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php }?>
                    <?php if (count($onlineCourse) > 0) {?>
                    <div class="row" id="test3">
                        <div class="col-xs-12" style="font-family:'標楷體'">
                            實體課程表
                        </div>
                    <?php }else{?>
                        <div class="col-xs-12" style="font-family:'標楷體';font-weight:bold;">
                            <?=$row1['class_no']?>
                        </div>
                    <?php }?>
                    <?php if ($roomCount == 1) {?>
                        <div class="col-xs-12 text-right"  style="font-family:'標楷體'">
                            上課地點:
                            <?php foreach ($roomName as $row4) {?>
                                <?=$row4['room_name']?>
                            <?php }?>
                        </div>
                        <?php }?>
                        <div class="col-xs-12">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr style="background-color:DarkBlue; color:white;">
                                        <?php if ($roomCount > 1) {?>
                                        <th class="text-center" style="font-family:'標楷體'">日期</th>
                                        <th class="text-center" style="font-family:'標楷體'">星期</th>
                                        <th class="text-center" style="font-family:'標楷體'">時間</th>
                                        <th class="text-center" style="font-family:'標楷體'">課程</th>
                                        <th class="text-center" style="font-family:'標楷體'">講座</th>
                                        <th class="text-center" style="font-family:'標楷體'">上課地點</th>
                                        <?php } else {?>
                                        <th class="text-center" style="font-family:'標楷體'">日期</th>
                                        <th class="text-center" style="font-family:'標楷體'">星期</th>
                                        <th class="text-center" style="font-family:'標楷體'">時間</th>
                                        <th class="text-center" style="font-family:'標楷體'">課程</th>
                                        <th class="text-center" style="font-family:'標楷體'">講座</th>
                                        <?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $count=0;$tmp="";?>
                                    <?php foreach ($realCourse as $row3) {?>
                                    <tr class="text-center">
                                        <?php $date=str_replace('-','/',$row3['use_date']);
                                              $date=substr($date,5,5);
                                              if($tmp!=$date||$count==0){
                                                    $tmp=$date;
                                              }else{
                                                $date="";
                                                $row3['cday']="";
                                              }
                                              $count++;
                                        ?>
                                        <td style="font-family:'標楷體'"><?=$date?></td>
                                        <td style="font-family:'標楷體'"><?=$row3['cday']?></td>
                                        <td style="font-family:'標楷體'"><?=$row3['ltime']?></td>
                                        <td style="font-family:'標楷體'"><?=$row3['class_name']?></td>
                                        <?php $name = array();
                                              $i=0;
                                        foreach($row3['teacher_info'] as $ti){
                                        if ($ti['title'] != "" && $ti['title'] != "無" ) {
                                            $name[$i] = $ti['name'] . " " . $ti['title'];
                                        } else if ($ti['name'] == "教務組" || $ti['title'] == "無") {
                                            $name[$i] = $ti['name'] . "<br>";
                                        }else {
                                            if ($ti['teacher_type'] == 1) {
                                                $name[$i] = $ti['name'] . " " . "老師" . "<br>";
                                            }
                                            if ($ti['teacher_type'] == 2) {
                                            $name[$i] = $ti['name'] . " " . "(助)" . "<br>";
                                            }
                                        }
                                        $i++;
                                        }
                                        
                                        $te=implode(" ",$name);                                        
                                        ?>
                                        <td style="font-family:'標楷體'"><?=$te?></td>
                                        <?php if ($roomCount > 1) {?>
                                        <td style="font-family:'標楷體'"><?=$row3['classroom_name']?></td>
                                        <?php }?>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php }?>
                   
                </div>

                <?php if($tmp_seq==1) {?>
                <div id="edit_form">
                    <div class="row">
                        <div class="col-xs-12">
                            <form id="edit-form" role="form" class="form-inline" method="post"
                                action="<?=base_url('create_class/print_schedule/save')?>">
                                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                                <div class="col-xs-12" style="font-family:'標楷體'">
                                    一、承辦人：<?=$data[0]['contactor']?>(分機
                                    <?=$data[0]['add_val1']?>)、代理人：<?=$data[0]['description']?>(分機
                                    <?=$data[0]['add_val2']?>)。
                                </div>
                                <div class="col-xs-12" style="font-family:'標楷體'">
                                    二、研習人數 <?=$data[0]['sel_number']?>人；研習總時數
                                    <?=$data[0]['range_real'] + $data[0]['range_internet']?>小時
                                    <?php if($data[0]['range_internet']!=0){?>
                                    (實體時數 <?=$data[0]['range_real']?>小時、線上時數 <?=$data[0]['range_internet']?>小時)。
                                    <?php }?>
                                </div>
                                <div class="col-xs-12" style="font-family:'標楷體'">
                                    <?php include "fckeditor/fckeditor_php5.php";
                                if(!empty($data[0]['worker_mail'])){
                                    $worker_mail = $data[0]['worker_mail'][0]['mail'];
                                    $range = $data[0]['range'];
                                    $true_count = $data[0]['sel_number'];
                                    $content = str_replace("@@@@", $worker_mail, $content);
                                    $content = str_replace("@@@", $true_count, $content);
                                    $content = str_replace("@@", $range, $content);
                                }
                                    $sBasePath = '../../../fckeditor/';

                                    $oFCKeditor = new FCKeditor('FCKeditor1');
                                    $oFCKeditor->BasePath = $sBasePath;
                                    $oFCKeditor->Height = 500;
                                    $oFCKeditor->Value = trim(stripslashes($content));
                                    $oFCKeditor->Create();
                                
                                ?>
                                </div>
                                
                                <div class="col-xs-12" style="font-family='標楷體'">
                                    <input type='button' name="btnSave" id="btnSave" value='儲存' onclick='submit();'
                                        class='button' />
                                    <input type='button' name='btnprint' id='btnprint' value='列印'
                                        onclick='printScreen2();' class='button' />
                                    <input type='hidden' name='year' id='year' value='<?=$data[0]['year']?>' />
                                    <input type='hidden' name='class_no' id='class_no'
                                        value='<?=$data[0]['class_no']?>' />
                                    <input type='hidden' name='term' id='term' value='<?=$data[0]['term']?>'>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <?php }else{?>
                <div id="edit_form">
                    <div class="row" id="test5">
                        <div class="col-xs-12">
                            <form role="form" class="form-inline">
                                <div class="col-xs-12" style="font-family:'標楷體'">
                                    一、承辦人：<?=$data[0]['name']?>(分機
                                    <?=$data[0]['add_val1']?>)、代理人：<?=$data[0]['description']?>(分機
                                    <?=$data[0]['add_val2']?>)。
                                </div>
                                <div class="col-xs-12" style="font-family:'標楷體'">
                                    二、研習人數 <?=$data[0]['sel_number']?>人；研習總時數
                                    <?=$data[0]['range_real'] + $data[0]['range_internet']?>小時

                                    <?php if($data[0]['range_internet']!=0){?>
                                    (實體時數 <?=$data[0]['range_real']?>小時、線上時數 <?=$data[0]['range_internet']?>小時)。
                                    <?php }?>
                                </div>

                                <div class="col-xs-12" style="font-family:'標楷體'">
                                    <?php
                                    if(!empty($data[0]['worker_mail'])){
                                        $worker_mail = $data[0]['worker_mail'][0]['mail'];
                                        $range = $data[0]['range'];
                                        $true_count = $data[0]['sel_number'];
                                        $content = str_replace("@@@@", $worker_mail, $content);
                                        $content = str_replace("@@@", $true_count, $content);
                                        $content = str_replace("@@", $range, $content);
                                        echo $content;     
                                    }                
                                ?>
                                </div>
                                <div class="col-xs-12" style="font-family:'標楷體';margin-top:1%;margin-bottom:1%">
                                    <!--<input type='button' name='btnprint' id='btnprint' value='列印'
                                        onclick='printScreen();' class='button' />-->
                                    <button class="btn btn-info" name='btnprint' id='btnprint' onclick='printScreen();'>列印</button>
                                    <a class="btn btn-info" id="back" href="<?=base_url('create_class/print_schedule')?>">返回</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php }?>

            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!--<style>
    @page { size: auto;  margin: 0mm; }
    
</style>-->
<!--<style media="print">
    @page { margin: 50; }
</style>-->
<script>
function printScreen()
{
        document.getElementById('btnprint').style.display= "none";
        document.getElementById('back').style.visibility = 'hidden'; 
        
        //document.title.style.visibility="hidden";
        var head_str = "<html><head><title></title></head><body>"; //先生成頭部
        var foot_str = "</body></html>"; //生成尾部
        var older = document.body.innerHTML;
        var new_str1 = document.getElementById('require_query_form').innerHTML;
        var new_str2 = document.getElementById('edit_form').innerHTML;
        var old_str = document.body.innerHTML; //獲得原本頁面的程式碼
        //document.getElementById('header').style.display = 'none';
        //document.getElementById('footer').style.display = 'none';
        document.title = "　";
        //document.write(document.URL)="　";

        document.querySelector('footer').style = 'display: none'

        document.body.innerHTML = head_str + new_str1 + new_str2 + foot_str; //構建新網頁
        //document.title = "";
        //document.url="";
        //document.body.innerHTML =  new_str1 + new_str2;
        window.print(); //列印剛才新建的網頁
        document.body.innerHTML = older; //將網頁還
        document.getElementById('back').style.visibility = 'visible'; 
     
        return false;


}


function printScreen2()
{
    var head_str = "<html><body>"; //先生成頭部
        var foot_str = "</body></html>"; //生成尾部
        var older = document.body.innerHTML;
        var new_str1 = document.getElementById('require_query_form').innerHTML;
        var new_str4 = document.getElementById('FCKeditor1').value; //獲取指定列印區域
        var old_str = document.body.innerHTML; //獲得原本頁面的程式碼
        document.body.innerHTML = head_str + new_str1 + new_str4 + foot_str; //構建新網頁
        window.print(); //列印剛才新建的網頁
        document.body.innerHTML = older; //將網頁還原
        return false;
}
</script>