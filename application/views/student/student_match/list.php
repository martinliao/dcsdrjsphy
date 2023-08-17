<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php if($page_data['mode'] != 'STUDENT'){ ?>
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('year', $choices['query_year'], $filter['year'], 'class="form-control" id="year_before"');
                                ?>
                            </div>

                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input tpye="text" name="class_no" id="class_no" value="<?=$filter['class_no'];?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input tpye="text" name="class_name" id="class_name" value="<?=$filter['class_name'];?>" class="form-control">
                            </div>
                            <?php if(isset($this->flags->user['group_id'])) {
                                if((in_array('16', $this->flags->user['group_id'])) || (in_array('1', $this->flags->user['group_id'])) || (in_array('15', $this->flags->user['group_id']))){ ?>
                                <div class="form-group">
                                    <label class="control-label">報名機關:</label>
                                    <input tpye="text" name="ck" id="ck" value="<?=$filter['ck'];?>" class="form-control">
                                </div>
                                <?php } 
                            }?>
                        </div>



                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                <?php } ?>
                <div class="col-xs-12">
                    <button class="btn btn-info btn-sm">搜尋</button>
                    <span style="color:red"><p> 1、此專區僅開放可異動媒合之班期學員，與他期學員互換期別用。請輸入聯絡方式，並自行與擬換期之學員聯繫。</p></span>
                    <span style="color:red"><p> 2、媒合成功後，請填具「學員異動表」，核章後逕傳真予公訓處班期承辦人辦理換期。(FAX：2932-3334)</p></span>
                </div>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">開課起訖日</th>
                            <th class="text-center">帶班承辦人</th>
                            <th class="text-center">聯絡電話</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row){ ?>
                        <tr>
                            <td><?=$row['year'];?></td>
                            <td><?=$row['class_no'];?></td>
                            <td><?=$row['term'];?></td>
                            <td><a href="<?=$row['url'];?>" ><?=$row['class_name'];?></a></td>
                            <td><?=substr($row['start_date1'], 0, 10);?>~<?=substr($row['end_date1'], 0, 10);?></td>
                            <td><?=$row['worker_name'];?></td>
                            <td><?=$row['office_tel'];?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <form>
                    <div class="row ">
                        <div class="col-lg-4">
                            Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                        </div>
                        <div class="col-lg-8  text-right">
                            <?=$this->pagination->create_links();?>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>