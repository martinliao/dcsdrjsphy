<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading" >
                <i class="fa fa-list fa-lg"></i> 
                <?=($page_name == 'add')?'新增(計畫外)':'修改(計畫外)';?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="require_query_form">
                    <div class="row" id="test2">
                        <div class="col-xs-12">
                            <form id="edit-form" role="form" class="form-inline" method="post"
                                    action="<?=$link_action?>">
                                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <td style="color: black;background-color: #d5d1d1;width: 20%;font-weight: bold">
                                            <font style="color:red">*</font>班期名稱/期別
                                        </td>
                                        <td> 
                                            <input type="text" class="form-control" id="class_name" name="class_name" value="<?=isset($info[0]['class_name'])?$info[0]['class_name']:''?>" style="width: 50%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: black;background-color: #d5d1d1;width: 20%;font-weight: bold"><font style="color:red">*</font>上課日期</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker" id="course_date" name="course_date" value="<?=isset($info[0]['course_date'])?$info[0]['course_date']:''?>"/>
                                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: black;background-color: #d5d1d1;width: 20%;font-weight: bold"><font style="color:red">*</font>上課教室</td>
                                        <td>
                                            <input type="text" class="form-control" id="classroom" name="classroom" value="<?=isset($info[0]['classroom'])?$info[0]['classroom']:''?>" >
                                        </td>
                                    </tr>
                                    <?php if($page_name != 'add'){?>
                                    <tr>
                                        <td style="color: black;background-color: #d5d1d1;width: 20%;font-weight: bold"><font style="color:red">*</font>學員人數</td>
                                        <td>
                                            <input type="text" class="form-control datepicker" id="student_count" name="student_count" value="<?=isset($info[0]['student_count'])?$info[0]['student_count']:''?>"/>
                                            <a class="btn btn-info" id="back" href="<?=$link_importStudent?>" target="_blank">匯入名單</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td style="color: black;background-color: #d5d1d1;width: 20%;font-weight: bold"><font style="color:red">*</font>時數</td>
                                        <td>
                                            <input type="text" class="form-control datepicker" id="hours" name="hours" value="<?=isset($info[0]['hours'])?$info[0]['hours']:''?>"/>
                                        </td>
                                    </tr>
                                </table>
                                <input type="hidden" id="year" name="year" value=""></input>
                                <input type="hidden" id="class_no" name="class_no" value=""></input>
                                <input type="hidden" id="term" name="term" value=""></input>
                                <input type="button" class="btn btn-info" value="儲存" onclick="saveFun()">
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#course_date").datepicker();
        $('#datepicker2').click(function(){
            $("#course_date").focus();
        });
    });

    function saveFun(){
        var obj = document.getElementById('edit-form');
        var class_name = document.getElementById('class_name').value.trim();
        var course_date = document.getElementById('course_date').value.trim();
        var classroom = document.getElementById('classroom').value.trim();
        var hours = document.getElementById('hours').value.trim();

        if(class_name == ''){
            alert('班期名稱/期別不能為空');
            return false;
        }
        if(course_date == ''){
            alert('上課日期不能為空');
            return false;
        }
        if(classroom == ''){
            alert('上課教室不能為空');
            return false;
        }

        if(hours == ''){
            alert('時數不能為空');
            return false;
        }

        obj.submit();
    }
</script>