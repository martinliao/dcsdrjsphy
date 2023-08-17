<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" action="" >
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row ">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                            <label class="control-label">班期名稱:</label>
                            <input type="text" name="query_class_name" value="<?=$filter['query_class_name'];?>" class="form-control">
                            <label class="control-label">班期代碼:</label>
                            <input type="text" name="query_class_no" value="<?=$filter['query_class_no'];?>"  class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm"  onclick="selectAction(1)">查詢</button>
                            <button class="btn btn-info btn-sm"  onclick="selectAction(2)">列印</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
               
                <!-- /.table head -->
                <form method="post" role="form">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">局處名稱</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期數</th>
                            <th class="text-center">排序</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row){?>
                        <tr class="text-center">
                            <td><?=$row['bureau_name']?></td>
                            <td><?=$row['year']?></td>
                            <td><?=$row['class_name']?></td>
                            <td><?=$row['total_term']?></td>
                            <td><input type="text" id="query_sort[]" name="query_sort[]"  maxlength="3" size="3" value="<?=$row['sort']?>" class="form-control"></td>
                            <input type="hidden" name="query_class_term[]" value="<?=$row['term']?>" >
                            <input type="hidden" name="query_class_no[]" value="<?=$row['class_no']?>" >
                            <input type="hidden" name="query_class_year[]" value="<?=$row['year']?>" >
                        </tr>
                        <?php  }?>
                    <tbody>
                </table>
                    <input type="hidden" name="item_id" id="item_id" value="">
                    <button class="btn btn-info" >確定</button>
                </form>
                
                
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to
                        <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?>
                        of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>


function selectAction($number){
    if($number==1){
        document.getElementById("filter-form").action = "<?=$link_index;?>";
        document.filter-form.submit();
        
    }
    if($number==2){
        document.getElementById("filter-form").action = "<?=$link_export;?>";
        document.getElementById("filter-form").setAttribute('target', '_blank'); 
        document.filter-form.submit();
    }
}


function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
</script>
