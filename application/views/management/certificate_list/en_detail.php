<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="actSave" role="form" method="POST" action="<?=$save_url;?>" class="form-inline"  target="_blank">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" id="doActionImport" name="doActionImport" value="">
                    <input type="hidden" id="seq_no" name="seq_no" value="<?=$detail_data['seq_no'];?>">
                    <input type="hidden" id="range_real" name="range_real" value="<?=$detail_data['range_real'];?>">
                    <input type="hidden" id="temp_list_cer_text"  value="">
                    <input type="hidden" id="view_one" name="view_one" value="">
                    <div class="row">
                        <div class="col-xs-12" style="width: auto; font-size: 12px;">
                            <div class="col-xs-3" style="width: auto;">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['year'];?>"disabled>
                            </div>    
                            <div class="col-xs-3" style="width: auto;">    
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_no'];?>"disabled>
                            </div>    
                            <div class="col-xs-3" style="width: auto;"> 
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_name'];?>"disabled>
                            </div>    
                            <div class="col-xs-3" style="width: auto;"> 
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['term'];?>"disabled>
                            </div> 
                        </div>
                        <div class="col-xs-12">
                            <BR>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="col-xs-12">
                                    <a type="button" onclick="go_back()" value="返回" class="btn btn-info">返回</a>
                                </div>
    
                                <input type="hidden" name="special_type" value="<?=$special_type;?>">																									 
                                <div class="col-xs-12">
                                    <label class="control-label"><font color="RED">*</font>書證名稱:</label>
                                    <input type="text" name="certificate_name" class="form-control" value="<?=$cer_list_data['cer_name'];?>">
                                </div>
                                <div class="col-xs-12">
                                    <label class="control-label"><font color="RED"></font>&nbsp書證文號:</label>
                                    <input type="text" name="post_certificate_number" class="form-control" value="<?=$cer_list_data['cer_number'];?>">
                                </div>
                                <div class="col-xs-12">
                                    <label class="control-label">&nbsp;發證單位:</label>
                                    <input type="text" name="cer_unit" class="form-control" value="<?=$cer_list_data['cer_unit'];?>">
                                </div>
                                <div class="col-xs-12">
                                    <label class="control-label"><font color="RED">*</font>發證日期:</label>
                                    <div class="input-daterange input-group" id="datepicker" >
                                    <input type="text" class="form-control" name="cer_date" id="datepicker1" value="<?=$cer_list_data['cer_date'];?>" readonly="readonly"/>
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                                                class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <label class="control-label"><font color="RED">*</font>書證樣版:</label>
                                    <select class="form-control" id="cer_type" name="cer_type" onchange="get_img_file_id()">
                                        <?php
                                            foreach ($type_list as $value) {
                                                $temp_select = $value["id"]==$cer_list_data['type_id']?'selected':'';
                                                echo '<option value="'.$value["id"].'"'.$temp_select.' >'.$value["title"].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <?php 
                                    if($cer_list_data['id']!=''){
                                        $top_text = $cer_list_data['qr_top_text'];
                                        $bottom_text = $cer_list_data['qr_bottom_text'];
                                    }else{
                                        $top_text = $type_list[0]['qr_top_text'];
                                        $bottom_text = $type_list[0]['qr_bottom_text'];
                                    }
                                ?>

                                <div class="col-xs-12">
                                    <label class="control-label">&nbsp;QRcode上方文字:</label>
                                    <input type="text" name="qr_top_text" class="form-control" value="<?=htmlspecialchars($top_text,ENT_HTML5|ENT_QUOTES)?>">
                                </div>
                                <div class="col-xs-12">
                                    <label class="control-label">&nbsp;QRcode下方文字:</label>
                                    <input type="text" name="qr_bottom_text" class="form-control" value="<?=htmlspecialchars($bottom_text,ENT_HTML5|ENT_QUOTES)?>">
                                </div>
                                <div class="col-xs-12">
                                    <label class="control-label"><font color="RED">*</font>書證文字內容:</label>
                                    <?php 
                                    if($cer_list_data['id']!=''){
                                        $demo_text = $cer_list_data['cer_text'];
                                    }else{
                                        $demo_text = $type_list[0]['demo_text'];
                                    }
                                    ?>
                                    <textarea class="form-control"  id="demo_text" name="demo_text" rows="3" style="resize:none;width:95%;"><?=$demo_text;?></textarea>
                                </div>                            
                            </div>
                            <div class="col-xs-6">
                                <div class="col-xs-12">
                                    <table style="border-width:3px; border-style:  ridge; border-color:green; width:80%;" cellpadding="10" border='0'>
                                        <tr><td>&nbsp文字可用參數列舉供參</td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp<<課程年度>></td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp<<期別>></td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp<<時數>></td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp<<開訓日期>></td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp<<結訓日期>></td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp&lt;BR&gt;為換下一行</td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp&lt;b&gt;為粗體字的開始</td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp&lt;/b&gt;為粗體字的結束<font color="red">(請檢查<結束>前一定要有<開始>)</font></td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp&lt;I&gt;為斜體字的開始</td></tr>
                                        <tr><td style="background-color:#FFFFFF;">&nbsp&lt;/I&gt;為斜體字的結束<font color="red">(請檢查<結束>前一定要有<開始>)</font></td></tr>
                                    </table>
                                </div>  
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-5">
                            
                            </div>
                            <div class="col-xs-7">
                                <div class="row" style="margin:20px 0px 0px 0px">
                                    <a type="button" onclick="view()" class="btn btn-info">書證預覽</a> (請設定左列項目再預覽)
                                </div>
                            </div>      
                        </div> 

                        <div class="col-xs-12">
                            <a type="button" onclick="save()" value="匯入" class="btn btn-info">儲存</a>
                            <!-- <a class="btn btn-info" target="_block" href="<?=base_url('files/example_files/12b.csv');?>">證書PDF下載</a> -->
                            <a class="btn btn-info" onclick="download_cer()">書證下載</a>
                        </div>
                        <div class="col-xs-12">
                            <BR>
                        </div>
                    </div>

                <!-- </form> -->
                <!-- /.table head -->
                <table class="table table-bordered table-striped table-condensed form-inline"  >
                    <!-- <form id="actSave" method="POST" action="<?=$save_url;?>" > -->
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" id="doAction" name="doAction" value="">
                    <thead>
                        <tr height="30">
                        <!--
                            <td width="80" align="center" bgcolor="#5D7B9D"><font color="#ffffff">功能</font></td>
                         -->
                            <?php
                                if(true) {
                                    echo "<td align=\"center\" width=\"8%\" bgcolor=\"#5D7B9D\"><font color=\"#ffffff\"><input type=\"checkbox\" id=\"checkAll\" name=\"checkAll\" value=\"\">可發證人員</font></td>";
                                }
                            ?>
                            <!-- <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">□可發證人員</font></td> -->
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">學號</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">服務機關</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">英文姓名</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">總成績</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">名次</font></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $col='';
                        foreach ($model as $row) {
                            $col = ($col == '#ffffff') ? '#dcdcdc' : '#ffffff';
                            echo '<tr class="score_row">';
                            //echo '  <td align="center" bgcolor="' . $col . '">';
                            //echo '      <input type="button" class="button" value="修改" onclick=upd("' . $row['id'] . '")>';
                            //echo '  </td>';
                            if(true) {

                                if ($row['listData']["yn_sel"]=='4'||$row['listData']["yn_sel"]=='5') //5:取消報名, 4:退訓
                                {
                                    $col = '#FF69B4';   // ping color
                                }
                                echo '  <td align="center" bgcolor="' . $col . '"><input type="checkbox" class="checkbox1" name="chkPerson['.$row['id'].']" '.$cer_check[$row['id']].' ></td>';
                            }
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['st_no'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['beaurau_name'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['title_name'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['name'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['en_name'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['s1'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '"><input type="text" size="3" class="main_score" name="scoreInfo_Rank['.$row['id'].']" value="'.$rank_data[$row['id']].'"></td>';
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                    </form>
                    <?php //var_dump($model[0]); ?>
                </table>
                <div class="col-xs-12">
                    <a type="button" onclick="save()" value="匯入" class="btn btn-info">儲存</a>
                    <a type="button" onclick="go_back()" value="返回" class="btn btn-info">返回</a>
                </div>
                <form id="view_cer"  role="form" method="POST" action="<?=$view_cer_url?>" target="_blank">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="cer_action" value="no_file">
                    <input type="hidden" name="certificate_number" value="">
                    <input type="hidden" name="content_text" value="">
                    <input type="hidden" name="unit" value="">
                    <input type="hidden" name="bg_file_name" value="<?=$file_name['bg_file_name'];?>">
                    <input type="hidden" name="signature_file_name" value="<?=$file_name['signature_file_name'];?>">
                    <input type="hidden" name="seal_file_name" value="<?=$file_name['seal_file_name'];?>">
                    <input type="hidden" name="bg_path" value="0">
                    <input type="hidden" name="signature_path" value="0">
                    <input type="hidden" name="seal_path" value="0">
                    <input type="hidden" name="qrcode_top_text" value="">
                    <input type="hidden" name="qrcode_bottom_text" value="">
                </form>
                <div id="ttt"></div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
    $("#checkAll").change(function(){
        $(".checkbox1").prop('checked', $(this).prop("checked"));
    });

function download_cer(){
    $('#view_one').val('');
    url = '<?=base_url('management/certificate_list/admin_download_en_cer_pdf')?>';
    var ck_box = [];
    /*
    $("input:checkbox:checked").each(function(index, value){     
        ck_box[index] = $(this).prop('name');
　　    alert(ck_box[index]);     
    }); 
    */
    $('#actSave').prop('action','https://dcsdcourse.taipei.gov.tw/base/admin/management/certificate_list/admin_download_en_cer_pdf');
    $('#actSave').prop('target','_blank');
    obj = document.getElementById("actSave");
    obj.submit();
}

function view(){
    $('#view_one').val('view_one');
    url = '<?=base_url('management/certificate_list/admin_download_en_cer_pdf')?>';
    var ck_box = [];
    /*
    $("input:checkbox:checked").each(function(index, value){     
        ck_box[index] = $(this).prop('name');
　　    alert(ck_box[index]);     
    }); 
    */
    $('#actSave').prop('action','https://dcsdcourse.taipei.gov.tw/base/admin/management/certificate_list/admin_download_en_cer_pdf');
    $('#actSave').prop('target','_blank');
    obj = document.getElementById("actSave");
    obj.submit();
}

function go_back(){
    document.location = '<?=base_url('management/certificate_list/cer_list/'.$detail_data['seq_no'])?>';
}

function save(){
    var errortext = "";
    if($("input[name='certificate_name']").val()==""){
        errortext += "書證名稱 未填寫\n"
    }
    // if($("input[name='post_certificate_number']").val()==""){
    //     errortext += "書證文號 未填寫\n"
    // }
    if($("input[name='cer_date']").val()==""){
        errortext += "發證日期 未填寫\n"
    }
    if($("#cer_type").val()==""){
        errortext += "書證樣版 未選取\n"
    }
    if($("#demo_text").val()==""){
        errortext += "書證文字內容 未填寫\n"
    }

    if (errortext != ""){
        alert(errortext);
    }else{
        
        $('#actSave').prop('action','<?=$save_url;?>');
        $('#actSave').prop('target','_self')
        document.all.doAction.value = 'save';   //mark (待修改)動作新增 或 修改
        obj = document.getElementById("actSave");
        obj.submit();
    }
}

function get_img_file_id(){
    var cer_type = $('#cer_type').val();
    var link  = '<?=base_url('management/certificate_list/get_img_file_id')?>';
            var data = {
                '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
                'fid': cer_type
            }
    $.ajax({
                url: link,
                data: data,
                dataType: 'json',
                type: "POST",
                async :false,   //必須用同步傳送 否則無法return 很重要 很重要 很重要 
                error: function(xhr) {
                    alert('Ajax request error');
                },
                success: function(json) {
                    $("input[name='bg_file_name']").val(json.bg_file_name); //背景檔案名稱
                    $("input[name='signature_file_name']").val(json.signature_file_name); //簽字章檔案名稱
                    $("input[name='seal_file_name']").val(json.seal_file_name); //官防檔案名稱
                    $("input[name='bg_path']").val('0'); //背景檔案路徑種類
                    $("input[name='signature_path']").val('0'); //簽字章檔案路徑種類
                    $("input[name='seal_path']").val('0'); //官防檔案路徑種類
                    $('#demo_text').text(json.demo_text); //更新書證內容
                    $("input[name='qr_top_text']").val(json.qr_top_text);
                    $("input[name='qr_bottom_text']").val(json.qr_bottom_text);																										 
                }
            });
    //alert(cer_type);
}

function view_cer(){
        $("input[name='certificate_number']").val($("input[name='post_certificate_number']").val()); //書證文號
        $("input[name='content_text']").val($('#demo_text').val()); //書證文字範本
        $("input[name='unit']").val($("input[name='cer_unit']").val()); //發證單位
        $("input[name='qrcode_top_text']").val($("input[name='qr_top_text']").val()); 
        $("input[name='qrcode_bottom_text']").val($("input[name='qr_bottom_text']").val());           
        obj = document.getElementById("view_cer");
        obj.submit();	

}            

$(document).ready(function() {
    $("#datepicker1").datepicker();
    $('#datepicker2').click(function(){
        //$( "#datepicker1" ).datepicker( "option", "dateFormat", "yy/mm/dd" );
        $("#datepicker1").focus();
    });

});


</script>