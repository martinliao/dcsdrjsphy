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
                                <label class="control-label" style="width: 82px;">年度:</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" name="class_no">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" name="class_name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label" style="width: 82px;">講師:</label>
                                <input type="text" class="form-control"  name="teacher">
                            </div>
                            <div class="form-group">
                                <label class="control-label">講義名稱:</label>
                                <input type="text" class="form-control" name="queryFile">
                            </div>
                            <div class="form-group">
                                <label class="control-label">課程名稱:</label>
                                <input type="text" class="form-control" name="course_name">
                            </div>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                            <button class="btn btn-info btn-sm">搜尋</button>
                        </div>
                    </div>
                </form>
                
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">課程名稱</th>
                            <th class="text-center">講師</th>
                            <th class="text-center">講義名稱</th>
                            <th class="text-center">實體檔案下載</th>
                            <th class="text-center">上傳時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($files as $file): ?>
                        <tr>
                            <td><?=$file->year?></td>
                            <td><?=$file->class_name?>
                            <td><?=$file->description?></td>

                            <td><?=$file->tname?></td>
                            <td>
                                <?php if (empty($file->files)): ?>
                                    無講義資料供下載
                                <?php endif ?>
                                <?php foreach($file->files as $f): ?>
                                    <?=$f->title?>
                                <?php endforeach ?>                                
                            </td>
                            <td>
                                <?php if (empty($file->files)): ?>
                                    無講義資料供下載
                                <?php endif ?>                            
                                <?php foreach($file->files as $f): ?>
                                    <?=$f->file_path?>
                                <?php endforeach ?>
                            </td>
                            <td>
                                <?php if (empty($file->files)): ?>
                                    無講義資料供下載
                                <?php endif ?>                            
                                <?php foreach($file->files as $f): ?>
                                    <?=$f->cre_time_stamp?>
                                <?php endforeach ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tobdy>
                </table>
               
                <form>
                    <div class="row ">
                        <div class="col-lg-4">
                            Showing 10 entries
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
<form id="download-form" role="form" method="post">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
	<input type="hidden" name="path" value="" />
</form>
<script>
    function go_download(url){
        obj = document.getElementById('download-form');
        obj.path.value = url;
        obj.action = '<?=base_url("management/lecture_notes_assignments/download")?>'
        obj.submit();
    }
</script>