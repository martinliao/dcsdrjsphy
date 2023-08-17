<style type="text/css">
    #rows input, #rows select {
        border-radius: 3px;
        font-size: 12px;
        height: 30px;
        line-height: 1.5;
        padding: 5px 10px;
    }    
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <form id="data-form" role="form" class="form-inline" method="POST" action="">  
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr style="background: #8CBBFF;">
                            <th colspan="2">請假紀錄修改-學員姓名-<?=$info[0]['name']?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" >
                        <tr>
                            <td style="width: 20%">請假日期</td>
                            <td>
                                <select name="vacation_date" id="vacation_date">
                                <?php foreach($room_uses as $room_use): ?>
                                    <option value="<?=$room_use->use_date?>" <?=($info[0]['vacation_date'] == $room_use->use_date)?'selected="selected"':'' ?>><?=$room_use->use_date?></option>
                                <?php endforeach ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">請假時間</td>
                            <td>
                                <?php
                                    $start_hour = substr($info[0]['from_time'], 0, 2);
                                    $start_minute = substr($info[0]['from_time'], 2, 2);
                                    $end_hour = substr($info[0]['to_time'], 0, 2);
                                    $end_minute = substr($info[0]['to_time'], 2, 2);
                                    echo '<select name="start_hour" data-size="10">';

                                    for($j=0;$j<=23;$j++){
                                        $hour = str_pad($j,2,'0',STR_PAD_LEFT);
                                        $selected = ($start_hour==$hour)?'selected="selected"':'';
                                        echo '<option value="'.$hour.'"'.' '.$selected.'>'.$hour.'</option>';
                                    }
                                    echo '</select>';
    
                                    echo '<select name="start_minute" >';
                                    for($j=0;$j<=59;$j++){
                                        $minute = str_pad($j,2,'0',STR_PAD_LEFT);
                                        $selected = ($start_minute==$minute)?'selected="selected"':'';
                                        echo '<option value="'.$minute.'"'.' '.$selected.'>'.$minute.'</option>';
                                    }
                                    echo '</select>';
                                    echo '分~';
                                    echo '<select name="end_hour" data-size="10">';
                                    for($j=0;$j<=23;$j++){
                                        $hour = str_pad($j,2,'0',STR_PAD_LEFT);
                                        $selected = ($end_hour==$hour)?'selected="selected"':'';
                                        echo '<option value="'.$hour.'"'.' '.$selected.'>'.$hour.'</option>';
                                    }
                                    echo '</select>';
    
                                    echo '<select name="end_minute" >';
                                    for($j=0;$j<=59;$j++){
                                        $minute = str_pad($j,2,'0',STR_PAD_LEFT);
                                        $selected = ($end_minute==$minute)?'selected="selected"':'';
                                        echo '<option value="'.$minute.'"'.' '.$selected.'>'.$minute.'</option>';
                                    }
                                    echo '</select>';
                                    echo '分';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">合計時數</td>
                            <td><?=$info[0]['hours']?>小時</td>
                        </tr>
                       
                    </tbody>
                </table>
                <input type="submit" class="btn btn-info" value="確定">
                </from>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>