<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 同步志工系統
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <label class="control-label">實體系統</label>
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">上課日期</th>
                            <th class="text-center">開始時間</th>
                            <th class="text-center">結束時間</th>
                            <th class="text-center">教室</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            echo '<tr>';
                            echo '<td class="text-center" id="btn_group">'.$get['change_date'].'</td>';
                            echo '<td class="text-center" id="btn_group">'.$get['from_time'].'</td>';
                            echo '<td class="text-center" id="btn_group">'.$get['to_time'].'</td>';
                            echo '<td class="text-center" id="btn_group">'.$room_name.'</td>';
                            echo '</tr>';
                        ?>
                    </tbody>
                </table>

                <form id="data-form" role="form" method="post" action="<?=$link_save;?>">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <label class="control-label">志工系統<font style="color: red">(下方為即將被上方取代之舊時段，請擇一汰換之，再入志工系統檢查!)</font></label>
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">選取</th>
                                <th class="text-center">上課日期</th>
                                <th class="text-center">開始時間</th>
                                <th class="text-center">結束時間</th>
                                <th class="text-center">教室</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                for($i=0;$i<count($list);$i++){
                                    echo '<tr>';
                                    echo '<td class="text-center" id="btn_group"><input type="radio" name="vid" value="'.$list[$i]['id'].'"></td>';
                                    echo '<td class="text-center" id="btn_group">'.$list[$i]['date'].'</td>';
                                    echo '<td class="text-center" id="btn_group">'.$list[$i]['start_time'].'</td>';
                                    echo '<td class="text-center" id="btn_group">'.$list[$i]['end_time'].'</td>';
                                    echo '<td class="text-center" id="btn_group">'.$list[$i]['name'].'</td>';
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
