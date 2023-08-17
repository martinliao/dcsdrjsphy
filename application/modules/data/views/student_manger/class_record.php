<div class="row">
        <div class="panel panel-default">
            <!-- /.panel-heading -->
            <!-- <div class="panel-body"> -->
                <a href="<?=$link_export?>" class="btn btn-info btn-sm" >匯出</a>
                <button class="btn btn-info btn-sm noprint" onclick="print_page();">列印</button>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="9">臺北市政府公務人員訓練處   單一學員上課紀錄查詢</th>
                        </tr>
                        <tr>
                            <th class="text-center">學號</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">年度/班期名稱/期別</th>
                            <th class="text-center">職稱</th>
                            <th class="text-center">現職單位</th>
                            <th class="text-center">報名單位</th>
                            <th class="text-center">教室(課程表)</th>
                            <th class="text-center">開課日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($list as $row){?>
                    <?php if($row['year']!=''){?>
                        <tr>
                            <td><?=$row['st_no']?></td>
                            <td><?=$row['pname']?></td>
                            <td><?=$row['year']?>年 <?=$row['class_name']?> (第<?=$row['term']?>期)</td>
                            <td><?=$row['name']?></td>
                            <td><?=!empty($row['bname'])?$row['bname']:$row['bname2']?></td>
                            <td><?=$row['unit_name']?></td>
                            <td><?=$row['room_code']?>
                                <a title="連結至課程表" href="<?=base_url('create_class/print_schedule/print/').$row['seq_no']?>" 
                                    onclick="window.open(this.href, 'Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">課程表</a>
                                <a href="#"  onclick='printStudentList("<?=$row['year']?>","<?=$row['class_id']?>","<?=$row['term']?>","<?=$row['class_name']?>")'>名冊</a></td>
                            <td><?=substr($row['start_date1'],0,10)?></td>
                        </tr>
                    <?php }?>
                    <?php }?>
                    </tbody>
                </table>
                <div style="text-align:right">
                    <p>列印時間：<?=$now?></p>
                </div>
            <!-- </div> -->
            <!-- /.panel -->
        </div>
</div>
<style>
@media print {
    a[href]:after {
      display: none;
      visibility: hidden;
   }
   
   .noprint{ 
        display: none;
        visibility: hidden; 
    } 
}
</style>
<script>
function printStudentList(strYEAR,strCLASS_NO,strTERM,strName){
    var myW=window.open ('../../../student_list_pdf.php?&year='+strYEAR+'&class_no='+strCLASS_NO+'&term='+strTERM+'&class_name='+strName+'&tmp_seq=0&ShowRetirement=1', 'newwindow', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function print_page(){   
    $(".footer").addClass("noprint");
    $(".col-lg-7").addClass("noprint");
    document.title = '學員上課紀錄';
    window.print(); //列印剛才新建的網頁
}  
</script>