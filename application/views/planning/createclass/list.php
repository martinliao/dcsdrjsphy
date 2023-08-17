<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control" id="query_year"');
                                ?>
                            </div>
                            <?php if($user_bureau == '379680000A'){ ?>
                            <div class="form-group">
                                <label class="control-label">草案、確定計畫、新增計畫</label>
                                <?php
                                    echo form_dropdown('query_class_status', $choices['query_class_status'], $filter['query_class_status'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">系列別</label>
                                <?php
                                    echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">次類別</label>
                                <select class="form-control" name='query_second' id='query_second'>
                                    <option value="">請選擇次類別</option>
                                    <?php
                                        if(isset($choices['query_second']) && !empty($choices['query_second'])){
                                            for($i=0;$i<count($choices['query_second']);$i++){
                                                if($choices['query_second'][$i]['item_id'] == $filter['query_second']){
                                                    echo '<option value="'.$choices['query_second'][$i]['item_id'].'" selected="selected">'.$choices['query_second'][$i]['name'].'</option>';
                                                } else {
                                                    echo '<option value="'.$choices['query_second'][$i]['item_id'].'">'.$choices['query_second'][$i]['name'].'</option>';
                                                }
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="query_class_no" value="<?=$filter['query_class_no'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name'];?>">
                            </div>
                            <?php if($user_bureau == '379680000A'){ ?>
                            <div class="form-group">
                                <label class="control-label">取消開班</label>
                                <input type="checkbox" class="form-control" name="query_is_cancel" value="1" <?= isset($filter['query_is_cancel']) && $filter['query_is_cancel']=='1'?'checked':'';?>>
                            </div>
                            <?php } ?>
                            <button class="btn btn-info">查詢</button>
                            <a href="<?=base_url('planning/setclass/add/?from=createclass')?>" class="btn btn-warning" target="_blank">匯入</a>
                            <?php if($user_bureau == '379680000A'){ ?>
                            <a class="btn btn-warning" onclick="doPrint()">列印年度優先調查表</a>
                            <a class="btn btn-warning" onclick="doExport()">匯出</a>
                            <?php } ?>
                        </div>
                       <!-- <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                        </div>-->
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width: 3%;"  class="sorting<?=($filter['sort']=='series_name asc')?'_asc':'';?><?=($filter['sort']=='series_name desc')?'_desc':'';?>" data-field="series_name" >系列</th>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <th style="width: 8%;"  class="sorting<?=($filter['sort']=='second_name asc')?'_asc':'';?><?=($filter['sort']=='second_name desc')?'_desc':'';?>" data-field="second_name" >次類別</th>
                                <?php } ?>
                                <th style="width: 8%;"  class="sorting<?=($filter['sort']=='dev_type_name asc')?'_asc':'';?><?=($filter['sort']=='dev_type_name desc')?'_desc':'';?>" data-field="dev_type_name" >局處名稱</th>
                                <th style="width: 3%;"  class="sorting<?=($filter['sort']=='year asc')?'_asc':'';?><?=($filter['sort']=='year desc')?'_desc':'';?>" data-field="year" >年度</th>
                                <th style="width: 3%;"  class="sorting<?=($filter['sort']=='term asc')?'_asc':'';?><?=($filter['sort']=='term desc')?'_desc':'';?>" data-field="term" >期別</th>
                                <th style="width: 5%;"  class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                                <th style="width: 30%;"  class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                                <th style="width: 7%;"  class="sorting<?=($filter['sort']=='contactor asc')?'_asc':'';?><?=($filter['sort']=='contactor desc')?'_desc':'';?>" data-field="contactor" >局處承辦人</th>
                                <th style="width: 10%;"  class="sorting<?=($filter['sort']=='tel asc')?'_asc':'';?><?=($filter['sort']=='tel desc')?'_desc':'';?>" data-field="tel" >局處電話</th>
                                <th style="width: 3%;"  class="sorting<?=($filter['sort']=='range asc')?'_asc':'';?><?=($filter['sort']=='range desc')?'_desc':'';?>" data-field="range" >期程</th>
                                <th style="width: 7%;"  class="sorting<?=($filter['sort']=='room_code asc')?'_asc':'';?><?=($filter['sort']=='room_code desc')?'_desc':'';?>" data-field="room_code" >教室</th>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <th style="width: 5%;"  class="sorting<?=($filter['sort']=='is_cancel asc')?'_asc':'';?><?=($filter['sort']=='is_cancel desc')?'_desc':'';?>" data-field="is_cancel" >取消開班</th>
                                <?php } ?>
                                <th style="width: 5%;"  class="sorting<?=($filter['sort']=='ecpa_class_id asc')?'_asc':'';?><?=($filter['sort']=='ecpa_class_id desc')?'_desc':'';?>" data-field="ecpa_class_id" >ECPA課程類別代碼</th>
                                <th style="width: 5%;" >功能</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $seq=1; foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$seq++;?></td>
                                <td><?=$row['series_name'];?></td>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <td><?=$row['second_name'];?></td>
                                <?php } ?>
                                <td><?=$row['dev_type_name'];?></td>
                                <td><?=$row['year'];?></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['contactor'];?></td>
                                <td><?=$row['tel'];?></td>
                                <td><?=$row['range'];?></td>
                                <td><?=$row['room_name'];?></td>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <?php 
                                    if($row['is_cancel']=='1'){
                                        $cancel="<td>取消開班</td>";
                                    }else if($row['5a_is_cancel']=='Y'){
                                        $cancel="<td><span style='color:red'>取消</span></td>";
                                    }else{
                                        $cancel="<td></td>";
                                    }
                                ?>
                                <?=$cancel?>
                                <!--<td><?=$row['is_cancel']=='1'?'取消開班':'';?></td>-->
                                <?php } ?>
                                <td><?=$row['ecpa_class_id'];?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_view'])) { ?>
                                    <a type="button" class="btn btn-outline btn-success btn-xs btn-toggle" title="View" href="<?=$row['link_view'];?>">
                                        <i class="fa fa-eye fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_edit'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$row['link_edit'];?>">
                                        <i class="fa fa-pencil fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_delete'])) { ?>
                                    <button type="button" class="btn btn-outline btn-danger btn-xs" onclick="ajaxDelete(this, '確認要刪除選單「<?=$row['name'];?>」?', '<?=$row['link_delete'];?>')">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </form>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

function removeOptions(selectbox) {
    var i;
    for (i = selectbox.options.length - 1; i >= 0; i--) {
        selectbox.remove(i);
    }
}

function doPrint(){
    if(document.getElementById("query_year")==''){
        alert('請選年度!');
    }
    var link_path = 'http://dcsdcourse.taipei.gov.tw/base/admin/require_dev_set_sort_print.php?query_year=' + document.getElementById("query_year").value + '&query_bureau=' + "<?=$user_bureau?>";
    window.open(link_path,'planSearch','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=900');
    
}

function doExport(){
    obj = document.getElementById('filter-form');
    obj.action = "<?=base_url('planning/createclass/exportCsv')?>";
    $("#filter-form").attr('target', '_blank').submit();
    location.reload();
}

function getSecond(){
    removeOptions(document.getElementById("query_second"));
    var series = document.getElementById('query_type').value;

    if(series == ''){
        return false;
    }

    var link = "<?=$link_get_second_category;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);

            if (result.length != 0) {
                var second = document.getElementById('query_second');
                var option_name = '請選擇次類別代碼';
                var option_value = '';
                var new_option = new Option(option_name, option_value);
                second.options.add(new_option);
                for (var i = 0; i < result.length; i++) {
                    var option_name = result[i]['name'];
                    var option_value = result[i]['item_id'];
                    var new_option = new Option(option_name, option_value);
                    second.options.add(new_option);
                }
            }
        }
    });
}
</script>
