<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">30O 書證管理區(新增/修改書證版型)
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="add_type" name="add_type" role="form" method="POST" action="<?=$save_url;?>" class="form-inline" enctype="multipart/form-data">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" id="doActionImport" name="doActionImport" value="">
                    <input type="hidden" id="action" name="action" value="">
                    <input type="hidden" id="seq_no" name="seq_no" value="<?=$detail_data['seq_no'];?>">
                    <input type="hidden" id="range_real" name="range_real" value="<?=$detail_data['range_real'];?>">
                    <div class="row">
                        <div class="col-xs-12">
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-4">
                                <div class="row">
                                    <label class="control-label"><span style="color:RED">*</span>書證版型名稱:</label>
                                    <input type="text" name="title" class="form-control" value="<?=htmlspecialchars($type_data['title'], ENT_QUOTES);?>">
                                </div>
                                <BR>                             
                                <div class="row">
                                    <label class="control-label">發證單位:</label>
                                    <input type="text" name="cer_unit" class="form-control" value="<?=$detail_data['cer_unit'];?>">
                                </div>



                                <div class="row" style="margin:20px 0px 0px 0px;display: inline-block;">
                                    <div class="row" style="float:left; margin-right:20px">
                                        <label class="control-label">選擇邊框:</label>
                                            <select class="form-control" name="bg_select">
                                                <option value="-1">新增</option>
                                                <?php
                                                foreach ($bg_select_option as $value) {
                                                    $bg_selected = $type_data['bg_file_id']==$value['id']?'selected':'';
                                                    echo '<option value="'.$value['id'].'" '.$bg_selected.'>'.$value['title_name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                    </div>
                                    <div class="row" style="float:left">
                                    <a type="button" onclick="delete_file('bg')" class="btn btn-danger">刪除</a>
                                    </div>
                                    <div class="row" style="float:left;width:1500px">    
                                        <label class="control-label">上傳新邊框:　檔案類型:jpg</label>
                                        <input type="file" class="custom-file-input" name="bg_file">
                                    </div>
                                    <div style="clear:both;"></div>
                                    <!--
                                    <div class="row">    
                                        <label class="control-label">新邊框名稱:</label>
                                        <input type="text" class="custom-file-input" name="bg_file_name">
                                    </div>
                                    <div class="row">    
                                        <a type="button" onclick="add_bg_file()" value="匯入" class="btn btn-info">新增邊框</a>
                                    </div>
                                    -->
                                </div>

                                <div class="row" style="margin:20px 0px 0px 0px;display: inline-block;">
                                    <div class="row" style="float:left; margin-right:20px">
                                        <label class="control-label">選擇簽字章:</label>
                                            <select class="form-control" name="signature_select" style="max-width:300px">
                                                <option value="-1">新增</option>
                                                <?php
                                                foreach ($signature_select_option as $value) {
                                                    $signature_selected = $type_data['signature_file_id']==$value['id']?'selected':'';
                                                    echo '<option value="'.$value['id'].'" '.$signature_selected.'>'.$value['title_name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                    </div>
                                    <div class="row" style="float:left">
                                        <a type="button" onclick="delete_file('signature')" class="btn btn-danger">刪除</a>
                                    </div>
                                    <div class="row" style="float:left;width:1500px">    
                                            <label class="control-label">上傳新簽字章:　檔案類型:png</label>
                                            <input type="file" class="custom-file-input" name="signature_file">
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>

                                <div class="row" style="margin:20px 0px 0px 0px;display: inline-block;">
                                    <div class="row" style="float:left; margin-right:20px">
                                        <label class="control-label">選擇關防章:</label>
                                            <select class="form-control" name="seal_select">
                                                <option value="-1">新增</option>
                                                <?php
                                                foreach ($seal_select_option as $value) {
                                                    $seal_selected = $type_data['seal_file_id']==$value['id']?'selected':'';
                                                    echo '<option value="'.$value['id'].'" '.$seal_selected.'>'.$value['title_name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                    </div>
                                    <div class="row" style="float:left">
                                    <a type="button" onclick="delete_file('seal')" class="btn btn-danger">刪除</a>
                                    </div>
                                    <div class="row" style="float:left;width:1500px">    
                                            <label class="control-label">上傳新關防章:　檔案類型:png</label>
                                            <input type="file" class="custom-file-input" name="seal_file">                   
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>

                                <div class="row" style="margin:20px 0px 0px 0px">
                                    <a type="button" onclick="view_cer()" class="btn btn-info">證書預覽</a>
                                </div>
                                <div class="row" style="margin:20px 0px 0px 0px">
                                    <a type="button" onclick="save()" value="匯入" class="btn btn-info">儲存</a>
                                </div>


                            </div>

                            <div class="col-xs-4">
                                <div class="row">    
                                    <label class="control-label"><span style="color:RED">*</span>書證文字範本:</label>
                                </div> 
                                <div class="row">   
                                    <textarea class="form-control" id="demo_text" name="demo_text" rows="8" style="resize:none;width:95%;"><?=htmlspecialchars($type_data['demo_text'], ENT_QUOTES);?></textarea>
                                </div> 
                                <div class="row"><!--特殊排版-->
                                    <BR>    
                                    <!-- <label class="control-label">特殊排版:</label><input type="checkbox" id="pbox1" value="first_checkbox" class="form-group" <?= $s_special_type=="1"? "checked":"" ?>>    -->
                                    <label for="special_type" class="form-group">特殊排版: </label>
                                    <input type="checkbox" id="special_type" name="s_special_type" value="1" class="form-group" <?= $type_data['special_type']=="1"? "checked":"" ?>><br>
                                    (此排版用於新式固定證書使用)
                                </div> 																										   
                            </div>

                            <div class="col-xs-4">
                                <div class="row">    
                                    <label class="control-label">府頒證書範例文字:</label>
                                </div>
                                <div class="row">    
                                    <<姓名>> 君參加本府公務人員訓練處<<班期名稱>>第<<期別>>期（<<開訓日期>>~<<結訓日期>>），完成研習(共計<<時數>>小時) 特此證明
                                </div>

                                <div class="row">
                                    <BR>    
                                </div>
                                                               
                                <div class="row">    
                                    <label class="control-label">處頒證書範例文字:</label>
                                </div>
                                <div class="row">    
                                    <<姓名>> 君參加本府公務人員訓練處<<班期名稱>>第<<期別>>期（<<開訓日期>>~<<結訓日期>>），完成研習(共計<<時數>>小時)特此證明
                                </div>  

                                <div class="row"> 
                                    <BR>   
                                </div>

                                <div class="row">    
                                    <label class="control-label">府頒獎狀範例文字:</label>
                                </div>
                                <div class="row">    
                                    <<服務單位>> <<職稱>> <<姓名>> 君參加本府公務人員訓練處<<班期名稱>>第<<期別>>期結訓成績評列第<<名次>>特發獎狀以資嘉勉
                                </div>  

                                <div class="row"> 
                                    <BR>   
                                </div>

                                <div class="row">    
                                    <label class="control-label">處頒獎狀範例文字:</label>
                                </div>
                                <div class="row">    
                                    <<姓名>> 君參加臺北市政府公務人員訓練處<<班期名稱>>第<<期別>>期結訓成績評列第<<名次>>特發獎狀以資嘉勉
                                </div> 

                                <div class="row"> 
                                    <BR>   
                                </div>

                                <div class="row">    
                                    <label class="control-label">特殊排版範例文字:</label>
                                </div>
                                <div class="row">
                                    <<姓名>> 君&lt;BR>參加本府公務人員訓練處<<課程年度>>年&lt;BR><<班期名稱>>第<<期別>>期（<<開訓日期>>~<<結訓日期>>），完成研習(共計<<時數>>小時) 特此證明    
                                    
                                </div> 

                                <!-- 測試用 -->
                                <div class="row" style="margin:20px 0px 0px 0px">
                                    <!-- <a type="button" onclick="test()" class="btn btn-info">Test</a> -->
                                    <a type="button" onclick="go_back()" class="btn btn-info">回上一頁</a>
                                </div>

                            </div>

                        </div>



                        


                        
                    </div>

                </form>
                <form id="view_cer"  role="form" method="POST" action="<?=$view_cer_url;?>" target="_blank">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="cer_action" value="file">
                    <input type="hidden" name="content_text" value="">
                    <input type="hidden" name="unit" value="">
                    <input type="hidden" name="bg_file_name" value="">
                    <input type="hidden" name="signature_file_name" value="">
                    <input type="hidden" name="seal_file_name" value="">
                    <input type="hidden" name="bg_path" value="">
                    <input type="hidden" name="signature_path" value="">
                    <input type="hidden" name="seal_path" value="">
					<input type="hidden" name="special_type" value=""> <!--特殊排版-->																	  
                </form>
                <!-- /.table head -->
                
                
               
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>




function go_back(){
    document.location = '<?=base_url('management/certificate_type')?>';
}

function save(){
    document.getElementById("action").value = '<?=$type_action?>';
    obj = document.getElementById("add_type");
    obj.submit();
}

function no_select(target_select_name){
    var no_select = false;
    if($("select[name='"+target_select_name+"']").val()== -1){
        no_select = true;
    } 
    return no_select;
}

function no_file(target_file_name){
    var no_file = false;
    if($("input[name='"+target_file_name+"']").val()== ""){
        no_file = true;
    }
    return no_file;
}  

function have_img(target_select_name,target_file_name){
    //alert($("select[name='signature_select']").val());
    var have_img = true;
    if(no_select(target_select_name)){
        if(no_file(target_file_name)){
            //alert("不須背景");
            have_img = false;
        }
    }   
    return have_img;
}

function get_img_file_name(target_select_name,target_file_name){
    if(have_img(target_select_name,target_file_name)){
        if(!no_select(target_select_name)){
            //alert($("select[name='"+target_select_name+"']").val());
            var fid = $("select[name='"+target_select_name+"']").val();   //取得檔案id
            var f_name = '';
            //取得檔案名稱
            var link  = '<?=base_url('management/certificate_type/get_file_name_by_id')?>';
            var data = {
                '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
                'fid': fid
            }
            $.ajax({
                url: link,
                data: data,
                dataType: 'text',
                type: "POST",
                async :false,   //必須用同步傳送 否則無法return 很重要 很重要 很重要 
                error: function(xhr) {
                    alert('Ajax request error');
                },
                success: function(response) {
                    //alert(response);
                    f_name = response;
                }
            });
            return f_name;
        }else{
            //alert($("input[name='"+target_file_name+"']").val().replace(/.*(\/|\\)/, ''));
            return $("input[name='"+target_file_name+"']").val().replace(/.*(\/|\\)/, '');
        }
    }else{
        return "";
    }
}

function temp_path(target_select_name,target_file_name){   //0使用正常路徑 1使用temp路徑 2不使用路徑
    var temp_path = '2';
    var temp_select = no_select(target_select_name);
    var temp_file = no_file(target_file_name);
    if(temp_select && temp_file){   //沒選擇檔案也沒選擇SELECT
        temp_path = '2';
    }

    if(!temp_select && !temp_file){  //有選擇檔案也有選擇SELECT
        temp_path = '0';
    }

    if(temp_select && !temp_file){  //只有選擇檔案
        temp_path = '1';
    }

    if(!temp_select && temp_file){  //只有選擇SELECT
        temp_path = '0';
    }
    return temp_path;

}

function test(){
    var fid = $("select[name='bg_file']").val();   //取得檔案id
    var link  = '<?=base_url('management/certificate_type/get_file_name_by_id')?>';
            var data = {
                '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
                'fid': fid
            }
            $.ajax({
                url: link,
                data: data,
                dataType: 'text',
                type: "POST",
                async :false,   //必須用同步傳送 否則無法return 很重要 很重要 很重要 
                error: function(xhr) {
                    alert('Ajax request error');
                },
                success: function(response) {
                    //alert(response);
                    f_name = response;
                }
            });

}

function delete_file(file_sl){
            var del_txt = '';
            var del_na = '';
            var del_id = 0;
            if (file_sl == 'bg'){
                del_txt = $("select[name='bg_select'] :selected").text();
                del_id = $("select[name='bg_select']").val();
                del_na = '邊框';
            }else if(file_sl == 'signature'){
                del_txt = $("select[name='signature_select'] :selected").text();
                del_id = $("select[name='signature_select']").val();
                del_na = '簽字章';
            }else if(file_sl == 'seal'){
                del_txt = $("select[name='seal_select'] :selected").text();
                del_id = $("select[name='seal_select']").val();
                del_na = '關防章';
            }

        if (del_txt == '新增'){
            alert('不可刪除');
        }else{
            var yes = confirm('你確定要刪除 【'+del_txt+'】的'+del_na+'嗎？\n(注意！其它已使用此圖片的版型會受影響)');
            if (yes) {


                var link  = '<?=base_url('management/certificate_type/delete_file_by_id')?>';
                var data = {
                    '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
                    'id': del_id,
                    'file_sl' : file_sl                
                }
                $.ajax({
                    url: link,
                    data: data,
                    dataType: 'text',
                    type: "POST",
                    async :false,   //必須用同步傳送 否則無法return 很重要 很重要 很重要 
                    error: function(xhr) {
                        alert('Ajax request error');
                    },
                    success: function(res) {
                        alert('【'+del_txt+'】已刪除');
                        $("select[name='"+file_sl+"_select']").empty();

                        if(!hasIllegalChar(res)){
                            $("select[name='"+file_sl+"_select']").html(res);
                        }
                        
                    }
                });
            }
        }
}

function hasIllegalChar(str){
    return new RegExp(".*?script[^&gt;]*?.*?(&lt;\/.*?script.*?&gt;)*", "ig").test(str);
}

function view_cer(){
    document.getElementById("action").value = 'view_cer';
        var form = $('#add_type')[0];
        var formData = new FormData(form);
        //var url = '<?=base_url('management/certificate_type/type_add');?>';
        var url = '<?=$save_url?>';
        $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async:false,    //避免圖未上傳完成造成預覽錯誤
                    success: function (msg) {
                        console.log("SUCCESS");
                        //var form = $('#view_cer')[0];
                        //var formData2 = new FormData(form);
                         //formData2.append($("input[name='title']").clone());
                        //formData2.submit();
                        //window.open('cer_pdf/4', '_blank');
                        $("input[name='content_text']").val($('#demo_text').val()); //書證文字範本
                        $("input[name='unit']").val($("input[name='cer_unit']").val()); //發證單位
                        $("input[name='bg_file_name']").val(get_img_file_name('bg_select','bg_file')); //背景檔案名稱
                        $("input[name='bg_path']").val(temp_path('bg_select','bg_file')); //背景檔案路徑種類

                        $("input[name='signature_file_name']").val(get_img_file_name('signature_select','signature_file')); //簽字章檔案名稱
                        $("input[name='signature_path']").val(temp_path('signature_select','signature_file')); //簽字章檔案路徑種類
                        
                        $("input[name='seal_file_name']").val(get_img_file_name('seal_select','seal_file')); //官防檔案名稱
                        $("input[name='seal_path']").val(temp_path('seal_select','seal_file')); //官防檔案路徑種類

                        if ($('#special_type').prop("checked")) {  //特殊排版
                            $("input[name='special_type']").val(1);
                        }else{
                            $("input[name='special_type']").val('');
                        }                        
                        obj = document.getElementById("view_cer");
                        obj.submit();	
                    }
                });
}



</script>