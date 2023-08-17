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
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="query_class_no" id="query_class_no" value="<?=$filter['query_class_no'];?>">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="query_class_name" id="query_class_name" value="<?=$filter['query_class_name'];?>">
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
                                <button class="btn btn-info">查詢</button>
                                <a id="clear" class="btn btn-warning" onclick="clearFun()">清除</a>
                            </div>
                        </div>
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-6" >
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"  onchange="sendFun()"');
                            ?>
                        </div>
                        
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 15%;" class="sorting<?=($filter['sort']=='series_name asc')?'_asc':'';?><?=($filter['sort']=='series_name desc')?'_desc':'';?>" data-field="series_name" >系列別</th>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <th style="width: 15%;" class="sorting<?=($filter['sort']=='second_name asc')?'_asc':'';?><?=($filter['sort']=='second_name desc')?'_desc':'';?>" data-field="second_name" >次類別名稱</th>
                                <?php } else { ?>
                                <th style="width: 15%;" class="sorting<?=($filter['sort']=='dev_type_name asc')?'_asc':'';?><?=($filter['sort']=='dev_type_name desc')?'_desc':'';?>" data-field="dev_type_name" >局處名稱</th>
                                <?php } ?>
                                <th style="width: 15%;" class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                                <th style="width: 35%;" class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                                <th style="width: 10%;">選取轉入</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$row['series_name'];?></td>
                                <?php if($user_bureau == '379680000A'){ ?>
                                <td><?=$row['second_name'];?></td>
                                <?php } else { ?>
                                <td><?=$row['dev_type_name'];?></td>
                                <?php } ?>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['current_exist'])) { ?>
                                    <a type="button" class="btn btn-warning btn-xs btn-toggle" style="background-color: #DCDCDC" disabled>
                                        <i class="fa fa-pencil fa-lg"><?=$current_year;?>年度</i>
                                    </a>
                                    <?php } else { ?>
                                    <a type="button" class="btn btn-warning btn-xs btn-toggle" href="<?=$row['link_current'];?>">
                                        <i class="fa fa-pencil fa-lg"><?=$current_year;?>年度</i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['next_exist'])) { ?>
                                    <a type="button" class="btn btn-warning btn-xs btn-toggle" style="background-color: #DCDCDC" disabled>
                                        <i class="fa fa-pencil fa-lg"><?=$next_year?>年度</i>
                                    </a>
                                    <?php } else { ?>
                                    <a type="button" class="btn btn-warning btn-xs btn-toggle" href="<?=$row['link_next'];?>">
                                        <i class="fa fa-pencil fa-lg"><?=$next_year?>年度</i>
                                    </a>
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
/*$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});*/
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

function clearFun(){
    document.getElementById('query_class_no').value = '';
    document.getElementById('query_class_name').value = '';
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
