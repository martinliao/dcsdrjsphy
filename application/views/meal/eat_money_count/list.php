<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="form" method="GET">
                        <input hidden id='stype' name='type' value="0">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='saction' name='action' value="">
                    </form>
                <div id="filter-form" role="form" class="form-inline">
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label" >切換週期:</label>
                            <button class="btn btn-info" onclick="fowardweek(-7);"><<<</button>
                            <button class="btn btn-info" onclick="getCurrentWeek();">本週</button>
                            <button class="btn btn-info" onclick="fowardweek(7);" >>>></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">日期區間(起日):</label>
                            <div class="input-group" id="end_date" >
                                <input type="text" class="form-control datepicker" value="<?=$sess_start_date?>" id="datepicker1" name="end_date" >
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>

                            <label class="control-label">(迄日):</label>
                            <div class="input-group" id="end_date" >
                                <input type="text" class="form-control datepicker" value="<?=$sess_end_date?>" id="test1" name="end_date" >
                                <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">列印方式</label>
                            <select id="ptype">
                                <option value="0">合併列印</option>
                                <option value="1">班期統計</option>
                                <option value="2">場地外借</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="print1" class="btn btn-info btn-sm">列印</button>
                            <button id="checkbtn" class="btn btn-info btn-sm">檢核</button>
                            <button id="madebtn" class="btn btn-info btn-sm">產製預估人數</button>
                            <button id="print4" class="btn btn-info btn-sm">列印預估人數</button>
                        </div>
                    </div>
</div>
            </div>
        </div>
    </div>
    <!-- /.panel -->
</div>

<div hidden id="printTable">




</div>
<!-- /.col-lg-12 -->
<script type="text/javascript">
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

        $('#syear').val($('#year').val());

        $('#sseason').val($('#season').val());
        $('#stype').val(type);
        $('#sstartMonth').val($('#startMonth').val());
        $('#sendMonth').val($('#endMonth').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());

        $( "#form" ).submit();
    });

    $('#madebtn').click(function(){
        if($('#datepicker1').val() == "") {
            alert("起日不能為空！");
        }
        else if($('#test1').val() == "") {
            alert("迄日不能為空！");
        }
        else {
            var isMade = confirm('確定產製嗎?');

            if (isMade === true) {
                $('#stype').val($('#type').val());
                $('#sstart_date').val($('#datepicker1').val());
                $('#send_date').val($('#test1').val());
                $('#saction').val("made");

                $( "#form" ).submit();
            } 
            else {
                return;
            }
        }
    });

    $('#checkbtn').click(function(){
        if($('#datepicker1').val() == "") {
            alert("起日不能為空！");
        }
        else if($('#test1').val() == "") {
            alert("迄日不能為空！");
        }
        else {
            $('#stype').val($('#type').val());
            $('#sstart_date').val($('#datepicker1').val());
            $('#send_date').val($('#test1').val());
            $('#saction').val("check");

            $( "#form" ).submit();
        }
    });

    $('#print1').click(function(){
        if($('#datepicker1').val() == "") {
            alert("起日不能為空！");
        }
        else if($('#test1').val() == "") {
            alert("迄日不能為空！");
        }
        else {
            print1($('#datepicker1').val(),$('#test1').val(),"print1");
        }
   });

   $('#print4').click(function(){
        if($('#datepicker1').val() == "") {
            alert("起日不能為空！");
        }
        else if($('#test1').val() == "") {
            alert("迄日不能為空！");
        }
        else {
            print4($('#datepicker1').val(),$('#test1').val(),"print4");
        }
   });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });

});
function print1(s1,s2,action){

    let ptype = $("#ptype").val()
    console.log(ptype)
    
    var link = "<?=$link_refresh;?>";
    if(ptype == 0){
        window.location.href=link+"?d1="+s1+"&d2="+s2+"&action=print1";
        //ApiGet("eat_money_count?start_date="+s1+"&end_date="+s2+"&action=print1","print1")
    }
    else if(ptype == 1){
        // ApiGet("eat_money_count?start_date="+s1+"&end_date="+s2+"&action=print1","print1")
        window.location.href=link+"?d1="+s1+"&d2="+s2+"&action=print2";
    }else if(ptype == 2){
        // ApiGet("eat_money_count?start_date="+s1+"&end_date="+s2+"&action=print1","print1")
        window.location.href=link+"?d1="+s1+"&d2="+s2+"&action=print3";
    }

}

function print4(s1,s2,action){
    ApiGet("eat_money_count?start_date="+s1+"&end_date="+s2+"&action=print4","print4")
}

function ApiGet(url,name){
    $.ajax({
        async: false,
        url: url,
        type: "GET",
        dataType: "json",
        success: function (Jdata) {
            console.log(Jdata);
            if(name == "print1"){
                document.getElementById("printTable").innerHTML = "";
                createprint1html(Jdata);
                $("#printTable").show();
                printData("printTable");
                $("#printTable").hide();
            }
            else if(name == "print2"){
                document.getElementById("printTable").innerHTML = "";
                createprint2html(Jdata);
                $("#printTable").show();
                printData("printTable");
                $("#printTable").hide();
            }
            else if(name == "print3"){
                document.getElementById("printTable").innerHTML = "";
                createprint3html(Jdata);
                $("#printTable").show();
                printData("printTable");
                $("#printTable").hide();
            }
            else if(name == "print4"){
                document.getElementById("printTable").innerHTML = "";
                createprint4html(Jdata);
                $("#printTable").show();
                printData("printTable");
                $("#printTable").hide();
            }

        }
    });
}

function createprint1html(Jdata){

    let len1 = Jdata.sql1.length;
    let len2 = Jdata.sql2.length;
    var totAll1 = 0 ;
    var totAll2 = 0 ;
    var totAll3 = 0 ;

    html = "<center><table border='1' cellspacing='0' cellpadding='0'><tr><td nowrap='' align='center'><div style='width:60px'>承辦人</div></td>"+
    "";

    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
          html =html +"<td nowrap align='center' valign='top' width='50'>" +Jdata.sql1[i].worker_name+"</td>";
    }

    html = html +"</td>";



    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html + "<td align='center' rowspan='3' width='30'><div style='width:1em;'>長官暨講座</div></td>"+
    "<td align='center' rowspan='3' width='40'><div style='width:1em;'>請款人數</div></td>";

    for(let i = 0 ; i < Jdata.sql2.length; i++ ){
         html = html + "<td nowrap align='center' valign='top' width='80' colspan='2'>" +Jdata.sql1[i].worker_name+"</td>";
    }

    for(let i2 = 0 ; i2 < (5 - len2);i2++){
        html = html  +  "<td nowrap='' width='80' colspan='2'></td>";
    }

    html = html +"</tr>";


    html= html + "<tr><td align='center'>班期</td><td align='center'></td>";

    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
         html = html + "<td align='center' valign='top'><div style='height:100pxlayout-flow:vertical-ideographictext-align:left'>"+Jdata.sql1[i].class_name+  "第" + Jdata.sql1[i].term + "期</div></td>"

    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    for(let i = 0 ; i < Jdata.sql2.length; i++ ){
         html = html +  "<td align='center' valign='top' rowspan='2' colspan='2'><div style='height:100pxlayout-flow:vertical-ideographictext-align:left'>"+ Jdata.sql1[i].APP_REASON +"</div></td>";
    }

    for(let i2 = 0 ; i2 < (5 - len2);i2++){
        html = html  +  "<td rowspan='2' colspan='2'></td>";
    }

    html = html + "</tr>";

    html = html + "<tr><td align='center'>調訓人數</td><td align='center'></td>";
    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
     html = html + "<td align='center' valign='top'>"+Jdata.sql1[i].no_persons+"</td>"
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }
    html = html + "</tr>";


    html = html +"<tr><td align='center'>桌次</td><td align='center'></td>";
    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
     html = html + "<td align='center' valign='top'></td>";
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html + "<td align='center' width='30'></td><td align='center' width='40'></td>";

    for(let i = 0 ; i < Jdata.sql2.length; i++ ){
         html = html +  "<td align='center' colspan='2'></td>";
    }

    for(let i2 = 0 ; i2 < (5 - len2);i2++){
        html = html  +  "<td align='center' colspan='2'></td>";
    }

    html = html + "</tr>";


    for(let s = 0 ; s < Jdata.sql3.length; s++){
        html = html + "<tr><td align='center' rowspan='6'>"+Jdata.sql3[s].dt_nm+"<br> " +Jdata.sql3[s].cWeek+ " </td><td align='center' height='40' rowspan='2'>早餐</td>";
            for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
                html = html + "<td align='center' valign='top'>"+(Jdata.sql3[s].sub[b].m_name == null ? "":Jdata.sql3[s].sub[b].m_name) + "<br></td>";
            }
            for(let i = 0 ; i < (15 - len1);i++){
                html = html  +  "<td width='50'></td>";
            }
            html = html +"<td align='center' width='30'></td><td align='center' width='40'>&nbsp</td>"

            for(let s2 = 0 ; s2 < Jdata.sql2.length ;s2++){
                html = html + "<td align='center'></td><td align='center'></td>";
            }

            for(let i2 = 0 ; i2 < (5 - len2);i2++){
                html = html  + "<td align='center'></td><td align='center'></td>";
            }

            html = html + " </tr><tr>";
            let tot1 = 0;
            let tot2 = 0;

            for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
                html = html + "<td align='center' valign='top'>" + ((Jdata.sql3[s].sub[b].m_cnt == 0 || Jdata.sql3[s].sub[b].m_cnt == null) ?""  :(Jdata.sql3[s].sub[b].m_cnt + "<br>") ) + "</td>";
                if(Jdata.sql3[s].sub[b].m_teach_cnt != null && Jdata.sql3[s].sub[b].m_teach_cnt != ""){
                    tot1 = (tot1 + Jdata.sql3[s].sub[b].m_teach_cnt)
                }
                if( Jdata.sql3[s].sub[b].m_cnt != null && Jdata.sql3[s].sub[b].m_cnt != ""){
                    tot2 = (tot2 + Jdata.sql3[s].sub[b].m_cnt)
                }

            }

            for(let i = 0 ; i < (15 - len1);i++){
                html = html  +  "<td width='50'></td>";
            }

            totAll1 = totAll1 + (tot1 + tot2)

            html = html + "<td align='center' width='30'>" + (tot1 > 0 || tot1 == null ? "" :tot1) +"</td><td align='center' width='40'>"+(tot1+tot2)+"</td>";
            for(let s2 = 0 ; s2 < Jdata.sql2.length ;s2++){
                html = html + "<td align='center'></td><td align='center'></td>";
            }
            for(let i2 = 0 ; i2 < (5 - len2);i2++){
                html = html  + "<td align='center'></td><td align='center'></td>";
            }
            html = html + "</tr><tr><td align='center' height='40' rowspan='2'>午餐</td>"

            for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
                html = html + "<td align='center' valign='top'>" + ((Jdata.sql3[s].sub[b].l_name == 0 || Jdata.sql3[s].sub[b].l_name == null) ?""  :(Jdata.sql3[s].sub[b].l_name + "<br>") ) +"</td>";

            }

            for(let i = 0 ; i < (15 - len1);i++){
                html = html  +  "<td width='50'></td>";
            }

            html = html + "<td align='center' width='30'></td><td align='center' width='40'>&nbsp</td>"

            for(let s2 = 0 ; s2 < Jdata.sql2.length ;s2++){
                html = html + "<td align='center'></td><td align='center'></td>";
            }
            for(let i2 = 0 ; i2 < (5 - len2);i2++){
                html = html  + "<td align='center'></td><td align='center'></td>";
            }
            html = html + "</tr><tr>";

            tot1 = 0;
            tot2 = 0;

            for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
                html = html + "<td align='center' valign='top'>" + ((Jdata.sql3[s].sub[b].l_cnt == 0 || Jdata.sql3[s].sub[b].l_cnt == null) ?""  :(Jdata.sql3[s].sub[b].l_cnt + "<br>") ) + "</td>";
                if(Jdata.sql3[s].sub[b].l_teach_cnt != null && Jdata.sql3[s].sub[b].l_teach_cnt != ""){
                    tot1 = (tot1 + Jdata.sql3[s].sub[b].l_teach_cnt)
                }
                if( Jdata.sql3[s].sub[b].l_cnt != null && Jdata.sql3[s].sub[b].l_cnt != ""){
                    tot2 = (tot2 + Jdata.sql3[s].sub[b].l_cnt)
                }

            }
            for(let i = 0 ; i < (15 - len1);i++){
                html = html  +  "<td width='50'></td>";
            }

            totAll2 = totAll2 + tot1 + tot2;

            html = html + "<td align='center' width='30'>" + (tot1 > 0 || tot1 == null ? "" :tot1) +"</td><td align='center' width='40'>"+(tot1+tot2)+"</td>";

            for(let s2 = 0 ; s2 < Jdata.sql2.length ;s2++){
                html = html + "<td align='center'></td><td align='center'></td>";
            }

            for(let i2 = 0 ; i2 < (5 - len2);i2++){
                html = html  + "<td align='center'></td><td align='center'></td>";
            }

            html = html + " </tr><tr><td align='center' height='40' rowspan='2'>晚餐</td>";

            for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
                html = html + "<td align='center' valign='top'>" + ((Jdata.sql3[s].sub[b].d_name == 0 || Jdata.sql3[s].sub[b].d_name == null) ?""  :(Jdata.sql3[s].sub[b].d_name + "<br>") ) +"</td>";

            }
            for(let i = 0 ; i < (15 - len1);i++){
                html = html  +  "<td width='50'></td>";
            }
            html = html +"<td align='center' width='30'></td><td align='center' width='40'>&nbsp</td>";

            for(let s2 = 0 ; s2 < Jdata.sql2.length ;s2++){
                html = html + "<td align='center'></td><td align='center'></td>";
            }
            for(let i2 = 0 ; i2 < (5 - len2);i2++){
                html = html  + "<td align='center'></td><td align='center'></td>";
            }
            html = html + "</tr><tr>";

            tot1 = 0;
            tot2 = 0;

            for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
                html = html + "<td align='center' valign='top'>" + ((Jdata.sql3[s].sub[b].d_cnt == 0 || Jdata.sql3[s].sub[b].d_cnt == null) ?""  :(Jdata.sql3[s].sub[b].d_cnt + "<br>") ) + "</td>";
                if(Jdata.sql3[s].sub[b].d_teach_cnt != null && Jdata.sql3[s].sub[b].d_teach_cnt != ""){
                    tot1 = (tot1 + Jdata.sql3[s].sub[b].d_teach_cnt)
                }
                if( Jdata.sql3[s].sub[b].d_cnt != null && Jdata.sql3[s].sub[b].d_cnt != ""){
                    tot2 = (tot2 + Jdata.sql3[s].sub[b].d_cnt)
                }

            }
            for(let i = 0 ; i < (15 - len1);i++){
                html = html  +  "<td width='50'></td>";
            }
            totAll3 = totAll3 +  tot1 + tot2;

            html = html + "<td align='center' width='30'>" + (tot1 > 0 || tot1 == null ? "" :tot1) +"</td><td align='center' width='40'>"+(tot1+tot2)+"</td>";

            for(let s2 = 0 ; s2 < Jdata.sql2.length ;s2++){
                html = html + "<td align='center'></td><td align='center'></td>";
            }
            for(let i2 = 0 ; i2 < (5 - len2);i2++){
                html = html  + "<td align='center'></td><td align='center'></td>";
            }
    }

    html = html + "<tr><td align='center'>備註欄</td><td align='center'></td>";


    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
     html = html + "<td align='center' valign='top'></td>";
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html + "<td align='center' width='30'>早餐</td><td align='center' width='40'>"+ totAll1 + "</td></tr>";
    html = html + "<tr><td align='center'></td><td align='center'></td>";
    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
     html = html + "<td align='center' valign='top'></td>";
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html + "<td align='center' width='30'>午餐</td><td align='center' width='40'>"+ totAll2 + "</td></tr>"
    html = html + "<tr><td align='center'></td><td align='center'></td>";

    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
     html = html + "<td align='center' valign='top'></td>";
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html =html + "<td align='center' width='30'>晚餐</td><td align='center' width='40'>" + totAll3 + "</td></tr>";

    html = html + "<tr><td align='center'>&nbsp</td><td align='center'></td>";

    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
     html = html + "<td align='center' valign='top'></td>";
    }
    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html + "<td align='center' width='30'></td><td align='center' width='40'></td></tr>";

    html = html + "<tr><td align='center'>上課教室</td><td align='center'></td>";

    for(let i = 0 ; i < Jdata.sql1.length; i++ ){
     html = html + "<td align='center' valign='top'>"+Jdata.sql1[i].ROOM_CODE+"</td>";
    }
    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html + "<td align='center' width='30'>金額</td><td align='center' width='40'>" +Jdata.total[0].totalmon+"</td></tr>";

    html = html +"</table></center>";
  document.getElementById("printTable").innerHTML = html;

}

function createprint4html(Jdata){
    let len1 = Jdata.sql1.length;
    let len2 = Jdata.sql2.length;
    var totAll1 = 0 ;
    var totAll2 = 0 ;
    var totAll3 = 0 ;

    html = "<center><table border='1' cellspacing='0' cellpadding='0'><tr><td nowrap='' align='center'>"+
    "";

    html = html +"<tr><td nowrap align='center'><div style='width:60px'>承辦人</div></td><td align='center'><div style='width:60px'></div></td>"


    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"<td nowrap align='center' valign='top' width='50'>"+data.sql1[i].worker_name+"</td>"
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }
    html = html +"<td align='center' rowspan='3' width='30'><div style='width:1em'>長官暨講座</div></td><td align='center' rowspan='3' width='40'><div style='width:1em'>請款人數</div></td></tr>"
    html = html +"<tr><td align='center'>班期</td><td align='center'></td>";

    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +" <td align='center' valign='top'><div style='height:100pxlayout-flow:vertical-ideographictext-align:left'>"+data.sql1[i].class_name+"   第 " + data.sql1[i].term +" 期</div></td>"
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }
    html = html +"</tr><tr><td align='center'>調訓人數</td><td align='center'></td>";

    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"  <td align='center' valign='top'>"+data.sql1[i].no_persons+"</td>"
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html +"</tr><tr><td align='center'>桌次</td><td align='center'></td>";

    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"<td align='center' valign='top'></td>"
    }
    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }
    html = html +"<td align='center' width='30'></td><td align='center' width='40'></td></tr>";

    for(let s = 0 ; s < Jdata.sql3.length; s++){
        html = html +"<tr><td align='center' rowspan='6'> "+Jdata.sql3[s].dt_nm+"<br> " +Jdata.sql3[s].cWeek+ "</td>"
        html = html +"<td align='center' height='40' rowspan='2'>早餐</td>";
        for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
            html = html + "<td align='center' valign='top'>"+(Jdata.sql3[s].sub[b].m_name == null ? "":Jdata.sql3[s].sub[b].m_name) + "<br></td>";
        }
        for(let i = 0 ; i < (15 - len1);i++){
            html = html  +  "<td width='50'></td>";
        }
        html =html +" <td align='center' width='30'></td><td align='center' width='40'>&nbsp</td></tr><tr>";
        let tot1 = 0;
        let tot2 = 0;

        for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
            html = html + "<td align='center' valign='top'>"+(Jdata.sql3[s].sub[b].m_cnt == null ? "":Jdata.sql3[s].sub[b].m_cnt) + "<br></td>";
            if(Jdata.sql3[s].sub[b].m_teach_cnt != null && Jdata.sql3[s].sub[b].m_teach_cnt != ""){
                tot1 = (tot1 + Jdata.sql3[s].sub[b].m_teach_cnt)
            }
            if( Jdata.sql3[s].sub[b].m_cnt != null && Jdata.sql3[s].sub[b].m_cnt != ""){
                tot2 = (tot2 + Jdata.sql3[s].sub[b].m_cnt)
            }
        }
        for(let i = 0 ; i < (15 - len1);i++){
            html = html  +  "<td width='50'></td>";
        }


        totAll1 = totAll1 + (tot1 + tot2)

        html = html + "<td align='center' width='30'>" + (tot1 > 0 || tot1 == null ? "" :tot1) +"</td><td align='center' width='40'>"+(tot1+tot2)+"</td>";

        html = html + "</tr><tr><td align='center' height='40' rowspan='2'>午餐</td>"
        for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
            html = html + "<td align='center' valign='top'>"+(Jdata.sql3[s].sub[b].l_name == null ? "":Jdata.sql3[s].sub[b].l_name) + "<br></td>";
        }
        for(let i = 0 ; i < (15 - len1);i++){
            html = html  +  "<td width='50'></td>";
        }
        html =html +"<td align='center' width='30'></td><td align='center' width='40'>&nbsp</td></tr><tr>"
        tot1 = 0;
        tot2 = 0;

        for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
            html = html + "<td align='center' valign='top'>"+(Jdata.sql3[s].sub[b].l_cnt == null ? "":Jdata.sql3[s].sub[b].l_cnt) + "<br></td>";
            if(Jdata.sql3[s].sub[b].l_teach_cnt != null && Jdata.sql3[s].sub[b].l_teach_cnt != ""){
                tot1 = (tot1 + Jdata.sql3[s].sub[b].l_teach_cnt)
            }
            if( Jdata.sql3[s].sub[b].l_cnt != null && Jdata.sql3[s].sub[b].l_cnt != ""){
                tot2 = (tot2 + Jdata.sql3[s].sub[b].l_cnt)
            }
        }
        for(let i = 0 ; i < (15 - len1);i++){
            html = html  +  "<td width='50'></td>";
        }
        totAll2 = totAll2 + tot1+tot2;
        html = html + "<td align='center' width='30'>" + (tot1 > 0 || tot1 == null ? "" :tot1) +"</td><td align='center' width='40'>"+(tot1+tot2)+"</td>";
        html = html + "</tr><tr><td align='center' height='40' rowspan='2'>晚餐</td>";

        for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
            html = html + "<td align='center' valign='top'>"+(Jdata.sql3[s].sub[b].d_name == null ? "":Jdata.sql3[s].sub[b].d_name) + "<br></td>";
        }
        for(let i = 0 ; i < (15 - len1);i++){
            html = html  +  "<td width='50'></td>";
        }
        html =html +"<td align='center' width='30'></td><td align='center' width='40'>&nbsp</td></tr><tr>"
        tot1 = 0;
        tot2 = 0;

        for(let b = 0 ; b < Jdata.sql3[s].sub.length ;b++){
            html = html + "<td align='center' valign='top'>"+(Jdata.sql3[s].sub[b].d_cnt == null ? "":Jdata.sql3[s].sub[b].d_cnt) + "<br></td>";
            if(Jdata.sql3[s].sub[b].d_teach_cnt != null && Jdata.sql3[s].sub[b].d_teach_cnt != ""){
                tot1 = (tot1 + Jdata.sql3[s].sub[b].d_teach_cnt)
            }
            if( Jdata.sql3[s].sub[b].d_cnt != null && Jdata.sql3[s].sub[b].d_cnt != ""){
                tot2 = (tot2 + Jdata.sql3[s].sub[b].d_cnt)
            }
        }
        for(let i = 0 ; i < (15 - len1);i++){
            html = html  +  "<td width='50'></td>";
        }
        totAll2 = totAll2 + tot1+tot2;

        html = html + "<td align='center' width='30'>" + (tot1 > 0 || tot1 == null ? "" :tot1) +"</td><td align='center' width='40'>"+(tot1+tot2)+"</td></tr>";

    }

    html = html + "<tr><td align='center'>備註欄</td><td align='center'></td>"

    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"<td align='center' valign='top'></td>"
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html +"<td align='center' width='30'>早餐</td><td align='center' width='40'>" + totAll1 +"</td></tr>" 
    html = html +"<tr><td align='center'></td><td align='center'></td>"
    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"<td align='center' valign='top'></td>"
    }
    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }
    html = html +"<td align='center' width='30'>午餐</td><td align='center' width='40'>" + totAll2 +"</td></tr>" 
    html = html +"<tr><td align='center'></td><td align='center'></td>"
    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"<td align='center' valign='top'></td>"
    }
    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html +"<td align='center' width='30'>午餐</td><td align='center' width='40'>" + totAll3 +"</td></tr>" 

    html = html +"<tr><td align='center'>&nbsp</td><td align='center'></td>";
    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"<td align='center' valign='top'></td>"
    }
    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html +"<td align='center' width='30'></td><td align='center' width='40'></td></tr>"

    html =html +"<tr><td align='center'>上課教室</td><td align='center'></td>";

    for(i = 0 ; i < Jdata.sql1.length ; i++){
        html = html +"<td align='center' valign='top'>"+ Jdata.sql1[i].ROOM_CODE+"</td>"
    }

    for(let i = 0 ; i < (15 - len1);i++){
        html = html  +  "<td width='50'></td>";
    }

    html = html +"<td align='center' width='30'>金額</td><td align='center' width='40'>"+Jdata.total[0].totalmon+"</td></tr>";

    html = html +"</table></center>";
    document.getElementById("printTable").innerHTML = html;
}

</script>
<!-- <?=json_encode($datas)?> -->

