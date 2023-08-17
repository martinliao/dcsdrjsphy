define(["jquery", "core/log", "jqueryui"], function ($, log) {
	var Example = {
		init: function () {
			self = this;
			$("input[name='room_type']").change(function () {
				$.each($("input[name='room_type']"), function () {
					this.setCustomValidity("");
				});
				var checked = $("input[name='room_type']:checked").length;
				if (checked == 0) {
					$("input[name='room_type']")[0].setCustomValidity("請至少選擇1種.");
				}
			});
			$("#query_available").submit(function (e) {
				e.preventDefault();
				//console.log("submit!!");
				var checked = $("input[name='room_type']:checked").length;
				if (checked == 0) {
					$("input[name='room_type']")[0].setCustomValidity("請至少選擇1種.");
					$("input[name='room_type']")[0].reportValidity();
					return false;
				}
				// ToDo: No need these more.
				var seqNo = $(this).find('input[name="seq_no"]').val();
				var startDate = $(this).find('input[name="start_date"]').val();
				var endDate = $(this).find('input[name="end_date"]').val();
				// $(this).find('input[name="room_type"]').val()
				var timeId = $(this).find('select[name="room_time"]').val();

				/** 直接呼叫 datatable (init or reload) */
				//self.getRoomSchedule("01", parseInt(timeId), startDate, endDate);
				/** 改load, 再呼叫 getRoomSchedule? */
				var dataObj= {
					start_date: startDate,
					end_date: endDate,
					room_type: '01',
					time_id: timeId
				};
				dataObj[M.cfg.csrfname] = M.cfg.csrfhash;
				$('#available_card').load(
					M.cfg.wwwroot + "Booking/availableTable/" + seqNo, dataObj, function (a,b,c) {
					self.getRoomSchedule("01", parseInt(timeId), startDate, endDate);
				});
				return false;
				// room_type=01&room=&start_date=2023-05-08&end_date=2023-05-15
				// self.getRoomSchedule('01', 16, '2023-05-08', '2023-05-15');
			});
		},

		getRoomSchedule: function (roomType, timeId, startDate, endDate) {
			//self = this;
			// planning/classroom?sort=&room_type=01&room=&start_date=2023-05-08&end_date=2023-05-15
			if (!$.fn.DataTable.isDataTable("#available_table")) {
				$("#available_table").DataTable({
					responsive: true,
					dom: 'Bfrtip',
					"bSort": false,
					processing: true,
					serverSide: true,
					order: [],
					screenY: 200,
					ajax: {
						url: M.cfg.wwwroot + "Room/getAvailableRoom",
						type: "POST",
						data: function (d) {
							var dataObj = {
								room_type: "01",
								start_date: $("#query_available").find('input[name="start_date"]').val(),
								end_date: $("#query_available").find('input[name="end_date"]').val(),
								room_time: $("#query_available").find('select[name="room_time"]').val(),
							};
							dataObj[M.cfg.csrfname] = M.cfg.csrfhash;
							return $.extend({}, d, dataObj);
						},
					},
					columnDefs: [{
						targets: [0, 1, 3],
						orderable: false,
						width: "3%",
					}],
					info: false,
					paging: false,
					searching: false,
					drawCallback: function (settings) {
						//self.init();
						//log.debug("draw2 done. ToDo: check booking.");
						/** Checkbox event */
						$('#available_data').find('input[type=checkbox]').on('click', function(e){
							var checked = $(this).is(':checked');
							var seqNo = $("#query_available").find('input[name="seq_no"]').val();
							var roomId = $(this).data('room_id');
							var bookingDate = $(this).data('bookingdate');
							var roomTime = $("#query_available").find('select[name="room_time"]').val();
							var dataObj = {
								seq_no: seqNo,
								room_id: roomId,
								room_time: roomTime,
								bookingdate: bookingDate,
								checked: checked
							};
							if (checked) {
								self.doBooking(dataObj, function (response) {
									if (response.success == true) {
										toastr['success']('成功: seq_no: ' + seqNo + ', Room: '+ roomId, ' date: '+ bookingDate);
									} else {
										toastr['error']('失敗.' + response.message);
									}
								});
							} else {
								self.removeBooking(dataObj, function (response) {
									//if (response.success == true) {
									if (response.status == true) {
										toastr.warning('刪除成功. seq_no: ' + seqNo + ', Room: '+ roomId, ' date: '+ bookingDate);
									} else {
										toastr['error']('刪除失敗, ' + response.message);
									}
								});
							}
						});
					},
				});
				//$('#available_table').DataTable().columns([2]).visible(false);
			} else {
				$("#available_table").DataTable().ajax.reload();
			}
		},
		doBooking: function(dataObj, responseCallback) {
			dataObj[M.cfg.csrfname] = M.cfg.csrfhash;
			//log.debug(dataObj);
			$.ajax({
				url: M.cfg.wwwroot + "Room/bookingRoom",
				type: "POST",
				data: dataObj,
				dataType: 'json',
				success: responseCallback
			});
		},
		removeBooking: function(dataObj, responseCallback) {
			var removeObj = {
				seq_no: dataObj.seq_no,
				room_id: dataObj.room_id,
				'cat_id': '01',
				'booking_period': dataObj.room_time,
				'start_date': dataObj.bookingdate,
				'end_date': dataObj.bookingdate
			};
			removeObj[M.cfg.csrfname] = M.cfg.csrfhash;
			log.debug(removeObj);
			//用舊的. url: M.cfg.wwwroot + "Room/bookingRoom", // ToDo: new request?
			$.ajax({
				url: M.cfg.wwwroot + "venue_rental/classroom/ajax/del_booking",
				type: "POST",
				data: removeObj,
				dataType: 'json',
				success: responseCallback
			});
		}
	};

	return Example;
});
