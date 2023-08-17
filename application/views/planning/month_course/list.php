<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline"  action="<?=$link_detail?>" target=_blank>
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                            <label class="control-label">系列別:</label>
                            <?php
                                echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                            ?>
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
                            <label class="control-label">月別起:</label>
                            <?php
                                echo form_dropdown('query_month_start', $choices['query_month'], $filter['query_month_start'], 'class="form-control" id="query_month_start"');
                            ?>
                            <label class="control-label">迄:</label>
                            <?php
                                echo form_dropdown('query_month_end', $choices['query_month'], $filter['query_month_end'], 'class="form-control id="query_month_end"');
                            ?>
                            <label class="control-label">報名狀態:</label>
                            <select name='status' id='status' class='DropDownList_120'>
							    <option value=''>請選擇狀態</option>
							    <option value='1'>已報名（首次報名）</option>
							    <option value='2'>已報名（二次報名）</option>
							    <option value='1and2'>已報名（含首次和二次）</option>
							    <option value='0'>未報名</option>
						</select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm" >確認</button>
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