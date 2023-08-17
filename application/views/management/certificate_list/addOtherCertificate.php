<script src="<?=base_url("static/plugin/jqueryui/jquery-ui.js")?>"></script>
<style>
.dialog{
    width:400px;
    /* height:300px; */
    background-color:#FFF;
    /* padding:10px; */
    position: fixed;
    top:30%;
    left:40%;
    border: 3px solid #2894FF;
    z-index:9001;
}    
.dialog_title{
    padding: 5px;
    background-color: #00AEAE;
    height: 11%;
}
.dialog_content{
    padding: 10px;
    padding-left: 5px;
    height: 70%;
    word-break: break-word;
    max-height: 400px;
    overflow: scroll;    
}
.dialog_footer{
    padding: 10px;
    height: 20%;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12" style="width: auto; font-size: 12px;">
                        <div class="col-xs-3" style="width: auto;">
                            <label class="control-label">年度:</label>
                            <input type="text" class="form-control" value="<?=$require->year;?>"disabled>
                        </div>    
                        <div class="col-xs-3" style="width: auto;">    
                            <label class="control-label">班期代碼:</label>
                            <input type="text" class="form-control" value="<?=$require->class_no;?>"disabled>
                        </div>    
                        <div class="col-xs-3" style="width: auto;"> 
                            <label class="control-label">班期名稱:</label>
                            <input type="text" class="form-control" value="<?=$require->class_name;?>"disabled>
                        </div>    
                        <div class="col-xs-3" style="width: auto;"> 
                            <label class="control-label">期別:</label>
                            <input type="text" class="form-control" value="<?=$require->term;?>"disabled>
                        </div> 
                    </div>
                </div>
                <form method="POST" enctype="multipart/form-data" id="otherCertForm" onsubmit="return checkCerForm()">
                <div class="col-xs-12">
                    <br>
                    <div class="col-xs-3" style="width: auto;">
                    批次上傳
                    <input type="file" name="otherCertificateBatch[]" multiple="true" accept=".pdf,.jpg,.png" class="otherCert">
</div>
                    <div class="col-xs-3" style="width: auto;"> 
                        <a type="button" onclick="go_back()" value="返回" class="btn btn-info">返回</a>
                    </div>                     
                    <br>
                </div>
                <!-- </form> -->
                <!-- /.table head -->
                <table class="table table-bordered table-striped table-condensed form-inline"  >
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" id="doAction" name="doAction" value="">
                    <thead>
                        <tr height="30">
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">學號</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">服務機關</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">總成績</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff" style="width:40px;">上傳</font></td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($online_apps as $online_app):?>
                        <?php 
                        $col = ($col == '#ffffff') ? '#dcdcdc' : '#ffffff';
                        //5:取消報名, 4:退訓
                        if (in_array($row['listData']["yn_sel"], ['4','5'])){
                            $col = '#FF69B4';   // ping color
                        }                        
                        ?>
                        <tr>
                        <td align="center" bgcolor="<?=$col?>"><?=htmlspecialchars($online_app['st_no'], ENT_HTML5|ENT_QUOTES)?></td>
                        <td align="center" bgcolor="<?=$col?>"><?=htmlspecialchars($online_app['beaurau_name'], ENT_HTML5|ENT_QUOTES)?></td>
                        <td align="center" bgcolor="<?=$col?>"><?=htmlspecialchars($online_app['title_name'], ENT_HTML5|ENT_QUOTES)?></td>
                        <td align="center" bgcolor="<?=$col?>"><?=htmlspecialchars($online_app['name'], ENT_HTML5|ENT_QUOTES)?></td>
                        <td align="center" bgcolor="<?=$col?>"><?=htmlspecialchars(number_format(floatval($online_app['main_score'] + $online_app['modi_num']), 2), ENT_HTML5|ENT_QUOTES)?></td>
                        <td align="center" bgcolor="<?=$col?>" style="width:30%;">
                        
                        <?php if(isset($certificate_others[$online_app['id']])): ?>
                            <a href="<?=htmlspecialchars($certificate_others[$online_app['id']]['link'], ENT_HTML5|ENT_QUOTES)?>" download="<?=htmlspecialchars($certificate_others[$online_app['id']]['cer_name'], ENT_HTML5|ENT_QUOTES)?>">
                             <?=htmlspecialchars($certificate_others[$online_app['id']]['cer_name'], ENT_HTML5|ENT_QUOTES)?>
                            </a>
                            <button type="button" class="btn btn-info" onclick="deleteOtherCertificate(<?=htmlspecialchars($certificate_others[$online_app['id']]['id'], ENT_HTML5|ENT_QUOTES)?>, '<?=addslashes(htmlspecialchars($certificate_others[$online_app['id']]['cer_name'], ENT_HTML5|ENT_QUOTES))?>')">刪除</button>
                        <?php else: ?>
                            <input type="file" name="otherCertificate[<?=htmlspecialchars($online_app['id'], ENT_HTML5|ENT_QUOTES)?>]" accept=".pdf,.jpg,.png" class="otherCert">
                        <?php endif ?>
                        </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                    </form>
                </table>
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-info" style="display:none">送出</button>
                    <a type="button" onclick="go_back()" value="返回" class="btn btn-info">返回</a>
                </div>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<form method="POST" id="deleteOtherCertificateForm">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
</form>
<div id="dialog-bg" style="height:100%;width:100%; background-color:#000;position: fixed;top:0px;left:0px;opacity:0.5;z-index:9000;display:none">&nbsp</div>

<div id="dialog" class="dialog" style="display:none">
  <div class="dialog_title">上傳檔案</div>
  <div class="dialog_footer">
    <button type="button" class="btn btn-primary" id="certsubmit" onclick="certSubmit()">送出</button>
    <button type="button" class="btn btn-primary" onclick="cancel()">取消</button>   
  </div>  
  <div id="dialog_content" class="dialog_content">
   
  </div>
</div>
 
<script>

function go_back(){
    document.location = '<?=base_url('management/certificate_list/cer_list/'.urlencode($require->seq_no))?>';
}    
function deleteOtherCertificate(id, name)
{
    if(confirm("確定要刪除" + name + "嗎?")){
        $("#deleteOtherCertificateForm").attr("action", "<?=base_url('management/certificate_list/deleteOtherCertificate/')?>" + id);
        $("#deleteOtherCertificateForm").submit();
    }
}

$(".otherCert").on('change', function(e){
    if (this.value != ""){
        $("#dialog").css("display", "");
        $("#dialog-bg").css("display", "");
        var files = $(".otherCert");
        let filelist = "";
        for(let i=0;i<files.length;i++){
            for(let j=0;j<files[i].files.length;j++){
                filelist += htmlencode(files[i].files[j].name) + "<br>";
            }
        }
        $("#dialog_content").html(filelist);
    }
});

function certSubmit()
{
    $("#certsubmit").attr("disabled", true);
    setInterval(messagecolor, 1000);
    $("#otherCertForm").submit();
}

function cancel()
{
    $(".otherCert").val("");
    $("#dialog").css("display", "none");
    $("#dialog-bg").css("display", "none");
}

function checkCerForm()
{
    $(".dialog_title").html($(".dialog_title").html() + '(<font style="background-color:#FFF" color="#FF2D2D" id="uploadmessage">上傳中</font>)');
    return true;
}

if (typeof htmlencode == 'undefined'){
    function htmlencode(s){
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(s));
        return div.innerHTML;
    }
}

function messagecolor()
{
    let col = "#FFF";
    if ($("#uploadmessage").css("background-color") == "rgb(255, 255, 255)"){
        col = "#000";
    }
    $("#uploadmessage").css("background-color", col);
}

</script>