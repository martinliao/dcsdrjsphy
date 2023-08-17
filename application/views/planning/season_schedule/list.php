<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline" action="">
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">年度</label>
                            <?php
                                echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                            ?>
                            <label class="control-label">起始月份:</label>
                            <?php
                                echo form_dropdown('query_month_start', $choices['query_month'], $filter['query_month_start'], 'class="form-control"');
                            ?>
                            <label class="control-label">結束月份:</label>
                            <?php
                                echo form_dropdown('query_month_end', $choices['query_month'], $filter['query_month_end'], 'class="form-control"');
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                        <label class="control-label">季別:</label>
                        <?php
                            echo form_dropdown('query_season', $choices['query_season'], $filter['query_season'], 'class="form-control"');
                        ?>
                            <label class="control-label">系列別:</label>
                            <?php
                                echo form_dropdown('query_type', $choices['query_type'], $filter['query_type'], 'class="form-control" id="query_type" onchange="getSecond()"');
                            ?>                            
                            <label class="control-label">次類別:</label>
                            <select class="form-control" name='query_second' id='query_second'>
                                    <option value="">請選擇次類別</option>
                                    <?php
                                        if(isset($choices['query_second']) && !empty($choices['query_second'])){
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
                            <button class="btn btn-info btn-sm" onclick="selectAction(1)">查詢</button>
                            <button class="btn btn-info btn-sm" onclick="selectAction(2)">匯出</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">系列別</th>
                                    <th class="text-center">次類別</th>
                                    <th class="text-center">班期名稱</th>
                                    <th class="text-center">班期性質</th>
                                    <th class="text-center">開班起迄日期</th>
                                    <th class="text-center">期別</th>
                                    <th class="text-center">時數</th>
                                    <th class="text-center">承辦人</th>
                                    <th class="text-center">電話</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($list as $row) {?>
                                <tr class="text-center">
                                    <td dt="c1"><?=$row['type_name']?></td>
                                    <td dt="c2"><?=$row['bureau_name']?></td>
                                    <td><?=$row['class_name']?></td>
                                    <?php   $course='';
                                            if($row['is_assess']==1 && $row['is_mixed']==1){
                                                $course='混成';
                                            }
                                            else{
                                                $course='考核';
                                            }
                                    ?>
                                    <td><?=$course?></td>
                                    <td><?=substr($row['start_date1'],0,10).'~'.substr($row['end_date1'],0,10)?></td>
                                    <td><?=$row['term']?></td>
                                    <td><?=$row['range']?></td>
                                    <td><?=$row['bu_name']?></td>
                                    <td><?=$row['tel']?></td>
                                </tr>
                                <?php }?>
                            </tbody>
                          
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
<script>


function selectAction($number){
    if($number==1){
        document.getElementById("filter-form").action = "<?=$link_detail;?>";
        document.filter-form.submit();
        
    }
    if($number==2){
        document.getElementById("filter-form").action = "<?=$link_export;?>";
        document.filter-form.submit();
    }
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

jQuery.fn.rowspan = function(){ 
    var i=0; 
    var pText=''; 
    var sObj;   //預計進行RowSpan物件 
    var rcnt=0; //計算rowspan的數字 
    var tlen=this.length; 
    return this.each(function(){ 
        i=i+1; 
        rcnt=rcnt+1; 
	        //與前項不同 
	        if(pText!=$(this).text()) 
	        { 
	            if(i!=1) 
	            { 
	                //不是剛開始，進行rowspan 
	                sObj.attr('rowspan',rcnt-1); 
	                rcnt=1; 
	            } 
	            //設定要rowspan的物件 
	            sObj=$(this); 
	            pText=$(this).text(); 
	        } 
	        else 
	        { 
	            $(this).hide(); 
	        } 
	             
	        if(i==tlen) 
	        { 
	            sObj.attr('rowspan',rcnt); 
	        } 
	    }); 
	} 

$('td[dt="c1"]').rowspan();
//$('td[dt="c2"]').rowspan();  
</script>