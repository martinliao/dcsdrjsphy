<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i>查詢線上課程學習紀錄
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div> 
                    <form id="filter-form" role="form" action="">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                        </thead>
                        <tbody>
                            <tr>
                                <td>年度</td>
                                <td><?=$require->year?></td>
                                <td>
                                    班期代碼<br>
                                    班期名稱<br>
                                </td>
                                <td>
                                    <?=$require->class_no?><br>
                                    <?=$require->class_name?><br>
                                                  
                                </td>
                                <td>期別</td>
                                <td><?=$require->term?></td>
                            </tr>
                            <tr>
                                <td>查詢項目</td>
                                <td colspan="5">
                                    <input type="hidden" name="year" value="<?=$filter['year']?>">
                                    <input type="hidden" name="class_no" value="<?=$filter['class_no']?>">
                                    <input type="hidden" name="term" value="<?=$filter['term']?>">
                                    <?php 
                                        $un_register_checked = ($filter['query_type'] == "un_register") ? 'checked' : '' ;
                                        $un_finish_checked = ($filter['query_type'] == "un_finish") ? 'checked' : '' ;
                                    ?>
                                    <input type="radio" name="query_type" value="un_register" style="height:auto" <?=$un_register_checked?> ><label>查詢未加入會員</label><br>
                                    <input type="radio" name="query_type" value="un_finish" style="height:auto" <?=$un_finish_checked?>> <label>查詢未完成課程</label>
                                    <label style="margin-left:20px;">課程：</label>
                                    <select name="unfinish_course_id">
                                        <option value="-1">請選擇</option>
                                        <?php foreach($onlines as $online): ?>
                                            <?php 
                                                $selected = ($online->elearn_id == $filter['unfinish_course_id'])? 'selected' : '';
                                            ?>
                                            <option  value="<?=$online->elearn_id?>" <?=$selected?> ><?=$online->class_name?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr> 
                            <tr>
                                <td colspan="6">
                                <button class="btn btn-info btn-sm">搜尋</button>
                                </td>
                            </tr>                           
                        </tbody>
                    </table>                       
                    </form>
                </div>
                <div>
                <form method="POST" >
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="year" value="<?=$filter['year']?>">
                    <input type="hidden" name="class_no" value="<?=$filter['class_no']?>">
                    <input type="hidden" name="term" value="<?=$filter['term']?>">                
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr bgcolor="#8CBBFF">
                                <th><input type="checkbox" onclick="check_All(this);">寄信通知</th>
                                <th>姓名</th>
                                <th>局處名稱</th>
                                <th>電話</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($list as $row): ?>
                            <tr>
                                <td><input type="checkbox" name="emails[]" value="<?=$row->EMAIL?>" id="ids"></td>
                                <td><?=$row->name?></td>
                                <td><?=$row->bureau_name?></td>
                                <td><?=$row->office_tel?></td>
                                <td><?=$row->EMAIL?></td>
                            </tr>
                            <?php endforeach ?>                            
                            <?php if($filter['query_type'] == "unfinish" && empty($list)): ?>
                                <td colspan="5"><font style="color:red">[本班期學員皆已完成課程]</font></td>
                            <?php elseif($filter['query_type'] == "un_register" && empty($list)): ?>
                                <td colspan="5"><font style="color:red">[本班期學員皆已加入會員]</font></td>
                            <?php endif?>
                        </tbody>
                    </table>
                    
                        <button form="test" class="btn btn-info btn-sm" id="show" onlick="mailSet()">選擇</button>
                        <div class="row" id="mail_test" style="display:none;margin-bottom: 5px;margin-top: 5px" >
                            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <input type="hidden" name="send" value="" />
                            <div class="col-xs-6" >
                                <label class="control-label">內文範本</label>
                                    <select class="form-control" id="mail_content_template">
                                        <option>請選擇範本</option>
                                        <?php foreach ($templates['course_content_template'] as $template): ?>
                                            <option value="<?=$template->id?>"><?=$template->title?></option>
                                        <?php endforeach ?>                                 
                                    </select>
                            </div>
                            <div class="col-xs-12">
                                <label class="control-label">內文內容</label>
                                <textarea class="form-control" id="mail_content" name="mail_content"></textarea>
                            </div>
                        </div>

                        <button class="btn btn-info btn-sm" style="margin-bottom: 5px ;display:none" id="send_button">寄送</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="<?=HTTP_PLUGIN;?>ckeditor_4.14.0_full/ckeditor/ckeditor.js"></script>
<script type="text/javascript">

$(document).ready(function(){

    $("#show").click(function(){
        var testval=[];

        $('#ids:checked').each(function() {
            testval.push($(this).val());
        });

        if(testval.length==0) {
            alert("請選擇被通知人");
        }else{
            $("#mail_test").show();
            $("#send_button").show();
        }
        
    });
});


$('#mail_content_template').on('change',function(){
    getTemplate(this.value, 'mail_content');
});

function check_All(source) {
    checkboxes = document.getElementsByName('emails[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}


function getTemplate(id, type){
    console.log(id);
    $.ajax({
      method: "GET",
      url: "/base/admin/data/template_list/getTemplate/" + id
    }).done(function(result) {
        var template = JSON.parse(result);
        //console.log(template);
        
        if (type == "mail_content"){
            if(template==null){
                CKEDITOR.instances.mail_content.setData("");
            }else{
                CKEDITOR.instances.mail_content.setData(template.content);
            }
            
        }
        
    }); 
}


$("#filter-form").keydown(function(e) {
  //Enter key
  if (e.which == 13) {
    e.preventDefault();
    return false;
  }
});

$(function() {
    CKEDITOR.config.extraPlugins += (CKEDITOR.config.extraPlugins ? ',lineheight' : 'lineheight');
    CKEDITOR.replace('mail_content', {
        language: 'zh',
        uiColor: '#AADBCB',
    });
    
});




</script>