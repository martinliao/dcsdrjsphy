<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline" method="get">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12" >
                            <label class="control-label">起日</label>
                            <div class="input-group" id="start_date" >
                                <input type="text" class="form-control" id="datepicker1" name="start_date" value="<?=$filter['start_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="control-label">迄日</label>
                            <div class="input-group" id="end_date" >
                                <input type="text" class="form-control" id="test1" name="end_date" value="<?=$filter['end_date'];?>"/>
                                <span class="input-group-addon" style="cursor: pointer;"id="datepicker4" ><i class="fa fa-calendar"></i></span>
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <div class="form-group">
                                <a class="btn btn-info btn-sm" onclick="fowardwee(-7)"><<</a>
                                <a class="btn btn-info btn-sm" onclick="GetNextWeekTime()">下週擬評估</a>

                                <a class="btn btn-info btn-sm" onclick="fowardwee(+7)">>></a>
                            </div>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">編號</th>
                                <th class="text-center"><input type="checkbox" id="chkISEVALUATE">評估否</th>
                                <th class="text-center"><input type="checkbox" id="chkIsOnline">線上問卷</th>
                                <th class="text-center">系列</th>
                                <th class="text-center">單位/類別</th>
                                <th class="text-center">年度</th>
                                <th class="text-center">課表</th>
                                <th class="text-center">期別</th>
                                <th class="text-center">研習日期</th>
                                <th class="text-center">帶班人員</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key => $row) { ?>
                            <tr class="text-center">
                                <td><?=$key+1;?></td>
                                <td>
                                <?php if($row['isevaluate'] == 'Y') {?>
                                    <?php if($row['teacher_count'] > '0') {?>
                                    老師已選
                                    <?php }else{ ?>
                                    <input type="checkbox" name="cancelEvaluated[<?=$row['seq_no'];?>]" value="<?=$row['seq_no'];?>" style='border:0' checked />
                                    <input type="hidden" name="isEvaluated[<?=$row['seq_no'];?>]" value="<?=$row['seq_no'];?>" style='border:0' checked />
                                    <?php } ?>
                                <?php }else{ ?>
                                <input type="checkbox" id="chkISEVALUATE" name="chkISEVALUATE[]" value="<?=$row['seq_no'];?>" style='border:0'>
                                <?php } ?>
                                </td>
                                <td>
                                    <?php if($row['question_addr'] != '') {?>
                                    <input type="checkbox" name="setOnline[<?=$row['seq_no'];?>]" value="<?=$row['seq_no'];?> " style='border:0' checked />
                                        <?php if($row['question_id'] > '0') {?>
                                        <a href='<?=$row['question_addr'];?>' target='_blank'>Q<?=$row['question_id'];?></a>
                                        <?php } ?>
                                    <input type="hidden" name="isOnline[<?=$row['seq_no'];?>]" value="<?=$row['seq_no'];?>" style='border:0' />
                                    <?php }else{ ?>
                                    <input type="checkbox" id="chkIsOnline" name="chkIsOnline[]" value="<?=$row['seq_no'];?>" style='border:0' />
                                    <?php } ?>
                                </td>
                                <td><?=$row['CLASS_TYPE_NAME'];?></td>
                                <td>
                                    <?php if($row['type'] == 'A') {?>
                                    <?=$row['TYPE_NAME_A'];?>
                                    <?php }else{ ?>
                                    <?=$row['TYPE_NAME_B'];?>
                                    <?php } ?>
                                </td>
                                <td><?=$row['year'];?></td>
                                <?php $link=base_url("create_class/print_schedule/print/".$row['seq_no']."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."") ?>
                                <td><a href="<?=$link?>" target=_blank><?=$row['class_name']?></a></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['S_DATE'];?></td>
                                <td><?=$row['worker_name'];?></td>
                            </tr>

                        <?php } ?>
                        </tbody>
                    </table>
                    <?php if(!empty($list)) {?>
                    <a class="btn btn-primary btn-save" href="#" title="Save">確認</a>
                    <?php } ?>
                </form>

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


$(document).ready(function() {
  $("#test1").datepicker();
  $('#datepicker4').click(function(){
    $("#test1").focus();
  });
  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });
});
    var $form_list = $('#list-form');
    $form_list.find('#chkISEVALUATE').click(function(){
        var checked = $(this).prop('checked');
        $form_list.find('tbody #chkISEVALUATE').each(function(){
            $(this).prop('checked', checked);
            if (checked == true) {
                $(this).closest('tr').addClass('active');
            } else {
                $(this).closest('tr').removeClass('active');
            }
        });
    });

    $form_list.find('#chkIsOnline').click(function(){
        var checked = $(this).prop('checked');
        $form_list.find('tbody #chkIsOnline').each(function(){
            $(this).prop('checked', checked);
            if (checked == true) {
                $(this).closest('tr').addClass('active');
            } else {
                $(this).closest('tr').removeClass('active');
            }
        });
    });

    $(".btn-save").click(function(){
        $('#list-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>

/*Date.prototype.Format = function (fmt) { 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}


function setDate(type)
{
        var now_query_start = "<?=$filter['start_date']?>";
        var now_query_end = "<?=$filter['end_date']?>";
        var query_time = "";

        if (now_query_end == ""){
            query_time = new Date();
        }else{
            query_time = new Date(now_query_end);
        }

        var start = "";
        var end = "";

        if (now_query_start == ""){
            query_time = new Date().Format("yyyy-MM-dd");
        }else{
            query_time = now_query_end;
        }      

        var sub = new Date(query_time);
        sub = sub.getDay();
        if (sub == 0) sub = 7;

        if (type == 'last'){
            start = new Date(query_time);
            start.setDate(start.getDate() - 7 - (sub - 1) );
            $("#datepicker1")[0].value = start.Format("yyyy-MM-dd");
            end = new Date(query_time);
            end.setDate(end.getDate() - 1 - (sub -1));
            $("#datepicker3")[0].value = end.Format("yyyy-MM-dd");   
        }else if (type == "next"){
            start = new Date(query_time);
            start.setDate(start.getDate()-1 - (8 - sub) );
            $("#datepicker1")[0].value = start.Format("yyyy-MM-dd");
            end = new Date(query_time);
            end.setDate(end.getDate() + 7 - (7 - (sub-1))); 
            $("#datepicker3")[0].value = end.Format("yyyy-MM-dd");
           
        }
        

        if (type != "nextsearch"){
            $("#filter-form").submit();
        }
}
function getNextWeek(type)
{
    var now_query_start = "";
        var now_query_end = "";
        var query_time = "";

        if (now_query_end == ""){
            query_time = new Date();
        }else{
            query_time = new Date(now_query_end);
        }

        var start = "";
        var end = "";

        if (now_query_start == ""){
            query_time = new Date().Format("yyyy-MM-dd");
        }else{
            query_time = now_query_end;
        }      

        var sub = new Date(query_time);
        sub = sub.getDay();
        if (sub == 0) sub = 7;
    if(type="nexsearch"){

    start = new Date(query_time);
    start.setDate(start.getDate()+8 - (8 - sub) );
    $("#datepicker1")[0].value = start.Format("yyyy-MM-dd");
    end = new Date(query_time);
    end.setDate(end.getDate() + 14 - (7 - (sub-1))); 
    $("#datepicker3")[0].value = end.Format("yyyy-MM-dd");
    }   

}
*/

Date.prototype.addDay=function   (num) 
{ 
	this.setDate(this.getDate()+num); 
	return  this; 
} 

function fowardwee(days)
{
    var date1 = document.getElementById("datepicker1").value;
    var date2 = document.getElementById("test1").value;

    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        document.getElementById("datepicker1").value = sdate; 
        document.getElementById("test1").value = edate;
    }
    document.getElementById("filter-form").submit()
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    var dd = result.getDate();
    var mm = result.getMonth()+1;
    var yy = result.getFullYear();

    if(mm < 10){
        mm = '0'+mm;
    }

    if(dd<10){
        result = yy+'-'+mm+'-0'+dd;
    }else{
        result = yy+'-'+mm+'-'+dd;

    }
    
    return result;
}

function GetNextWeekTime()
{
	var NowDate = new Date();//取得當前日期
	var strDate = NowDate.getFullYear()+"/"+(NowDate.getMonth() + 1)+"/"+NowDate.getDate();
	var week = NowDate.getDay();//取得星期
	week = (week==0?7:week);
	var Monday = NowDate.addDay(7-(week-1)); //下週一
    if(Monday.getDate()<10){
        var strMon = Monday.getFullYear()+"-"+((Monday.getMonth() + 1)<10?"0"+(Monday.getMonth() + 1) : ""+(Monday.getMonth() + 1))+"-0"+Monday.getDate(); 

    }else{
        var strMon = Monday.getFullYear()+"-"+((Monday.getMonth() + 1)<10?"0"+(Monday.getMonth() + 1) : ""+(Monday.getMonth() + 1))+"-"+Monday.getDate(); 
    }


	var Sunday = Monday.addDay(6);//下週日 = 下週一 + 6
    if(Sunday.getDate()<10){
        var strSun = Sunday.getFullYear()+"-"+((Sunday.getMonth() + 1)<10?"0"+(Sunday.getMonth() + 1) : ""+(Sunday.getMonth() + 1))+"-0"+Sunday.getDate(); 
    }else{
	    var strSun = Sunday.getFullYear()+"-"+((Sunday.getMonth() + 1)<10?"0"+(Sunday.getMonth() + 1) : ""+(Sunday.getMonth() + 1))+"-"+Sunday.getDate();
    }
	document.getElementById('datepicker1').value = strMon;
    document.getElementById('test1').value = strSun;

}

</script>
