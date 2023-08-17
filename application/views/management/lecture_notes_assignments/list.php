<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            
            <div class="panel-body">
                <?php if ($filter['query_search_from'] != "2B"): ?>
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('year', $choices['year'], $filter['year'], 'class="form-control" id="penYear"');
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
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">講師:</label>
                                <input type="text" class="form-control" id="query_teacher" name="query_teacher" value="<?=$filter['query_teacher'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">課程名稱:</label>
                                <input type="text" class="form-control" id="query_course_name" name="query_course_name" value="<?=$filter['query_course_name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">講義名稱:</label>
                                <input type="text" class="form-control" id="query_file_title" name="query_file_title" value="<?=$filter['query_file_title'];?>">
                            </div>
                        </div>
                        <div class=col-xs-12>
                            <div class="form-group">
                                <label class="control-label">季別:</label>
                                <?php
                                    echo form_dropdown('argSeason', $choices['argSeason'], $filter['argSeason'], 'class="form-control" id="argSeason"');
                                ?>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" id="allQuery" name="allQuery" value="allQuery" <?=set_checkbox("allQuery", "allQuery", $filter['allQuery']=='allQuery');?> class="form-group"><label class="control-label">查詢所有講義上傳</label>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" id="allClassesQuery" name="allClassesQuery" value="allClassesQuery" <?=set_checkbox("allClassesQuery", "allClassesQuery", $filter['allClassesQuery']=='allClassesQuery');?> class="form-group"><label class="control-label">查詢所有班期</label>
                            </div>
                            <a class="btn btn-info btn-sm" onclick="sendSearch()" >查詢</a>
                            <a class="btn btn-info btn-sm" onclick="printReport()">一覽表</a>
                        </div>
                        <div class=col-xs-12>
                            <font style="font-weight: bold;">※以班期代碼查詢：一年僅一期依課程名稱排序；一年多期則依老師、課程名稱排序</font>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>

               


                <?php endif ?>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#8CBBFF">
                            <th class="text-center">編輯</th>
                            <th class="text-center">無講義</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">列印</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">下載期別</th>
                            <th class="text-center">課程名稱</th>
                            <th class="text-center">講師</th>
                            <th class="text-center">講義名稱</th>
                            <th class="text-center">實體檔案下載</th>
                            <th class="text-center">同課程同意授權否</th>
                            <th class="text-center">上傳時間</th>
                        </tr>
                        <form id="data-form" role="form" method="post" >
                            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <input type="hidden" name="year" id="year" value=''>
                            <input type="hidden" name="class_no" id="class_no" value=''>
                            <input type="hidden" name="class_name" id="class_name" value=''>
                            <input type="hidden" name="course_code" id="course_code" value=''>
                            <input type="hidden" name="id" id="id" value=''>
                        </form>
                        <form id="download-form" role="form" method="post" >
                            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <input type="hidden" name="path" id="path" value="">
                        </form>
                    </thead>
                    <tbody>
                        <?php foreach( $list as $row ){ ?>
                        <tr>
                            <td class="text-center">
                                <a class="btn btn-default btn-sm" onClick='into_model(<?=$row['year'];?>,"<?=$row['class_no'];?>","<?=$row['class_name'];?>","<?=$row['course_code'];?>","<?=$row['teacher_id'];?>");'>編輯</a>
                            </td>
                            <?php if($row['status'] == '1'){ ?>
                            <td align="center"><input type="checkbox" name="enable" onclick='enableFun(this,<?=$row['year'];?>,"<?=$row['class_no'];?>","<?=$row['course_code'];?>","<?=$row['teacher_id'];?>")' checked/>設定</td>
                            <?php }else{ ?>
                            <td align="center"><input type="checkbox" name="enable" onclick='enableFun(this,<?=$row['year'];?>,"<?=$row['class_no'];?>","<?=$row['course_code'];?>","<?=$row['teacher_id'];?>")'/>設定</td>
                            <?php } ?>
                            <td><?=$row['year'];?></td>
                            <td>
                                <select>
                                    <option value="-1">請選擇</option>
                                    <option value="1">第1期</option>
                                    <option value="2">第2期</option>
                                    <option value="3">第3期</option>
                                    <option value="4">第4期</option>
                                    <option value="5">第5期</option>
                                    <option value="6">第6期</option>
                                    <option value="7">第7期</option>
                                    <option value="8">第8期</option>
                                    <option value="9">第9期</option>
                                    <option value="10">第10期</option>
                                    <option value="11">第11期</option>
                                    <option value="12">第12期</option>
                                </select>
                                <input type="button" name="" class='button' onclick="prtConsent(this)" value="授權書列印" />
                                <input type="hidden" value="v1=<?=$row['year'];?>&v3=<?=$row['class_name'];?>&v4=<?=$row['c_name'];?>" />
                            </td>
                            <td><?=$row['class_name'];?></td>
                            <td><?=$row['set_to_terms'];?></td>
                            <td><?=$row['c_name'];?></td>
                            <td><?=$row['name'];?></td>
                            <td><?=$row['file_name'];?></td>
                            <td><?=$row['download_name'];?></td>
                            <td><?=$row['is_authorize'];?></td>
                            <td><?=$row['cre_time_stamp'];?></td>
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
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
function printReport() {
    obj = document.getElementById("filter-form");
    obj.action = "<?=$exportcsv;?>"
    obj.submit();
}

function sendSearch() {
    obj = document.getElementById("filter-form");
    obj.action = "<?=$link_refresh;?>"
    obj.submit();
}

function prtConsent(obj) {
    if(obj.parentNode.getElementsByTagName('select')[0].value==-1) {
        alert("請選擇期別");
        return false;
    }
    else {
        var gtVal = obj.parentNode.getElementsByTagName('input')[1].value+"&v2="+obj.parentNode.getElementsByTagName('select')[0].value;
        window.open("<?=$dl_consent;?>?"+gtVal, 'winPrint');
    }
}

function into_model(year,class_no,class_name,course_code,id) {
    obj=document.getElementById("data-form");
    document.getElementById("year").value=year;
    document.getElementById("class_no").value=class_no;
    document.getElementById("class_name").value=class_name;
    document.getElementById("course_code").value=course_code;
    document.getElementById("id").value=id;
    obj.action='<?=$filter['detail_link'];?>';

    obj.submit();
    //document.location = ('Lecture_Notes_Assignments_l2.php?year='+year+'&class_no='+class_no+'&term='+term+'&class_name='+class_name+'&course_code='+course_code+'&id='+id);
}

function go_download(url){
    obj =document.getElementById('download-form');
    obj.path.value = url;
    obj.action='<?=base_url('management/lecture_notes_assignments/download')?>';
    obj.submit();
}

function enableFun(obj,year,class_no,id,teacher_id){
        var status;
        if(obj.checked){
            status = '1';
        } else {
            status = '0';
        }

        var url = '<?=base_url('management/lecture_notes_assignments/ajax/enableFun');?>';

        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'year': year,
            'class_no': class_no,
            'id': id,
            'teacher_id': teacher_id,
            'status': status,
        }

        $.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            alert('設定成功');
                            location.reload();
                        } else {
                            alert('設定失敗');
                        }
                    }
        });

    }
</script>