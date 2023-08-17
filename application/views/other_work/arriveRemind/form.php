
<style>
    .labelWidth{
        width: 200px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <div class="panel-body">
                <div class="col-xs-12">
                <?php echo validation_errors(); ?>                
                </div>
                <div class="col-xs-12">
                    <form id="filter-form" method="POST" role="form" class="form-inline">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="row">
                            <div class="col-xs-12" >
                                <label class="control-label labelWidth"><font color="red">*</font>蒞臨人員</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="" name="member_name" autocomplete="off" readonly>
                                    <input type="hidden" class="form-control" value="" name="idno" autocomplete="off" readonly>
                                    <input type="hidden" class="form-control" value="" name="member_type" autocomplete="off" readonly>
                                </div>        
                                <button type="button" class="btn btn-info" onclick="selectMember()">選擇蒞臨人員</button>                                                  
                            </div>
                            <div class="col-xs-12" >
                                <label class="control-label labelWidth"><font color="red">*</font>寄送對象(輸入姓名)</label>
                                <div class="input-group" id="start_date">
                                    <input type="text" class="form-control" value="" name="remind_member_name" autocomplete="off">
                                </div>                                                          
                            </div>
                            <div class="col-xs-12" >
                                <label class="control-label labelWidth"><font color="red">*</font>寄送郵件信箱(以,分開)</label>
                                <div class="input-group col-xs-6" id="start_date">
                                   <input type="text" class="form-control" value="" name="email" autocomplete="off">
                                </div>                                                          
                            </div>
                            <div class="col-xs-12" >
                                <label class="control-label labelWidth"><font color="red">*</font>提醒期間</label>
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker" value="<?=$now->format('Y-01-01')?>" id="remind_sdate" name="remind_sdate" autocomplete="off">
                                    <span class="input-group-addon" style="cursor: pointer;" id="remindSdateDatepicker"><i class="fa fa-calendar"></i></span>
                                </div>
                                <label class="control-label">至</label>
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker" value="<?=$now->format('Y-12-31')?>" id="remind_edate" name="remind_edate" autocomplete="off">
                                    <span class="input-group-addon" style="cursor: pointer;" id="remindEdateDatepicker"><i class="fa fa-calendar"></i></span>
                                </div>                                                      
                            </div>                            
                            <div class="col-xs-12" >
                                <button class="btn btn-info btn-sm">送出</button>       
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#remind_sdate").datepicker();
        $('#remindSdateDatepicker').click(function(){
            $("#remind_sdate").focus();
        });
        $("#remind_edate").datepicker();
        $('#remindEdateDatepicker').click(function(){
            $("#remind_edate").focus();
        });
    });

    function selectMember()
    {
        var link = "<?=$link_refresh;?>";
        window.open(link+'selectMember', '_blank', "width=500, height=600", true);
    }

    function choose(idno, name, queryType)
    {
        console.log(name);
        console.log(idno);
        console.log(queryType);
        let identity = (queryType == 'teacher') ? '講座' : '學員';
        $("input[name=member_name]").val(name + '(' + identity + ')');
        $("input[name=idno]").val(idno);
        $("input[name=member_type]").val(queryType);
    }
</script>