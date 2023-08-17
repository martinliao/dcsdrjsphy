$(document).ready(function() {
    $('#side-menu .nav-second-level li a:not(.active)').hover(function(){
        $(this).animate({'left':"10px"}, 150);
    }, function() {
        $(this).animate({'left':"0px"}, 60);
    })

    // list-form click checkbox`
    var $form_list = $('#list-form');
    $form_list.find('#chkall').click(function(){
        var checked = $(this).prop('checked');
        $form_list.find('tbody [type=checkbox]').each(function(){
            $(this).prop('checked', checked);
            if (checked == true) {
                $(this).closest('tr').addClass('active');
            } else {
                $(this).closest('tr').removeClass('active');
            }
        });
    });

    $form_list.find('tbody [type=checkbox]').click(function(){
        var checked = $(this).prop('checked');
        if (checked == true) {
            $(this).closest('tr').addClass('active');
        } else {
            $(this).closest('tr').removeClass('active');
        }
    });

    $('#list-form table .sorting').on('click', function(){
        $('#filter-form [name=sort]').val($(this).attr('data-field')+' asc');
        $('#filter-form').submit();
    });

    $('#list-form table .sorting_asc').on('click', function(){
        $('#filter-form [name=sort]').val($(this).attr('data-field')+' desc');
        $('#filter-form').submit();
    });

    $('#list-form table .sorting_desc').on('click', function(){
        $('#filter-form [name=sort]').val($(this).attr('data-field')+' asc');
        $('#filter-form').submit();
    });

    var matched, browser;
    jQuery.uaMatch = function( ua ) {
        ua = ua.toLowerCase();
        var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
            /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
            /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
            /(msie) ([\w.]+)/.exec( ua ) ||
            ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
            [];
        return {
            browser: match[ 1 ] || "",
            version: match[ 2 ] || "0"
        };
    };
    matched = jQuery.uaMatch( navigator.userAgent );
    browser = {};
    if ( matched.browser ) {
        browser[ matched.browser ] = true;
        browser.version = matched.version;
    }
    // Chrome is Webkit, but Webkit is also Safari.
    if ( browser.chrome ) {
        browser.webkit = true;
    } else if ( browser.webkit ) {
        browser.safari = true;
    }
    jQuery.browser = browser;

    $(".datepicker").datepicker({yearRange:"-100:+2"});
    // $('.datepicker, .input-group.date, .input-daterange').datepicker({
    //     autoclose: true,
    //     todayBtn: true,
    //     language: 'zh-TW',
    //     format: 'yyyy-mm-dd',
    //     todayHighlight: true,
    // });
    // $('.datetimepicker').datetimepicker({
    //     autoclose: true,
    //     todayBtn: true,
    //     language: 'zh-TW',
    //     format: 'yyyy-mm-dd hh:ii',
    //     todayHighlight: true,
    // });

    // $(".datepicker").datepicker({
    //     dateFormat: "yy-mm-dd",
    //     changeMonth: true,
    //     changeYear: true,
    //     // yearRange: "1950:"+(new Date).getFullYear(),
    //     language:"zh-TW"
    // });

    // data-form submit
    $(".btn-save").click(function(){
        $('#data-form').submit();
    });
    $("#data-form input").keypress(function(e){
        if (e.keyCode == 13) {
            $('#data-form').submit();
        }
    });
    $("#filter-form input").keypress(function(e){
        if (e.keyCode == 13) {
            $('#filter-form').submit();
        }
    });

    // fancybox
    $("a[rel=fancybox_group]").fancybox({
        'transitionIn'      : 'none',
        'transitionOut'     : 'none',
        'titlePosition'     : 'over',
        'titleFormat'       : function(title, currentArray, currentIndex, currentOpts) {
            return '<span id="fancybox-title-over">Image ' +  (currentIndex + 1) + ' / ' + currentArray.length + ' ' + title + '</span>';
        }
    });
    /* $(".fancybox-thumb").fancybox({ */
        /* prevEffect  : 'none', */
        /* nextEffect  : 'none', */
        /* helpers : { */
            /* title   : { */
                /* type: 'outside' */
            /* }, */
            /* thumbs  : { */
                /* width   : 80, */
                /* height  : 80 */
            /* } */
        /* } */
    /* }); */


    if (CI._ALERT.message) {
        bk_alert(CI._ALERT.kind, CI._ALERT.message, CI._ALERT.sec, CI._ALERT.layout);
    }

    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }

    $('#side-menu').metisMenu();

});

var actionCancel = function(url) {
    var $form = $('#list-form');
    if ($form.find('[name="rowid[]"]:checked').size() > 0) {
        var yesfunc = function() {
            $form.attr('action', url).submit();
        }

        var nofunc = function() {
            // bk_alert(4, 'ok', 4, 'center');
        }

        var msg = '<p>確認取消以下尚未月結訂單?</p>';
        $form.find('[name="rowid[]"]:checked').each(function(){
            var str = $(this).closest('tr').find('td:eq(1)').html().trim()
            msg +=  $.trim(str) + '<br>';
        });


        bk_confirm(3, msg, 'center', yesfunc, nofunc);
    } else {
        bk_alert(3, '沒有勾選資料', 4, 'center');
    }
}

var actionCombined = function(url) {
    var $form = $('#list-form');
    if ($form.find('[name="rowid[]"]:checked').size() > 0) {
        var yesfunc = function() {
            $form.attr('action', url).submit();
        }

        var nofunc = function() {
            // bk_alert(4, 'ok', 4, 'center');
        }

        var msg = '<p>確認特約廠商月結訂單</p>';
        var payment_method = $('[name="payment_method"]').val();
        if(payment_method == '1'){
            var payment = 'ATM轉帳';
        }
        if(payment_method == '6'){
            var payment = '現金收付';
        }
        if(payment_method == '11'){
            var payment = '支票';
        }
        if(payment_method == '12'){
            var payment = '公司帳戶劃撥';
        }
        if(payment_method == '13'){
            var payment = '匯款';
        }

        msg +=  '<p>付款方式：' + payment + '</p>';

        $form.find('[name="rowid[]"]:checked').each(function(){
            var str = $(this).closest('tr').find('td:eq(1)').html().trim()
            msg +=  $.trim(str) + '<br>';
        });

        bk_confirm(3, msg, 'center', yesfunc, nofunc);
    } else {
        bk_alert(3, '沒有勾選資料', 4, 'center');
    }
}

var actionDelete = function(url) {
    var $form = $('#list-form');
    if ($form.find('[name="rowid[]"]:checked').size() > 0) {
        var yesfunc = function() {
            $form.attr('action', url).submit();
        }

        var nofunc = function() {
            // bk_alert(4, 'ok', 4, 'center');
        }

        var msg = '<p>確認刪除勾選資料?</p>';
        // $form.find('[name="rowid[]"]:checked').each(function(){
        //     var str = $(this).closest('tr').find('td:eq(1)').html().trim()
        //     msg +=  $.trim(str) + '<br>';
        // });


        bk_confirm(0, msg, 'center', yesfunc, nofunc);
    } else {
        bk_alert(3, '沒有勾選資料', 4, 'center');
    }
}
var setSwitch = function(obj, msge ,url) {
    var $form = $('#list-form');
    var yesfunc = function() {
        window.location.href = url;
    }

    var nofunc = function() {
        // bk_alert(4, 'ok', 4, 'center');
    }

    var msg = '<p>'+ msge +'</p>';


    bk_confirm(0, msg, 'center', yesfunc, nofunc);

}
var actionFax = function(url) {
    var $form = $('#list-form');
    if ($form.find('[name="rowid[]"]:checked').size() > 0) {
        var yesfunc = function() {
            $form.attr('action', url).submit();
        }

        var nofunc = function() {
            // bk_alert(4, 'ok', 4, 'center');
        }

        var msg = '<p>確認傳真以下訂單資料?</p>';
        $form.find('[name="rowid[]"]:checked').each(function(){
            var str = $(this).closest('tr').find('td:eq(1)').html().trim()
            msg +=  $.trim(str) + '<br>';
        });


        bk_confirm(0, msg, 'center', yesfunc, nofunc);
    } else {
        bk_alert(3, '沒有勾選資料', 4, 'center');
    }
}
var actionSubmit = function(id) {
    $('#'+id).submit();
}

var noty_type = {
    0: 'alert',
    1: 'information',
    2: 'success',
    3: 'warning',
    4: 'error'
}
var bk_alert = function(type, msg, sec, layout) {
    var millisecond = sec * 1000;
    var n = noty({
        text: msg,
        type: noty_type[type],
        layout : layout,  // Top, TopLeft, TopCenter, TopRight, CenterLeft, Center, CenterRight, Bottom, BottomLeft, BottomCenter, BottomRight
        timeout: millisecond,
        theme: 'relax',  // bootstrapTheme,
        closeWith: ['click'], // ['click', 'button', 'hover', 'backdrop'] // backdrop click will close all notifications
        animation: {
            // open: {height: 'toggle'}, // jQuery animate function property object
            // close: {height: 'toggle'}, // jQuery animate function property object
            open: 'animated flipInX',
            close: 'animated flipOutX',
            easing: 'swing', // easing
            speed: 500 // opening & closing animation speed
        }
    });
}

var bk_confirm = function(type, msg, layout, yesfunc, nofunc) {
    var n = noty({
        text : msg,
        type: noty_type[type],
        layout : layout,  // top, topLeft, topCenter, topRight, centerLeft, center, centerRight, bottom, bottomLeft, bottomCenter, bottomRight
        theme: 'relax',  // bootstrapTheme,
        dismissQueue: true,
        animation: {
            open: 'animated bounceInDown',
            close: 'animated bounceOutUp',
            easing: 'swing', // easing
            speed: 300 // opening & closing animation speed
        },
        buttons : [
            {
                addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {
                    $noty.close();
                    yesfunc();
                }
            },
            {
                addClass: 'btn btn-danger', text: 'Cancel', onClick: function ($noty) {
                    $noty.close();
                    nofunc();
                }
            }
        ]
    });
}

var bk_confirm_2 = function(type, msg, layout, yesfunc) {
    var n = noty({
        text : msg,
        type: noty_type[type],
        layout : layout,  // top, topLeft, topCenter, topRight, centerLeft, center, centerRight, bottom, bottomLeft, bottomCenter, bottomRight
        theme: 'relax',  // bootstrapTheme,
        dismissQueue: true,
        animation: {
            open: 'animated bounceInDown',
            close: 'animated bounceOutUp',
            easing: 'swing', // easing
            speed: 300 // opening & closing animation speed
        },
        buttons : [
            {
                addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {
                    $noty.close();
                    yesfunc();
                }
            },
        ]
    });
}

var removeImageBtn = function(obj) {
    $(obj).closest('tr').remove();
    toggleImageButton();
}

var deleteImage = function(obj)
{
    var $image_block = $(obj).closest('.image-block');
    $image_block.find('div').html('<i class="fa fa-plus fa-2x"></i>');
    $(obj).hide();

    // $image_block.closest('.form-group').find('[type=hidden]').val('');
    $image_block.closest('.form-group').find('input').val('');
    // $image_block.closest('td').find('[type=hidden]').val('');
    $image_block.closest('.td').find('input').val('');
}
var selectImage = function(obj)
{
    var $image_block = $(obj).closest('.image-block');
    $image_block.find('[type=file]').click();
}
var changeImage = function(input)
{
    var $image_block = $(input).closest('.image-block');
    var filename = $(input).val();
    var fileSize = 0;
    var SizeLimit = 2048;  //上傳上限，單位:kb
    var ext = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
    if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg" || ext == "bmp")) {
        if (navigator.userAgent.match("MSIE")) {  //FOR IE
            var img = new Image();
            img.onload = function(){
                fileSize = this.fileSize;
            }
            img.src = input.value;
        } else {  //FOR Firefox,Chrome
            fileSize = input.files.item(0).size;
        }
        fileSize =Math.floor(fileSize / 1000);
        if (fileSize <= SizeLimit) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var $img = $('<img class="img-rounded">').attr('src', e.target.result);
                $image_block.find('div').html($img);
            }
            reader.readAsDataURL(input.files[0]);

            $image_block.find('a.close').show();
            $image_block.find('[type=hidden]').val(filename);
        } else {
            bk_alert(4, '您所選擇的檔案大小為 ' + fileSize + ' KB<br>已超過上傳上限 ' + SizeLimit + ' KB<br>不允許上傳！', 4, 'topCenter');
        }
    } else {
        //bk_alert(1, '只能上傳圖片檔', 'topCenter', 3)
        bk_alert(4, '只能上傳圖片檔案', 4, 'topCenter');
    }
}

var number_format = function(number, decimals, dec_point, thousands_sep) {
  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}

var padLeft = function(str,lenght){
    str = str.toString()
    if(str.length >= lenght)
        return str;
    else
        return padLeft("0" +str,lenght);
}

function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}
