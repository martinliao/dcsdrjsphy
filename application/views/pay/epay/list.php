<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="filter-form" role="form" class="form-inline">
                    <form id="updform" method="GET" action="epay/detail">
                        <input hidden type="text" id="bldt" name="bldt" value="">
                        <input hidden type="text" id="s1" name="s1" value="">
                        <input hidden type="text" id="s2" name="s2" value="">
                    </form>
                    <form id="form" method="GET">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='srows' name='rows'>
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">切換週期:</label>
                            <button class="btn btn-info" onclick="fowardweek(-7);">
                                <<</button> <button class="btn btn-info" onclick="getCurrentWeek();">本週
                            </button>
                            <button class="btn btn-info" onclick="fowardweek(7);">>></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">日期區間:</label>
                            <div class="input-group" id="start_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_start_date?>" id="datepicker1"
                                    name="start_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group" id="end_date">
                                <input type="text" class="form-control datepicker" value="<?=$sess_end_date?>" id="test1" name="end_date">
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
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
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">匯出</th>
                            <th class="text-center">調整</th>
                            <th class="text-center">出單日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <tr class="text-center">
                            <td><button class="btn btn-info" onclick="prints('<?=date_format(date_create($data["bill_date"]), "Y-m-d")?>')"; >下載</button></td>
                            <td><button  class="btn btn-info" onclick="modify('<?=date_format(date_create($data["bill_date"]), "Y-m-d")?>')";>調整</button></td>
                            <td><?=date_format(date_create($data["bill_date"]), "Y-m-d")?></td>
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
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>
<div  id="printTable">
    

</div>


<script type="text/javascript">
function sendFun(){
    if($('#datepicker1').val() == "" || $('#test1').val() == ""){
        alert("請選擇日期區間")
        return;
    }
    
    $('#Search').click();
}

function check_all(obj,cName)
{
    var checkboxs = document.getElementsByName(cName);
    for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
}

function getCurrentWeek()
{
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,-diff);
    edate = addDays(sdate,6);
    document.getElementById("datepicker1").value = sdate;
    document.getElementById("test1").value = edate;
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

function fowardweek(days)
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
    else
    {
        var today = getCurrentWeek();
    }
}


$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){
        if($('#datepicker1').val() == "" || $('#test1').val() == ""){
            alert("請選擇日期區間")
            return;
        }

        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#srows').val($('select[name=rows]').val());

        $( "#form" ).submit();
    });



    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });

    $("#money1").datepicker();
    $('#money2').click(function(){
    $("#money1").focus();
  });
});

function modify(bldt){
    $('#bldt').val(bldt);
    $('#s1').val($('#datepicker1').val());
        $('#s2').val($('#test1').val());
    // $('#send_date').val($('#test1').val());

    $( "#updform" ).submit();

}

function prints(data){
    
    ApiGet("?date="+data+"&type=print","print",data)
}

function ApiGet(url,name,date){
    $.ajax({
        async: false,
        url: url,
        type: "GET",
        dataType: "json",
        success: function (Jdata) {
            console.log(url);
            console.log(Jdata);
            if(name == "print"){
                document.getElementById("printTable").innerHTML = "";
                createhtml(Jdata,date);
                
            }

        }
    });
}

function hasIllegalChar(str){
    return new RegExp(".*?script[^&gt;]*?.*?(&lt;\/.*?script.*?&gt;)*", "ig").test(str);
}

function createhtml(data,date){
    let page_size = 5;
    let total_page = Math.ceil(data.length / page_size);
    let html = "";

    let total_tax = 0;
    let total_h_tax = 0;
    let total_aftertax = 0;
    let total_hour_fee = 0;
    let total_traffic_fee = 0;

    for(let pageIndex=0; pageIndex<total_page; pageIndex++) {
        let part_tax = 0;
        let part_h_tax = 0;
        let part_aftertax = 0;
        let part_hour_fee = 0;
        let part_traffic_fee = 0;
        let traffic_fee = 0;
        html += "<div style='page-break-after: always;'><div align='center'><h1>付款憑單受款人清單</h1></div><table width='100%'><tbody><tr><td width='33%' valign='top'>"+
            "支用機關名稱：臺北市政府公務人員訓練處<br>付款憑單編號：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>"+
                "<td align='center' width='33%' valign='top'>出單日期："+date+"    </td>"+
                "<td align='right' width='33%' valign='top'>支付處收件編號：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>第 "+(pageIndex+1)+" 頁 / 共 "+total_page+" 頁"+
                "<td width='4%' valign='top'></td></tr></tbody></table>";
        html += "<table border='1' cellspacing='0' cellpadding='0' width='100%' class='t1'>"+
            "<tbody><tr><td width='5%' class='t2' rowspan='2' align='center'>序號</td>"+
            "<td width='8%' height='40' class='t2' align='center'>總代號</td>"+
            "<td width='22%' class='t2' align='center'>金融機關名稱</td>"+
            "<td width='10%' class='t2' rowspan='2' align='center'>存款人姓名或名稱<br>(全銜)</td>"+
            "<td width='8%' class='t2' rowspan='2' align='center'>稅額</td>"+
            "<td width='8%' class='t2' rowspan='2' align='center'>補充保費</td>"+
            "<td width='8%' class='t2' rowspan='2' align='center'>鐘點費</td>"+
            "<td width='8%' class='t2' rowspan='2' align='center'>交通費</td>"+
            "<td width='8%' class='t2' rowspan='2' align='center'>合計</td>"+
                
            "<tr><td height='40' class='t2' align='center' colspan='2'>分行別‧科目‧存戶帳號</td></tr>";

        for(let i=pageIndex*page_size ; i<pageIndex*page_size+page_size; i++)
        {
            if(data[i] == undefined) {
                break;
            }
            if(Number(data[i].traffic_fee) < 0){
                traffic_fee = 0;
            }else{
                traffic_fee = Number(data[i].traffic_fee);
            }
            
	        //20211019 變更計算方式，鐘點費=原鐘點-稅率-補充保費，合計=原鐘點費+交通費

            new_hour_fee = Number(data[i].hour_fee) - Number(data[i].tax) - Number(data[i].h_tax);
            new_aftertax = Number(data[i].hour_fee) + traffic_fee;


	        total_tax += Number(data[i].tax);
            total_h_tax += Number(data[i].h_tax);
            //total_hour_fee += Number(data[i].hour_fee);
            total_hour_fee += new_hour_fee;
            total_traffic_fee += traffic_fee;
            //total_aftertax += Number(data[i].aftertax);
            total_aftertax += new_aftertax;
            part_tax += Number(data[i].tax);
            part_h_tax += Number(data[i].h_tax);
            //part_hour_fee += Number(data[i].hour_fee);
            part_hour_fee += new_hour_fee;
            part_traffic_fee += traffic_fee;
            //part_aftertax += Number(data[i].aftertax);
            part_aftertax += new_aftertax;


            html +=
            "<tr><td class='t2' rowspan='2' align='center'>"+(i+1)+"</td>"+
            "<td height='40' class='t2' align='center'>"+data[i].teacher_bank_id+"</td>"+
            "<td class='t2' align='center'>"+(data[i].bankname == null ? "":data[i].bankname)+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].teacher_acct_name+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].tax+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].h_tax+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+new_hour_fee+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+traffic_fee+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+new_aftertax+"</td>";

            //20211019舊計算方式 備份
            /* html +=
            "<tr><td class='t2' rowspan='2' align='center'>"+(i+1)+"</td>"+
            "<td height='40' class='t2' align='center'>"+data[i].teacher_bank_id+"</td>"+
            "<td class='t2' align='center'>"+(data[i].bankname == null ? "":data[i].bankname)+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].teacher_acct_name+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].tax+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].h_tax+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].hour_fee+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+traffic_fee+"</td>"+
            "<td class='t2' rowspan='2' align='center'>"+data[i].aftertax+"</td>"; */

            if(data[i].note == null) {
                data[i].note = "";
            }

            html +=
            // "<td class='t2' rowspan='2' align='center'>"+data[i].note+"</td></tr>"+
            "</tr>"+
            "<tr><td height='40' class='t2' align='center' colspan='2'>"+data[i].teacher_account+"</td></tr>";

            if(i == pageIndex*page_size+page_size-1 || i == data.length-1) {
                html += "<tr><td></td><td colspan='2' style='text-align:right;'>小計</td>"+
                    "<td></td><td align='center'>"+part_tax+"</td><td align='center'>"+part_h_tax+"</td><td align='center'>"+part_hour_fee+"</td><td align='center'>"+part_traffic_fee+"</td><td align='center'>"+part_aftertax+"</td></tr>";
            }

            if(i == data.length-1) {
                html += "<tr><td></td><td colspan='2' style='text-align:right;'>總計</td>"+
                    "<td></td><td align='center'>"+total_tax+"</td><td align='center'>"+total_h_tax+"</td><td align='center'>"+total_hour_fee+"</td><td align='center'>"+total_traffic_fee+"</td><td align='center'>"+total_aftertax+"</td></tr>";
                
                html += '<table cellspacing="0" cellpadding="0" width="100%" class="t1"><tr><td height="60" class="t2" align="left"><font size="3">備註：<br>1.稅額：\
                    <u>'+total_tax+'</u>元 ,請入台北富邦商業銀行公庫處(0122102-210550200715)<br>戶名：臺北市政府公務人員訓練處各項費款代扣繳專戶(代碼：A2153)\
                    <br>2.補充保費：<u>'+total_h_tax+'</u>元，請入台北富邦商業銀行公庫處（0122102-210550200711）<br>戶名：臺北市政府公務人員訓練處健保費代扣繳專戶（代碼：A0730）<br></font></td></tr></talbe>';
            }
        }

        html += "</tbody></table></div>";
    }

    console.log("A")

    // document.getElementById("printTable").innerHTML = html;
    let newWin= window.open("");

    if(!hasIllegalChar(html)){
        newWin.document.write(html);
    } else {
        newWin.document.write('error');
    }
}
</script>
