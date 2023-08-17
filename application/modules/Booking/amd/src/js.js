define(["jquery", "core/log", "mod_Booking/js2", "jqueryui"], function ($, log, booking2) {
	//, "mod_Booking/multi_modal"
	//, "bootstrapDataTables"
	//"mod_Booking/multi_modal", 
	// datatables
	// bootstrapDataTables // 需要 datatables.net
	// jqueryDataTables
	// "mod_bootstrapbase/bootstrap",
	var Example = {
		//document.addEventListener("DOMContentLoaded", () => {
		init: function () {
			that = this;
			log.debug('mod_Booking, init');
			//const form = $('.modal-body').html();
			var _seqNo = null;
			$("#all_class li").each(function () {
				$(this).find("a").on("click", function (a, b, c) {
						_seqNo = $(this).data("seq_no"); // $.trim($(this).text());
						//log.debug('li click: ' +  _seqNo);
						$("#query_bookingroom input[id=seq_no]").val(_seqNo);
						log.debug('loading... Booking/query/' + _seqNo);
						$("#show_booking_data").load(M.cfg.wwwroot + "Booking/query/" + _seqNo, function (a,b,c) {
							log.debug('loading... Booking/session/' + _seqNo);
							$("#session_detail").load(M.cfg.wwwroot + "Booking/session/" + _seqNo, function(a,b,c){
								log.debug('session_detail loaded');
								that.getBookingLists();
								//that.sessionReady();
								$("#available_room").on('shown.bs.modal', function (e) {
									$("#available_room").on('click', '.btn-secondary', function() {
										$('#available_room').modal('hide');
									});
								});
							});
						});
					});
			});
			$(document).on('show.bs.modal', '.modal', function () {
				var zIndex = 1040 + (10 * $('.modal:visible').length);
				$(this).css('z-index', zIndex);
				setTimeout(function() {
					$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
				}, 0);
			});
		},
		sessionReady: function () {
			$("#new_booking").on("click", function () {
				debugger;
                $("#available_room").modal("show");
                /*$("#available_room").on('shown.bs.modal', function (e) {
                    log.debug('available_room is shown.');
// //debugger;
// 					$("#query_start_date").datepicker({
// 						showMonthAfterYear: true,
// 						changeMonth: true,
// 						changeYear: true,
// 						yearRange: "-100:+0"
// 					});
// 					$('#query_small_cal1').click(function() {
// 						$("#query_start_date").focus();
// 					});
// 					$("#query_end_date").datepicker({
// 						showMonthAfterYear: true,
// 						changeMonth: true,
// 						changeYear: true,
// 						yearRange: "-100:+0"
// 					});
// 					$('#query_small_cal2').click(function() {
// 						$("#query_end_date").focus();
// 					});
                });/** */
            });/** */
		},

		getBookingLists: function () {
			if (!$.fn.DataTable.isDataTable("#booking_table")) {
				$("#booking_table").DataTable({
					processing: true,
					serverSide: true,
					order: [],
					ajax: {
						url: M.cfg.wwwroot + "Booking/getLists",
						type: "POST",
						data: function (d) {
							var dataObj = {
								seq_no: $("#query_bookingroom input[id=seq_no]").val(),
							};
							dataObj[M.cfg.csrfname] = M.cfg.csrfhash;
							return $.extend({}, d, dataObj);
						},
					},
					columnDefs: [
						{
							targets: [0, 1],
							orderable: false,
							width: "5%",
						},
					],
					info: false,
					paging: false,
					searching: false,
					drawCallback: function (settings) {
						$("#booking_table").on("click", ".edit", function () {
							//initDaterange0();
							//initDaterange1('<?=$form['start_date']?>', '<?=$form['end_date']?>');
							//catId = $(this).data("cat_id");
							seqNo = $(this).data("seq_no");
							$("#available_room").modal("show");
						});
						$("#booking_table").on("click", ".delete", function () {
							//var seqNo = $("#query_available").find('input[name="seq_no"]').val();
							var seqNo = $(this).data('seq_no');
							var roomId = $(this).data('room_id');
							var bookingPeriod = $(this).data('booking_period');
							var roomType = $(this).data('cat_id');
							var startDate = $(this).data('start_date');
							var endDate = $(this).data('end_date');
							var dataObj = {
								seq_no: seqNo,
								room_id: roomId,
								'cat_id': roomType,
								'booking_period': bookingPeriod,
								'start_date': startDate,
								'end_date': endDate,
							};
							dataObj[M.cfg.csrfname] = M.cfg.csrfhash;
							log.debug('DELETE: seq_no: ' + seqNo + ', Room: '+ roomId, ' date: '+ startDate + '~' + endDate);
							//url: M.cfg.wwwroot + "Room/bookingRoom", // ToDo: new request?
							$.ajax({
								url: M.cfg.wwwroot + "venue_rental/classroom/ajax/del_booking",
								type: "POST",
								data: dataObj,
								dataType: 'json',
								success: function (response) {
									//if (response.success == true) { // ToDo: The response is status(NOT sucess).
									if (response.status == true) {
										toastr['success']('seq_no: ' + seqNo + ', Room: '+ roomId, ' date: '+ startDate + '~' + endDate);
										$("#booking_table").DataTable().ajax.reload();
									} else {
										toastr['error'](response.message);
									}
								}
							});
						});
					},
				});
				$("#booking_table").DataTable().columns([2]).visible(false);
			} else {
				$("#booking_table").DataTable().ajax.reload();
			}
		},

		cb: function (start, end) {
			$('input[name="daterange"] span').html(
				start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
			);
		},

		initDaterange: function () {
			var start = moment().subtract(29, "days");
			var end = moment();

			$('input[name="daterange"]').daterangepicker(
				{
					startDate: start,
					endDate: end,
					ranges: {
						Today: [moment(), moment()],
						Yesterday: [
							moment().subtract(1, "days"),
							moment().subtract(1, "days"),
						],
						"Last 7 Days": [moment().subtract(6, "days"), moment()],
						"Last 30 Days": [moment().subtract(29, "days"), moment()],
						"This Month": [moment().startOf("month"), moment().endOf("month")],
						"Last Month": [
							moment().subtract(1, "month").startOf("month"),
							moment().subtract(1, "month").endOf("month"),
						],
					},
				},
				cb
			);

			cb(start, end);
		},

		// 最簡單的 + 最常用功能
		initDaterange1: function (start, end) {
			$('input[name="daterange"]').daterangepicker({
				timePicker: false,
				locale: {
					format: "YYYY-MM-DD",
				},
				startDate: start,
				endDate: end,
				ranges: {
					今天: [moment(), moment()],
					往回1個月: [moment().subtract(29, "days"), moment()],
					往前1個月: [moment(), moment().add(29, "days")],
					這個月: [moment().startOf("month"), moment().endOf("month")],
					下個月: [
						moment().subtract(1, "month").startOf("month"),
						moment().subtract(1, "month").endOf("month"),
					],
				},
			});
		},

		// 最簡單的
		initDaterange0: function () {
			$('input[name="daterange"]').daterangepicker({
				timePicker: false,
				locale: {
					format: "YYYY-MM-DD",
				},
			});
		},
	};
	return Example;
});
