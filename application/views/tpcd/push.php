<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="savsSet" method="post" action="<?=base_url('tpcd/push/send')?>">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-xs-8">
                                    <label class="control-label">推播台北通-主標題(限制字數200字)：</label>
                                    <br>
                                    <input class="form-control" type="text" name="message_title" id="message_title" value="<?=htmlspecialchars($list[0]['message_title'],ENT_HTML5|ENT_QUOTES)?>" readonly>
                                
                                    <label class="control-label">推播台北通-主內容：</label>
                                    <br>
                                    <textarea class="form-control" name="message_content" id="message_content" cols="10" rows="10" readonly></textarea>
                               
                                    <label class="control-label">推播台北通-通知列標題(限制字數200字)：</label>
                                    <br>
                                    <input class="form-control" type="text" name="notification_title" id="notification_title" value="<?=htmlspecialchars($list[0]['notification_title'],ENT_HTML5|ENT_QUOTES)?>" readonly>
                               
                                    <label class="control-label">推播台北通-通知列內容(限制字數200字)：</label>
                                    <br>
                                    <textarea class="form-control" name="notification_context" id="notification_context" cols="10" rows="4" readonly><?=htmlspecialchars($list[0]['notification_context'],ENT_HTML5|ENT_QUOTES)?></textarea>
                                </div>
                                <div class="col-xs-4">
                                    <label class="control-label">推播台北通對象：</label>
                                    <br>
                                    <input type="radio" class="form-inline" name="type" value="1">本府員工
                                    <br>
                                    <input type="radio" class="form-inline" name="type" value="2" checked>特定人員<font style="color: red;">(身分證號，逗號隔開，推播後將清空內容)</font>
                                    <textarea class="form-control" name="send_list" id="send_list" cols="10" rows="25"></textarea>
                                </div>
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-info" onclick="saveFun()">即時推播</button>
                                <a href="<?=htmlspecialchars($link_log,ENT_HTML5|ENT_QUOTES)?>"><button type="button" class="btn btn-info">推播紀錄</button></a>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script src="<?=HTTP_PLUGIN;?>ckeditor_4.14.0_full/ckeditor/ckeditor.js"></script>

<script>
    var message_content = <?php echo json_encode(htmlspecialchars_decode($list[0]['message_content']));?>;

    $(function() {
        CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ',lineheight' : 'lineheight');
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
        CKEDITOR.config.shiftEnterMode = CKEDITOR.ENTER_P;
        CKEDITOR.replace('message_content', {
            language: 'zh',
            uiColor: '#333333',
            height: '350px',
        });

        CKEDITOR.config.toolbar = [['Source','Bold','Underline','Strike'],['Link','Unlink'],['Image','Maximize'],['TextColor','BGColor','RemoveFormat','FontSize']]

        CKEDITOR.instances.message_content.setData(message_content);
    });

    function saveFun(){
        var list = $("#send_list").val();

        var type = $('input[name=type]:checked').val();
        if(type == 1) {
            var message = '將推播訊息至台北通全市府員工?';
        } else if( type == 2){
            if(list.trim() == ''){
                alert('寄送對象不能為空');
                return false;
            }

            var message = '將推播訊息至台北通特定人員?';
        }
        
        if(confirm(message)){
            $('#savsSet').submit();
        } else {
            return false;
        }
    }
</script>