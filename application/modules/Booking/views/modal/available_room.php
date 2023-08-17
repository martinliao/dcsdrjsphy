<style type="text/css">
    .modal-dialog90 {
        width: 80% !important;
        display: block !important;
    }

    .modal-dialog90 .modal-body {
        height: 70vh;
        overflow-y: auto;
    }

    .modal-dialog {
        overflow-y: initial !important
    }

    .modal-body {
        height: 80vh;
        overflow-y: auto;
    }/** */
</style>
<!-- query_available -->
<!-- <div class="modal fade" id="available_room" role="dialog" style="background: rgba(0, 0, 0, 0.5);z-index: 1072 !important; padding: 30px" -->
<div id="available_room" class="modal fade" role="dialog" 
    aria-labelledby="availableRoomModalLabel" aria-hidden="true">
    <!-- modal-lg modal-dialog-centered -->
    <div class="modal-dialog modal-dialog90 ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="availableRoomModalLabel" class="modal-title"><?= $form['class_name'] ?> 第<?= $form['term']; ?>期 人數:<?= $form['no_persons'] ?> , 訓練期程<?= $form['range'] ?>小時 預約教室 - 可選的教室選擇</h4>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="-1">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body">

                <form id="query_available" class="form-inline">
                    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
                    <input type="hidden" id="seq_no" name="seq_no" value="<?= set_value('seq_no', $form['seq_no']); ?>">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label">預約使用日期</label>
                            <div class="input-daterange input-group" style="width: 300px;">
                                <input type="text" class="form-control datepicker" id="query_start_date" name="start_date" value="<?= $filter['start_date']; ?>" />
                                <span class="input-group-addon" style="cursor: pointer;" id="query_small_cal1"><i class="fa fa-calendar"></i></span>
                                <!-- <span class="input-group-addon">to</span> -->
                                <input type="text" class="form-control datepicker" id="query_end_date" name="end_date" value="<?= $filter['end_date']; ?>" />
                                <span class="input-group-addon" style="cursor: pointer;" id="query_small_cal2"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">類別</label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="room_type" id="Check1" value="A" <?= set_checkbox('class_room_type_A', 'A', $filter['class_room_type_A'] == 'A'); ?>>
                                一般教室
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="room_type" id="Check2" value="B" <?= set_checkbox('class_room_type_B', 'B', $filter['class_room_type_B'] == 'B'); ?>>
                                電腦教室
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="control-label">時段</label>
                            <select class="form-control" id="room_time" name="room_time">
                                <!-- onchange="get_place();" -->
                                <option value="">請選擇</option>
                                <?php foreach ($choices['time_list'] as $key => $time) { ?>
                                    <?php if ($key == 16) : ?>
                                        <option value="<?= $key; ?>" selected><?= $time; ?></option>
                                    <?php else : ?>
                                        <option value="<?= $key; ?>"><?= $time; ?></option>
                                    <?php endif; ?>
                                <?php } ?>
                            </select>
                        </div>
                        <button class="btn btn-info btn-sm">查詢</button>
                        <!-- <button type="submit" class="btn btn-primary" id="btn">開始預約教室</button> -->
                        <!-- <a class="btn btn-info btn-sm" onclick="doClear()">清除</a> -->
                    </div>
                </form>
                <hr />
                <div class="card card-primary card-outline" id="available_card">
                    place holder
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="arclose">取消</button>
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button> -->
                <!-- <button type="submit" class="btn btn-primary" id="btn">開始預約教室</button> -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    require(['jquery', "core/log", "mod_Booking/js2", 'mod_bootstrapbase/bootstrap'], function($, log, booking2) {
        booking2.init();
        // $(document).ready(function() {
        //     $("#start_date").datepicker();
        //     $('#small_cal1').click(function() {
        //         $("#start_date").focus();
        //     });
        // });

        // $(document).ready(function() {
        //     $("#end_date").datepicker();
        //     $('#small_cal2').click(function() {
        //         $("#end_date").focus();
        //     });
        // });
    });
</script>
