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
                        <input hidden id='syear' name='year' value="">
                        <input hidden id='stype' name='type' value="0">
                        <input hidden id='sseason' name='season' value="">
                        <input hidden id='smonth' name='month' value="">
                        <input hidden id='sfirstSeries' name='firstSeries' value="">
                        <input hidden id='ssecondSeries' name='secondSeries' value="">
                        <input hidden id='sstart_date' name='start_date' value="">
                        <input hidden id='send_date' name='end_date' value="">
                        <input hidden id='siscsv' name='iscsv' value="">
                        <input hidden id='sfilename' name='filename' value="">
                        <input hidden id='srows' name='rows' value="">
                        <input hidden id='checkArr' name='checkArr' value="">
                        <input hidden id='page' name='page' value="<?=$sess_page;?>">
                    </form>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <select id='year'>
                                <?php foreach ($choices['query_year'] as $year): ?>
                                    <option value='<?=$year?>' <?=$sess_year == $year ? "selected" : ""?> ><?=$year;?></option>
                                <?php endforeach?>
                                </select>
                                <label class="control-label">月份</label>
                                <select id='month'>
                                <option value=""><?=$choices['query_month'][''];?></option>
                                <?php for ($i = 1; $i < sizeof($choices['query_month']); $i++) {?>
                                    <option value="<?=$i;?>" <?=$sess_month == $i ? "selected" : ""?> ><?=$choices['query_month'][$i];?></option>
                                <?php }?>
                                </select>
                                <label class="control-label">季別</label>
                                <select id='season'>
                                    <option value=""><?=$choices['query_season'][''];?></option>
                                <?php for ($i = 1; $i < sizeof($choices['query_season']); $i++) {?>
                                    <option value="<?=$i;?>" <?=$sess_season == $i ? "selected" : ""?> ><?=$choices['query_season'][$i];?></option>
                                <?php }?>
                                </select>
                                <br/>
                                <label class="control-label">開課日期的查詢期間:</label>
                                <div class="input-group" id="start_date">
                                    <input type="text" class="form-control datepicker"  value="<?=$sess_start_date?>" id="datepicker1" name="start_date">
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
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">系列別</label>
                            <select name='query_year' id='firstSeries'>
                                    <option value="">請選擇</option>
                                <?php for ($i = 0; $i < sizeof($category); $i++) {?>
                                    <option value="<?=$category[$i]["ITEM_ID"];?>" <?=$sess_firstSeries == $category[$i]["ITEM_ID"] ? "selected" : ""?> ><?=$category[$i]["DESCRIPTION"];?></option>
                                <?php }?>

                            </select>
                            <label class="control-label">次類別</label>
                            <select name='query_year' id='secondSeries'>
                                <option value="">請選擇次類別</option>
                                <?php if ($sess_firstSeries == "B") {$count = 1;} else { $count = 0;}?>
                                <?php for ($i = 0; $i < sizeof($category[$count]['sub']); $i++) {?>
                                    <option value="<?=$category[$count]['sub'][$i]['item_id'];?>" <?=$sess_secondSeries == $category[$count]['sub'][$i]['item_id'] ? "selected" : ""?> ><?=$category[$count]['sub'][$i]['NAME'];?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
                            <button class="btn btn-info" onclick="ClearData()">清除日期</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">匯出檔案名:</label>
                            <input id="filename" type="text" class="form-control">
                            <button id="csv" class="btn btn-info">匯出</button>
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
                            <th>本頁全選<input type="checkbox" name="checkedAll" onclick="checkAll(this,'checked');"></th>
                            <th class="text-center">系列別</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">開課起迄日</th>
                            <th class="text-center">研習時數</th>
                            <th class="text-center" style="width: 10%">研習對象</th>
                            <th class="text-center">課程內容</th>
                            <th class="text-center">講座</th>
                            <th class="text-center">承辦人/分機</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        <?php 
                            $sizecount=1;
                            if(sizeof($data["listArrange"])!=0) {
                                $sizecount=sizeof($data["listArrange"]);
                            }  
                        ?>
                        <tr class="text-center">
                            <td rowspan='<?=$sizecount?>'><input type="checkbox" name="checked" value="<?=$data['seq_no']?>"></td>
                            <td rowspan='<?=$sizecount?>'><?=$data["DESCRIPTION"]?></td>
                            <td rowspan='<?=$sizecount?>'><?=$data["year"]?></td>
                            <td rowspan='<?=$sizecount?>'><?=$data["class_name"]?></td>
                            <td rowspan='<?=$sizecount?>'><?=$data["term"]?></td>
                            <td rowspan='<?=$sizecount?>'><?=substr($data["start_date1"],0,-8) . " ~ " . substr($data["end_date1"],0,-8)?></td>
                            <td rowspan='<?=$sizecount?>'><?=$data["range"]?></td>
                            <td rowspan='<?=$sizecount?>'><?=$data["respondant"]?></td>
                            <?php if(sizeof($data["listArrange"])!=0) { 
                                $first = array_shift($data["listArrange"]);
                            ?>
                                
                                <td><?=$first['DESCRIPTION']?></td>
                                <td><?=$first['teacher_list']?></td>
                            <?php } else {?>
                                <td></td>
                                <td></td>
                            <?php } ?>
                            <td rowspan='<?=$sizecount?>'>
                            <?php
                                echo $data["worker_name"].'/'.$data["ext1"];
                            ?>
                            </td>
                        </tr>
                            <?php foreach ($data["listArrange"] as $key => $value) { ?>
                                <tr class="text-center">
                                    <td><?=$value["DESCRIPTION"]?></td>
                                    <td><?=$value["teacher_list"]?></td>
                                </tr>
                            <?php } ?>
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
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
function sendFun(){
    let count = 0;
    let type = 0;
    if($('#season').val() !=""){
        count++;
        type = 1;
    }
    if($('#month').val() !="" ){
        count++;
        type = 2;
    }
    if($('#datepicker1').val() !="" || $('#test1').val() !=""){
        count++;
        type = 3;
    }
    if(count > 1){
        alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
        return;
    }
    else if(count == 1) {
        $('#Search').click();
    }
}

$(document).ready(function() {
  $("#test1").datepicker();
  $('#test2').click(function(){
    $("#test1").focus();
  });

  $('#Search').click(function(){
        let count = 0;
        let type = 0;
        if($('#season').val() !=""){
            count++;
            type = 1;
        }
        if($('#month').val() !="" ){
            count++;
            type = 2;
        }
        if($('#datepicker1').val() !="" || $('#test1').val() !=""){
            count++;
            type = 3;
        }
        if(count > 1){
            alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
            return;
        }

        $('#syear').val($('#year').val());

        $('#sseason').val($('#season').val());
        $('#stype').val(type);
        $('#smonth').val($('#month').val());
        $('#sfirstSeries').val($('#firstSeries').val());
        $('#ssecondSeries').val($('#secondSeries').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(0);
        $('#sfilename').val("");
        $('#srows').val($('select[name=rows]').val());

        $( "#form" ).submit();
    });

  $("#datepicker1").datepicker();
  $('#datepicker2').click(function(){
    $("#datepicker1").focus();
  });

  $('#csv').click(function(){
        let count = 0;
        let type = 0;
        if($('#season').val() !=""){
            count++;
            type = 1;
        }
        if($('#month').val() !="" ){
            count++;
            type = 2;
        }
        if($('#datepicker1').val() !="" || $('#test1').val() !=""){
            count++;
            type = 3;
        }
        if(count > 1){
            alert("請只填寫一項查詢區間:\n選了季就不能選月/日.\n選了月就不能選季/日.\n選了日就不能選月/季");
            return;
        }

        if($('#filename').val() == ""){
            alert("請輸入檔案名稱");
            return;
        }

        var countLength = 0;
        var checkArr = '';
        var checkboxs=document.getElementsByName('checked');
        
        for(var i=0;i<checkboxs.length;i++)
        {
            if(checkboxs[i].checked) {
                checkArr += checkboxs[i].value + ',';
            }
            else {
                countLength++;
            }
        }

        if(countLength == checkboxs.length) {       // 沒有勾選，全匯
            checkArr = 'all';
        }
        

        $('#syear').val($('#year').val());
        $('#sseason').val($('#season').val());
        $('#stype').val(type);
        $('#smonth').val($('#month').val());
        $('#sfirstSeries').val($('#firstSeries').val());
        $('#ssecondSeries').val($('#secondSeries').val());
        $('#sstart_date').val($('#datepicker1').val());
        $('#send_date').val($('#test1').val());
        $('#siscsv').val(1);
        $('#sfilename').val($('#filename').val());
        $('#sfilename').val();
        $('#srows').val($('select[name=rows]').val());
        $('#checkArr').val(checkArr);

        $( "#form" ).submit();
    });

  $( "#firstSeries" ).change(function() {
      var data = <?=json_encode($category)?>;
      for(let i = 0 ; i < data.length;i++){
          if(this.value == data[i].ITEM_ID)
          {
            changeSeSelecter(data[i].sub);
          }
      }
    });
});

function changeSeSelecter(data){
    document.getElementById('secondSeries').innerHTML = "";
    var html = "<option value=''>請選擇次類別</option>";
    for(let i =0 ; i < data.length;i++){
        html = html + "<option value="+data[i]['item_id']+">"+data[i]['NAME']+"</option>"
    }
    document.getElementById('secondSeries').innerHTML = html;
}
function checkAll(id,name)
{
    var checkboxs=document.getElementsByName(name);
    for(var i=0;i<checkboxs.length;i++)
    {
        checkboxs[i].checked=id.checked;
    }
}

</script>