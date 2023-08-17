<style type="text/css">
    table {
        border-collapse: collapse;
    }
     
    table, th, td {
        border: 1px solid black;
    }
</style>
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">系列別</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">開課起迄日</th>
                            <th class="text-center">研習時數</th>
                            <th class="text-center" style="width: 10%">研習對象</th>
                            <th class="text-center">課程內容</th>
                            <th class="text-center">講座</th>
                            <th class="text-center">承辦人/分機</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        <?php 
                            $start_date = substr($data["start_date1"],0,-8);
                            $start_date_array = explode('-', $start_date);
                            $start_date = ($start_date_array[0]-1911).'/'.$start_date_array[1].'/'.$start_date_array[2];
                            $end_date = substr($data["end_date1"],0,-8);
                            $end_date_array = explode('-', $end_date);
                            $end_date = ($end_date_array[0]-1911).'/'.$end_date_array[1].'/'.$end_date_array[2];
                        ?>
                        <tr class="text-center">
                            <td><?=$data["DESCRIPTION"]?></td>
                            <td><?=$data["year"]?></td>
                            <td><?=$data["class_name"]?></td>
                            <td><?=$data["term"]?></td>
                            <td><?=$start_date . " ~ " . $end_date?></td>
                            <td><?=$data["range"]?></td>
                            <td><?=$data["respondant"]?></td>
                            <?php 
                                $i = 1;
                                $description = '';
                                $teacher_list = '';
                                if(isset($data["listArrange"])){
                                    foreach ($data["listArrange"] as $key => $value) { 
                                        $description .= $i.'.'.$value["DESCRIPTION"].'<br>'; 
                                        $teacher_list .= $value["teacher_list"];
                                        $i++;
                                    }
                                } 
                            ?>
                            <td><?=$description?></td>
                            <td><?=$teacher_list?></td>
                            <td>
                            <?php
                                echo $data["worker_name"].'/'.$data["ext1"];
                            ?>
                            </td>
                        </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
           
