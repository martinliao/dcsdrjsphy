<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" target=_blank action="<?=base_url('planning/season_course_capture/export')?>" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">季別:</label>
                            <?php
                                echo form_dropdown('query_season', $choices['query_season'], $filter['query_season'], 'class="form-control"');
                            ?>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">月別:</label>
                            <?php
                                echo form_dropdown('query_month_start', $choices['query_month'], $filter['query_month_start'], 'class="form-control"');
                            ?>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">系列別:</label>
                            <?php
                                echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                            ?>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">次類別:</label>
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
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm" onclick="Check_input_validate();">列印</button>
                            <a href="<?=base_url('planning/season_course_capture')?>" class="btn btn-info btn-sm">返回</a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function Check_input_validate()
{
    if(document.getElementById("query_type").value == '')
    {
        alert('請選擇系列別');
        return false;
    }
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

</script>
