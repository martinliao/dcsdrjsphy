
                <table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="19">臺北市政府公務人員訓練處 混成班期統計報表</th>
                        </tr>
                        <tr>
                            <th class="text-center">年度</th>
                            <th class="text-center">月份</th>
                            <th class="text-center">系列</th>
                            <th class="text-center">單位<br>類別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">班期<br>性質</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">線上課程內容</th>
                            <th class="text-center" style="width:10%">線上課<br>程講座</th>
                            <th class="text-center">實體課程內容</th>
                            <th class="text-center" style="width:10%">實體課程<br>講座</th>
                            <th class="text-center">報名<br>人數</th>
                            <th class="text-center">結訓<br>人數</th>
                            <th class="text-center">結訓<br>(男)</th>
                            <th class="text-center">結訓<br>(女)</th>
                            <th class="text-center">訓練<br>期程</th>
                            <th class="text-center">訓練人<br>天次</th>
                            <th class="text-center">人天次<br>(男)</th>
                            <th class="text-center">人天次<br>(女)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $tmp_count = count($datas);
                            
                            if($tmp_count > 0){
                                echo '<tr class="text-left">';
                                echo '<td colspan="11" style="text-align: right">總計：</td>';
                                echo '<td>'.$datas[$tmp_count-1]["total_scount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["gcount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["gcountm"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["gcountf"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["range"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["lcount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["mcount"].'</td>';
                                echo '<td>'.$datas[$tmp_count-1]["TOTAL_COUNT"]["fcount"].'</td>';
                                echo '</tr>';
                            }

                            for($i=0;$i<$tmp_count;$i++){
                                echo '<tr>';
                                echo '<td>'.$datas[$i]['year'].'</td>';
                                echo '<td>'.intval($datas[$i]['month']).'</td>';
                                echo '<td>'.$datas[$i]['series'].'</td>';
                                echo '<td>'.$datas[$i]['description'].'</td>';
                                echo '<td>'.$datas[$i]['class_name'].'</td>';
                                echo '<td>考核+混成</td>';
                                echo '<td>'.$datas[$i]['term'].'</td>';
                                echo '<td>'.$datas[$i]['onlineCourse'].'</td>';
                                echo '<td>'.$datas[$i]['onlineTeacher'].'</td>';
                                echo '<td>'.$datas[$i]['phyCourse'].'</td>';
                                echo '<td>'.$datas[$i]['phyTeacher'].'</td>';
                                echo '<td>'.$datas[$i]['scount'].'</td>';
                                echo '<td>'.$datas[$i]['gcount'].'</td>';
                                echo '<td>'.$datas[$i]['gcountm'].'</td>';
                                echo '<td>'.($datas[$i]['gcount']-$datas[$i]['gcountm']).'</td>';
                                echo '<td>'.$datas[$i]['range'].'</td>';
                                echo '<td>'.$datas[$i]['lcount'].'</td>';
                                echo '<td>'.$datas[$i]['mcount'].'</td>';
                                echo '<td>'.$datas[$i]['fcount'].'</td>';
                            }

                        ?>
                    </tbody>
                </table>
               