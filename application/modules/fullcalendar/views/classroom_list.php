<!-- Custom Fonts -->
<link href="<?=HTTP_PLUGIN;?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<!-- Bootstrap DateTime and Date Picker CSS -->
<link href="<?=HTTP_PLUGIN;?>datepicker/css/jquery-ui.css" rel="stylesheet">
<!-- .panel-body -->
<!-- query_reservation -->
<div class="row">
    <form id="query_reservation" role="form" class="form-inline">
        <input type="hidden" name="sort" value="" />
        <div class="col-xs-12">
            <div class="form-group required">
                <label class="control-label">場地類別</label>
                <?php
                $choices['room_type'] = array('' => '請選擇') + $choices['room_type'];
                echo form_dropdown('room_type', $choices['room_type'], $filter['room_type'], 'class="form-control" id="set_room_type" onchange="getRoomList();"');
                ?>
            </div>
            <div class="form-group">
                <label class="control-label">場地名稱</label>
                <?php
                $choices['room'] = array('' => '請選擇') + $choices['room'];
                echo form_dropdown('room', $choices['room'], $filter['room'], 'class="form-control" id="room" ');
                ?>
            </div>
            <div class="form-group required">
                <label class="control-label">使用日期</label>
                <div class="input-daterange input-group" style="width: 300px;">
                    <input type="text" class="form-control datepicker" id="start_date" name="start_date" value="<?= $filter['start_date']; ?>" />
                    <span class="input-group-addon" style="cursor: pointer;" id="small_cal1"><i class="fa fa-calendar"></i></span>
                    <!-- <span class="input-group-addon">to</span> -->
                    <input type="text" class="form-control datepicker" id="end_date" name="end_date" value="<?= $filter['end_date']; ?>" />
                    <span class="input-group-addon" style="cursor: pointer;" id="small_cal2"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <button class="btn btn-info btn-sm">查詢</button>
            <a class="btn btn-info btn-sm" onclick="doClear()">清除</a>
        </div>
        <div class="col-xs-6">
            <span style="white-space:pre; color:#000000;"><b>黑色</b>:表示已預約, <b>藍色</b>:表示外借已使用, <b>紅色</b>:表示班期已使用 (本功能只能修改預約資料)</span>
        </div>
    </form>
</div>
<div id="show_reservation_data"></div>
<!--div class="card-body pad table-responsive">
                    <table class="table table-bordered table-sm" id="booking_table" width="100%">
                        <thead>
                            <tr>
                                <th>期別</th>
                                <th>開課起日</th>
                                <th>開課迄日</th>
                                <th>教室名稱</th>
                                <th>預約時段</th>
                            </tr>
                        </thead>
                    </table>
                </div-->
<!-- /.panel-body -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        $("#query_reservation").submit(function(e) {
            e.preventDefault();
            var roomId = $(e.target).find('select[name="room"]').val(); //'B102';
            var roomType = $(e.target).find('select[name="room_type"]').val(); // '01';
            require(['jquery', "core/log", "mod_fullcalendar/js", 'mod_bootstrapbase/bootstrap'], function($, log, fullcal) {
                var startDate = $("#query_reservation").find('input[name="start_date"]').val();
                var endDate = $("#query_reservation").find('input[name="end_date"]').val();
                fullcal.rEvents = [];
                fullcal.rQueryRooms = [];
                //fullcal.roomColors = new Map();
                fullcal.getRemoteData(roomId, roomType, startDate, endDate);
                // Debug
                //fullcal.getSampleData(); // Sample for debug/test
                //fullcal.getEventsData();
                //fullcal.query();
                log.debug('rjs: submit and loading...');
                //data from PHP sample: booking.getBookingLists(<?= set_value('seq_no', $form['seq_no']); ?>);
            });
        });
        /** 以下 script 從 planning/classroom/list 來; 除了命名或bug, 原則不動 */
        getRoomList = function() {
            var url = '<?= base_url('planning/classroom/ajax/get_room'); ?>';
            var room_type = $('#set_room_type').val();
            var data = {
                '<?= $csrf["name"]; ?>': '<?= $csrf["hash"]; ?>',
                'room_type': room_type,
            }
            $.ajax({
                url: url,
                data: data,
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        setRoomList(response.data);
                    } else {

                    }
                }
            });
        }

        function setRoomList(DataList) {
            obj = document.getElementById('room');
            dataAry = DataList;
            obj.options.length = 0;
            var new_option = new Option('請選擇', '');
            obj.options.add(new_option);
            for (i = 0; i < dataAry.length; i++) {
                strAry = dataAry[i];
                if (strAry[0] != "") {
                    var new_option = new Option(strAry.room_name, strAry.room_id);
                    obj.options.add(new_option);
                }
            }
        }

        function doClear() {
            $("#start_date").val('');
            $("#end_date").val('');
        }

        $(document).ready(function() {
            $("#start_date").datepicker();
            $('#small_cal1').click(function() {
                $("#start_date").focus();
            });
        });

        $(document).ready(function() {
            $("#end_date").datepicker();
            $('#small_cal2').click(function() {
                $("#end_date").focus();
            });
        });
    });
</script>