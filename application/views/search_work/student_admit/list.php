<!-- <?php print_r($datas)?> -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                    <form id="form" method="GET">
                        <input hidden id='syear' name='year' value="">                      
                        <input hidden id='sclassno' name='classno' value="0">                      
                        <input hidden id='stype' name='type' value="">
                        <input hidden id='sclassname' name='classname' value="">
                        <input hidden id='sapplyunit' name='applyunit' value="">
                        <input hidden id='siscsv' name='iscsv' value="0">
                        <input hidden id='srows' name='rows' value="0">
                        <input hidden id='sact' name='act' value="">
                    </form>
                <div id="filter-form" role="form" class="form-inline">
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="control-label">年度:</label>
                            <select id='year'>
                            <?php foreach ($choices['query_year'] as $year): ?>
                                <option value='<?= $year?>' <?= $sess_year == $year ?"selected":"" ?> ><?= $year;?></option>
                            <?php endforeach?>
                            </select>
                            <label class="control-label">班期代碼:</label>
                            <input id="classno" type="text" value="<?= $sess_classno?>" class="form-control">
                            <label class="control-label">班期名稱:</label>
                            <input id="classname" type="text" value="<?= $sess_classname?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">錄取狀態:</label>
                            <select id="type">
                                <option value="3" <?= $sess_type == 3 ?"selected":"" ?> >錄取名冊</option>
                                <option value="2" <?= $sess_type == 2 ?"selected":"" ?> >未錄取名冊</option>
                            </select>
                            <label class="control-label">報名機關:</label>
                            <input id="applyunit" type="text" value="<?= $sess_applyunit?>" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="Search" class="btn btn-info btn-sm">查詢</button>
                            <!-- <button id="csv" class="btn btn-info btn-sm">匯出</button> -->
                            <button id="print" class="btn btn-info btn-sm">列印</button>
                            <button id="clean" class="btn btn-info btn-sm">清除</button>
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
                <p>本表的「應完成日」為開課起日再加七天，本表的「實際完成日」乃為帶班情形一覽表中，設定「帶班完成」之日期</p>
                <!-- /.table head -->
                <table border="1" id="printTable" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="12">
                                <?php if($sess_type == 3)  {?>
                                    班期學員錄取名單
                                <?php }else{?>
                                    班期學員未錄取名單
                                <?php }?>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-center">機關</th>
                            <th class="text-center">年度</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">開班起日</th>
                            <th class="text-center">開班迄日</th>
                            <th class="text-center">承辦人(分機)</th>
                            <th class="text-center">時數</th>
                            <!-- <th class="text-center">
                                <?php if($sess_type == 3)  {?>
                                    局處錄取人數
                                <?php }else{?>
                                    局處未錄取人數
                                <?php }?>
                            </th> -->
                            <th class="text-center">
                                <?php if($sess_type == 3)  {?>
                                    總錄取人數
                                <?php }else{?>
                                    總未錄取人數
                                <?php }?>
                            </th>
                            <th class="text-center">
                                <?php if($sess_type == 3)  {?>
                                    錄取名冊
                                <?php }else{?>
                                    未錄取名冊
                                <?php }?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        <?php foreach ($datas as $data): ?>
                        
                        <tr class="text-center">
                            <td><?= $data["description"]?></td>
                            <td><?= $data["year"]?></td>
                            <td><?= $data["class_no"]?></td>
                            <td><?= $data["term"]?></td>
                            <!-- <td><a href="#" onclick="open_schedule('<?= $data['year']?>','<?= $data['class_no']?>','<?= $data['term']?>')"><?= $data["class_name"]?></a></td> -->
                            <td>
                                <a title="連結至課程表"
                                    href="<?=base_url('create_class/print_schedule/print/'.$data["seq_no"].'?query_year='.$data["year"].'&query_class_no='.$data["class_no"].'&rows=10&query_class_name='.$data["class_name"])?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;"><?=$data["class_name"]?></a>
                            </td>
                            <td><?= substr($data["start_date1"],0,-8)?></td>
                            <td><?= substr($data["end_date1"],0,-8)?></td>
                            <td><?= $data["first_name"]?><?= ($data['ADD_VAL1']!=""?'(':"") . $data['ADD_VAL1'] . ($data['ADD_VAL1']!=""?')':"")?></td>
                            <td><?= $data["range"]?></td>
                            <!-- <td><?= $data["apply_count"]?></td> -->
                            <td><?= $data["tapply_count"]?></td>
                            <td>
                                <a href="#" onclick="open_record('<?= $data['year']?>','<?= $data['class_no']?>','<?= $data['term']?>','<?= $sess_type?>')">名冊</a>
                            </td>
                            <!-- <td>
                                <a title="名冊"
                                    href="/base/admin/student_list_pdf.php?uid=55&tmp_seq=0&ShowRetirement=1&year=<?= $data["year"]?>&class_no=<?= $data["class_no"]?>&term=<?= $data["term"]?>"
                                    onclick="window.open(this.href, 'Print_Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">名冊</a>
                            </td> -->

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
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script>
function sendFun(){
    if(window.location.href.indexOf("?") != -1) {
        $('#Search').click();
    }
}
function open_record(year,class_no,term,type){
        var link = "<?=$link_refresh;?>";
		window.open(link+'?p=1&act=detail&year=' + year+'&class_no='+class_no+'&term='+term+'&type='+type,'deatil','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=768,width=1000');
	}
function open_schedule(year,class_no,term){
        var link = "<?=$link_refresh;?>";
		window.open(link+'?act=schedule&tmp_seq=0&year=' + year+'&class_no='+class_no+'&term='+term,'deatil','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=768,width=1000');
	}
$(document).ready(function() {
    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });

    $('#Search').click(function(){

       
        $('#syear').val($('#year').val());
        
        $('#sclassno').val($('#classno').val());
        $('#stype').val($("#type").val());
        $('#sclassname').val($('#classname').val());
        $('#sapplyunit').val($('#applyunit').val());
        $('#siscsv').val(0);
        $('#srows').val($('select[name=rows]').val());
        $('#sact').val("search");
        $( "#form" ).submit();
    });
    $('#print').click(function(){
        printData("printTable");
    });
    $('#clean').click(function(){
        $('#year').val("108");
        $('#classno').val("");
        $("#type").val("3");
        $('#classname').val("");
        $('#applyunit').val("");
    });
    // $('#csv').click(function(){

       
    //     $('#syear').val($('#year').val());
        
    //     $('#sclassno').val($('#classno').val());
    //     $('#stype').val($("#type").val());
    //     $('#sclassname').val($('#classname').val());
    //     $('#sapplyunit').val($('#applyunit').val());
    //     $('#siscsv').val(1);
    //     $( "#form" ).submit();
    // });

    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){  
    $("#datepicker1").focus();   
  });
  
});
</script>