<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover " >
                    <thead>
                        <tr class="text-center">
                            <td colspan="9">
                                年度：<?=$require->year?>&nbsp;&nbsp;&nbsp;&nbsp;班期名稱：<?=$require->class_name?>&nbsp;&nbsp;&nbsp;&nbsp;期別：<?=$require->term?>&nbsp;&nbsp;&nbsp;&nbsp;開課起始日：
                                <?=date_format(date_create($require->start_date1),'Y-m-d')?>~<?=date_format(date_create($require->end_date1),'Y-m-d')?>&nbsp;&nbsp;&nbsp;&nbsp;承辦人(分機)：<?=$require->name?>  (<?=$require->phone?>)
                            </td>
                        </tr>
                        <tr class="text-center">
                            <td colspan="9">異動截止日期：<?=$require->dupdate?>
                                &nbsp;&nbsp;實招人數：<?=$require->tcount?>人&nbsp;&nbsp;報名人數：<?=$require->pcount?>人&nbsp;&nbsp;人數額度限制：<?=$require->sd_cnt?>人&nbsp;&nbsp;
                                <?php 
                                    $exchange = ($require->sd_change == 1) ? 'color="red">互調:是' : '>互調:否';
                                    $modify = ($require->sd_another == 1) ? 'color="red">換員:是' : '>換員:否';
                                    $change_term = ($require->sd_chgterm == 1) ? 'color="red">換期:是' : '>換期:否';
                                    $cancel = ($require->sd_cancel == 1) ? 'color="red">取消:是' : '>取消:否';
                                ?>
                                <font <?=$exchange?></font>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <font <?=$modify?></font>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <font <?=$change_term?> </font>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <font <?=$cancel?> </font>
                            </td>
                        </tr>
                        <tr>
                            <th>學號</th>
                            <th>姓名</th>
                            <th>服務單位</th>
                            <th>職稱</th>
                            <th>報名</th>
                            <th>選員(調訓)</th>
                            <th>異動(互調、換期、取消參訓、換員)</th>
                            <th>報到</th>
                            <th>結訓</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stud_modifylogs as $log): ?>
                        <tr>
                            <td>
                            <?php if($log->yn_sel == '6' && !empty($log->select_stno)){ ?>
                                原:<?=$log->select_stno?>
                            <?php }else if($log->st_no == 999) {
                                echo "";
                            }else{?>
                                <?=$log->st_no?>
                            <?php } ?>
                            </td>
                            <td><?=$log->name?></td>
                            <td><?=$log->student_beaurau_name?></td>
                            <td><?=$log->title?></td>
                            <td style="background-color:#c1ffc1 ">
                                <?php if (($log->cancel_enrollment_date != "" && $log->enrollment_date <=$log->cancel_enrollment_date)): ?>
                                    <?php if ($log->cancel_enrollment_date != ""): ?>
                                        <font color='red'><?=$log->cancel_enrollment_bureau_name?><br>取消:<?=$log->cancel_enrollment_date?></font><br>
                                    <?php else: ?>
                                        <font color='red'><?=$log->cancel_class_bureau_name?><br>取消開班:<?=$log->cancel_class_date?></font><br>
                                    <?php endif ?>
                                <?php endif ?>

                                <?php if ($log->enrollment_date != "") :?>
                                    <?=$log->enrollment_bureau_name?>
                                    <br>報名:<?=$log->enrollment_date?><br>
                                <?php endif ?>    
                            </td>
                            <td style="background-color:#ffe1ff">
                                <?php if ($log->yn_sel != 2 && $log->select_date && ($log->cancel_enrollment_date == "" || $log->enrollment_date>$log->cancel_enrollment_date)): ?>
                                    <?php if($log->select_type == "批次換期"): ?>
                                        選員:<?=$log->select_date?> (自第<?=$log->select_from_term?>期批次換期)    
                                    <?php else: ?>
                                        選員:<?=$log->select_date?>
                                    <?php endif ?>
                                    <br>
                                    <?php if($log->training_date != ""): ?>
                                        調訓:<?=$log->training_date?>
                                    <?php endif ?>
                                <?php endif ?>
                            </td>
                            <td style="background-color:#E0EBFF">
                            <?php if ($log->cancel_enrollment_date == "" || $log->enrollment_date >$log->cancel_enrollment_date): ?>
                                
                                <?php if ($log->exchange_from_date != ""): ?>
                                    <?=$log->exchange_from_bureau_name?><br>
                                    互調(原為<?=$log->exchange_from_name?>):<?=$log->exchange_from_date?><br>
                                <?php endif ?>
                                
                                <?php if ($log->exchange_to_date != ""): ?>
                                    <?=$log->exchange_to_bureau_name?><br>
                                    互調(與第<?=$log->exchange_to_term?>期<?=$log->exchange_to_name?>互調):<?=$log->exchange_to_date?><br>
                                <?php endif ?>
                                
                                <?php if ($log->modify_term_to_date != ""): ?>
                                    <?=$log->modify_term_to_bureau_name?><br>
                                    換期(to第<?=$log->modify_term_to_term ?>期):<?=$log->modify_term_to_date?><br>
                                <?php endif ?>
                                
                                <?php if ($log->modify_term_from_date != ""): ?>
                                    <?=$log->modify_term_from_bureau_name?><br>
                                    換期(from第<?=$log->modify_term_from_term?>期):<?=$log->modify_term_from_date?><br>
                                <?php endif ?>
                                
                                <?php if ($log->delete_student_date != ""): ?>
                                    <font color="red">
                                    <?=$log->delete_student_bureau_name?><br>
                                    取消參訓:<?=$log->delete_student_date?>
                                    <?php if ($log->delete_modify_log != ""): ?>
                                        <br><?=$log->delete_modify_log?><br>
                                    <?php endif ?>
                                    </font>
                                    <br>
                                <?php endif ?>
                                
                                <?php if ($log->modify_student_from_date != ""): ?>
                                    <font color="red">
                                    <?=$log->modify_student_from_bureau ?><br>
                                    換員(原為<?=$log->modify_student_from_name?>):<?=$log->modify_student_from_date?><br>
                                    </font>
                                <?php endif ?>
                                
                                <?php if ($log->modify_student_to_date != ""): ?>
                                        <FONT color="red">
                                    <?=$log->modify_student_to_bureau?><br>
                                    換員(換成<?=$log->modify_student_to_name?>):<?=$log->modify_student_to_date?><br>
                                            </font>
                                <?php endif ?>
                                
                            <?php endif ?>
                            </td>
                            <td>
        					<?php if ($log->yn_sel == 5): ?>
        			  			<font color="red">未報到</font>
        			  		<?php endif ?>                            
                            </td>
                            <td>
                            <?php if ($log->yn_sel == 1): ?>
        			  			是
        			  		<?php elseif ($log->yn_sel == 4): ?>
        			  			退訓
                            <?php else: ?>
        			  			否
        			  		<?php endif ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <!-- /.table end -->
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <!-- <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div> -->
                </div>  
                <!--<a href="<?=base_url("management/signup_change_report")?>" class="btn btn-info btn-sm">返回</a>-->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
    $(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});



//合併上下欄位(colIdx)
    jQuery.fn.rowspan = function(colIdx) {
        return this.each(function() {
            var that;
            $('tr', this).each(function(row) {
                var thisRow = $('td:eq(' + colIdx + '),th:eq(' + colIdx + ')', this);
                if ((that != null) && ($(thisRow).html() == $(that).html())) {
                    rowspan = $(that).attr("rowSpan");
                    if (rowspan == undefined) {
                        $(that).attr("rowSpan", 1);
                        rowspan = $(that).attr("rowSpan");
                    }
                    rowspan = Number(rowspan) + 1;
                    $(that).attr("rowSpan", rowspan);            
                    $(thisRow).remove(); ////$(thisRow).hide();
                } else {
                    that = thisRow;
                }
                that = (that == null) ? thisRow : that;
            });
            alert('1');
        });
    }
     
    ////當指定欄位(colDepend)值相同時，才合併欄位(colIdx)
    jQuery.fn.rowspan = function(colIdx, colDepend) {
        return this.each(function() {
            var that;
            var depend;
            $('tr', this).each(function(row) {
                var thisRow = $('td:eq(' + colIdx + '),th:eq(' + colIdx + ')', this);
                var dependCol = $('td:eq(' + colDepend + '),th:eq(' + colDepend + ')', this);
                if ((that != null) && (depend != null) && ($(thisRow).html() == $(that).html()) && ($(depend).html() == $(dependCol).html())) {
                    rowspan = $(that).attr("rowSpan");
                    if (rowspan == undefined) {
                        $(that).attr("rowSpan", 1);
                        rowspan = $(that).attr("rowSpan");
                    }
                    rowspan = Number(rowspan) + 1;
                    $(that).attr("rowSpan", rowspan);
                    $(thisRow).remove(); ////$(thisRow).hide();
                     
                } else {
                    that = thisRow;
                    depend = dependCol;
                }
                that = (that == null) ? thisRow : that;
                depend = (depend == null) ? dependCol : depend;
            });
        });
    }
     
    ////合併左右欄位
    jQuery.fn.colspan = function(rowIdx) {
        return this.each(function() {
            var that;
            $('tr', this).filter(":eq(" + rowIdx + ")").each(function(row) {
                $(this).find('th,td').each(function(col) {
                    if ((that != null) && ($(this).html() == $(that).html())) {
                        colspan = $(that).attr("colSpan");
                        if (colspan == undefined) {
                            $(that).attr("colSpan", 1);
                            colspan = $(that).attr("colSpan");
                        }
                        colspan = Number(colspan) + 1;
                        $(that).attr("colSpan", colspan);
                        $(this).remove();
                    } else {
                        that = this;
                    }
                    that = (that == null) ? this : that;
                });
            });
        });
    }


$(function() {
        
        $('.table').rowspan(8,1);
        $('.table').rowspan(7,1);
        $('.table').rowspan(6,1);
        $('.table').rowspan(5,1);
        $('.table').rowspan(3,1);
        $('.table').rowspan(2,1);
        $('.table').rowspan(1,1);
        $('.table').rowspan(0);
                
});


</script>