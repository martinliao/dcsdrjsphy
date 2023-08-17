<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>課表預覽與陳核</title>
	<link rel="stylesheet" type="text/css" href="https://dcsdcourse.taipei.gov.tw/base/admin/static/css/master.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://dcsdcourse.taipei.gov.tw/base/api/css/jquery.min.js"></script>



    <script>

    function mutiPrint2() {
        
        $.ajax({
        type: "GET",
        data: {
            
        },
        url: "https://dcsdcourse.taipei.gov.tw/base/admin/create_class/print_schedule/mutiPrint2?seq_nos%5B%5D=<?php echo htmlspecialchars($_GET['seq_nos'],ENT_HTML5|ENT_QUOTES);?>",	
        cache: false,
        success: function (res) {
            $("#test1").empty();
            
            if(!hasIllegalChar(res)){
                $("#test1").html(res);
            }
            
            $("#training_text").val(res);
        },
        error: function (res) {
            alert('res');
        }
        });
    }

    function hasIllegalChar(str){
        return new RegExp(".*?script[^&gt;]*?.*?(&lt;\/.*?script.*?&gt;)*", "ig").test(str);
    }

    function to_leader_ok() {
        $("#leader").attr("disabled",false);        
    }
    function to_leader_notok() {
        $("#leader").attr("disabled",true);        
    }
    function to_go(){
        $("#filter-form").submit();
    }
    

</script>

</head>
<body onload="mutiPrint2()">

<?php

//$form['worker'] = $_SESSION['user_idno'];
//$form['worker'] = "F227164127";

//var_dump($_SESSION['user_idno']);

if ($form1[0]['status'] == ""){
        
    if (($_SESSION['user_idno']) == $form['worker'] ){
            $status = 1;
            $form1[0]['status'] = 1;
        }else{
            $status = 1;
            $disabled = "disabled";
        }
    
}elseif($form1[0]['status'] == 1){
    if (($_SESSION['user_idno']) == $form['worker'] ){
        $status = 1;
        $form1[0]['status'] = 1;
    }else{
        $status = 1;
        $disabled = "disabled";
    }
}elseif($form1[0]['status'] == 3){
    $disabled = "disabled";
    $status = $form1[0]['status'];
}elseif($form1[0]['status'] == 4){
    $disabled = "disabled";
    $status = $form1[0]['status'];
}else{
    $status = $form1[0]['status'];
}
//var_dump($form1[0]['status']);
?>

<div class="container">
        <div class="container" style="width:650px;margin-top:30px" id="test1">   
        
        </div>
    <br>
    
    <p style="color:red">比對訓練計畫後，於下方欄位勾選註記(修正班名及期數需加會訓練計畫承辦人)</p>
    

    <form id="filter-form"  role="form" method="POST" action="course_sch_app?seq_nos=<?php echo htmlspecialchars($_GET['seq_nos'],ENT_HTML5|ENT_QUOTES);?>&post=<?php echo $status;?>"  enctype="multipart/form-data">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
        <div class="input-group mb-3">
            <div class="col-md-2 col-sm-8">
                <span class="input-group-text">項目</span>
            </div>
            <div class="col-md-1 col-sm-4">
                <span class="input-group-text" >修正內容</span>
            </div>
            <div class="col-md-3 col-sm-12">
                <span class="input-group-text" >原名稱/期數/時數</span>
            </div>
            <div class="col-md-3 col-sm-12">
                <span class="input-group-text" >修正名稱/期數/時數</span>
            </div>
            <div class="col-md-3 col-sm-12">
                <span class="input-group-text">備註</span>
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="col-md-2 col-sm-8">
                <select class="form-select" id="pot1" name="pot1" <?=$disabled?>>
                    <?php
                     $lists = array('班期名稱', '期數', '時數');
                     if($form1[0]['pot1']==""){
                         $pot1 = "班期名稱";
                     }else{
                         $pot1 = $form1[0]['pot1'];
                     }
                    foreach($lists as $list){ 
                        if($pot1 == $list){
                    ?>
                        <option value="<?php echo $list?>" selected><?php echo $list?></option>   
                    <?php }else{?>
                        <option value="<?php echo $list?>"><?php echo $list?></option>                    
                    <?php
                        }
                    }?>

                </select>
            </div>
            <div class="col-md-1 col-sm-4">
                <select class="form-select" id="fix1" name="fix1" <?=$disabled?> >
                <?php
                     $lists = array(1, 0);
                     $lists2 = array('是', '否');
                     if($form1[0]['fix1']==""){
                         $fix1 = 0;
                     }elseif($form1[0]['fix1'] == 0){
                         $fix1 = $form1[0]['fix1'];
                     }else{
                        $fix1 = $form1[0]['fix1'];
                     }
                    foreach($lists as $list){ 
                        if($fix1 == $lists[$list]){
                    ?>
                        <option value="<?php echo $lists[$list]?>" selected><?php echo $lists2[$list]?></option>   
                    <?php }else{?>
                        <option value="<?php echo $lists[$list]?>"><?php echo $lists2[$list]?></option>                    
                    <?php
                        }
                    }?>       
                </select>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="原名稱/期數/時數" rows="1" id="bef1" name="bef1" placeholder="原名稱/期數/時數" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?> ><?php echo $form1[0]['bef1'];?></textarea>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="修正名稱/期數/時數" rows="1" id="aft1" name="aft1" placeholder="修正名稱/期數/時數" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?> ><?php echo $form1[0]['aft1'];?></textarea>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="備註" rows="1" id="rem1" name="rem1" placeholder="備註" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?> ><?php echo $form1[0]['rem1'];?></textarea>
            </div>
        </div>
        <div class="input-group mb-3">
            <div class="col-md-2  col-sm-8">
                <select class="form-select" id="pot2" name="pot2" <?=$disabled?>>
                <?php
                     $lists = array('班期名稱', '期數', '時數');
                     if($form1[0]['pot1']==""){
                         $pot2 = "期數";
                     }else{
                         $pot2 = $form1[0]['pot2'];
                     }
                    foreach($lists as $list){ 
                        if($pot2==$list){
                    ?>
                    <option value="<?php echo $list?>" selected><?php echo $list?></option>   
                    <?php }else{?>
                    <option value="<?php echo $list?>"><?php echo $list?></option>                    
                    <?php
                        }
                    }?>
                </select>
            </div>
            <div class="col-md-1  col-sm-4">
                <select class="form-select" id="fix2" name="fix2" <?=$disabled?>>
                <?php
                     $lists = array(1, 0);
                     $lists2 = array('是', '否');
                     if($form1[0]['fix2']==""){
                         $fix2 = 0;
                     }elseif($form1[0]['fix2']==0){
                         $fix2 = 0;
                     }else{
                        $fix2 = 1;
                     }
                    foreach($lists as $list){ 
                        if($fix2 == $lists[$list]){
                    ?>
                        <option value="<?php echo $lists[$list]?>" selected><?php echo $lists2[$list]?></option>   
                    <?php }else{?>
                        <option value="<?php echo $lists[$list]?>"><?php echo $lists2[$list]?></option>                    
                    <?php
                        }
                    }?>       
                </select>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="原名稱/期數/時數" rows="1" id="bef2" name="bef2" placeholder="原名稱/期數/時數" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?>><?php echo $form1[0]['bef2'];?></textarea>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="修正名稱/期數/時數" rows="1" id="aft2" name="aft2" placeholder="修正名稱/期數/時數" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?>><?php echo $form1[0]['aft2'];?></textarea>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="備註" rows="1" id="rem2" name="rem2" placeholder="備註" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?>><?php echo $form1[0]['rem2'];?></textarea>
            </div>
        </div>
        <div class="input-group mb-3">
            <div class="col-md-2 col-sm-8">
                <select class="form-select" id="pot3" name="pot3" <?=$disabled?>>
                <?php
                     $lists = array('班期名稱', '期數', '時數');
                     if($form1[0]['pot1']==""){
                         $pot3 = "時數";
                     }else{
                         $pot3 = $form1[0]['pot3'];
                     }
                    foreach($lists as $list){ 
                        if($pot3==$list){
                    ?>
                    <option value="<?php echo $list?>" selected><?php echo $list?></option>   
                    <?php }else{?>
                    <option value="<?php echo $list?>"><?php echo $list?></option>                    
                    <?php
                        }
                    }?>
                </select>
            </div>
            <div class="col-md-1 col-sm-4">
                <select class="form-select" id="fix3" name="fix3" <?=$disabled?>>
                <?php
                     $lists = array(1, 0);
                     $lists2 = array('是', '否');
                     if($form1[0]['fix3']==""){
                         $fix3 = 0;
                     }elseif($form1[0]['fix3']==0){
                         $fix3 = 0;
                     }else{
                        $fix3 = 1;
                     }
                    foreach($lists as $list){ 
                        if($fix3 == $lists[$list]){
                    ?>
                        <option value="<?php echo $lists[$list]?>" selected><?php echo $lists2[$list]?></option>   
                    <?php }else{?>
                        <option value="<?php echo $lists[$list]?>"><?php echo $lists2[$list]?></option>                    
                    <?php
                        }
                    }?>         
                </select>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="原名稱/期數/時數" rows="1" id="bef3" name="bef3" placeholder="原名稱/期數/時數" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';"  <?=$disabled?>><?php echo $form1[0]['bef3'];?></textarea>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="修正名稱/期數/時數" rows="1" id="aft3" name="aft3" placeholder="修正名稱/期數/時數" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?>><?php echo $form1[0]['aft3'];?></textarea>
            </div>
            <div class="col-md-3 col-sm-12">
                <textarea class="form-control" aria-label="備註" rows="1" id="rem3" name="rem3" placeholder="備註" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?>><?php echo $form1[0]['rem3'];?></textarea>
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="col-md-2 col-sm-12">
                <span class="input-group-text">承辦人意見：</span>
            </div>
            <div class="col-md-10 col-sm-12">
                <textarea class="form-control" aria-label="" rows="5" id="opinion" name="opinion" placeholder="" onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';" <?=$disabled?>><?php echo $form1[0]['opinion'];?></textarea>
            </div>            
        </div>
        <div class="input-group mb-3">
            <div class="col-md-2 col-sm-12">
                <span class="input-group-text">承辦人姓名：</span>
            </div>
            <div class="col-md-2 col-sm-12">
                <input type="text" class="form-control" aria-label="承辦人姓名" value="<?php echo $form['worker_name']?>" id="worker" name="worker" readonly="readonly">
            </div>
            
        </div>
        
        <?php 
        if ($form1[0]['to_leader'] == 1){
            $to_leader_txt2 = "";
            $to_leader_txt = "checked=checked";
            $leader_txt = "";
        }else{
            $to_leader_txt2 = "checked=checked";
            $to_leader_txt = "";
            $leader_txt = "disabled";
        }

        if ($form1[0]['boss_op'] == 0){
            $boss_op_txt = "";
            $boss_op_txt2 = "checked=checked";
           
        }else{
            $boss_op_txt = "checked=checked";
            $boss_op_txt2 = "";
            
        }
        if ($form1[0]['leader_op'] == 0){
            $leader_op_txt = "";
            $leader_op_txt2 = "checked=checked";
            
        }else{
            $leader_op_txt = "checked=checked";
            $leader_op_txt2 = "";
            
        }
        ?>

        <div class="input-group mb-3">
            <div class="col-md-2 col-sm-12">
                <span class="input-group-text">會辦承辦人：</span>
            </div>
            <div class="col-md-3 col-sm-12" style="width:215px">
                <div class="input-group-text" style="background-color:#FFFFFF; border:0px" >
                    <input class="form-check-input" type="radio" name="to_leader" id="to_leader" value="1" <?php echo $to_leader_txt;?> onclick="to_leader_ok()" <?=$disabled?>>
                    <label class="form-check-label" for="to_leader">
                    是(會辦訓練計畫承辦人)
                    </label>
                </div>
                <div class="input-group-text"  style="background-color:#FFFFFF; border:0px">
                    <input class="form-check-input" type="radio" name="to_leader" id="to_leader" value="0" <?php echo $to_leader_txt2;?>  onclick="to_leader_notok()" <?=$disabled?>>
                    <label class="form-check-label" for="to_leader">
                        否
                    </label>
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <select class="form-select" id="leader" name="leader" <?php echo $leader_txt;?>  <?=$disabled?>>
                <?php 
                     $bosss = $form['signuser'];
                    foreach ($bosss as $boss) {
                        if($boss['group_id'] == "25"){

                            if ($boss['idno'] == $form1[0]['leader']){
                                $leadername = $boss['name'];
                    ?>
                                <option value="<?php echo $boss['idno'];?>" selected><?php echo $boss['name'];?></option>
                    <?php 
                            }else{
                    ?>
                                <option value="<?php echo $boss['idno'];?>" ><?php echo $boss['name'];?></option>
                    <?php 
                    }
                    }
                    } ?>
                </select>
            </div>
            <!-- <div class="col-md-1 col-sm-12">
                
            </div> -->
            <div class="col-md-2 col-sm-12">
            <br><br>
                <span class="input-group-text">送陳主管：</span>
            </div>
            <div class="col-md-2 col-sm-12">
            <br><br>
                <select class="form-select" id="boss" name="boss" <?=$disabled?>>
                    <?php 
                     $bosss = $form['signuser'];
                    foreach ($bosss as $boss) {
                        if($boss['group_id'] == "26"){

                            if ($boss['idno'] == $form1[0]['boss']){
                                $bossname = $boss['name'];
                    ?>
                                <option value="<?php echo $boss['idno'];?>" selected ><?php echo $boss['name'];?></option>
                    
                    <?php 
                            }else{
                    ?>
                                <option value="<?php echo $boss['idno'];?>" ><?php echo $boss['name'];?></option>
                    
                    <?php 
                    }
                    }
                    } ?>
                </select>
            </div>
             
                    
        </div>
        <?php
        
        if (($form1[0]['status'] == 2) && ($form1[0]['boss']==$_SESSION['user_idno'])){
            
        ?>
        <div class="input-group mb-3" style="background-color:rgb(209, 253, 239)">
            <div class="col-md-2 col-sm-12">
                <span class="input-group-text" style="background-color:rgb(209, 253, 239); border:0px">主管意見：</span>
            </div>
            <div class="col-md-10 col-sm-12">
                <div class="input-group-text" style="background-color:rgb(209, 253, 239); border:0px" >
                    <input class="form-check-input" type="radio" name="boss_op" id="boss_op" value="1" <?php echo $boss_op_txt;?>>
                    <label class="form-check-label" for="boss_op">
                    同意
                    </label>
                </div>
                <div class="input-group-text"  style="background-color:rgb(209, 253, 239); border:0px">
                    <input class="form-check-input" type="radio" name="boss_op" id="boss_op"  value="0"<?php echo $boss_op_txt2;?>>
                    <label class="form-check-label" for="boss_op">
                        退回　
                    </label>
                    <textarea class="form-control" rows="1" id="boss_centext" name="boss_centext"  onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';"></textarea>
                </div>
            </div>                  
        </div>

        <?php 
        }
        
        if (($form1[0]['status'] == 3)&&($form1[0]['leader']==$_SESSION['user_idno'])){
            
        ?>

        <div class="input-group mb-3" style="background-color:rgb(255, 221, 221)">
            <div class="col-md-2 col-sm-12">
                <span class="input-group-text" style="background-color:rgb(255, 221, 221); border:0px">訓練計畫承辦人意見：</span>
            </div>
            <div class="col-md-10 col-sm-12">
                <div class="input-group-text" style="background-color:rgb(255, 221, 221); border:0px" >
                    <input class="form-check-input" type="radio" name="leader_op" id="leader_op" value="1" <?php echo $leader_op_txt;?>>
                    <label class="form-check-label" for="leader_op">
                    同意
                    </label>
                </div>
                <div class="input-group-text"  style="background-color:rgb(255, 221, 221); border:0px">
                    <input class="form-check-input" type="radio" name="leader_op" id="leader_op" value="0" <?php echo $leader_op_txt2;?>>
                    <label class="form-check-label" for="leader_op">
                        退回　
                    </label>
                    <textarea class="form-control" rows="1" id="leader_centext" name="leader_centext"  onpropertychange="this.style.height = this.scrollHeight + 'px';" oninput="this.style.height = this.scrollHeight + 'px';"></textarea>
                </div>
            </div>                  
        </div>
        <?php
        }
        ?>

        <div class="input-group mb-3">
            <div class="col-md-6 col-sm-12">
                <span style="color:blue">


                <B>核淮人：<?=$form1[0]['boss_name']?>　核淮日期/時間： <?=$form1[0]['boss_date']?></B><br>
                <B>核閱人：<?=$form1[0]['leader_name']?>　核淮日期/時間： <?=$form1[0]['leader_date']?></B><BR>
                    <?php
                    if ($form1[0]['id'] == "" ){
                        ?>
                        <span style="color:rgb(138, 138, 138)"></span>
                        <?php
                    }elseif($form1[0]['status'] == 1 ){
                        ?>
                        <span style="color:rgb(138, 138, 138)">被退回，待重新送陳！</span>
                        <?php
                    }elseif($form1[0]['status'] == 2 ){
                            ?>
                            <span style="color:rgb(138, 138, 138)">已完成送陳，待主管簽核！</span>
                            <?php
                    }elseif($form1[0]['status'] == 3 ){
                            ?>
                            <span style="color:rgb(138, 138, 138)">已完成主管簽核，待會辦承辦人核閱！</span>
                            <?php  
                    }elseif($form1[0]['status'] > 3 ){
                        if($form1[0]['to_leader'] == 0){
                            ?>
                            <span style="color:rgb(138, 138, 138)">已完成簽核程序 完成時間：<?=$form1[0]['end_date']?></span>
                            <?php

                        }else{
                     ?>
                            <span style="color:rgb(138, 138, 138)">已完成簽核程序 完成時間：<?=$form1[0]['end_date']?></span>
                            <?php
                        }
                    }
                    ?>
                    



                </span>
            </div>            
            <div class="col-md-2 col-sm-12" style="width:215px">
            
            <input type="hidden" id="training_text" name="training_text" value="">
            <input type="hidden" id="boss_sign" name="boss_sign" value="<?=$bossname?>">
            <input type="hidden" id="leader_sign" name="leader_sign" value="<?=$leadername?>">
           </div>
            <div class="col-md-2 col-sm-12 d-grid gap-2">
                <?php 
                //var_dump($_SESSION['user_idno']);
                //var_dump($form['worker']);
                //var_dump($_SESSION['user_idno']);
                
                if (($form1[0]['status'] == 1)&&($_SESSION['user_idno']==$form['worker'])){
                    
                    ?>
                <input class="btn btn-primary" type="button" value="送陳" style="border-color:#2e6da4;background-color:#337ab7;height:40px" onclick="to_go()">
            <?php
            }elseif(($form1[0]['status'] == 2)&&($form1[0]['boss']==$_SESSION['user_idno'])){
                    ?>
                <input class="btn btn-primary" type="button" value="送出" style="border-color:#2e6da4;background-color:#337ab7;height:40px" onclick="to_go()">
            <?php
                }elseif(($form1[0]['status'] == 3)&&($form1[0]['leader']==$_SESSION['user_idno'])){
                ?>
                <input class="btn btn-primary" type="button" value="核閱完成" style="border-color:#2e6da4;background-color:#337ab7;height:40px" onclick="to_go()">
            <?php
                }else{
             }
            ?>
            </div>     
        </div>

        
    </form>

<br><br><br><br>


</div>


</body>
</html>