		<form id="data-form" role="form" method="post" action="<?= $link_save2; ?>">
			<div class="card card-default">
				<div class="card-body">
                    <?php include('default_form.inc.php'); ?>
                </div><!-- /.card-body -->
			</div><!-- ./card -->
		</form>

<script type="text/javascript">
    <?php if($fmap == 'N'){ ?>
    //$(document).ready(function() {
    document.addEventListener("DOMContentLoaded", () => {
        detectBrowser();
        fmap_off();
    });
    <?php } ?>

document.addEventListener("DOMContentLoaded", () => {
    jQuery(function(){
        if(1==jQuery("select#is_assess option:selected").val()) {
            jQuery("#is_mixed").prop( "readonly", false);
        }
        else {
            jQuery("#is_mixed").prop( "readonly", true);
        }
        changAct();
    });

    //jQuery("#is_mixed").change(function() {
    //    changAct();
    //});

    jQuery("#is_assess").change(function() {
        if(1==jQuery(this).val()) {
            jQuery("#is_mixed").prop( "readonly", false);
        }
        else {
            jQuery("#is_mixed").prop( "readonly", true);
        }
    });
});

function changAct(){
    if(1==jQuery("select#is_mixed option:selected").val()) {
        jQuery("#online_course").css("display","") //顯示
    }
    else {
        jQuery("#online_course").css("display","none") //隱藏
    }
}

var currentSelected = "";

function chooseOne(cb) {
    if(currentSelected!="") {
        currentSelected.checked = false;
    }
    //變更目前勾選的checkbox
    if(cb.checked)  {
        currentSelected = cb;
    } else {
        currentSelected="";
    }
}

function detectBrowser(){  
    if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > 0) {  
        $(".checkbox-inline input[type=checkbox]").css({
            "position" : "absolute",
            "margin-left" : "-22px" 
        });
        $(".radio-inline input[type=radio]").css({
            "position" : "absolute",
            "margin-left" : "-22px" 
        });
    }  
}  

function fmap_off(){
    jQuery("#map1").prop( "checked", false);
    jQuery("#map1").prop( "disabled", true);
    jQuery("#map2").prop( "checked", false);
    jQuery("#map2").prop( "disabled", true);
    jQuery("#map3").prop( "checked", false);
    jQuery("#map3").prop( "disabled", true);
    jQuery("#map4").prop( "checked", false);
    jQuery("#map4").prop( "disabled", true);
    jQuery("#map5").prop( "checked", false);
    jQuery("#map5").prop( "disabled", true);
    jQuery("#map6").prop( "checked", false);
    jQuery("#map6").prop( "disabled", true);
    jQuery("#map7").prop( "checked", false);
    jQuery("#map7").prop( "disabled", true);
    jQuery("#map8").prop( "checked", false);
    jQuery("#map8").prop( "disabled", true);
    jQuery("#map9").prop( "checked", false);
    jQuery("#map9").prop( "disabled", true);
    jQuery("#map10").prop( "checked", false);
    jQuery("#map10").prop( "disabled", true);
    jQuery("#map11").prop( "checked", false);
    jQuery("#map11").prop( "disabled", true);
}

function fmap_on(){
    jQuery("#map1").prop( "disabled", false);
    jQuery("#map2").prop( "disabled", false);
    jQuery("#map3").prop( "disabled", false);
    jQuery("#map4").prop( "disabled", false);
    jQuery("#map5").prop( "disabled", false);
    jQuery("#map6").prop( "disabled", false);
    jQuery("#map7").prop( "disabled", false);
    jQuery("#map8").prop( "disabled", false);
    jQuery("#map9").prop( "disabled", false);
    jQuery("#map10").prop( "disabled", false);
    jQuery("#map11").prop( "disabled", false);
}

<?php if($page_name == 'edit') { ?>
function bookingFun(seq_no){
    //var path = '../../../classroom/add/'+seq_no;
    var path = '../../../classroom/popup_add/'+seq_no;
    //planning/classroom/popup_add/27973
    //debugger;
    //alert('預約教室，另開新視窗');
    //window.open(path,'booking','fullscreen=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes');
    $('.content-wrapper').IFrame('createTab', 'Home', path, 'index', true);
    /*$('.content-wrapper').IFrame({
        onTabClick(item) {
            return item
        },
        onTabChanged(item) {
            return item
        },
        onTabCreated(item) {
            return item
        },
        autoIframeMode: true,
        autoItemActive: true,
        autoShowNewTab: true,
        autoDarkMode: false,
        allowDuplicates: true,
        loadingScreen: 750,
        useNavbarItems: true
    });*/
    $('#modal-lg').modal('show');
}

function updateBookingFun(seq_no){
    updateResquire(seq_no);
    var link = "<?=$link_get_room;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'seq_no': seq_no
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);
            
            if (result.length != 0) {
                document.getElementById('room_name').value = result[0]['room_name'];
                document.getElementById('set_start_date1').value = result[0]['start_date1'];
                document.getElementById('set_end_date1').value = result[0]['end_date1'];
            }else{
                document.getElementById('room_name').value = '';
                document.getElementById('set_start_date1').value = '';
                document.getElementById('set_end_date1').value = '';
            }
        }
    });
}


function updateResquire(seq_no){
    var link = "<?=$link_update_require;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'seq_no': seq_no
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {

        }
    });
}
<?php } ?>

function notAtLocalFun(type){
    if(type == '非公訓處上課'){
        document.getElementById('room_name').value = '非公訓處上課';
        document.getElementById('enableRoom').value = '本處上課';
        jQuery("#set_start_date1").prop( "disabled", false);
        jQuery("#set_end_date1").prop( "disabled", false);
        alert('選非公訓處上課者，請接續點選預定開課起訖日，處外場地須自行洽外機關預約');
        $("input#set_start_date1").trigger("focus");
    } else if(type == '本處上課'){
        document.getElementById('room_name').value = '';
        document.getElementById('enableRoom').value = '非公訓處上課';
        document.getElementById('set_start_date1').value = '';
        document.getElementById('set_end_date1').value = '';
        jQuery("#set_start_date1").prop( "disabled", true);
        jQuery("#set_end_date1").prop( "disabled", true);
    }
}

function query_classname(page_name){
    if(page_name == 'add'){
        var path = '../../../query_classname.php';
    } else if(page_name == 'edit'){
        var path = '../../../../query_classname.php';
    }
    window.open(path,'selbFee','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
}

function showBureau(para,page_name){
    if(para == 'dev_type'){
        if(page_name == 'add'){
            var path = '../../../co_bureau.php?field1=dev_type&field2=dev_type_name&mode=1';
        } else if(page_name == 'edit'){
            var path = '../../../../co_bureau.php?field1=dev_type&field2=dev_type_name&mode=1';
        }
    } else if(para == 'req_beaurau'){
        if(page_name == 'add'){
            var path = '../../../co_bureau.php?field1=req_beaurau&field2=req_beaurau_name&mode=2';
        } else if(page_name == 'edit'){
            var path = '../../../../co_bureau.php?field1=req_beaurau&field2=req_beaurau_name&mode=2';
        }

    }

    var myW=window.open(path, 'selBureau','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=700');
    myW.focus();
}

function showEcpaClassName(ecpa_class_id){
    if(ecpa_class_id == ''){
         document.getElementById('ecpa_class_name').value = '';
        return false;
    }

    var link = "<?=$link_get_ecpa_name;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'ecpa_class_id': ecpa_class_id
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            if (response.length != 0) {
                document.getElementById('ecpa_class_name').value = response;
            }
        }
    });
}

function removeOptions(selectbox) {
    var i;
    for (i = selectbox.options.length - 1; i >= 0; i--) {
        selectbox.remove(i);
    }
}

function getSecond(){
    removeOptions(document.getElementById("beaurau_id"));

    var series = document.getElementById('type').value;

    if(series == ''){
        return false;
    }

    var link = "<?=$link_get_second_category;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            var result = jQuery.parseJSON(response);
             <?php if(isset($link_get_classno) && !isset($transfer)) {?>
                getClassNO();
            <?php } ?>
            if (result.length != 0) {
                for (var i = 0; i < result.length; i++) {
                    var second = document.getElementById('beaurau_id');
                    var option_name = result[i]['name'];
                    var option_value = result[i]['item_id'];
                    var new_option = new Option(option_name, option_value);
                    second.options.add(new_option);
                }
            }
        }
    });
}

function addCourse() {
    var num = $('#course_content table tbody tr').size();
    var html = '';
    html += '<tr>';
    html += '<td>';
    html += '<input class="form-control" type="text" name="course_name[]" value="">';
    html += '</td>';
    html += '<td>';
    html += '<select class="form-control" name="material[]">';
    html += '<option value="4">無</option>';
    html += '<option value="0">實境錄製教材(單一主題)</option>';
    html += '<option value="1">實境錄製教材(系列性主題)</option>';
    html += '<option value="2">全動畫教材(貴局處無經費)</option>';
    html += '<option value="3">全動畫教材(貴局處有經費)</option>';
    html += '</select>';
    html += '</td>';
    html += '<td align="right">';
    html += '<button type="button" class="btn btn-danger btn-sm" id="remove_'+ num +'" onclick="removeItem(this, '+ num +')">刪除</button>';
    html += '</td>';
    html += '</tr>';
    $('#course_content table tbody').append(html);
}

function removeItem(obj, num) {
    $(obj).closest('tr').remove();
}

function openCourSeltor() {
    <?php if($page_name == 'add'){ ?>
        window.open("../../../elearnQuery.php",'selbFee','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=580,width=600');
    <?php } else if ($page_name = 'edit') { ?>
        window.open("../../../../elearnQuery.php",'selbFee','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=580,width=600');
    <?php } ?> 
}

function explodeStr() {
    var getContent = jQuery("#hidStr").val().split("|,|");
    var num = $('#online_course table tbody tr').size();
    var html = '';
    if(getContent.length=3) {
        var num = $('#online_course table tbody tr').size();
        var html = '';
        html += '<tr>';
        html += '<td>';
        html += '<input class="form-control" type="text" name="online_course_name[]" id="online_course_name[]" value="'+getContent[1]+'">';
        html += '</td>';
        html += '<td>';
        html += '<input class="form-control" type="text" name="hours[]" id="hours[]" value="'+getContent[2]+'">';
        html += '<input type="hidden" value="'+getContent[0]+'" name="elrid[]" id="elrid[]">';
        html += '</td>';
        html += '<td align="right">';
        html += '<button type="button" class="btn btn-danger btn-sm" id="remove_'+ num +'" onclick="removeItem(this, '+ num +')">刪除</button>';
        html += '</td>';
        html += '</tr>';
        $('#online_course table tbody').append(html);
    } else {
        alert("insert exception");
    }
}

function checkSave(){
    if(document.getElementById('is_mixed').value == '1'){
        /*if($('#online_course table tbody tr').size() == 0){
            alert('線上課程至少1門');
            return false;
        }*/
    }

    var obj = document.getElementById('data-form');
    obj.submit();
}

//$(document).ready(function() {
document.addEventListener("DOMContentLoaded", () => {
    $( "#start_date1" ).click(function() {
        $("input#set_start_date1").trigger("focus");
    });

    $( "#end_date1" ).click(function() {
        $("input#set_end_date1").trigger("focus");
    });
});

<?php if(isset($link_get_classno) && !isset($transfer)) {?>
function getClassNO(){
    var series = document.getElementById('type').value;
    var class_name = document.getElementById('class_name').value;
    var link = "<?=$link_get_classno;?>";
    var data = {
        '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
        'type': series,
        'class_name': class_name
    }

    $.ajax({
        url: link,
        data: data,
        dataType: 'text',
        type: "POST",
        error: function(xhr) {
            alert('Ajax request error');
        },
        success: function(response) {
            document.getElementById('class_no').value = response;
        }
    });
}
<?php } ?>
</script>