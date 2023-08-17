define(["jquery", "core/log", "mod_Booking/js", "css!datatables", "datatables"], function ($, log, booking) {
	// "mod_bootstrapbase/bootstrap", 
	var Example = {
		_seqNo: null,
		init: function () {
			/*$('#booking_room').on('show.bs.modal', function(e) {
				//$(document).multiModal('show', e.target);
				//var seqNo = $(e.target).data("seq_no");
				//log.debug('seq_no: ' + seqNo);
			});/** */
			var that= this;
			$('#booking_room').on('shown.bs.modal', function(e) {
				//var seqNo = $(e.target).data("seq_no");
				debugger;
				var seqNo = that._seqNo;
				log.debug('seq_no: ' + seqNo);
				$("#show_booking_data").load(M.cfg.wwwroot + "Booking/query/" + seqNo, function (a,b,c) {
					$("#booking_room").modal({backdrop: 'static', keyboard: false}, "show");
					log.debug('start... load Booking/session/'+seqNo);
					$("#session_detail").load(M.cfg.wwwroot + "Booking/session/" + seqNo, function (a,b,c) {
						log.debug('session_detail loaded(first, call booking.getBookingLists)');
						//booking.getBookingLists();
						booking.sessionReady();
					});
				});
			});
			//debugger;
			$("#tambah0, #tambah").click(function () {
			//$("#tambah").click(function () {
				that._seqNo = $(this).data("seq_no");
				log.debug('this._seqNo: ' + this._seqNo);
			});/** */
			$("#booking_room").on("show", function () {
				$("body").addClass("modal-open");
			}).on("hidden", function () {
				$("body").removeClass("modal-open")
			});
		},
	};
	return Example;
});
