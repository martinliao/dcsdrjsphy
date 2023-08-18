<style type="text/css">
    .modal-header .close {
        margin-top: -17px !important;
        font-size: 36px;
        outline: none;
    }
    .modal-dialog70 {
        width: 70vw !important;
    }
    /* .modal-dialog80 .modal-body {
        height: 75vh;
        overflow-y: auto;
    } */
    body.modal-open {
        overflow: hidden;
    }
</style>
<!-- modal : 預約教室 -->
<div class="modal" id="booking_room" role="dialog" data-keyboard=false data-backdrop=static 
    aria-labelledby="myBookRoomLabel" aria-hidden="true"
    >
<!-- modal-lg -->
    <div class="modal-dialog modal-lg modal-dialog70">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">預約教室(in 3B)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="-1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="show_booking_data">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                <!-- <button type="submit" class="btn btn-primary" id="btn">查詢預約的教室</button> -->
                <!--button type="button" class="btn btn-success btn-sm" id="tambah2">開始預約教室</button-->
            </div>
        </div>
    </div>
</div>
