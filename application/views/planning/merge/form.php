<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
    <button class="close" data-dismiss="alert" type="button">×</button>
    <?=validation_errors();?>
</div>
<?php } ?>

<form id="data-form" role="form" method="post" action="<?=$link_save;?>" onsubmit="return checkForm(this);">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" name="year" value="<?=$form[0]['year'];?>" />
    <input type="hidden" name="class_no" value="<?=$form[0]['class_no'];?>" />

    <div class="tab-pane">
        <table class="table table-bordered table-condensed table-hover">
            <thead>
                <tr>
                    <th width="5%"></th>
                    <th width="10%">年度</th>
                    <th width="40%">班期名稱</th>
                    <th width="7%">期別</th>
                    <th width="8%">開班起日</th>
                    <th width="8%">開班迄日</th>
                    <th width="8%">承辦人</th>
                    <th width="14%">選員人數/報名總人數</th>
                </tr>
            </thead>
            <tbody>
            <?php
                for($i=0;$i<count($form);$i++){
                    echo '<tr>';
                    echo '<td class="text-center"><input type="checkbox" name="term[]" value="'.$form[$i]['term'].'"></td>';
                    echo '<td>'.$form[$i]['year'].'</td>';
                    echo '<td>'.$form[$i]['class_name'].'</td>';
                    echo '<td>'.$form[$i]['term'].'</td>';
                    echo '<td>'.$form[$i]['start_date1'].'</td>';
                    echo '<td>'.$form[$i]['end_date1'].'</td>';
                    echo '<td>'.$form[$i]['contactor'].'</td>';
                    echo '<td></td>';
                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
    </div>
</form>
<script type="text/javascript">
    function checkForm(obj){
        var check_cnt = $("input:checkbox[name='term[]']:checked").length;

        if(check_cnt <= 1){
            alert('請至少勾選2個期別');
            return false;
        }

        return true;
    }
</script>