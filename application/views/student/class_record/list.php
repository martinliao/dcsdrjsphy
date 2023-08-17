<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline noprint">
                <input type="hidden" id="enter_id_number" name="enter_id_number" value=<?=$enter_id_number?>>
                <?php if($check_id == 50 || $check_id == 'WW' || $check_id == 10 || $check_id == 31 || $check_id == 32) {?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度:</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" name="query_class_no" value="<?=$filter['query_class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" name="query_class_name" value="<?=$filter['query_class_name']?>">
                            </div>
                            <?php if($check_id == 50 || $check_id == 10 || $check_id == 31 || $check_id =='WW'){?>
                            <div class="form-group">
                                <label class="control-label">機關名稱:</label>
                                <input type="text" class="form-control" name="query_bureau_name" value="<?=$filter['query_bureau_name']?>">
                            </div>
                            <?php }?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">學員姓名:</label>
                                <input type="text" class="form-control" name="query_student_name" value="<?=$filter['query_student_name']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">身分證字號:</label>
                                <input type="text" class="form-control" name="query_idno" value="<?=$filter['query_idno']?>">
                            </div>
                            
                        </div>
                        <div id="back" class="col-xs-12">
                            <button class="btn btn-info btn-sm" >查詢</button>
                            <a href="<?=$link_export?>" class="btn btn-info btn-sm" >匯出</a>
                            <button class="btn btn-info btn-sm" onclick="print_page();" >列印</button>
                            <a href="#" onclick="file_upload()" >公訓處表單下載區</a>
                        </div>
                    </div>
                <?php }else{?>
                    <div id="back" class="col-xs-12">
                        <a href="<?=$link_export?>" class="btn btn-info btn-sm" >匯出</a>
                        <button class="btn btn-info btn-sm" onclick="print_page();">列印</button>
                        <a href="#" onclick="file_upload()" >公訓處表單下載區</a>
                    </div>
                <?php }?>
                </form>
                
                <form method="post" id="list_form" name="list_form">
                <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>">
                <?php if(count($list)==0 && $enter_id_number!=''){?>
                    <span style="color:red;">查無資料</span>
                <?php }else{?>
            <div id="content-table">
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="11">臺北市政府公務人員訓練處   單一學員上課紀錄查詢</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="width:2%">學號</th>
                            <th class="text-center" style="width:5%">姓名</th>
                            <th class="text-center" style="width:23%">年度/班期名稱/期別</th>
                            <th class="text-center" style="width:5%">職稱</th>
                            <th class="text-center" style="width:10%">就職機關</th>
                            <th class="text-center" style="width:10%">報名機關</th>
                            <th class="text-center" style="width:12%">教室(課程表)</th>
                            <th class="text-center" style="width:5%">開課日期</th>
                            <th class="text-center" style="width:12%">異動表<br>上傳</th>
                            <th class="text-center" style="width:12%">書證下載</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($list as $row){?>
                    <?php if($row['year']!=''){?>
                        <tr class="text-center">
                            <td><?=$row['st_no']?></td>
                            <td><?=$row['pname']?></td>
                            <td><?=$row['year']?>年 <?=$row['class_name']?> (第<?=$row['term']?>期)</td>
                            <td><?=$row['name']?></td>
                            <td><!--<a   onClick=window.open("../info_detail.php?id=<?=$row['pname']?>&year=<?=$row['year']?>&name=<?=$row['class_id']?>&term=<?=$row['term']?>&userid=<?=$row['id']?>&classname=<?=$row['class_name']?>");return false></a>--><?=$row['bname']?></td>
                            <td><?=$row['unit_name']?></td>
                            <td><?=$row['room_code']?>
                                <!--<a id="schedule"title="連結至課程表" href="<?=base_url('create_class/print_schedule/print/').$row['seq_no']?>" 
                                    onclick="window.open(this.href, 'Class_Schedule_Detail','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,resizable=1,height=600,width=1050');return false;">課程表</a>-->

                               <a   onClick=window.open("../create_class/print_schedule/print/<?=$row['seq_no']?>");return false>課程表</a>


 <!--                                <a href="#"  onclick='printStudentList("<?=$row['year']?>","<?=$row['class_id']?>","<?=$row['term']?>","<?=$row['class_name']?>")'>名冊</a></td> -->
                               <a href='#' onclick='go_schedule_Register_update("<?=$row['year']?>","<?=$row['class_id']?>","<?=$row['term']?>","<?=$uid?>")'>名冊</a>

                            <td><?=substr($row['start_date1'],0,10)?></td>
                           
                            <td>
                                <a href="#" onClick=window.open("../student/class_record/modify_upload/<?=htmlspecialchars($row['seq_no'],ENT_HTML5|ENT_QUOTES)?>",'upload_modify',config='height=350,width=500,location=no')>上傳</a>
                                <?php if(!empty($row['filename']) && !empty($row['path'])) { ?>
                                    <br>
                                    <a href="../student/class_record/download/<?=htmlspecialchars($row['seq_no'],ENT_HTML5|ENT_QUOTES)?>"><?=htmlspecialchars($row['filename'],ENT_HTML5|ENT_QUOTES)?></a>
                                <?php } ?>
                            </td>
                            
                            <td>
                                <?php 
                                    if (is_array($cer_user_list[$row['seq_no']])){
                                        foreach ($cer_user_list[$row['seq_no']] as $datas) {
                                            //echo $datas['seq_no'];
                                            if($datas['category'] == '1'){
                                                echo '<a href="../management/certificate_list/download_cer_pdf/'.$datas['id'].'" target="_blank">'.$datas['cer_name'].'</a><BR>';
                                            } else if($datas['category'] == '2'){
                                                echo '<a href="../management/certificate_list/download_en_cer_pdf/'.$datas['id'].'" target="_blank">'.$datas['cer_name'].'</a><BR>';
                                            }          
                                         }
                                    }

                                    if (is_array($userOtherCert[$row['seq_no']])){
                                        foreach ($userOtherCert[$row['seq_no']] as $otherCert) {
                                            echo "<a href=\"".htmlspecialchars($otherCert['link'], ENT_HTML5|ENT_QUOTES)."\" download=\"".htmlspecialchars($otherCert['cer_name'], ENT_HTML5|ENT_QUOTES)."\">書證</a>";
                                        }
                                    } 
                                    
                                                                       
                                ?>
                            </td>
                        </tr>
                    <?php }?>
                    <?php }?>
                    </tbody>
                </table>
                <div style="text-align:right">
                    <p>列印時間：<?=$now?></p>
                </div>
                <?php }?>
            </div>
            </div>
            <!-- /.panel -->
        </div>
    </div>
</div>
<style>
@media print {
    a[href]:after{
        content: none;
    }
   
   .noprint{ 
        display: none;
        visibility: hidden; 
    } 
    #content-table a[href]:after{
        display: none
    }
}
</style>
<script>
function file_upload()
{
	var myW=window.open('https://dcsd.gov.taipei/News_Content.aspx?n=A87166D7FD0AAE7C&sms=64E43555801A6402&s=F75B9E71041B1331','checkview','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,height=800,width=1024');
    //var myW=window.open('http://www.dcsd.taipei.gov.tw/ct.asp?xItem=1121653&ctNode=32010&mp=122001','checkview','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,height=800,width=1024');
	myW.focus();
}
function printStudentList(strYEAR,strCLASS_NO,strTERM,strName){
    var myW=window.open ('../print_student_list_pdf.php?&year='+strYEAR+'&class_no='+strCLASS_NO+'&term='+strTERM+'&class_name='+strName+'&tmp_seq=0&ShowRetirement=1', 'newwindow', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function go_schedule_Register_update(strYEAR,strCLASS_NO,strTERM,uid){
    var myW=window.open ('<?=base_url("student_list_pdf.php")?>?uid='+uid+'&year='+strYEAR+'&class_no='+strCLASS_NO+'&term='+strTERM+'&tmp_seq=0&ShowRetirement=1', 'newwindow', 'height=768, width=1024, top=0, left=0, toolbar=no, menubar=no, scrollbars=YES, resizable=no,location=no, status=no');
    myW.focus();
}

function print_page(){   
   // document.getElementById('back').style.visibility = 'hidden'; 
 
    //window.print(); //列印剛才新建的網頁

    //document.getElementById('back').style.visibility = 'visible'; 


        document.getElementById('back').style.visibility = 'hidden'; 
        

        //document.title.style.visibility="hidden";
        var head_str = "<html><head><title></title></head><body>"; //先生成頭部
        var foot_str = "</body></html>"; //生成尾部
        var older = document.body.innerHTML;
        var new_str1 = document.getElementById('content-table').innerHTML;
        //var new_str2 = document.getElementById('edit_form').innerHTML;
        var old_str = document.body.innerHTML; //獲得原本頁面的程式碼
        //document.getElementById('header').style.display = 'none';
        //document.getElementById('footer').style.display = 'none';
        document.title = "　";
        //document.write(document.URL)="　";

        document.querySelector('footer').style = 'display: none'

        document.body.innerHTML = head_str + new_str1 +  foot_str; //構建新網頁
        //document.title = "";
        //document.url="";
        //document.body.innerHTML =  new_str1 + new_str2;
        window.print(); //列印剛才新建的網頁
        document.body.innerHTML = older; //將網頁還
        document.getElementById('back').style.visibility = 'visible'; 

}  
</script>