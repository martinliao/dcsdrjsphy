<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">

                    <div class="row">
                        <form id="filter-form" role="form" class="form-inline">
                        <div class="col-xs-12">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['year'];?>"disabled>
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_no'];?>"disabled>
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_name'];?>"disabled>
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['term'];?>"disabled>
                        </div>
                        </form>
                        <div class=col-xs-12>
                            <input type="button"  onclick="go_to_add()" class="btn btn-info" value="新增" >
                            <input type="button" onclick="go_back()" value="回上頁" class="btn btn-info">
                        </div>
                    </div>
                    <br>

                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">類別</th>
                            <th class="text-center">百分比</th>
                            <th class="text-center" colspan="2">修改</th>
                        </tr>
                    </thead>
                    <form id="data-form" role="form" method="post" >
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <input type="hidden" name="year" id="year" value="<?=$detail_data['year'];?>">
                        <input type="hidden" name="class_no" id="class_no" value="<?=$detail_data['class_no'];?>">
                        <input type="hidden" name="class_name" id="class_name" value="<?=$detail_data['class_name'];?>">
                        <input type="hidden" name="term" id="term" value="<?=$detail_data['term'];?>">
                        <input type="hidden" name="id" id="id" value="">
                    </form>
                    <tbody>
                        <?php foreach($list as $row){ ?>
                        <tr>
                            <td><?=$choices['grade_type'][$row['grade_type']];?></td>
                            <td><?=$row['proportion'];?>%</td>
                            <td class="text-center"><button  onclick="go_to_edit('<?=$row['id'];?>')" >修改</button></td>
                            <td class="text-center"><button  onclick="btn_del('<?=$row['id'];?>')">刪除</button></td>
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

<script>
    function go_to_add() {
        obj=document.getElementById("data-form");
        obj.action='<?=base_url('management/point_maintain/add')?>';
        obj.submit();
    }

    function go_to_edit(url) {
        obj=document.getElementById("data-form");
        obj.action='<?=base_url('management/point_maintain/edit/')?>'+url;
        obj.submit();
    }

    function go_back(){
        obj =document.getElementById('data-form');
        obj.action='<?=base_url('management/point_maintain/')?>';
        obj.submit();
    }

    function btn_del(url){

        if(confirm('確定刪除? ') == false) return false; // 刪除之前要先做確認 custom by chiahua
        obj =document.getElementById('data-form');
        obj.id.value = url;

        obj.action='<?=base_url('management/point_maintain/delete')?>';

        obj.submit();


    }

</script>