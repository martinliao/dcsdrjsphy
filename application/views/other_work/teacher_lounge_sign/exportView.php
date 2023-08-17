<table  border="1" id="printTable" class="table table-bordered table-condensed table-hover">
    <thead>
        <tr>
            <th class="text-center" style="background-color: #858484">日期</th>
            <th class="text-center" style="background-color: #858484">星期</th>
            <th class="text-center" style="background-color: #858484">休息室</th>
            <th class="text-center" style="background-color: #858484">08:00-12:00</th>
            <th class="text-center" style="background-color: #face4a;color:red;font-size: 28px">12:00-13:40</th>
            <th class="text-center" style="background-color: #858484">13:40-17:30</th>
            <th class="text-center" style="background-color: #858484">17:30-</th>
        </tr>
    </thead>
    <tbody>
        <?php
            for($i=0;$i<$days;$i++){
                $today = date('Y-m-d');
                $thisday = date('Y-m-d',strtotime("$sess_start_date + $i days"));
                $disabled = '';

                if(strtotime($today)>strtotime($thisday)){
                    $disabled = 'disabled';
                }

                $current_date = date('m/d',strtotime("$sess_start_date + $i days"));
                $current_day = date('w',strtotime("$sess_start_date + $i days"));

                switch ($current_day) {
                    case '0':
                        $current_day = '日';
                        break;
                    case '1':
                        $current_day = '一';
                        break;
                    case '2':
                        $current_day = '二';
                        break;
                    case '3':
                        $current_day = '三';
                        break;
                    case '4':
                        $current_day = '四';
                        break;
                    case '5':
                        $current_day = '五';
                        break;
                    case '6':
                        $current_day = '六';
                        break;
                    default:
                        break;
                }

                echo '<tr>';
                echo '<td rowspan="5" class="text-center">'.$current_date.'</td>';
                echo '<td rowspan="5" class="text-center">'.$current_day.'</td>';
                echo '<td class="text-center">C301</td>';

                $keep_key = 'C301_'.$thisday;
                if(isset($keep_list[$keep_key])){
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                } else {
                    $key = 'A_C301_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'B_C301_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'C_C301_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'D_C301_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }
                }
                echo '</tr>';

                echo '<tr>';
                echo '<td class="text-center">C302</td>';

                $keep_key = 'C302_'.$thisday;
                if(isset($keep_list[$keep_key])){
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                } else {
                    $key = 'A_C302_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'B_C302_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'C_C302_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'D_C302_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }
                }
                echo '</tr>';

                echo '<tr>';
                echo '<td class="text-center">C303</td>';

                $keep_key = 'C303_'.$thisday;
                if(isset($keep_list[$keep_key])){
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                } else {
                    $key = 'A_C303_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'B_C303_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'C_C303_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'D_C303_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }
                }
                echo '</tr>';

                echo '<tr>';
                echo '<td class="text-center">C304</td>';

                $keep_key = 'C304_'.$thisday;
                if(isset($keep_list[$keep_key])){
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                } else {
                    $key = 'A_C304_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'B_C304_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'C_C304_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'D_C304_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }
                }
                echo '</tr>';

                echo '<tr>';
                echo '<td class="text-center">C305</td>';

                $keep_key = 'C305_'.$thisday;
                if(isset($keep_list[$keep_key])){
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                    echo '<td>'.$keep_list[$keep_key].'</td>';
                } else {
                    $key = 'A_C305_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'B_C305_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'C_C305_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }

                    $key = 'D_C305_'.$thisday;
                    if(isset($data_list[$key])){
                        echo '<td>';
                        echo $data_list[$key]['description1'];
                        echo $data_list[$key]['description2'];
                        echo $data_list[$key]['description3'];
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }
                }
                echo '</tr>';
            }
        ?>
    </tbody>
</table>