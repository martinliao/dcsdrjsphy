<style>
    .min-w{
        min-width:150px;
    }

  

    .button_click {
        background-color: #666666;
        color: white;
    }
</style>

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
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label min-w" >年度</i></label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>   
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label min-w">班期代碼</i></label>
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no']?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label min-w">班期名稱</i></label>
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name']?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label min-w">開班起始月份</i></label>
                                <?php
                                    echo form_dropdown('query_month', $choices['query_month'], $filter['query_month'], 'class="form-control"');
                                ?>                             
                            </div>
                        </div>                        
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label min-w">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div style="margin-top: 5px;">
                                <button class="btn btn-info <?php if($filter['query_type']==1){?> button_click <?php } ?>" name="query_type" value="1" style="min-width: 400px;">查詢(已發調訓通知且有錄取人員之班期)</button>
                                <span style="color:red">※辦理學員異動請點選第1個按鍵作查詢</span>
                            </div>
                            <div style="margin-top: 5px;">
                                <button class="btn btn-info <?php if($filter['query_type']==2){?> button_click <?php } ?>" name="query_type" value="2" style="min-width: 400px;">查詢(已發調訓通知之所有班期)</button>
                            </div>
                            <div style="margin-top: 5px; width">
                                <button class="btn btn-info <?php if($filter['query_type']==3){?> button_click <?php } ?>" name="query_type" value="3" style="min-width: 400px;">查詢(已報名尚未開辦之班期)</button>
                            </div>
                            <div style="margin-top: 5px;">
                                <button class="btn btn-info <?php if($filter['query_type']==4){?> button_click <?php } ?>" name="query_type" value="4" style="min-width: 400px;">查詢(已報名但取消開班之班期)</button>
                            </div>    
                            <div style="margin-top: 5px;">
                                <a style="color:red;text-decoration:underline;" href="https://dcsd.gov.taipei/News_Content.aspx?n=A87166D7FD0AAE7C&sms=64E43555801A6402&s=F75B9E71041B1331" target="_blank" style="min-width: 400px;">公訓處表單下載區</a>
                            </div>                                                                                
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <th class="text-center" style="background:#8CBBFF;width:15%">編號</th>
                            <th class="text-center" style="background:#8CBBFF;width:15%">班期名稱</th>
                            <!--<th class="text-center" style="background:#8CBBFF">機關</th>-->
                            <th class="text-center" style="background:#8CBBFF">期別</th>
                            <th class="text-center" style="background:#8CBBFF;width:15%">開班起訖</th>
                            <th class="text-center" style="background:#8CBBFF;width:5%">錄取名額/未選員名單</th>
                            <?php if($filter['query_type'] != 3): ?>
                                <?php if($filter['query_type'] == 1): ?>
                                <th class="text-center" style="background:#8CBBFF">學員異動作業</th>
                                <th class="text-center" style="background:#8CBBFF;width:10%">異動截止日期</th>
                                <?php endif ?>
                                <th class="text-center" style="background:#8CBBFF;width:5%">取消參訓清單</th>
                                <th class="text-center" style="background:#8CBBFF;width:10%">列印課程表及<br>研習人員名冊</th>
                                <th class="text-center" style="background:#8CBBFF;width:5%">轉寄mail</th>
                                <th class="text-center" style="background:#8CBBFF">附件</th>
                                <?php if($filter['query_type'] != 4): ?>
                                    <th class="text-center" style="background:#8CBBFF;width:13%">異動及研習紀錄</th>
                                <?php endif ?>
                            <?php endif ?>
                            <th class="text-center" style="background:#8CBBFF">備註</th>
                        </thead>
                        <tbody>
                            <?php foreach($requires as $key => $require): ?>
                                <?php 
                                    if (!empty($require->start_date1)){
                                        $require->start_date1 = new DateTime($require->start_date1);
                                        $require->start_date1 = $require->start_date1->format("Y-m-d");
                                    }
                                    if (!empty($require->end_date1)){    
                                        $require->end_date1 = new DateTime($require->end_date1);
                                        $require->end_date1 = $require->end_date1->format("Y-m-d");
                                    }
                                ?>
                                <tr class="text-center">
                                    <td><?=$key+1?></td>
                                    <td><?=$require->class_name?></td>
                                    <!--<td></td>-->
                                    <td><?=$require->term?></td>
                                    <td><?=$require->start_date1.'/'.$require->end_date1?></td>
                                    <td>
                                        <?=$require->gcount?>
                                        /
                                        <?php if($require->nocount > 0): ?>
                                            <a onclick='go_no_record("<?=$require->year?>","<?=$require->class_no?>","<?=$require->term?>")'><?=$require->nocount?></a>
                                        <?php else: ?>
                                            <?=$require->nocount?>
                                        <?php endif ?>
                                    </td>
                                    <?php if($filter['query_type'] != 3): ?>
                                        <?php if($filter['query_type'] == 1): ?>
                                            <td>

                                            <?php if(!empty($require->updateday.' '.$require->updateday2) && $require->sd_modify == 1): ?>
                                                <?php if ($require->updateday.' '.$require->updateday2 > $now): ?>
                                                <a href="<?=base_url("management/vm_transaction/bureaus?year={$require->year}&term={$require->term}&class_no={$require->class_no}")?>" onlick="">異動作業</a>
                                                <?php endif ?>
                                            <?php endif ?>
                                            </td>
                                            <td><?=$require->sd_edate?></td>
                                        <?php endif ?>
                                        <td>
                                            <?php if($require->ccount != 0 ): ?>
                                            <a onclick='go_cancel_list("<?=$require->year?>","<?=$require->class_no?>","<?=$require->term?>")'>取消參訓清單</a>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <a onclick='go_schedule_undertake_update("<?=$require->seq_no?>")'>課程表</a>&nbsp;
                                            <a onclick='go_schedule_Register_update("<?=$require->year?>","<?=$require->class_no?>","<?=$require->term?>","<?=$uid?>","<?=$bureau_id?>")'>名冊</a>
                                        </td>
                                        <td><a onclick = "go_trun_email('<?=$require->year?>','<?=$require->class_no?>','<?=$require->term?>')">轉寄</a></td>
                                        <td>
                                            <?php 
                                                $CI = &get_instance();
                                                $class_info = [
                                                    'year' =>$require->year,
                                                    'class_no' =>$require->class_no,
                                                    'term' =>$require->term
                                                ];
                                                $files = $CI->require_model->getRequireFile($class_info);
                                                foreach($files as $file){
                                                    $file_name = basename($file->file_path);
                                                    echo "<a>".htmlspecialchars($file_name, ENT_HTML5|ENT_QUOTES)."</a><br>";
                                                }
                                            ?>
                                        </td>
                                        <?php if($filter['query_type'] != 4): ?>
                                        <td>
                                            <a onclick="go_change_record('<?=$require->year?>','<?=$require->class_no?>','<?=$require->term?>')">異動紀錄</a>&nbsp;
                                            <?php if ($require->isend == "Y"): ?>
                                            <a onclick="go_vacation('<?=$require->year?>','<?=$require->class_no?>','<?=$require->term?>')" href="#">研習紀錄</a>
                                            <?php endif ?>
                                        </td>
                                        <?php endif ?>
                                    <?php endif ?>
                                    <td style="width:15%">
                                        <?php if($require->is_cancel == 1): ?>
                                            <font color="red"><strong>(本班已取消開班)<strong></font>
                                        <?php endif ?>

                                        <?php if($require->is_assess == 1): ?>
                                            <?php if($require->is_mixed == 1): ?>
                                            考核+混成班期
                                            <?php elseif($require->is_mixed == 0) :?>
                                            考核班期
                                            <?php else: ?>
                                            無
                                            <?php endif ?>
                                        <?php endif ?>                                                                                
                                    </td> 
                                </tr>
                            <?php endforeach ?>                           
                        </tbody>
                    </table>
                </form>
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
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


function go_schedule_Register_update(strYEAR,strCLASS_NO,strTERM,uid,bureau_id){
    var myW=window.open ('../student_list_pdf.php?uid='+uid+'&year='+strYEAR+'&class_no='+strCLASS_NO+'&term='+strTERM+'&bureau_id='+bureau_id+'&ShowRetirement=1', '1', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function go_schedule_undertake_update(seq_no){
    var myW=window.open ('<?=base_url("create_class/print_schedule/print/")?>' + seq_no + '#', '2', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function go_cancel_list(year, class_no, term){
    var url = "<?=base_url("customer_service/class_info_ha/cancel_list")?>?year=" + year + "&class_no=" + class_no + "&term=" + term;
    var myW=window.open (url, 'cancel_list', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function go_no_record(year, class_no, term){
    var url = "<?=base_url("customer_service/class_info_ha/student_no_record")?>?year=" + year + "&class_no=" + class_no + "&term=" + term;
    var myW=window.open (url, 'student_no_record', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function go_change_record(year, class_no, term){
    var url = "<?=base_url("management/signup_change_report/detail")?>?year=" + year + "&class_no=" + class_no + "&term=" + term + "#";
    url = "<?=base_url("management/signup_change_report/detail")?>?year=" + year + "&class_no=" + class_no + "&term=" + term +  "#";
    var myW=window.open (url, 'change_record', 'height=768, width=1400, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();    
}

function go_vacation(year, class_no, term){
    var url = "<?=base_url("management/print_learn_list/print")?>?year=" + year + "&class_no=" + class_no + "&term=" + term + "&pdf=1#";
    var myW=window.open (url, 'vacation', 'height=768, width=1400, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();    

}

function go_trun_email(year, class_no, term){
    var url = "<?=base_url("create_class/progress/select_student_turn")?>?year=" + year + "&class_no=" + class_no + "&term=" + term + "#";
    var myW=window.open (url, 'vacation', 'height=768, width=1400, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();    

}

</script>