<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="form-group col-xs-6">
        <label class="control-label">年度</label>
        <input class="form-control" name="year" placeholder="" value="<?=set_value('year', $form['year']); ?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期代碼</label>
        <input class="form-control" name="class_no" placeholder="" value="<?=set_value('class_no', $form['class_no']); ?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">班期名稱</label>
        <input class="form-control" name="class_name" placeholder="" value="<?=set_value('class_name', $form['class_name']); ?>" disabled>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">期別</label>
        <input class="form-control" name="term" placeholder="" value="<?=set_value('term', $form['term']); ?>" readonly>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">可報名人數</label>
        <input class="form-control" name="no_persons" placeholder="" value="<?=set_value('no_persons', $form['no_persons']); ?>" disabled>
    </div>
    <div class="form-group col-xs-6 <?=form_error('room_id')?'has-error':'';?>">
        <label class="control-label">原有教室</label>
        <?php
            $year = $form['year'];
            $class_no = $form['class_no'];
            $term = $form['term'];
            echo form_dropdown('room_id', $choices['room_id'], set_value('room_id', ''), 'class="form-control" id="room_id" onchange="getDate('.$year.',\''.$class_no.'\','.$term.')"');
        ?>
        <?=form_error('room_id'); ?>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">日期</label>
        <select class="form-control" name='use_date' id='use_date'>
        
        </select>
    </div>
    <div class="form-group col-xs-6 <?=form_error('new_room_id')?'has-error':'';?>">
        <label class="control-label">更新教室</label>
        <input type="button" class="btn btn-xs btn-primary" onclick="showRoom()" value="查詢">
        <input type="hidden" id="new_room_id" name="new_room_id" class="btn btn-primary" value="">
        <input class="form-control" id="new_room" name="new_room" placeholder="" value="" readonly>
        <?=form_error('new_room_id'); ?>
    </div>
</form>

<script type="text/javascript">
    function removeOptions(selectbox) {
        var i;
        for (i = selectbox.options.length - 1; i >= 0; i--) {
            selectbox.remove(i);
        }
    }

    function showRoom(){
      var tmp = document.getElementById('use_date').value;
      if (tmp!="")
      {
        myW=window.open('../../../../co_room_popup.php?mode=2&field1=new_room_id&field2=new_room&course_date='+tmp,'show_room','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=700,width=640');
        myW.focus();
      }
      else
      {
        alert("請先選擇日期");
      }
    }

    function getDate(year,class_no,term){
        removeOptions(document.getElementById("use_date"));

        var room_id = document.getElementById('room_id').value;

        var link = "<?=$link_get_room_date;?>";
        var data = {
            '<?=$csrf["name"];?>': '<?=$csrf["hash"];?>',
            'year': year,
            'class_no': class_no,
            'term': term,
            'room_id':room_id
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
                    for (var i = 0; i < result.length; i++) {
                        var second = document.getElementById('use_date');
                        var option_name = result[i]['use_date'];
                        var option_value = result[i]['use_date'];
                        var new_option = new Option(option_name, option_value);
                        second.options.add(new_option);
                    }
                }
            }
        });
    }
</script>
