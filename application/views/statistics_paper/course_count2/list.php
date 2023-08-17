<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                    <form id="form" method="GET">
                        <input hidden name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <input hidden id='syear' name='year' value="">                      
                        <input hidden id='stype' name='type' value="0">
                        <input hidden id='sseries' name='series' value="">                     
                        <input hidden id='sseason' name='season' value="">
                        <input hidden id='sstartMonth' name='startMonth' value="">
                        <input hidden id='sendMonth' name='endMonth' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                        <input hidden id='srows' name='rows' value="0">
                        <input hidden id='squery_class_name' name='squery_class_name' value="">
                        <input hidden id='spbox1' name='pbox1' value="0">
                        <input hidden id='spbox2' name='pbox2' value="0">
                        <input hidden id='spbox3' name='pbox3' value="0">
                        <input hidden id='spbox4' name='pbox4' value="0">
                        <input hidden id='spbox5' name='pbox5' value="0">
                        <input hidden id='spbox6' name='pbox6' value="0">
                        <input hidden id='scbox1' name='cbox1' value="0">
                        <input hidden id='scbox2' name='cbox2' value="0">
                        <input hidden id='scbox3' name='cbox3' value="0">

                        <input hidden id='stbox1' name='tbox1' value="0">
                        <input hidden id='stbox2' name='tbox2' value="0">
                        <input hidden id='stbox3' name='tbox3' value="0">
                        <input hidden id='stbox4' name='tbox4' value="0">
                        <input hidden id='stbox5' name='tbox5' value="0">
                        <input hidden id='stbox6' name='tbox6' value="0">
                        <input hidden id='stbox7' name='tbox7' value="0"> 

                        <input hidden id='stcbox1' name='tcbox1' value="0">
                        <input hidden id='stcbox2' name='tcbox2' value="0">
                        <input hidden id='stcbox3' name='tcbox3' value="0">
                        <input hidden id='ssearch_ok' name='search_ok' value="<?= $ssearch_ok=="1"? "1":"0" ?>">
                    </form> 
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php if($sess_year==''){
                                $sess_year = date("Y")-1911;
                            }
                            ?>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">依季查詢:</label>
                            <select id='season'>
                                <option value=""><?= $choices['query_season'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_season']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_season == $i ?"selected":"" ?> ><?= $choices['query_season'][$i];?></option>
                            <?php } ?>
                            </select>
                            <label class="control-label">依月查詢:</label>
                            <select id='startMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_startMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                            <select id='endMonth'>
                                <option value=""><?= $choices['query_month'][''];?></option>
                            <?php for($i=1;$i<sizeof($choices['query_month']) ; $i++){ ?>
                                <option value="<?= $i;?>" <?= $sess_endMonth == $i ?"selected":"" ?> ><?= $choices['query_month'][$i];?></option>
                            <?php } ?>
                            </select>
                            <label class="control-label">系列別:</label>
                            <select id="series">
                                <option value="" <?= $sess_series==""? "selected":"" ?>>請選擇</option>
                                <option value="A" <?= $sess_series=="A"? "selected":"" ?>>行政系列</option>
                                <option value="B" <?= $sess_series=="B"? "selected":"" ?>>發展系列</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">依日期區間查詢:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_end_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <button class="btn btn-info" onclick="fowardweek(-7);"><<</button> 
                            <button class="btn btn-info" onclick="getCurrentWeek();">本週</button>
                            <button class="btn btn-info" onclick="fowardweek(7);">>></button>
                            <button class="btn btn-info btn-sm" onclick="setToday()">設定今天</button>
                            <button class="btn btn-info btn-sm" onclick="ClearData()">清除日期</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">班期名稱:</label>
                            <input type="text" class="form-control" id="query_class_name" name="query_class_name" value="<?=$sess_query_class_name?>">
                        </div>
                    </div>


                    <div class="row">   <!--  顯示條件  -->
                        <div class="col-xs-12" style="color:blue;">                               
                            <label class="control-label">顯示條件(請勾選)</label>
                            <input type="checkbox" id="allbox1" value="first_checkbox" class="form-group">
                             <label for="allbox1" class="form-group">條件全選 </label>                   
                        </div>
                    </div> 

                    <div class="row">   <!--  人數統計  -->
                        <div class="col-xs-12"  style="color:green;">                               
                            <label class="control-label">人數統計:</label>
                                <input type="checkbox" id="pbox1" value="first_checkbox" class="form-group" <?= $sess_pbox1=="1"? "checked":"" ?>>
                                <label for="pbox1" class="form-group">計劃 </label>
                                <input type="checkbox" id="pbox2" value="first_checkbox" class="form-group" <?= $sess_pbox2=="1"? "checked":"" ?>>
                                <label for="pbox2" class="form-group">報名 </label>
                                
                                <input type="checkbox" id="pbox6" value="first_checkbox" class="form-group" <?= $sess_pbox6=="1"? "checked":"" ?>>
                                <label for="pbox6" class="form-group">選員 </label> <!-- 改用非即時資料 -->
                                
                                <input type="checkbox" id="pbox3" value="first_checkbox" class="form-group" <?= $sess_pbox3=="1"? "checked":"" ?>>
                                <label for="pbox3" class="form-group">結訓 </label>
                                <input type="checkbox" id="pbox4" value="first_checkbox" class="form-group" <?= $sess_pbox4=="1"? "checked":"" ?>>
                                <label for="pbox4" class="form-group">人天次 </label>                                                             
                                <input type="checkbox" id="pbox5" value="first_checkbox" class="form-group" <?= $sess_pbox5=="1"? "checked":"" ?>>
                                <label for="pbox5" class="form-group">退休 </label>    
                                                 
                        </div>
                    </div> 

                    <div class="row">   <!--  班期資訊  -->
                        <div class="col-xs-12" style="color:cornflowerblue;">                               
                            <label class="control-label">班期資訊:</label>
                                <input type="checkbox" id="cbox1" value="first_checkbox" class="form-group" <?= $sess_cbox1=="1"? "checked":"" ?>>
                                <label for="cbox1" class="form-group">環教 </label>
                                <input type="checkbox" id="cbox2" value="first_checkbox" class="form-group" <?= $sess_cbox2=="1"? "checked":"" ?>>
                                <label for="cbox2" class="form-group">行銷 </label>
                                <input type="checkbox" id="cbox3" value="first_checkbox" class="form-group" <?= $sess_cbox3=="1"? "checked":"" ?>>
                                <label for="cbox3" class="form-group">教室 </label>                  
                        </div>
                    </div>

                    <div class="row">   <!--  講座資訊  -->
                        <div class="col-xs-12">                               
                            <label class="control-label">講座資訊:</label>
                                <input type="checkbox" id="tbox1" value="first_checkbox" class="form-group" <?= $sess_tbox1=="1"? "checked":"" ?>>
                                <label for="tbox1" class="form-group">講座 </label>
                                <input type="checkbox" id="tbox2" value="first_checkbox" class="form-group" <?= $sess_tbox2=="1"? "checked":"" ?>>
                                <label for="tbox2" class="form-group">機關 </label>
                                <input type="checkbox" id="tbox3" value="first_checkbox" class="form-group" <?= $sess_tbox3=="1"? "checked":"" ?>>
                                <label for="tbox3" class="form-group">職稱 </label>
                                <input type="checkbox" id="tbox4" value="first_checkbox" class="form-group" <?= $sess_tbox4=="1"? "checked":"" ?>>
                                <label for="tbox4" class="form-group">生日 </label>                                                             
                                <input type="checkbox" id="tbox5" value="first_checkbox" class="form-group" <?= $sess_tbox5=="1"? "checked":"" ?>>
                                <label for="tbox5" class="form-group">學歷 </label>  
                                <input type="checkbox" id="tbox6" value="first_checkbox" class="form-group" <?= $sess_tbox6=="1"? "checked":"" ?>>
                                <label for="tbox6" class="form-group">聘請別 </label>                                                             
                                <input type="checkbox" id="tbox7" value="first_checkbox" class="form-group" <?= $sess_tbox7=="1"? "checked":"" ?>>
                                <label for="tbox7" class="form-group">課程內容 </label>                                                                                    
                        </div>
                    </div> 

                    <div class="row">   <!--  評估鐘點  -->
                        <div class="col-xs-12" style="color:sandybrown;">                               
                            <label class="control-label">評估鐘點:</label>
                                <input type="checkbox" id="tcbox1" value="first_checkbox" class="form-group" <?= $sess_tcbox1=="1"? "checked":"" ?>>
                                <label for="tcbox1" class="form-group">鐘點費 </label>
                                <input type="checkbox" id="tcbox2" value="first_checkbox" class="form-group" <?= $sess_tcbox2=="1"? "checked":"" ?>>
                                <label for="tcbox2" class="form-group">交通費 </label>    
                                <input type="checkbox" id="tcbox3" value="first_checkbox" class="form-group" <?= $sess_tcbox3=="1"? "checked":"" ?>>
                                <label for="tcbox3" class="form-group">評估分數 </label>                                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
                              
                            <button id="csv" class="btn btn-info btn-sm" <?= $b_csv=="1"? "":"disabled" ?>>匯出</button>
                            <!--<label class="control-label">(匯出測試中未完成)</label>  
                            <button id="print" class="btn btn-info btn-sm">列印</button>    -->
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
                            </div>
                <!-- /.table head -->
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover" style="font-size:6px;">
                    <thead>
                        
                        <tr>
                            <th class="text-center" colspan="29">臺北市政府公務人員訓練處 各類班期報名人數統計表</th>
                        </tr>
                        
                        <tr>
                            <th class="text-center" style="background-color:blanchedalmond;">系列</th>
                            <th class="text-center" style="background-color:blanchedalmond;">次類別</th>
                            <th class="text-center" style="background-color:blanchedalmond;">局處名稱</th>
                            <th class="text-center" style="background-color:blanchedalmond;">承辦機關</th>
                            <th class="text-center" style="background-color:blanchedalmond;">策略主題</th> 
                            <th class="text-center" style="background-color:blanchedalmond;">班期名稱</th>
                            <th class="text-center" style="background-color:blanchedalmond;">年度</th>
                            <!--<th class="text-center" style="background-color:blanchedalmond;">期別</th>-->
                            <th class="text-center" style="background-color:blanchedalmond;">訓練期程</th>
                            <th class="text-center" style="background-color:blanchedalmond;">上課日期</th>
                            <?php
                            if($sess_pbox1 == 1){
                                echo '<th class="text-center" style="background-color:green;">計畫人數</th>';
                            }
                            if($sess_pbox2 == 1){
                                echo '<th class="text-center" style="background-color:green;">報名人數</th>';
                            }
                            if($sess_pbox6 == 1){
                                echo '<th class="text-center" style="background-color:green;">選員人數</th>';   //改用非即時資料
                            }
                            if($sess_pbox3 == 1){
                                echo '<th class="text-center" style="background-color:green;">結訓人數</th>';
                            }
                            if($sess_pbox4 == 1){
                                echo '<th class="text-center" style="background-color:green;">訓練人天次</th>';
                            }
                            if($sess_pbox5 == 1){
                                echo '<th class="text-center" style="background-color:green;">退休人員數</th>';
                            }                                                        


                            if($sess_cbox1 == 1){
                                echo '<th class="text-center" style="background-color:cornflowerblue;">環教班期</th>';
                            }
                            if($sess_cbox2 == 1){
                                echo '<th class="text-center" style="background-color:cornflowerblue;">政策行銷班期</th>';
                            }
                            if($sess_cbox3 == 1){
                                echo '<th class="text-center" style="background-color:cornflowerblue;">上課教室</th>';
                            }
                            ?>
                            <th class="text-center" style="background-color:blanchedalmond;">班期承辦人</th>
                            <?php
                            if($sess_tbox1 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">授課講座</th>';
                            }
                            if($sess_tbox2 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">任職機關</th>';
                            }
                            if($sess_tbox3 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">職稱</th>';
                            }
                            if($sess_tbox4 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">生日</th>';
                            }
                            if($sess_tbox5 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">學歷</th>';
                            }                                                        
                            if($sess_tbox6 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">聘請類別</th>';
                            }
                            if($sess_tbox7 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">課程內容</th>';
                            }


                            if($sess_tcbox1 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">鐘點費</th>';
                            } 
                            if($sess_tcbox2 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">交通費</th>';
                            }
                            if($sess_tcbox3 == 1){
                                echo '<th class="text-center" style="background-color:whitesmoke;">評估分數</th>';  
                            }
                            ?>                                                   
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <?php 
                            $fix_rowspan ="";
                            if(count($data['teachers']) > 1){
                                $fix_table = true;
                                $fix_rowspan = 'rowspan="'.count($data['teachers']).'"';
                                //echo $fix_rowspan;die();
                            }
                           
                            ?>
                            <td <?=$fix_rowspan?>><?= $data['TYPE']=='A'?"行政系列":"發展系列"?></td>
                            <td <?=$fix_rowspan?>><?= $data["description"]?></td>
                            <td <?=$fix_rowspan?>><?= $data["dev_type_name"]?></td>
                            <td <?=$fix_rowspan?>><?= $data["req_beaurau_name"]?></td>
                            <?php   //策略主題
                            if ($data['map1'] == '1'){
                                echo "<td ".$fix_rowspan.">A營造永續環境</td>";    
                            }elseif ($data['map2'] == '1'){
                                echo "<td ".$fix_rowspan.">B健全都市發展</td>";
                            }elseif ($data['map3'] == '1'){
                                echo "<td ".$fix_rowspan.">C發展多元文化</td>";
                            }elseif ($data['map4'] == '1'){
                                echo "<td ".$fix_rowspan.">D優化產業勞動</td>";
                            }elseif ($data['map5'] == '1'){
                                echo "<td ".$fix_rowspan.">E強化社會支持</td>";
                            }elseif ($data['map6'] == '1'){
                                echo "<td ".$fix_rowspan.">F打造優質教育</td>";
                            }elseif ($data['map7'] == '1'){
                                echo "<td ".$fix_rowspan.">G精進健康安全</td>";
                            }elseif ($data['map8'] == '1'){
                                echo "<td ".$fix_rowspan.">H精實良善治理</td>";
                            }else{
                                echo "<td ".$fix_rowspan."></td>";
                            }
                            ?>
                            <td <?=$fix_rowspan?>><?= $data["class_name"]."(第".$data["term"]."期)"?></td>
                            <td <?=$fix_rowspan?>><?= $data["year"]?></td>
                            <!--<td <?=$fix_rowspan?>><?= $data["term"]?></td>-->
                            <td <?=$fix_rowspan?>><?= $data["range"]?></td>
                            <td <?=$fix_rowspan?>>
                            <?php //2022-03-31
                            echo str_replace(" 00:00:00","",str_replace(",","<BR>",$data["start_date1"]));
                            ?>
                            </td>    <!-- 2021-11-09 上課日期 -->
                            <?php
                            if($sess_pbox1 == 1){
                                echo "<td ".$fix_rowspan.">".$data["No_Persons"]."</td>";
                            }

                            if($sess_pbox2 == 1){
                                echo "<td ".$fix_rowspan.">".$data["gcount"]."</td>";
                            }

                            if($sess_pbox6 == 1){
                                echo "<td ".$fix_rowspan.">".$data["peoples"]."</td>";  //改用非即時資料
                            }

                            if($sess_pbox3 == 1){
                                echo "<td ".$fix_rowspan.">".$data["gcount2"]."</td>";
                            }

                            if($sess_pbox4 == 1){
                                echo "<td ".$fix_rowspan.">".$data["lcount"]."</td>";
                            }

                            if($sess_pbox5 == 1){
                                echo "<td ".$fix_rowspan.">".$data["rcount"]."</td>";
                            }


                            if($sess_cbox1 == 1){
                                $temp_c1 = $data["env_class"] == "Y"?"☆":"";
                                echo "<td ".$fix_rowspan.">".$temp_c1."</td>";
                            }

                            if($sess_cbox2 == 1){
                                $temp_c2 = $data["policy_class"] == "Y"?"☆":"";
                                echo "<td ".$fix_rowspan.">".$temp_c2."</td>";
                            }

                            if($sess_cbox3 == 1){
                                echo "<td ".$fix_rowspan.">".$data["room"]."</td>";
                            }

                            ?>



                            <td <?=$fix_rowspan?>><?= $data["worker_fix"]?></td>
                            <!-- 2021-11-09 END -->

                            <?php
                            foreach ($data["teachers"] as $key => $teacher){
                                if($key==0){
                                    if($sess_tbox1 == 1){
                                        if ($teacher['teacher_type'] == 1){
                                            echo "<td>".$teacher["name"]."</td>";
                                        }else if ($teacher['teacher_type'] == 2){
                                            echo "<td>".$teacher["name"]."(助)</td>";
                                        }
                                    }
                                    if($sess_tbox2 == 1){
                                        echo "<td>".$teacher["corp"]."</td>";
                                    }
                                    if($sess_tbox3 == 1){
                                        echo "<td>".$teacher["position"]."</td>";
                                    }
                                    if($sess_tbox4 == 1){
                                        echo "<td>".substr($teacher["birth"],0,10)."</td>";
                                    }
                                    if($sess_tbox5 == 1){
                                        echo "<td>".$teacher["NAME"]."</td>";
                                    }
                                    if($sess_tbox6 == 1){
                                        echo "<td>".$teacher["DESCRIPTION"]."</td>";
                                    }
                                    if($sess_tbox7 == 1){
                                        echo "<td>".$teacher["course_detail"]."</td>";
                                    }
                                    if($sess_tcbox1 == 1){
                                        echo "<td>".$teacher["hour_fee"]."</td>";
                                    }
                                    if($sess_tcbox2 == 1){
                                        if($teacher["traffic_fee"]==-1){
                                            $teacher["traffic_fee"] = "0";
                                        }
                                        echo "<td>".$teacher["traffic_fee"]."</td>";
                                    }
                                    if($sess_tcbox3 == 1){
                                        echo "<td>".$teacher["report_score"]."</td>";
                                    }
                                }else{
                                    echo "<tr class='text-center'>";
                                    if($sess_tbox1 == 1){
                                        if ($teacher['teacher_type'] == 1){
                                            echo "<td>".$teacher["name"]."</td>";
                                        }else if ($teacher['teacher_type'] == 2){
                                            echo "<td>".$teacher["name"]."(助)</td>";
                                        }                                        
                                    }
                                    if($sess_tbox2 == 1){
                                        echo "<td>".$teacher["corp"]."</td>";
                                    }
                                    if($sess_tbox3 == 1){
                                        echo "<td>".$teacher["position"]."</td>";
                                    }
                                    if($sess_tbox4 == 1){
                                        echo "<td>".substr($teacher["birth"],0,10)."</td>";
                                    }
                                    if($sess_tbox5 == 1){
                                        echo "<td>".$teacher["NAME"]."</td>";
                                    }
                                    if($sess_tbox6 == 1){
                                        echo "<td>".$teacher["DESCRIPTION"]."</td>";
                                    }
                                    if($sess_tbox7 == 1){
                                        echo "<td>".$teacher["course_detail"]."</td>";
                                    }
                                    if($sess_tcbox1 == 1){
                                        echo "<td>".$teacher["hour_fee"]."</td>";
                                    }
                                    if($sess_tcbox2 == 1){
                                        if($teacher["traffic_fee"]==-1){
                                            $teacher["traffic_fee"] = "0";
                                        }
                                        echo "<td>".$teacher["traffic_fee"]."</td>";
                                    }
                                    if($sess_tcbox3 == 1){
                                        echo "<td>".$teacher["report_score"]."</td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                            
                            ?>
                            
                        </tr>
                        
                        <?php endforeach?>
                        
                        
                    </tbody>
                </table>
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div>
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
                <!-- <span align="right"><p>列印時間：2019/08/30 17:06</p></span> -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>


<script type="text/javascript">
function sendFun(){
    let count = 0;
    let type = 0;
    if($('#season').val() !=""){
        count++;
        type = 1;
    }
    if($('#startMonth').val() !="" || $('#endMonth').val() !=""){
        count++;
        type = 2;
    }
    if($('#datepicker1').val() !="" || $('#test1').val() !=""){
        count++;
        type = 3;
    }
    if(count > 1){
        alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
        return;
    }

    $('#Search').click();
}
function getCurrentWeek()
{
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,-diff);
    edate = addDays(sdate,6);
    document.getElementById("datepicker1").value = sdate;
    document.getElementById("test1").value = edate;
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    var dd = result.getDate();
    var mm = result.getMonth()+1;
    var yy = result.getFullYear();
    result = yy+'-'+mm+'-'+dd;
    return result;
}

function fowardweek(days)
{
    var date1 = document.getElementById("datepicker1").value;
    var date2 = document.getElementById("test1").value;
    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        document.getElementById("datepicker1").value = sdate; 
        document.getElementById("test1").value = edate;
    }
    else
    {
        var today = getCurrentWeek();
    }
}


$(document).ready(function() {
    $('#allbox1').click(function() {
    //get all checkbox which want to change
    var checkboxes = $(":checkbox");
    if($(this).is(':checked')) {
    checkboxes.prop('checked', 'checked');
    } else {
    checkboxes.removeAttr('checked');
    }
    });

    $(":checkbox").click(function() {
    $('#csv').prop('disabled', true);
    });

    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        let count = 0;
        let type = 0;
        if($('#season').val() !=""){
            count++;
            type = 1;
        }
        if($('#startMonth').val() !="" || $('#endMonth').val() !=""){
            count++;
            type = 2;
        }
        if($('#datepicker1').val() !="" || $('#test1').val() !=""){
            count++;
            type = 3;
        }
        if(count > 1){
            alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
            return;
        }

        $('#syear').val($('#year').val());
        $('#sseason').val($('#season').val());sseries
        $('#sseries').val($('#series').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(0);
        $('#srows').val($('select[name=rows]').val());
        $('#squery_class_name').val($('#query_class_name').val());

        if ($('#pbox1').prop("checked")) {
            $('#spbox1').val(1);
		}
        if ($('#pbox2').prop("checked")) {
            $('#spbox2').val(1);
		}
        if ($('#pbox3').prop("checked")) {
            $('#spbox3').val(1);
		}
        if ($('#pbox4').prop("checked")) {
            $('#spbox4').val(1);
		}
        if ($('#pbox5').prop("checked")) {
            $('#spbox5').val(1);
		}
        if ($('#pbox6').prop("checked")) {
            $('#spbox6').val(1);
		}

        if ($('#cbox1').prop("checked")) {
            $('#scbox1').val(1);
		}
        if ($('#cbox2').prop("checked")) {
            $('#scbox2').val(1);
		}  
        if ($('#cbox3').prop("checked")) {
            $('#scbox3').val(1);
		}                  

        if ($('#tbox1').prop("checked")) {
            $('#stbox1').val(1);
		}
        if ($('#tbox2').prop("checked")) {
            $('#stbox2').val(1);
		}
        if ($('#tbox3').prop("checked")) {
            $('#stbox3').val(1);
		}
        if ($('#tbox4').prop("checked")) {
            $('#stbox4').val(1);
		}
        if ($('#tbox5').prop("checked")) {
            $('#stbox5').val(1);
		}
        if ($('#tbox6').prop("checked")) {
            $('#stbox6').val(1);
		}
        if ($('#tbox7').prop("checked")) {
            $('#stbox7').val(1);
		}

        if ($('#tcbox1').prop("checked")) {
            $('#stcbox1').val(1);
		}
        if ($('#tcbox2').prop("checked")) {
            $('#stcbox2').val(1);
		}
        if ($('#tcbox3').prop("checked")) {
            $('#stcbox3').val(1);
		}
        $('#ssearch_ok').val(1);

        $( "#form" ).submit();
    });

    $('#print').click(function(){
        printData("printTable");
    });

    $('#csv').click(function(){
        let count = 0;
        let type = 0;
        if($('#season').val() !=""){
            count++;
            type = 1;
        }
        if($('#startMonth').val() !="" || $('#endMonth').val() !=""){
            count++;
            type = 2;
        }
        if($('#datepicker1').val() !="" || $('#test1').val() !=""){
            count++;
            type = 3;
        }
        if(count > 1){
            alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
            return;
        }

        $('#syear').val($('#year').val());
        $('#sseason').val($('#season').val());
        $('#sseries').val($('#series').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(1);
        $('#srows').val($('select[name=rows]').val());
        $('#squery_class_name').val($('#query_class_name').val());

        if ($('#pbox1').prop("checked")) {
            $('#spbox1').val(1);
		}
        if ($('#pbox2').prop("checked")) {
            $('#spbox2').val(1);
		}
        if ($('#pbox3').prop("checked")) {
            $('#spbox3').val(1);
		}
        if ($('#pbox4').prop("checked")) {
            $('#spbox4').val(1);
		}
        if ($('#pbox5').prop("checked")) {
            $('#spbox5').val(1);
		}
        if ($('#pbox6').prop("checked")) {
            $('#spbox6').val(1);
		}

        if ($('#cbox1').prop("checked")) {
            $('#scbox1').val(1);
		}
        if ($('#cbox2').prop("checked")) {
            $('#scbox2').val(1);
		}  
        if ($('#cbox3').prop("checked")) {
            $('#scbox3').val(1);
		}                  

        if ($('#tbox1').prop("checked")) {
            $('#stbox1').val(1);
		}
        if ($('#tbox2').prop("checked")) {
            $('#stbox2').val(1);
		}
        if ($('#tbox3').prop("checked")) {
            $('#stbox3').val(1);
		}
        if ($('#tbox4').prop("checked")) {
            $('#stbox4').val(1);
		}
        if ($('#tbox5').prop("checked")) {
            $('#stbox5').val(1);
		}
        if ($('#tbox6').prop("checked")) {
            $('#stbox6').val(1);
		}
        if ($('#tbox7').prop("checked")) {
            $('#stbox7').val(1);
		}

        if ($('#tcbox1').prop("checked")) {
            $('#stcbox1').val(1);
		}
        if ($('#tcbox2').prop("checked")) {
            $('#stcbox2').val(1);
		}
        if ($('#tcbox3').prop("checked")) {
            $('#stcbox3').val(1);
		}

        $( "#form" ).submit();
    });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });
});
</script>