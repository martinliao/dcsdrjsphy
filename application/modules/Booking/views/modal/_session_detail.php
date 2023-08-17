<style type="text/css">
    .modal-command {
        padding: 15px;
        text-align: right;
        border-top: 1px solid #e5e5e5;
    }
</style>

<div class="box-header with-border">
    <div class="mailbox-controls">
        <span class="box-title h4">班期內容</span>
        <div class="box-tools pull-right">
            <!-- <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button> -->
            <!-- 切換班期
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
            </div> -->
            <!-- /.btn-group -->
        </div>
        <!-- /.pull-right -->
    </div>
    <!-- /.box-tools -->
</div>
<!-- /.box-header -->
<div class="box-body no-padding">
    <!-- edit form -->
    <form id="query_bookingroom" role=form data-toggle="validator">
        <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
        <input type="hidden" id="seq_no" name="seq_no" value="<?= set_value('seq_no', $form['seq_no']); ?>">
        <div class="form-group col-xs-3">
            <label class="control-label">年度</label>
            <input class="form-control" name="year" id="year" readonly="" placeholder="" value="<?= set_value('year', $form['year']); ?>" required>
            <?= form_error('year'); ?>
        </div>
        <div class="form-group col-xs-4">
            <label class="control-label">班期代碼</label>
            <input class="form-control" name="class_no" id="class_no" readonly="" placeholder="" value="<?= set_value('class_no', $form['class_no']); ?>" required>
            <?= form_error('class_no'); ?>
        </div>
        <div class="form-group col-xs-5">
            <label class="control-label">班期名稱</label>
            <input class="form-control" name="class_name" id="class_name" readonly="" placeholder="" value="<?= set_value('class_name', $form['class_name']); ?>" required>
            <?= form_error('class_name'); ?>
        </div>
        <div class="form-group col-xs-6 required <?= form_error('term') ? 'has-error' : ''; ?>">
            <label class="control-label">期別</label>
            <input class="form-control" name="term" id="term" readonly="" placeholder="" value="<?= set_value('term', $form['term']); ?>" required>
            <?= form_error('term'); ?>
        </div>
        <div class="form-group col-xs-6 required <?= form_error('no_persons') ? 'has-error' : ''; ?>">
            <label class="control-label">本期人數</label>
            <input class="form-control" id="no_persons" name="no_persons" readonly placeholder="" value="<?= set_value('no_persons', $form['no_persons']); ?>" required>
            <?= form_error('no_persons'); ?>
        </div>
        <div class="form-group col-xs-6 required <?= form_error('range') ? 'has-error' : ''; ?>">
            <label class="control-label">訓練期程(小時)</label>
            <input class="form-control" id="range" name="range" readonly placeholder="" value="<?= set_value('range', $form['range']); ?>" required>
            <?= form_error('range'); ?>
        </div>
        <div class="form-group col-xs-3">
            <label class="control-label">開課起日</label>
            <div class="input-group" id="start_date">
                <input type="text" class="form-control <?= form_error('start_date') ? 'has-error' : ''; ?> datepicker" id="set_start_date" name="start_date" value="<?= set_value('start_date', !empty($form['start_date']) ? date('Y-m-d', strtotime($form['start_date'])) : ''); ?>" <?= ($unlock_start_date == 'true') ? '' : 'disabled' ?> />
                <span class="input-group-addon" style="cursor: pointer;"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
        <div class="form-group col-xs-3">
            <label class="control-label">開課迄日</label>
            <div class="input-group" id="end_date">
                <input type="text" class="form-control <?= form_error('end_date') ? 'has-error' : ''; ?> datepicker" id="set_end_date" name="end_date" value="<?= set_value('end_date', !empty($form['end_date']) ? date('Y-m-d', strtotime($form['end_date'])) : ''); ?>" <?= ($unlock_end_day1 == 'true') ? '' : 'disabled' ?> />
                <span class="input-group-addon" style="cursor: pointer;"><i class="fa fa-calendar"></i></span>
            </div>
        </div>

        <div class="modal-command">
            <!-- <button type="button" class="btn btn-secondary"></button> -->
            <button type="button" class="btn btn-success" id="new_booking">預約教室</button>
            <!-- <button type="button" class="btn btn-success" id="new_booking" data-toggle="modal" data-target="#available_room">預約教室</button> -->
        </div>
    </form>
</div>
<!-- /.box-body -->