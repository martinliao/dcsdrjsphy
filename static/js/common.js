//查詢作業
    //>>設定今天
function setToday(){
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,0);
    edate = addDays(sdate,0);
    document.getElementById('datepicker1').value = sdate;
    document.getElementById('test1').value = edate;
}

    //>>清除日期
function ClearData(){
    $('#datepicker1').val('')
    $('#test1').val('')
}

//多筆日期
    //>>設定今天
function setToday1(i){
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,0);
    edate = addDays(sdate,0);
    document.getElementById('datepicker'+i).value = sdate;
    document.getElementById('test'+i).value = edate;
}

    //>>前、後一週
function fowardweek1(days,i)
{   
    var date1 = document.getElementById('datepicker'+i).value;
    var date2 = document.getElementById('test'+i).value;
    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        document.getElementById('datepicker'+i).value = sdate; 
        document.getElementById('test'+i).value = edate;
    }
    else
    {
        var today = getCurrentWeek(i);
    }
}

    //>>本週
function getCurrentWeek1(i){
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,-diff);
    edate = addDays(sdate,6);
    document.getElementById('datepicker'+i).value = sdate;
    document.getElementById('test'+i).value = edate;
}


//-----

function fowardweek(days)
{
    var date1 = document.getElementById('datepicker1').value;
    var date2 = document.getElementById('test1').value;
    if(date1!="" && date2!="")
    {
        sdate = addDays(date1,days);
        edate = addDays(date2,days);
        document.getElementById('datepicker1').value = sdate; 
        document.getElementById('test1').value = edate;
    }
    else
    {
        var today = getCurrentWeek();
    }
}

function getCurrentWeek(){
    var today = new Date();
    var d = today.getDay();
    var diff = 6;
    if(d>0){
        diff = d-1;
    }
    sdate = addDays(today,-diff);
    edate = addDays(sdate,6);
    document.getElementById('datepicker1').value = sdate;
    document.getElementById('test1').value = edate;
}

function printData(tableid)
{
   var divToPrint=document.getElementById(tableid);
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.print();
   newWin.close();
}