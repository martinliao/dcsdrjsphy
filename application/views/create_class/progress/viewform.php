<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 問卷預覽
            </div>

            <div class="panel-body">
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">問卷預覽列表</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($form as $row) { ?>
                        <tr>
                            <td><a href="http://dcsdcourse.taipei.gov.tw/survey/client_reply_tmp.php?cmfid=<?=$row['id'];?>" target="_blank">
                            <?php
                            //20211203 Roger 非講座評估加入班期資訊
                            $formnamesl = mb_substr($row['formName'],0,4);
                               //echo $formnamesl;                      
                            if($formnamesl == "講座評估"){
                                echo $row['formName'];
                            }else{
                               echo $courseinfo->year."年-".$courseinfo->name."(".$courseinfo->ladder.")-".$row['formName'];
                            }
                            ?>
                            </a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>       
        </div>
    </div>
</div>