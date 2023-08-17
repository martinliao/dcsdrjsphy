<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="data-form" role="form" class="form-inline" method="post" action="<?=$link_add?>">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <div class="row" style="margin-bottom:2%;">
                        <div class="col-xs-12">
                            <label class="control-label">班期名稱</label>
                            <input type="text" class="form-control" name="class_name" placeholder="<?=$list[0]['class_name']?>第<?=$list[0]['term']?>期"  disabled>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:2%;">
                        <div class="col-xs-12">
                            <label class="control-label">教室</label>
                            <input type="text" class="form-control" name="room_code"  value="<?=$list[0]['room_code']?>" disabled>
                            <input type="hidden" class="form-control" name="class_no" value="<?=$list[0]['class_no']?>">
                            <input type="hidden" class="form-control" name="year" value="<?=$list[0]['year']?>">
                            <input type="hidden" class="form-control" name="term" value="<?=$list[0]['term']?>">
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:2%;">
                        <div class="col-xs-12">
                            <label class="control-label">身分證字號<span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="gid" id="gid">
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:2%;">
                        <div class="col-xs-12">
                            <label class="control-label">姓名</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:2%;">
                        <div class="col-xs-12">
                            <label class="control-label">刷卡日期</label>
                            <input type="text" class="form-control" id="use_date" name="use_date" value="<?php $use_date=substr($list[0]['use_date'],0,10); echo $use_date;?>" disabled>
                            <input type="hidden" class="form-control" name="use_date" value="<?php $use_date=substr($list[0]['use_date'],0,10); echo $use_date;?>">

                        </div>
                    </div>
                    <div class="row" style="margin-bottom:2%;">
                        <div class="col-xs-12">
                            <label class="control-label">刷卡時間<span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="pass_time">
                            <p style="color:red">(刷卡時間輸入範例:早上八點五十分10秒請輸入085010、下午1點12分20秒請輸入131220)</p>
                        </div>
                    </div>

                    <button class="btn btn-info">確定新增</button>
                    <a href="<?=base_url("management/card_record/?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">回刷卡紀錄管理</a>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>

</script>