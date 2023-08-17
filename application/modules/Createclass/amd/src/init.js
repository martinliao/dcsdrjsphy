define(["jquery", "core/log", "mod_Booking/js", "css!datatables", "datatables"], function ($, log, booking) {
	// "mod_bootstrapbase/bootstrap", 
	var Example = {
		_seqNo: null,
		init: function () {
			$("#tambah0, #tambah").click(function () {
				debugger;
				var seqNo = $(this).data("seq_no");
				log.debug('seq_no: ' + seqNo);
				$("#show_booking_data").load(M.cfg.wwwroot + "Booking/query/" + seqNo, function (a,b,c) {
					$("#booking_room").modal("show");
					log.debug('start... load Booking/session/'+seqNo);
					$("#session_detail").load(M.cfg.wwwroot + "Booking/session/" + seqNo, function (a,b,c) {
						log.debug('after session loaded, call booking.getBookingLists');
						booking.getBookingLists();
						booking.sessionReady();
						$("#available_room").on('shown.bs.modal', function (e) {
							$("#available_room").on('click', '.btn-secondary', function() {
								$('#available_room').modal('hide');
							});
						});
					});
				});
			});
			$("#booking_room").on("show", function () {
				$("body").addClass("modal-open");
			}).on("hidden", function () {
				$("body").removeClass("modal-open")
			});
		},
	};
	return Example;
});
