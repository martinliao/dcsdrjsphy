<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
            <div class="col-xs-12">
                <form id="list-form">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover" id="table">
                        <?php if($this->input->post('query_type')=='B') { $sum_people=0; $sum_time=0; $sum_time_real=0; $serial=1;?>
                        <thead>
                            <tr>
                                <th class="text-center">序號</th>
                                <th class="text-center">次類別</th>
                                <th class="text-center">系類別</th>
                                <th class="text-center">班期名稱</th>
                                <th class="text-center">研習對象</th>
                                <th class="text-center">初始期數</th>
                                <th class="text-center">實際期數</th>
                                <th class="text-center">每期人數</th>
                                <th class="text-center">每期時數(實體)</th>
                                <th class="text-center">每期時數(線上)</th>
                                <th class="text-center">每期時數(實+線)</th>
                                <th class="text-center">人數合計</th>
                                <th class="text-center">時數合計(實體)</th>
                                <th class="text-center">時數合計</th>
                                <th class="text-center">e大課程名稱</th>
                                <th class="text-center">預定開班時間</th>
                                <th class="text-center">環教班期</th>
                                <th class="text-center">政策行銷班期</th>
                                <th class="text-center">重大政策</th>
                                <th class="text-center">開放退休人員選課</th>
                                <th class="text-center">無須支應講座鐘點費</th>
                                <th class="text-center">上課地點非公訓處</th>
                                <th class="text-center">前1年承辦人</th>
                                <th class="text-center">承辦人</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $row) {?>
                            <tr >
                                <td width="1%"><?=$serial;?></td>
                                <td class="text-center" dt="c1"><?=$row['second_name'];?></td>
                                <td><?=$row['series_name'];?></td>
                                <td class="text-center" width="15%"><?=$row['class_name'];?></td>
                                <td class="text-center" width="8%"><?=$row['respondant'];?></td>
                                <td class="text-center"><?=$row['base_term']?></td>
                                <td class="text-center" width="1%"><?=$row['term'];?></td>
                                <td class="text-center" width="1%"><?=$row['no_persons'];?></td>
                                <td class="text-center" width="1%"><?=$row['range'];?></td>
                                <td class="text-center" width="1%"><?=$row['online_total_hours'];?></td>
                                <td class="text-center" width="1%"><?=$row['range']+$row['online_total_hours']?></td>
                                <td class="text-center" width="1%"><?=$row['term']*$row['no_persons'];?></td>
                                <td class="text-center" width="1%"><?=$row['term']*$row['range'];?></td>
                                <td class="text-center" width="1%"><?=$row['term']*($row['range']+$row['online_total_hours']);?></td>
                                <?php $online_course=array();
                                        $k=1;
                                    for($i=0;$i<count($row['online_course']);$i++){
                                        
                                        $online_course[$i]=$k.'.'.$row['online_course'][$i];
                                        $k++;
                                    }
                                    $course=implode("<br>",$online_course);
                                    //var_dump($row['online_course']);
                                    $row['each_term_date'] = str_replace(",","<br>",$row['each_term_date']);
                                ?>
                                <td><?=$course?></td>

                                <td class="text-center" width="8%"><?=$row['each_term_date']?></td>
                                <td width="1%"><?php if($row['env_class']=='Y') echo'☆'; else echo'';?></td>
                                <td width="1%"><?php if($row['policy_class']=='Y') echo'☆'; else echo'';?></td>
                                <td width="1%">
                                    <?php   $map_list='';
                                            if($row['map1'] == '1'){
	                                            $map_list = 'A營造永續環境 ';
                                            } 
                                            if($row['map2'] == '1'){
	                                            $map_list = 'B健全都市發展 ';
                                            }
                                            if($row['map3'] == '1') {
	                                            $map_list = 'C發展多元文化 ';
                                            }
                                            if($row['map4'] == '1') {
	                                            $map_list = 'D優化產業勞動 ';
                                            }
                                            if($row['map5'] == '1') {
	                                            $map_list = 'E強化社會支持 ';
                                            }
                                            if($row['map6'] == '1') {
	                                            $map_list = 'F打造優質教育 ';
                                            }
                                            if($row['map7'] == '1') {
	                                            $map_list = 'G精進健康安全 ';
                                            }
                                            if($row['map8'] == '1') {
	                                            $map_list = 'H精實良善治理 ';
                                            }
                                            if($row['map9'] == '1'){
                                                $map_list[] = "樂活宜居(45項)";
                                            }
                                            if($row['map10'] == '1'){
                                                $map_list[] = "友善共融(31項)";
                                            }
                                            if($row['map11'] == '1'){
                                                $map_list[] = "創新活力(37項)";
                                            }
                                        echo $map_list;
                                    ?>
                                </td> 
                                <?php 
                                    $open_retirement="";
                                    if($row['open_retirement']=='Y'){
                                    $open_retirement='☆';
                                }?>
                                <td width="1%"><?=$open_retirement?></td>
                                <td width="1%"><?php   $special = '';
                                            if($row['special_status'] == '1'){
                                                $special = '無須支應講座鐘點費';
                                            } 
                                            echo $special;
                                    ?>
                                </td>
                                <td width="1%"><?php   $special = '';
                                            if($row['special_status'] == '2'){
                                                $special = '上課地點非公訓處';
                                            } 
                                            echo $special;
                                    ?>
                                </td>
                                <?php $serial=$serial+1;?>
                                <td width="1%"><?= $row['pre_worker']?></td>
                                <td width="1%"><?= $row['BS_name']?></td>
                            </tr>
                            <?php $tmp_total= $row['range_real']+$row['range_internet']; $sum_time_real+=$row['range_real']; $sum_people+=$row['no_persons']; $sum_time+=$tmp_total?>
                            <?php } ?>
                            <tr id="test1">
                                <th class="text-center">人數合計</th>
                                <td colspan="23"><?=$sum_people?></td>
                            </tr>
                            <tr id="test1">
                                <th class="text-center">時數合計(實體)</th>
                                <td colspan="23"><?=$sum_time_real?></td>
                            </tr>
                            <tr id="test1">
                                <th class="text-center">時數合計</th>
                                <td colspan="23"><?=$sum_time?></td>
                            </tr>
                        </tbody>
                        <?php }?>
                        <?php if($this->input->post('query_type')=='A') {$sum_people=0;$sum_time=0; $i=1; $serial=1;?>
                            <thead>
                            <tr>
                                <th class="text-center">序號</th>
                                <th class="text-center">次類別</th>
                                <th class="text-center">所屬局處名稱</th>
                                <th class="text-center">班期名稱</th>
                                <th class="text-center">研習對象</th>
                                <th class="text-center">初始期數</th>
                                <th class="text-center">實際期數</th>
                                <th class="text-center">每期人數</th>
                                <th class="text-center">每期時數</th>
                                <th class="text-center">人數合計</th>
                                <th class="text-center">時數合計</th>
                                <th class="text-center">權重</th>
                                <th class="text-center">權重後時數</th>
                                <th class="text-center">課程預定日期</th>
                                <th class="text-center">天數</th>
                                <th class="text-center">教室</th>
                                <th class="text-center">環教班期</th>
                                <th class="text-center">政策行銷班期</th>
                                <th class="text-center">重大政策</th>
                                <th class="text-center">開放退休人員選課</th>
                                <th class="text-center">無須支應講座鐘點費</th>
                                <th class="text-center">上課地點非公訓處</th>
                                <th class="text-center">承辦人</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $row) { ?>
                            <tr class="text-center">
                                <td><?=$serial;?></td>
                                <?php $serial=$serial+1;?>
                                <td  dt="c1"><?=$row['second_name'];?></td>
                                <td><?=$row['dev_type_name'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['respondant'];?></td>
                                <td><?=$row['base_term']?></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['no_persons'];?></td>
                                <td><?=$row['range'];?></td>
                                <td><?=$row['term']*$row['no_persons'];?></td>
                                <td><?=$row['term']*$row['range'];?></td>
                                <td><?=$row['weights'];?></td>
                                <td><?=$row['weights']*$row['range']?></td>
                                <?php
                                    $row['each_term_date'] = str_replace(",","<br>",$row['each_term_date']);
                                ?>
                                <td><?=$row['each_term_date']?></td>
                                <td><?php $time1=$row['start_date1']; $time2=$row['end_date1']; 
                                        echo (strtotime($time2) - strtotime($time1))/ (60*60*24);?></td>
                                <td><?=$row['room_code']?></td>
                                <td><?php if($row['env_class']=='Y') echo'☆'; else echo'';?></td>
                                <td><?php if($row['policy_class']=='Y') echo'☆'; else echo'';?></td>
                                <td>
                                    <?php   $map_list='';
                                            if($row['map1'] == '1'){
	                                            $map_list = 'A營造永續環境 ';
                                            } 
                                            if($row['map2'] == '1'){
	                                            $map_list = 'B健全都市發展 ';
                                            }
                                            if($row['map3'] == '1') {
	                                            $map_list = 'C發展多元文化 ';
                                            }
                                            if($row['map4'] == '1') {
	                                            $map_list = 'D優化產業勞動 ';
                                            }
                                            if($row['map5'] == '1') {
	                                            $map_list = 'E強化社會支持 ';
                                            }
                                            if($row['map6'] == '1') {
	                                            $map_list = 'F打造優質教育 ';
                                            }
                                            if($row['map7'] == '1') {
	                                            $map_list = 'G精進健康安全 ';
                                            }
                                            if($row['map8'] == '1') {
	                                            $map_list = 'H精實良善治理 ';
                                            }
                                            if($row['map9'] == '1'){
                                                $map_list[] = "樂活宜居(45項)";
                                            }
                                            if($row['map10'] == '1'){
                                                $map_list[] = "友善共融(31項)";
                                            }
                                            if($row['map11'] == '1'){
                                                $map_list[] = "創新活力(37項)";
                                            }
                                        echo $map_list;
                                    ?>
                                </td> 
                                <?php 
                                    $open_retirement="";
                                    if($row['open_retirement']=='Y'){
                                    $open_retirement='☆';
                                }?>
                                <td><?=$open_retirement?></td>
                                <td>
                                <?php   $special = '';
                                            if($row['not_hourfee'] == 'Y'){
                                                $special = '☆';
                                            } 
                                            echo $special;
                                    ?>
                                </td>
                                <td><?php   $special = '';
                                            if($row['not_location'] == 'Y'){
                                                $special = '☆';
                                            } 
                                            echo $special;
                                    ?>
                                </td>
                                <td><?= $row['BS_name']?></td>
                            </tr>
                            <?php $sum_people+=$row['no_persons']; $sum_time+=$row['range'];?>
                            <?php } ?>
                            <tr>
                                <th class="text-center">人數合計</th>
                                <td colspan="22" class="text-left"><?=$sum_people;?></td>
                            </tr>
                            <tr>
                                <th class="text-center">時數合計</th>
                                <td colspan="22" class="text-left"> <?=$sum_time;?></td>
                            </tr>
                        </tbody>
                        <?php } ?>
                    </table>
                    <!--<a href="<?=base_url('planning/year_plan_search')?>" class="btn btn-info sm">返回</a>-->
                </form>
            </div>    
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
<script>
//合併相同的td
jQuery.fn.rowspan = function(){ 
    var i=0; 
    var pText=''; 
    var sObj;   //預計進行RowSpan物件 
    var rcnt=0; //計算rowspan的數字 
    var tlen=this.length; 
    return this.each(function(){ 
        i=i+1; 
        rcnt=rcnt+1; 
	        //與前項不同 
	        if(pText!=$(this).text()) 
	        { 
	            if(i!=1) 
	            { 
	                //不是剛開始，進行rowspan 
	                sObj.attr('rowspan',rcnt-1); 
	                rcnt=1; 
	            } 
	            //設定要rowspan的物件 
	            sObj=$(this); 
	            pText=$(this).text(); 
	        } 
	        else 
	        { 
	            $(this).hide(); 
	        } 
	             
	        if(i==tlen) 
	        { 
	            sObj.attr('rowspan',rcnt); 
	        } 
	    }); 
	} 

//$('td[dt="c1"]').rowspan(); 

</script>