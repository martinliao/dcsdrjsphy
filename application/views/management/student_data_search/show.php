<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="sort" value="" />
                    <div class="col-xs-12">                        
                        <div class="form-group row">
                            <label class="control-label">年度:<?php
                            echo $class['year'];
                          ?></label>
                        </div>                        
                        <div class="form-group">
                            <label class="control-label">班期名稱:<?php
                            echo $class['class_name'];
                          ?></label>
                        </div>
                        <div class="form-group">
                            <label class="control-label">期別:<?php
                            echo $class['term'];
                          ?></label>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <?php if(count($group_no)>1){
                                echo "<th>組別</th>";
                            } ?>
                            <th>學號</th>
                            <th>服務單位</th>
                            <th>職稱</th>
                            <th>姓名</th>
                            <th>出生年月日</th>
                            <th>身份證字號</th>
                            <th>電話</th>
                            <th>E-MAIL</th>
                            <th>男/女</th>
                            <th>現職區分</th>
                            <th>學歷</th>
                            <th>備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($list) ){$i=0;
                        foreach ($list as $key => $row) { ?>
                            <tr>
                                <?php if(count($group_no)>1){
                                echo "<td>".$row['group_no']."</td>";
                                }?>
                                <td><?=$key;?></td>
                                <td><?=$row['bureau_name'];?></td>
                                <td><?=$row['job_title'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['birthday'];?></td>
                                <td><?=$row['idno'];?></td>
                                <td><?=$row['phone'];?></td>
                                <td><?=$row['email'];?></td>
                                <td><?=$row['gender'];?></td>
                                <td><?=$row['job_distinguish'];?></td>
                                <td><?=$row['education'];?></td>
                                <?php if($row['yn_sel']==4){
                                    $status='退訓';
                                }else if($row['yn_sel']==5){
                                    $status='未報到';
                                }else{
                                    $status="";
                                } ?>
                                <td><?=$status;?></td>
                            </tr>
                    <?php }?>
                   <?php }?>
                    </tbody>
                </table>
                </form>
                <div class="row ">
                    <div class="col-lg-4">
                        Showing <?=$filter['total'];?> entries
                    </div>
               <!--     <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div> -->
                </div>
                <a class="btn btn-default" href="<?=$link_refresh;?>" title="返回">返回</a>
                </form>
            </div>
            <!-- /.table end -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->