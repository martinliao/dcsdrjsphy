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
                            <label class="control-label">年度</label>
                            <?php
                                echo form_dropdown('query_year', ['' => '全部'] + $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                            <label class="control-label">季別</label>
                            <?php
                                echo form_dropdown('query_season', $choices['query_season'], $filter['query_season'], 'class="form-control"');
                            ?>
                            <label class="control-label">月份</label>
                            <?php
                                echo form_dropdown('query_month_start', $choices['query_month'], $filter['query_month_start'], 'class="form-control"');
                            ?>
                            <label class="control-label">系列別代碼</label>
                            <?php
                                echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                            ?>
                            <label class="control-label">次類別代碼</label>
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
                        <div class="col-xs-12">
                            <label class="control-label">班期代碼</label>
                            <input type="text" class="form-control" name="query_class_no"
                                value="<?=$filter['query_class_no'];?>">
                            <label class="control-label">班期名稱</label>
                            <input type="text" class="form-control" name="query_class_name"
                                value="<?=$filter['query_class_name'];?>">
                            <button class="btn btn-info btn-sm">查詢</button>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                                <span style="color:red">取消班期：原確定計畫取消辦理之班期(尚未招生)</span>

                            </div>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="plan_status" id="plan_status" value="" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <th style="width: 10%;"  class="sorting<?=($filter['sort']=='series_name asc')?'_asc':'';?><?=($filter['sort']=='series_name desc')?'_desc':'';?>"
                                    data-field="series_name">主類別</th>
                                <th style="width: 10%;"  class="sorting<?=($filter['sort']=='second_name asc')?'_asc':'';?><?=($filter['sort']=='second_name desc')?'_desc':'';?>"
                                    data-field="second_name">次類別</th>
                                <?php } ?>
                                <th style="width: 10%;"  class="sorting<?=($filter['sort']=='year asc')?'_asc':'';?><?=($filter['sort']=='year desc')?'_desc':'';?>"
                                    data-field="year">年度</th>
                                <th style="width: 30%;"  class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>"
                                    data-field="class_name">班期名稱</th>
                                <th style="width: 10%;"  class="sorting<?=($filter['sort']=='base_term asc')?'_asc':'';?><?=($filter['sort']=='base_term desc')?'_desc':'';?>"
                                    data-field="base_term">初始期數</th>
                                <th style="width: 10%;"  class="sorting<?=($filter['sort']=='total_terms asc')?'_asc':'';?><?=($filter['sort']=='total_terms desc')?'_desc':'';?>"
                                    data-field="total_terms">期數</th>
                                <th style="width: 10%;">增期</th>
                                <th style="width: 10%;">減期</th>
                                <th style="width: 10%;">取消班期</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $row) { ?>
                            <tr>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <td><?=$row['series_name'];?></td>
                                <td><?=$row['second_name'];?></td>
                                <?php } ?>
                                <td><?=$row['year'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><?=$row['base_term'];?></td>
                                <td><?=$row['5a_num'];?></td>
                                <td class="text-center" id="btn_group">
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle"
                                        href="<?=$row['link_add'];?>">
                                        <i class="fa fa-lg">增期</i>
                                    </a>
                                </td>
                                <td class="text-center" id="btn_group">
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle"
                                        href="<?=$row['link_del'];?>">
                                        <i class="fa fa-lg">減期</i>
                                    </a>
                                </td>
                                <td class="text-center" id="btn_group">
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle"
                                        href="<?=$row['link_cancel_class'];?>">
                                        <i class="fa fa-lg">取消班期</i>
                                    </a>
                                </td>
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
// $(document).ready(function() {
//     $('#rows select').change(function(){
//         $('#filter-form').submit();
//     });

//     // <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
//     // $('#list-form').highlight('<?=$filter['q'];?>');
//     // <?php } ?>
// });


function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

function confirmFun(){
    var obj = document.getElementById('list-form');
    var plan_status = document.getElementById('class_status').value;
    document.getElementById('plan_status').value = plan_status;

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

</script>