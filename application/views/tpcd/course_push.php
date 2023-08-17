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
                        <form id="savsSet" method="post" action="<?=base_url('tpcd/course_push/setup')?>">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="row">
                            <div class="col-xs-12">
                                <label class="control-label">班期上課通知推播台北通於開班：
                                    <select name="before">
                                        <?php for($i=0;$i<=30;$i++) {?>
                                            <option value="<?=$i?>" <?=$list[0]['before']==$i?'selected':''?>><?=$i?></option>
                                        <?php } ?>
                                    </select>
                                    日前
                                </label>
                            </div>
                            <div class="col-xs-12">
                                <label class="control-label">報名資訊每月每人限推播：
                                    <select name="limit">
                                        <?php for($i=0;$i<=30;$i++) {?>
                                            <option value="<?=$i?>" <?=$list[0]['limit']==$i?'selected':''?>><?=$i?></option>
                                        <?php } ?>
                                    </select>
                                    則
                                </label>
                            </div>
                            <div class="col-xs-12">
                                <label class="control-label">推播台北通-主標題(限制字數200字)：</label>
                                <br>
                                <input class="form-control" type="text" name="message_title" id="message_title" value="<?=htmlspecialchars($list[0]['message_title'],ENT_HTML5|ENT_QUOTES)?>">
                            </div>
                            <div class="col-xs-12">
                                <label class="control-label">推播台北通-主內容：</label>
                                <br>
                                <textarea class="form-control" name="message_content" id="message_content" cols="100" rows="20"></textarea>
                            </div>
                            <div class="col-xs-12">
                                <label class="control-label">推播台北通-通知列標題(限制字數200字)：</label>
                                <br>
                                <input class="form-control" type="text" name="notification_title" id="notification_title" value="<?=htmlspecialchars($list[0]['notification_title'],ENT_HTML5|ENT_QUOTES)?>">
                            </div>
                            <div class="col-xs-12">
                                <label class="control-label">推播台北通-通知列內容(限制字數200字)：</label>
                                <br>
                                <textarea class="form-control" name="notification_context" id="notification_context" cols="100" rows="4"><?=htmlspecialchars($list[0]['notification_context'],ENT_HTML5|ENT_QUOTES)?></textarea>
                            </div>
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-info" onclick="saveFun()">設定</button>
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
            uiColor: '#AADBCB',
            height: '350px',
        });

        CKEDITOR.config.toolbar = [['Source','Bold','Underline','Strike'],['Link','Unlink'],['Image','SpecialChar','Maximize'],['TextColor','BGColor','RemoveFormat','FontSize']]

        CKEDITOR.instances.message_content.setData(message_content);
    });

    function saveFun(){
        // var content = $("#notification_context").val();
        var message = '推播台北通訊息將更新設定?';
    
        if(confirm(message)){
            $('#savsSet').submit();
        } else {
            return false;
        }
    }
</script>