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
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度：</label>
                                <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control" id="query_year"');
                                ?>
                                <label class="control-label" style="margin-left: 15px">季別：</label>
                                <?php
                                echo form_dropdown('query_season', $choices['query_season'], $filter['query_season'], 'class="form-control"');
                                ?>
                                <label class="control-label" style="margin-left: 15px">月別：</label>
                                <?php
                                    echo form_dropdown('query_month_start', $choices['query_month'], $filter['query_month_start'], 'class="form-control"');
                                ?>
                                <div class="form-group form-inline">
                                    <label class="control-label" style="margin-left: 15px">計畫：</label>
                                    <?php
                                        echo form_dropdown('class_status_search', $choices['class_status_search'],$filter['class_status_search'] ,'class="form-control" id="class_status_search"');
                                    ?>
                                </div>
                                <label class="control-label" style="margin-left: 15px">系列別代碼：</label>
                                <?php
                                echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                                ?>
                                <label class="control-label" style="margin-left: 15px">次類別代碼：</label>
                                <select class="form-control" name='query_second' id='query_second'>
                                    <option value="">請選擇次類別</option>
                                    <?php if(isset($choices['query_second']) && !empty($choices['query_second'])){
                                    for($i=0;$i<count($choices['query_second']);$i++){
                                    if($choices['query_second'][$i]['item_id'] == $filter['query_second']){
                                        echo '<option value="'.$choices['query_second'][$i]['item_id'].'" selected>'.$choices['query_second'][$i]['name'].'</option>';
                                    } else {
                                        echo '<option value="'.$choices['query_second'][$i]['item_id'].'">'.$choices['query_second'][$i]['name'].'</option>';
                                            }
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                                <label class="control-label">計畫</label>
                                <?php
                                    echo form_dropdown('class_status', $choices['class_status'], '', 'class="form-control" id="class_status"');
                                ?>
                                <a class="btn btn-warning btn-sm" onclick="confirmFun()" title="confirm">確定</a>
                                <a class="btn btn-warning btn-sm" onclick="setupBaseTerm()" title="confirm">設定初始期數</a>
                            </div>
                            <font style="color: red">(點選後全部班期初始期數即設定完成)</font>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="plan_status" id="plan_status" value="" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center"><input type="checkbox" id="chkall" style="zoom:1.5"></th>
                                <th  style="width:5%;" class="sorting<?=($filter['sort']=='year asc')?'_asc':'';?><?=($filter['sort']=='year desc')?'_desc':'';?>"
                                    data-field="year">年度</th>
                                <th  style="width:10%;" class="sorting<?=($filter['sort']=='series_name asc')?'_asc':'';?><?=($filter['sort']=='series_name desc')?'_desc':'';?>"
                                    data-field="series_name">主類別</th>
                                <th  style="width: 15%;" class="sorting<?=($filter['sort']=='second_name asc')?'_asc':'';?><?=($filter['sort']=='second_name desc')?'_desc':'';?>"
                                    data-field="second_name">次類別</th>
                                <th  style="width: 35%;" class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>"
                                    data-field="class_name">班期名稱</th>
                                <th  style="width: 10%;" class="sorting<?=($filter['sort']=='base_term asc')?'_asc':'';?><?=($filter['sort']=='base_term desc')?'_desc':'';?>"
                                    data-field="base_term">初始期數</th>
                                <th  style="width: 10%;" class="sorting<?=($filter['sort']=='class_property asc')?'_asc':'';?><?=($filter['sort']=='class_property desc')?'_desc':'';?>"
                                    data-field="class_property">班期性質</th>
                                <!-- <th>建立時間</th> -->
                                <th  style="width: 10%;" class="sorting<?=($filter['sort']=='class_status asc')?'_asc':'';?><?=($filter['sort']=='class_status desc')?'_desc':'';?>"
                                    data-field="class_status">計畫</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" style="zoom:1.5" name="rowid[]"
                                        value="<?=$row['year'].','.$row['class_no'];?>"></td>
                                <td><?=$row['year'];?></td>
                                <td><?=$row['series_name'];?></td>
                                <td><?=$row['second_name'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['base_term'];?></td>
                                <?php if($row['is_assess'] == '1' && $row['is_mixed'] == '1'){ ?>
                                <td>混成</td>
                                <?php } else if($row['is_assess'] == '1'){ ?>
                                <td>考核</td>
                                <?php } else if($row['is_mixed'] == '1'){ ?>
                                <td>混成</td>
                                <?php } else { ?>
                                <td></td>
                                <?php } ?>
                                <?php if($row['class_status'] == '1'){ ?>
                                <td>草案</td>
                                <?php } else if($row['class_status'] == '2'){ ?>
                                <td>確定計畫</td>
                                <?php } else if($row['class_status'] == '3'){ ?>
                                <td>新增計畫</td>
                                <?php } else { ?>
                                <td></td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
/*$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});*/

function confirmFun(){
    var obj = document.getElementById('list-form');
    var plan_status = document.getElementById('class_status').value;
    document.getElementById('plan_status').value = plan_status;

    if(plan_status == ''){
        alert('請選擇計畫');
        return false;
    }

    obj.submit();
}

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

function setupBaseTerm(){
    var year = document.getElementById('query_year').value;
    var msg = '確認設定'+year+'年的初始期數?'

    if(confirm(msg)){
        var link = "<?=$link_set_base_term;?>";
  
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'year': year
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
                if (response == 'OK') {
                    alert('設定成功');
                    location.reload();
                } else if(response == 'EXIST'){
                    alert('設定失敗，因該年度已設定過');
                } else {
                    alert(response);
                }
            }
        });
    }
}

</script>