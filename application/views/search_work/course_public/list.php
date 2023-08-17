<!-- <?php print_r($datas)?> -->
<style type="text/css">
    .modal-header .close {
        margin-top: -17px !important;
        font-size: 36px;
        outline: none;
    }
    .modal-dialog80 {
        width: 80% !important;
    }
    .pointer {
        cursor: pointer;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <form id="form" method="GET">
                        <input hidden id='sclassname' name='classname' value="">
                        <input hidden id='stype' name='type' value="">
                        <input hidden id='st_name' name='t_name' value="">
                        <input hidden id='sedu' name='edu' value="">
                        <input hidden id='suse_s_date' name='use_s_date' value="">
                        <input hidden id='suse_e_date' name='use_e_date' value="">
                        <input hidden id='st_source' name='t_source' value="">
                        <input hidden id='scre_s_date' name='cre_s_date' value="">
                        <input hidden id='scre_e_date' name='cre_e_date' value="">
                        <input hidden id='sjob' name='job' value="">
                        <input hidden id='siscsv' name='iscsv' value="setup">
                        <input hidden id='srows' name='rows' value="">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">班期名稱:</label>
                            <input type="text" id="classname"  value="<?=$sess_classname?>" class="form-control">
                            <label class="control-label">班期類別:</label>
                            <select id='type' class='form-control' style='width: 168px;'>
                                <option value=''>請選擇班期類別</option>
                                <?php foreach ($types as $type): ?>
                                    <option value='<?=$type["ITEM_ID"]?>' <?=$sess_type == $type["ITEM_ID"] ? "selected" : ""?> ><?=$type["DESCRIPTION"];?></option>
                                <?php endforeach?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label" style='width:90px;text-align:left;'>姓名:</label>
                            <input type="text" id="t_name"  value="<?=$sess_t_name?>" class="form-control">
                            <label class="control-label" style='width:90px;text-align:left;'>身分:</label>
                            <select id='job' class='form-control' style='width: 168px;'>
                                 <option value="all" <?=$sess_job == "all" ? "selected" : ""?> >請選擇</option>
                                 <option value="1" <?=$sess_job == "1" ? "selected" : ""?> >講師</option>
    　							 <option value="2" <?=$sess_job == "2" ? "selected" : ""?> >助教</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">聘請類別:</label>
                            <select id='t_source' class='form-control' style='width: 168px;'>
	        		 		    <option value=''>請選擇聘請類別</option>
                                <?php foreach ($source as $data): ?>
                                    <option value='<?=$data["ITEM_ID"]?>' <?=$sess_t_source == $data["ITEM_ID"] ? "selected" : ""?> ><?=$data["DESCRIPTION"];?></option>
                                <?php endforeach?>
                            </select>

                            <label class="control-label" style='width:90px;text-align:left;'>學歷:</label>
                            <select id='edu' class='form-control' style='width: 168px;'>
                                <option value=''>請選擇聘請類別</option>
                                <?php foreach ($studentype as $data): ?>
                                    <option value='<?=$data["ITEM_ID"]?>' <?=$sess_edu == $data["ITEM_ID"] ? "selected" : ""?> ><?=$data["DESCRIPTION"];?></option>
                                <?php endforeach?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">上課日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_use_s_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_use_e_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <button class="btn btn-info" onclick="fowardweek1(-7,1);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek1(1);">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek1(7,1);">>></button>
                            <button class="btn btn-info" onclick="setToday1(1)">設定今天</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">建檔日期:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_cre_s_date?>" id="datepicker3"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_cre_e_date?>" id="test3" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test4"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <button class="btn btn-info" onclick="fowardweek1(-7,3);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek1(3);">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek1(7,3);">>></button>
                            <button class="btn btn-info" onclick="setToday1(3)">設定今天</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id='Search' class="btn btn-info btn-sm">查詢</button>
                            <button id="clearDate" class="btn btn-info btn-sm">清除</button>
                            <button id="csv" class="btn btn-info btn-sm">匯出</button>
                            <button id="print" class="btn btn-info btn-sm">列印</button>
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
                </div>
                <!-- /.table head  style="width: 2300px;" -->
                <table border="1" id="printTable" class="table-bordered table-condensed table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="21">臺北市政府公務人員訓練處 講座基本資料一覽表</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="width:30px;">姓名</th>
                            <th class="text-center" style="width:30px;">身分</th>
                            <th class="text-center" style="width:30px;">別名</th>
                            <th class="text-center">身分證</th>
                            <th class="text-center" style="width:30px;">聘請類別</th>
                            <th class="text-center">學歷</th>
                            <th class="text-center">生日</th>
                            <th class="text-center">公司電話</th>
                            <th class="text-center">家用電話</th>
                            <th class="text-center">手機</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">上課日期</th>
                            <th class="text-center">教學時數</th>
                            <th class="text-center">郵遞區號</th>
                            <th class="text-center" style="width:130px;">地址</th>
                            <th class="text-center">任職機關</th>
                            <th class="text-center" style="width:30px;">職稱</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">經歷</th>
                            <th class="text-center" style="width: 500px;">可授課程</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $hrs = ($sess_use_s_date==''&& $sess_use_e_date=='')? 0:1;
                    foreach ($datas as $data): ?>
                        <tr>
                            <td><?=$data["name"]?></td>
                            <td><?=$data["teacher"] == 'Y' ? "講師" : "助教"?></td>
                            <td><?=$data["alias"]?></td>
                            <td><?=$data["idno"]?></td>
                            <td><?=$data["DESCRIPTION"]?></td>
                            <td><?=$data["EDU_NAME"]?></td>
                            <td><?=substr($data["birth"],0,10)?></td>
                            <td style="width:70px;word-break:break-all;"><?=$data["telo"]?></td>
                            <td style="width:70px;word-break:break-all;"><?=$data["telh"]?></td>
                            <td><?=$data["mobil"]?></td>
                            <!-- <td class="text-center"><a class="pointer" onclick='ApiGet("detail","<?=$data["year"]?>","<?=$data["class_no"]?>","<?=$data["term"]?>")'><?=$data["class_name"]?></a></td> -->
                            <td>
                            <a title="連結至課程表"
                                href="<?=base_url('create_class/print_schedule/print/'.$data["seq_no"].'?query_year='.$data["year"].'&query_class_no='.$data["class_no"].'&rows=10&query_class_name='.$data["class_name"])?>"
                                onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;"><?=$data["class_name"]?></a>
                            </td>
                            <td><?=$data["term"]?></td>
                            <td><?=substr($data["use_date"],0,10)?></td>
                            <td><?=$data["total_hrs"]?></td>
                            <td><?=$data["zone"]?></td>
                            <td><?=$data["addr"]?></td>
                            <td><?=$data["corp"]?></td>
                            <td><?=$data["position"]?></td>
                            <td style="width:100px;word-break:break-all;"><?=$data["email"]?></td>
                            <td style="width:200px;"><?=$data["career"]?></td>
                            <td><?=$data["CAN_TEACH"]?></td>
                        </tr>

                    <?php endforeach?>

                    </tbody>
                </table>
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8  text-right">
                    <?=$this->pagination->create_links();?>
                </div>
                <?php
                    if (count($datas)==0){
                    echo '<br><font color="#FF0000">查無資料</font>';
                    }
                ?>
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>

<!-- change Modal -->
<div class="modal fade bd-example-modal-lg firstPop" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog80" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="printinner" class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">返回</button>
                <button id="copySave" type="button" class="btn btn-primary">列印</button>
            </div>
        </div>
    </div>
</div>


<script>
    function sendFun(){
        let timeCount = 0;
        if($("#datepicker1").val() != "" && $("#test1").val() != "") {
            timeCount++;
        }
        if($("#datepicker3").val() != "" && $("#test3").val() != "") {
            timeCount++;
        }

        if(timeCount == 0) {
            alert("上課日期、建檔日期請擇一");
            return;
        }

        $('#Search').click();
    }

    function addDays(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        var dd = result.getDate();
        var mm = result.getMonth()+1;
        var yy = result.getFullYear();
        result = yy+'-'+mm+'-'+dd;
        return result;
    }

    function ApiGet(pagetype,year,class_no,term){
        $.ajax({
            async: false,
            url: "Course_public?pagetype="+pagetype+"&year="+year+"&class_no="+class_no+"&term="+term,
            type: "GET",
            dataType: "json",
            success: function (Jdata) {
                console.log(Jdata);
                
                    let tempHTML = '<div id="require_query_form">\
                                        <form action="" name="query_form" method="post" >';
                for(let j=0; j<Jdata.data.length; j++) {
                        tempHTML +=         '<div align="center" style="font-family:\'標楷體\'" >\
                                                <font size="5"><b>臺北市政府公務人員訓練處　　　講座基本資料一覽表</b></font>\
                                            </div>\
                                            <div align="center" style="margin: 15px 0px;font-family:\'標楷體\';">\
                                                <font size="4"><b>'+Jdata.data[j].year+'年度　'+Jdata.data[j].class_name+'　第'+Jdata.data[j].term+'期</b></font>\
                                            </div>\
                                            <div style="margin: 10px 0px;">\
                                                <font size="4"><b>\
                                                    <div style="float:left;font-family:\'標楷體\'">'+Jdata.data[j].class_no+'</div>';
                                                if(Jdata.classRoomName.length != 0) {
                        tempHTML +=                 '<div style="float:right;font-family:\'標楷體\'">\
                                                    上課地點：';
                                                    for(let i=0; i<Jdata.classRoomName.length; i++) {
                        tempHTML +=                     Jdata.classRoomName[i].room_id+' '+Jdata.classRoomName[i].name;
                                                    }
                                
                        tempHTML +=                 '</div>';
                                                }
                        tempHTML +=             '</b></font>\
                                            </div>';
                    if(Jdata.mixlist.length != 0) {
                        tempHTML +=         '<div style="font-family:\'標楷體\'">\
                                                <font size="4"><b>線上課程表</b></font>\
                                            </div>\
                                            <table class="table table-bordered table-condensed table-hover" width="600" >\
                                                <thead>\
                                                    <tr>\
                                                        <th class="text-center" width="60px">起日</th>\
                                                        <th class="text-center" width="60px">迄日</th>\
                                                        <th class="grid1 th" width="300px">線上課程名稱</th>\
                                                        <th class="text-center" width="120px">講座名稱</th>\
                                                        <th class="text-center" width="100px">上課地點</th>\
                                                    </tr>\
                                                </thead>\
                                                <tbody>';
                                                for(let i=0; i<Jdata.mixlist.length; i++) {
                        tempHTML +=                 '<tr>\
                                                        <td class="text-center">'+Jdata.mixlist[i].start_date+'</td>\
                                                        <td class="text-center">'+Jdata.mixlist[i].end_date+'</td>\
                                                        <td class="text-center">'+Jdata.mixlist[i].class_name+'</td>\
                                                        <td class="text-center" align="left">'+Jdata.mixlist[i].teacher_name+'</td>\
                                                        <td class="text-center">'+Jdata.mixlist[i].place+'</td>\
                                                    </tr>';
                                                }
                        tempHTML +=             '</tbody>\
                                            </table>\
                                            <br>';

                        tempHTML +=         '<div style="font-family:\'標楷體\'">\
                                                <font size="4"><b>實體課程表</b></font>\
                                            </div>';
                    }
                        tempHTML +=         '<table class="table table-bordered table-condensed table-hover" width="600px">\
                                                <thead>\
                                                    <tr>';
                                                    if(Jdata.classRoomName.length > 1) {
                        tempHTML +=                     '<th class="text-center" width="60px">日期</th>\
                                                        <th class="text-center" width="60px">星期</th>\
                                                        <th class="text-center" width="110px">時間</th>\
                                                        <th class="text-center" width="150px">課程</th>\
                                                        <th class="text-center" width="120px">講座</th>\
                                                        <th class="text-center"  width="100px">上課地點</th>';
                                                    }
                                                    else {
                        tempHTML +=                     '<th class="text-center" width="60px" >日期</th>\
                                                        <th class="text-center" width="60px">星期</th>\
                                                        <th class="text-center" width="110px">時間</th>\
                                                        <th class="text-center" width="240px">課程</th>\
                                                        <th class="text-center" width="120px">講座</th>';
                                                    }
                        tempHTML +=                 '</tr>\
                                                </thead>\
                                                <tbody>';
                                                for(let i=0; i<Jdata.list.length; i++) {
                        tempHTML +=                 '<tr>';
                                                    if(i == 0) {
                        tempHTML +=                    '<td class="text-center">'+Jdata.list[i].use_date+'</td>';
                                                    }
                                                    else {
                        tempHTML +=                     '<td class="text-center"></td>';
                                                    } 

                                                    if(i == 0) {
                        tempHTML +=                     '<td class="text-center">'+Jdata.list[i].cday+'</td>';
                                                    }
                                                      else {
                        tempHTML +=                     '<td class="text-center"></td>';
                                                    }
                        tempHTML +=                     '<td class="text-center">'+(Jdata.list[i].ltime != null ? Jdata.list[i].ltime != null:"")+'</td>\
                                                        <td class="text-center" style="text-align: left">'+Jdata.list[i].class_name+'</td>\
                                                        <td class="text-center">'+Jdata.list[i].name+'</td>';
                                                    if(Jdata.classRoomName.length > 1) {
                        tempHTML +=                     '<td class="text-center">'+Jdata.list[i].classroom_name+'</th>'
                                                    }
                                                        
                        tempHTML +=                 '</tr>';
                                                }
                        tempHTML +=             '</tbody>\
                                            </table>';
                }
                        tempHTML +=     '</form>\
                                    </div>';
                
                $('.modal-body').html(tempHTML);
                $(".firstPop").modal("show");
            }
        });
    }

    $(document).ready(function() {
        $("#datepicker1").datepicker();
        $('#datepicker2').click(function(){
            $("#datepicker1").focus();
        });

        $("#datepicker3").datepicker();
        $('#datepicker4').click(function(){
            $("#datepicker3").focus();
        });

        $("#clearDate").click(function(){
            $("#datepicker1").val("");
            $("#datepicker3").val("");
            $("#test1").val("");
            $("#test3").val("");
        });

        $('#Search').click(function(){
            let timeCount = 0;
            if($("#datepicker1").val() != "" && $("#test1").val() != "") {
                timeCount++;
            }
            if($("#datepicker3").val() != "" && $("#test3").val() != "") {
                timeCount++;
            }

            if(timeCount == 0) {
                alert("上課日期、建檔日期請擇一");
                return;
            }

            $('#sclassname').val($('#classname').val());
            $('#stype').val($('#type').val());
            $('#st_name').val($('#t_name').val());
            $('#sedu').val($('#edu').val());
            $('#suse_s_date').val($("#datepicker1").val());
            $('#suse_e_date').val($("#test1").val());
            $('#scre_s_date').val($("#datepicker3").val());
            $('#scre_e_date').val($("#test3").val());
            $('#st_source').val($("#t_source").val());
            $('#sjob').val($("#job").val());
            $('#siscsv').val(0);
            $('#srows').val($("select[name=rows]").val());

            $( "#form" ).submit();

        });

        $('#print').click(function(){
            printData("printTable");
        });

        $('#copySave').click(function(){
            printData("printinner");
        });

        $('#csv').click(function(){

            $('#sclassname').val($('#classname').val());
            $('#stype').val($('#type').val());
            $('#st_name').val($('#t_name').val());
            $('#sedu').val($('#edu').val());
            $('#suse_s_date').val($("#datepicker1").val());
            $('#suse_e_date').val($("#test1").val());
            $('#scre_s_date').val($("#datepicker3").val());
            $('#scre_e_date').val($("#test3").val());
            $('#st_source').val($("#t_source").val());
            $('#sjob').val($("#job").val());
            $('#siscsv').val(1);
            $( "#form" ).submit();

        });

        $("#test1").datepicker();
        $('#test2').click(function(){
            $("#test1").focus();
        });

        $("#test3").datepicker();
        $('#test4').click(function(){
            $("#test3").focus();
        });
    });

</script>