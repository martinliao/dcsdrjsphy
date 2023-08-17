<?php echo "<div style='color:red;'>".validation_errors()."</div>";?>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>

            <div class="panel-body"> 
            <font color="red">※步驟：點選課程性質→確認問卷名稱→預覽課程問卷→勾選評估老師→設定評估日期→確定</font><br>

            ⦿處內課程：一般整日課程。<br>

            ⦿處外課程：整天處外上課，不含行政滿意度，僅問兩題開放性意見。<br>

            ⦿處內課程(不用餐)：下午課程，不含用餐問項。<br>

            ⦿處內課程(住宿)：有住宿之管理班期。<br>

            ⦿特殊問卷：另訂題目之班期，請選擇特殊問卷名稱，設定填報起迄日後再按儲存。不評估講座時，請勾選特殊問卷講座不評估。<br>
            <font color="red">
                ※不記名問卷：不需驗證填報者，即可登入，但可重複填寫。勾選後，請複製不記名問卷網址(或產製條碼)供填報者上網填報。<br>
            </font>
            <font color="red">
                ※當問卷已開放填報，欲延長評估迄日時，請直接設定右欄【問卷結束日期】，再點選【設定日期】，再按【儲存】。<br>            
            </font>
            <font color="blue">
                ※班期問卷可新增多份，並立即自動儲存，可重設評估日期起訖，請與老師評估問卷分開操作，以免遺漏設定。 
            </font>
            </div>




            <!-- 班期資訊 ↓ -->
            <div class="panel-body">
            	<table class="table table-bordered table-condensed table-hover" style="text-align: center;">
            		<thead>
            		</thead>
            		<tbody>
            			<tr>
            				<th rowspan="2">年度</th>
            				<td rowspan="2"><?=isset($list[0]['year'])?$list[0]['year']:'';?></td>
            				<th>班期代碼</th>
            				<td><?=$list[0]['class_no'];?></td>
            				<th rowspan="2">期別</th>
            				<td rowspan="2">第<?=$list[0]['term'];?>期</td>
            			</tr>
            			<tr>
            				<th>班級名稱</th>
            				<td><?=$list[0]['class_name'];?></td>
            			</tr>            			     			
            		</tbody>
            	</table>
            </div>
            <!-- 班期資訊 ↑ -->

            <!-- 老師評估問卷 ↓ -->


            
            <form id="data-form" role="form" method="post" action="<?=$link_save;?>">
            <div class="panel-body">
                

                <table class="table"  width="500">
                    <tbody>
                        <tr>
                            <th class="tdr" style="font-size:100%">問卷設定</th>
                            <td>
                                <a href="<?=$link_view?>" target="_blank"><input type="button" value='預覽課程問卷'  class='button'/></a>　  

                                <input type="checkbox" name="isevaluate_no_teacher" value="Y" <?=($list[0]['isevaluate_no_teacher'] == 'Y') ? 'checked' : ''; ?>>特殊問卷講座不評估　
                                <input type="checkbox" name="anonymous" value="Y" <?=($anonymous == 'Y') ? 'checked' : ''; ?>>不記名問卷
                            </td>
                        </tr>
                        <?php
                            /* if($list[0]['isevaluate_no_teacher'] == 'Y' && !empty($special_evaluate_date)){
                                echo '<tr>';
                                echo '<th class="tdr" style="font-size:100%;background:#f1f1f1">特殊問卷評估日期</th>';
                                echo '<td style="background:#f1f1f1">'.date('Y-m-d',strtotime($special_evaluate_date[0]['beginDatetime'])).'~'.date('Y-m-d',strtotime($special_evaluate_date[0]['endDatetime'])).'</td>';
                                echo '</tr>';
                            } */

                            if($anonymous == 'Y' && $cmid > 0){
                                echo '<tr>';
                                echo '<th class="tdr" style="font-size:100%;background:#f1f1f1">不記名問卷網址</th>';
                                echo '<td style="background:#f1f1f1">'.$anonymous_url.'</td>';
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                </table>     
                <span style="height:40px;font-weight:bold;line-height:50px;font-size:24px">老師評估問卷：</span><BR>
                <input type='date' id='standard_date' name="standard_date" class="calendar_load"  value="<?=$default_start_date?>" />
                <input type='date' id='standard_date_end' name="standard_date_end" class="calendar_load"  value="<?=$default_end_date?>" />
                <input type='button' value='評估日期設定' class='button' onclick='set_time()'>
            </div>

            <div class="panel-body">
            	
                    <input hidden name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="text-center"><input type="checkbox" id="chkall_save" onclick="checkAll_save(this,'check');">評估儲存</th>
                                <th class="text-center"><input type="checkbox" id="chkall_teacher" onclick="checkAll_teacher(this,'check');">評估老師</th>
                                <th data-field="item_id">上課日期</th>
                                <th data-field="name">講師</th>
                                <th data-field="remark">課程</th>
                                <th data-field="item_id">職稱</th>
                                <th data-field="name">學歷</th>
                                <th>評估日期(起)</th>
                                <th>評估日期(訖)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" class="selectAll_save" name="rowid[]" value="<?=($row['teacher_id'].'@'.date('Y-m-d',strtotime($row['use_date'])).'@'.$row['course_code']);?>" <?=$row['isevaluate']=='Y'?'checked':'' ?> ></td>
                                <td class="text-center"><input type="checkbox" class="selectAll_teacher" name="rowid_teacher[]" value="<?=($row['teacher_id'].'@'.date('Y-m-d',strtotime($row['use_date'])).'@'.$row['course_code']);?>" <?=$row['isevaluate']=='Y'?'checked':'' ?> ></td>
                                <td><?=date('Y-m-d',strtotime($row['use_date']));?></td>
                                <td><?=$row['teacher_name'];?></td>
                                <td><?=$row['course_name'];?></td>
                                <td><?=$row['job_title'];?></td>
                                <td><?=$row['major'];?></td>
                                <td><input type="date" id="assess_start_date@<?=$row['teacher_id']?>@<?=date('Y-m-d',strtotime($row['use_date']))?>@<?=$row['course_code']?>" name="assess_start_date@<?=$row['teacher_id']?>@<?=date('Y-m-d',strtotime($row['use_date']))?>@<?=$row['course_code']?>" value="<?=!empty($row['assess_date'])?date('Y-m-d',strtotime($row['assess_date'])):''?>"></td>
                                <td><input type="date" id="assess_end_date@<?=$row['teacher_id']?>@<?=date('Y-m-d',strtotime($row['use_date']))?>@<?=$row['course_code']?>" name="assess_end_date@<?=$row['teacher_id']?>@<?=date('Y-m-d',strtotime($row['use_date']))?>@<?=$row['course_code']?>" value="<?=!empty($row['assess_date_end'])?date('Y-m-d',strtotime($row['assess_date_end'])):''?>"></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <input hidden name="year" value="<?=$list[0]['year']?>"></input>
                    <input hidden name="class_no" value="<?=$list[0]['class_no']?>"></input>
                    <input hidden name="term" value="<?=$list[0]['term']?>"></input>
                    <input hidden name="class_name" value="<?=$list[0]['class_name']?>"></input>
                
            </div>
            <div class="text-center" style="padding:0px 0px 30px 0px">
                <span style="color:red">(如有設定老師評估內容，請先儲存後再繼續以下班期問卷設定。)</span><br>
                <a class="btn btn-primary btn-save" href="#" title="Save">儲存</a>                
            </div>
   
            </form> 
            
            <!-- 老師評估問卷 ↑ -->
            <hr style="border-top:10px solid #d4d5d7 ;width:1000px">
            <!-- 特殊問卷 ↓ -->


            
            <form id="data-form2" role="form" method="post" action="<?=$link_insent;?>">
            <div class="panel-body">
                <span style="height:40px;font-weight:bold;line-height:50px;font-size:24px">班期問卷：</span><BR>
                <div style='float: left;' >
                    <input type='radio' name='inside' value='Y' onclick="autoFun(99)" <?=($list[0]['inside']=='Y' || empty($list[0]['inside']))?'checked':''?> />處內課程
                </div>
                <div style='float: left;margin-left: 5px'>
                    <input type='radio' name='inside' value='N' onclick="autoFun(100)" <?=($list[0]['inside']=='N')?'checked':''?> />處外課程
                </div>
                <div style='float: left;margin-left: 5px'>
                    <input type='radio' name='inside' value='Z' onclick="autoFun(101)" <?=($list[0]['inside']=='Z')?'checked':''?> />處內課程(不用餐)
                </div>
                <div style='float: left;margin-left: 5px'>
                    <input type='radio' name='inside' value='X' onclick="autoFun(102)" <?=($list[0]['inside']=='X')?'checked':''?> />處內課程(住宿)
                </div>
                <div style='float: left;margin-left: 5px'>
                    <input type='radio' name='inside' value='W' <?=($list[0]['inside']=='W')?'checked':''?> />特殊問卷
                </div>

                <table class="table"  width="500">
                    <tbody>
                        <tr>
                            <th class="tdr" style="font-size:100%">問卷名稱</th>
                            <td>
                            <select name='question_id' id='question_id' style="max-width:600px;height:39px">
                                <option value="0"  selected>==請選擇==</option>
                                <?php
                                    for($i=0;$i<count($form_list);$i++){

                                        foreach ($list2 as $key => $row3) { 
                                            if($form_list[$i]['name'] == $row3['formName']){
                                                unset($form_list[$i]);
                                            }
                                        }
                                        
                                    }
                                    foreach ($form_list as $list3) {
                                        if ($list3['id']==99){
                                            echo '<option value="'.$list3['id'].'" selected>'.$list3['name'].'</option>';
                                        }else{
                                            echo '<option value="'.$list3['id'].'">'.$list3['name'].'</option>';
                                        }
                                    }
                                ?>
                                </select>
                                <input type='date' id='standard_date' name="standard_date" class="calendar_load"  value="<?=$default_start_date?>" />
                <input type='date' id='standard_date_end' name="standard_date_end" class="calendar_load"  value="<?=$default_end_date?>" />
                <input type='submit' value='新增問卷' class='button'>                               
                            </td>
                        </tr>                        
                    </tbody>
                </table>     
                <input hidden name="other_sl" value="insert"></input>
                    <input hidden name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input hidden name="anonymous" value="<?=$anonymous;?>"></input>
                    <input hidden name="year" value="<?=isset($list[0]['year'])?$list[0]['year']:'';?>"></input>
                    <input hidden name="class_no" value="<?=$list[0]['class_no'];?>"></input>
                    <input hidden name="term" value="<?=$list[0]['term']?>"></input>
                    <input hidden name="class_name" value="<?=$list[0]['class_name']?>"></input>
            </div>
            </form> 

            <?php
            
            if($list2){?>

            <form id="data-form3" role="form" method="post" action="<?=$link_insent;?>">
            <input hidden name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
            <div class="panel-body">
            <input type='date' id='standard_date2' name="standard_date2" class="calendar_load"  value="<?=$default_start_date?>" />
                    <input type='date' id='standard_date_end2' name="standard_date_end2" class="calendar_load"  value="<?=$default_end_date?>" />
                    <input type='submit' value='批次設定評估日期' class='button'><span style="color:red">＊僅更新選取的項目</span><BR><BR>
            	
                    <input hidden name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width:5%"><input type="checkbox" id="chkall_other" onclick="checkotherAll_save(this,'check');">全選</th>
                                <th style="width:5%">序號</th>
                                <th style="width:60%">問卷名稱</th>
                                <th style="width:10%">評估日期(起)</th>
                                <th style="width:10%">評估日期(訖)</th>
                                <th style="width:10%">刪除</th>

                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list2 as $key => $row2) {                             
                            ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" class="selectAll_other" name="rowid_other[]" value="<?=($row2['id']);?>" ></td>                                
                                <td><?=$key+1?></td>
                                <td><?=$row2['formName'];?></td>
                                <td><?=!empty($row2['beginDatetime'])?date('Y-m-d',strtotime($row2['beginDatetime'])):''?></td>
                                <td><?=!empty($row2['endDatetime'])?date('Y-m-d',strtotime($row2['endDatetime'])):''?></td>
                                <td>
                                <?php
                                if ($row2['sing_count']>=1){
                                    echo "已有學員作答";
                                }else{
                                    echo '<a href="'.$link_del.'cmid='.$row2['cmid'].'&fid='.$row2['fid'].'" class="button">刪除</a>';
                                    
                                }  
                                
                                ?>
                                </td>
                            </tr>
                        <?php } 
                        ?>

                        </tbody>
                    </table>

                    <input hidden name="other_sl" value="update"></input>
                    <input hidden name="anonymous" value="<?=$anonymous;?>"></input>
                    <input hidden name="year" value="<?=isset($list[0]['year'])?$list[0]['year']:'';?>"></input>
                    <input hidden name="class_no" value="<?=$list[0]['class_no'];?>"></input>
                    <input hidden name="term" value="<?=$list[0]['term']?>"></input>
                    <input hidden name="class_name" value="<?=$list[0]['class_name']?>"></input>
                    
            </div>   
            </form> 
            <?php }?>
            <!-- 特殊問卷 ↑ -->


            

        </div>
    </div>
</div>

<script type="text/javascript">
    function set_time() {
        var obj=document.getElementsByName("rowid_teacher[]");
        var stand_date = document.getElementById("standard_date").value;
        var stand_date_end = document.getElementById("standard_date_end").value;
        var len = obj.length;
        var checked = false;
        for (i = 0; i < len; i++)
        {
            if(obj[i].checked==true)
            {
                obj_1="assess_start_date@"+obj[i].value;
                obj_2="assess_end_date@"+obj[i].value;
                document.getElementById(obj_1).value=stand_date;
                document.getElementById(obj_2).value=stand_date_end;
            }
        }
    }



    function checkAll_save(id,check){
        if($("#chkall_save").prop("checked")){
            $(".selectAll_save").each(function(){
                $(this).prop("checked",true);
            })
        } else {
            $(".selectAll_save").each(function(){
                $(this).prop("checked",false);
            })
        }
    }

    function checkAll_teacher(id,check){
        if($("#chkall_teacher").prop("checked")){
            $(".selectAll_teacher").each(function(){
                $(this).prop("checked",true);
            })
        } else {
            $(".selectAll_teacher").each(function(){
                $(this).prop("checked",false);
            })
        }
    }

    

    function checkotherAll_save(id,check){
        if($("#chkall_other").prop("checked")){
            $(".selectAll_other").each(function(){
                $(this).prop("checked",true);
            })
        } else {
            $(".selectAll_other").each(function(){
                $(this).prop("checked",false);
            })
        }
    }


    
    function autoFun(id){
      var objSelect = document.getElementById('question_id');
      var length = objSelect.options.length - 1;

      for(var i = length; i >= 0; i--){
        if(objSelect[i].selected == true){
          objSelect[i].selected = false;
        }
      }

      for(var i = 0; i <= length; i++){
        if(objSelect[i].value == id){
          objSelect[i].selected = true;
          break;
        }
      }
    }
</script>