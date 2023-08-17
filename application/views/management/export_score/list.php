<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('year', $choices['query_year'], $filter['year'], 'class="form-control" id="year_before"');
                                ?>
                            </div>

                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input tpye="text" name="class_no" id="class_no" value="<?=$filter['class_no'];?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input tpye="text" name="class_name" id="class_name" value="<?=$filter['class_name'];?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <button class="btn btn-info btn-sm">查詢</button>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                            ?>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <hr>
                <form id="list-form" method="post" class="form-inline">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom: 8px;">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('output_year', $choices['query_year'], '', 'class="form-control" id="output_year"');
                                ?>
                            </div>

                            <div class="form-group">
                                <label class="control-label">月份:</label>
                                <?php
                                    echo form_dropdown('output_month', $choices['query_month'], '', 'class="form-control" id="output_month"');
                                ?>
                            </div>

                            <input type="button" name='btnExport' id="btnExport" value="整批索引匯出" onclick="export_csv_all()" class='button' />
                            <input type="button" name='btn_load' id="btn_load" value="下載整批檔案" onclick="load_file()" class='button' />
                            <input type="button" name='btnEnv' id="btnEnv" value="全國教師下載整批檔案" onclick="export_env()" class='button' />
                        </div>
                    </div>
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr bgcolor="#8CBBFF">
                                <th class="text-center">產出</th>
                                <th class="text-center">年度</th>
                                <th class="text-center">期別</th>
                                <th class="text-center">班期代碼</th>
                                <th class="text-center">班期名稱</th>
                                <th class="text-center">目錄</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($list as $row) { ?>
                            <tr>
                                <td>
                                    <?php if($row['isend'] == 'Y'){ ?>
                                    <input type="button" name='btnSearch' id="btnSearch" value="終身學習時數檔" onclick="export_csv('<?=$row['year'];?>','<?=$row['term'];?>','<?=$row['class_no'];?>','1')" class='button' />
                                    <input type="button" name='btnEnvSearch' id="btnEnvSearch" value="環教時數檔" onclick="export_csv('<?=$row['year'];?>','<?=$row['term'];?>','<?=$row['class_no'];?>','2')" class='button' />
                                    <?php } ?>
                                </td>
                                <td><?=$row['year'];?></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['class_no'];?></td>
                                <td><?=$row['class_name'];?></td>
                                <td><input type="button" name='btnSearch' id="btnSearch" value="進入目錄" onclick="into_folder('<?=$row['into_folder'];?>')" /></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </form>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8  text-right">
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

    function export_csv(year,term,class_no, kind){ // custom by chiahua 加上查詢的條件
        query_year = document.getElementById("output_year").value;
        query_class_no = document.getElementById("class_no").value;
        query_class_name = document.getElementById("class_name").value;

        var url = '<?=base_url('management/export_score/ajax/export_scorecsv_output');?>';
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'year': year,
            'term': term,
            'class_no': class_no,
            'query_year': query_year,
            'query_class_no': query_class_no,
            'query_class_name': query_class_name,
            'kind': kind,
        }
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                             //console.log(response);
                            alert(response.msg);
                        } else {
                            alert(response.msg);
                        }
                    }

        });
    }

    function export_csv_all(){
        if((document.getElementById("output_year").value=='')||(document.getElementById("output_month").value=='')){
            alert('請選匯出年月!');
            return;
        }
        query_year=document.getElementById("output_year").value;
        query_month=document.getElementById("output_month").value;
        var url = '<?=base_url('management/export_score/ajax/export_csv_all');?>';
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'year': query_year,
            'month': query_month,
        }
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: 'json',
            success: function(response){
                        if (response.status) {
                            // console.log(response.person);
                            alert(response.msg);
                        } else {
                            alert(response.msg);
                        }
                    }

        });
    }

    function load_file(){
         var myW=window.open('<?=$link_load_all;?>','open','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=500,width=972');
         myW.focus();
    }


    function export_env(){
        document.getElementById("list-form").action = "<?=$link_zip;?>";
        if(document.getElementById("output_month").value=="") {
            alert("請選擇月份");
            return false;
        }
        else {
            document.getElementById("list-form").submit();
        }
    }

    function into_folder(url){
        document.location = (url);
    }

</script>
