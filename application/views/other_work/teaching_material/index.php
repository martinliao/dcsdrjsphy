<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <div class="panel-body">
                <div class="col-xs-12">
                    <form id="filter-form" role="form" class="form-inline">
                        <div class="row">
                            <div class="col-xs-12" >
                                <label class="control-label">上課日期</label>
                                <div class="input-group" id="start_date">
                                    <input type="text" class="form-control datepicker" value="<?=$filter['start_date']?>" id="datepicker1" name="start_date">
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="control-label">至</label>
                                <div class="input-group" id="end_date">
                                    <input type="text" class="form-control datepicker" value="<?=$filter['end_date']?>" id="datepicker3" name="end_date">
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                </div>
                                <div class="input-group">
                                    <button class="btn btn-info btn-sm">查詢</button>
                                </div>
                                <div class="input-group">
                                    <a class="btn btn-info btn-sm" href="<?=$link_export_pdf_range?>" title="export_list">匯出授權列表</a>
                               
                                </div>
                                <div class="input-group">
                                <a class="btn btn-info btn-sm" href="<?=$link_export_authlist?>" title="export_pdf">匯出簽名檔</a>

                                </div>                                                                
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                            </div>
                        </div>
                    </form>
                    
                    <table class="table table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">序號</th>
                                <th class="text-center">上課日</th>
                                <th class="text-center">班期名稱</th>
                                <th class="text-center">期別</th>
                                <th class="text-center">講座名稱</th>
                                <th class="text-center" colspan="4">同意授權簽名</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($materials as $key => $material): ?>
                            <tr>
                                <td><?=$key+1?></td>
                                <td><?=$material->use_date?></td>
                                <td><?=$material->class_name?></td>
                                <td><?=$material->term?></td>
                                <td><?=$material->teacher_name?></td>
                                <td>
                                    <?php
                                        $name=explode("-",$material->class_name);
                                        if(count($name)>1){
                                            $class_name=implode("",$name);
                                            $material->class_name=$class_name;
                                        }
                                        $token = "{$material->year}-{$material->class_id}-{$material->term}-{$material->t_id}-{$material->class_name}";
                                        $token = DES::encode($token, 'DE4LKM');
                                    ?>
                                    
                                    <?php if($material->auth_id !== null):?>
                                        <button class="btn btn-info btn-sm disabled">簽名上傳</button>
                                        <a target="_blank" href="<?=base_url("other_work/teaching_material/pdf?year={$material->year}&class_no={$material->class_id}&term={$material->term}&teacher_id={$material->t_id}")?>" class="btn btn-info btn-sm">下載</a>
                                    <?php else:?>
                                        <a target="_blank" href="/base/api/signature.php?token=<?=$token?>" class="btn btn-info btn-sm">簽名上傳</a>
                                        <button class="btn btn-info btn-sm disabled">下載</button>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?=$this->pagination->create_links();?>
                <!-- <div class="row">
                    <div class="col-lg-4">
                       
                    </div>
                    <div class="col-lg-8 text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>                 -->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#datepicker3").datepicker();
        $('#datepicker4').click(function(){
            $("#datepicker3").focus();
        });
        $("#datepicker1").datepicker();
        $('#datepicker2').click(function(){
            $("#datepicker1").focus();
        });
    });
    /*
    function authlist()
    {
        $('#sstart_date').val($('#datepicker1').val());
        $('#eend_date').val($('#test1').val());
        window.open("/base/admin/other_work/teaching_material/authlist?start_date="+ $('#sstart_date')+"&end_date"+$('#eend_date'), "_blank");
    }

    function authpdf()
    {
        $('#sstart_date').val($('#datepicker1').val());
        $('#eend_date').val($('#test1').val());
        window.open("/base/admin/other_work/teaching_material/authlist?start_date="+ $('#sstart_date')+"&end_date"+$('#eend_date'), "_blank");
    }
    */
</script>