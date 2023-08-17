<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form"  class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <select name='year' id='query_year'>
                                    <?php for($query_year=intVal(date('Y'))-1911; $query_year>=90; $query_year--){ ?>
                                        <option value='<?=str_pad($query_year, 3, "0", STR_PAD_LEFT)?>'><?=str_pad($query_year, 3, "0", STR_PAD_LEFT)?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" name="class_no" value="<?=$condition['class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" name="class_name" value="<?=$condition['class_name']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" name="term" value="<?=$condition['term']?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">學員姓名:</label>
                                <input type="text" class="form-control" name="student_name" value="<?=$condition['student_name']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">身份證字號:</label>
                                <input type="text" class="form-control" name="student_idno" value="<?=$condition['student_idno']?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">開始課程日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" style="width:100px" class="form-control datepicker" id="datepicker1" name="start_date" value="<?=$condition['start_date']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label">結束課程日期:</label>
                            <div class="input-group" id="end_date">
                                <input type="text" style="width:100px" class="form-control datepicker" id="test1" name="end_date" value="<?=$condition['end_date']?>">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm" >查詢</button>
                            <a href="<?=base_url("customer_service/co_admin_student_sel")?>" class="btn btn-info btn-sm">清除</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                    
                </form>
                <?php if(!empty($requires)){?>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#8CBBFF">
                            <th>年度</th>
                            <th>班期名稱</th>
                            <th>期別</th>
                            <th>錄取人數</th>
                            <th>開放名冊</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requires as $require): ?>
                        <tr>
                            <td><?=$require->year?></td>
                            <td><a href="<?=base_url("create_class/print_schedule/print/{$require->seq_no}")?>" target="_blank"><?=$require->class_name?></a></td>
                            <td><?=$require->term?></td>
                            <td>
                            <a href="<?=base_url("customer_service/co_admin_student_sel/detail?year={$require->year}&class_no={$require->class_no}&term={$require->term}")?>" target="_blank"><?=(empty($require->enroll)) ? '0' : $require->enroll;?></a>
                            </td>
                            <td><?=($require->co_open_member_sheet=='Y')? '是':'否' ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php }?>

                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
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

$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });

  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });
});

</script>
