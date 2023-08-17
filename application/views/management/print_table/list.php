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
                            <label class="control-label">年度</label>
                            <?php
                                    echo form_dropdown('year', $choices['year'], $filter['year'], 'class="form-control"');
                                ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label">班期代碼</label>
                            <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no'];?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label">班期名稱</label>
                            <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name'];?>">
                        </div>
                        <button class="btn btn-info btn-sm" onclick="serch();">查詢</button>
                        <input id="serchChecked" name="serchChecked" type="hidden" value="<?=$filter['serchChecked'];?>">
                        <input id="printAll" name="printAll" type="hidden" value="<?=$filter['printAll'];?>">
                        <div class="row">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                <?php if(!empty($list)){?>
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#5D7B9D"  style="color:white";>
                            <th>年度</th>
                            <th>班期代碼</th>
                            <th>班期名稱</th>
                            <th>期別</th>
                            <th>簽到表</th>    
                            <th>無茹素簽到表</th> 
                            <th>無刷卡資料簽到表</th>
                            <th>查堂素食登記與人工簽到表</th>
                            <th>評量表</th>
                            <th>座位表</th>                       
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$filter['year'];?></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['term'];?></td>
                                <td><a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>,'1');">簽到表</a></td>
                                <td><a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>,'0');">無茹素簽到表</a></td>
                                <td><a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>,'2');">無刷卡資料簽到表</a></td>
                                <td><a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>,'3');">查堂素食登記與人工簽到表</a></td>
                                <td><a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>,'4');">評量表</a></td>
                                <td><?php if (isset($row['room'])) { 
                                    foreach ($row['room'] as $key => $value) { 
                                        if($value['is_seat']=='Y'){ ?>
                                            <a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>,'5','<?=$value['room_id'];?>');"><?=$value['name'];?>[講師方向]</a>
                                            <a href="javascript:void(0);"  onclick="select(<?=$row['seq_no'];?>,'6','<?=$value['room_id'];?>');">[學員方向]</a><br>
                                        <?php }else{  ?>
                                            <span><?=$value['name'];?></span><br>    
                                        <?php }
                                    } 
                                } ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{?>
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#5D7B9D"  style="color:white";>
                            <th>年度</th>
                            <th>班期代碼</th>
                            <th>班期名稱</th>
                            <th>期別</th>
                            <th>簽到表</th>   
                            <th>無茹素簽到表</th>  
                            <th>無刷卡資料簽到表</th>
                            <th>查堂素食登記與人工簽到表</th>
                            <th>評量表</th>
                            <th>座位表</th>                       
                        </tr>
                    </thead>
                </table>
                <?php }?>
                </form>

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
            <!-- /.table end -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
    function sendFun(){
        var obj = document.getElementById('filter-form');
        obj.submit();
    }
    function select(seq_no,type,class_room=''){
        if (seq_no == undefined){
            alert('找無此資料');
        }
        if(type <4){   
            var url ='<?=base_url('management/print_table/checkinFrom/');?>'+seq_no+'?type='+type;
            window.open(url);
        }else if(type==4){
            var url ='<?=base_url('management/print_table/rating?seq_no=');?>'+seq_no+'&type='+type;
            window.open(url);
        }else if(type==5){
            var url ='<?=base_url('management/print_table/roomSeat/');?>'+seq_no+'?type='+type+'&classroom='+class_room;
            window.open(url);
        }else if(type==6){
            var url ='<?=base_url('management/print_table/roomSeat/');?>'+seq_no+'?type='+type+'&classroom='+class_room;
            window.open(url);
        }else{
        alert('找無此資料');
        }  
    }
    function serch(){
         serchChecked.value ='1';
         printAll.value='1';
    }
</script>