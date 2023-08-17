define(['jquery', "core/log", 'mod_bootstrapbase/bootstrap', 'fullcalendar'], function($, log, init) {
//$('#show_reservation_data').load('<?= site_url("Romm/getReservation?room_type=01&start_date=2023-05-17&end_date=2023-05-27") ?>');
//$('#show_reservation_data').load('<?= site_url("fullcalendar/fullcalendar")?>');
// require(['jquery', "core/log", "mod_Createclass/init", 'mod_bootstrapbase/bootstrap'], function($, log, init) {
//     log.setConfig({
//         "level": "trace"
//     });
//     //init.init();
//debugger;
    var Example = {
        rCalendar : null, 
        rEvents: null,
        roomMeta: new Map(),
        rRoomColors: ['#00a65a', '#f39c12', '#00c0ef', '#0073b7', '#3c8dbc', '#f7f7f7', '#001F3F', '#39CCCC', '#3D9970', '#01FF70', '#FF851B', '#F012BE', '#605ca8', '#D81B60', '#b5bbc8', '#556B2F', '#FFEFD5', 'Tomato', 'Orange', 'DodgerBlue', 'MediumSeaGreen', 'Gray', 'SlateBlue', 'Violet', 'LightGray', 'Fuchsia', 'Purple', 'Silver', 'DeepPink'],
        getRemoteData(roomId, roomType, startDate, endDate) {
            self= this;
            var dataObj = {
                room: roomId,
                room_type: roomType,
                start_date: startDate,
                end_date: endDate
            };
            dataObj[M.cfg.csrfname] = M.cfg.csrfhash;
            $.ajax({
                url: M.cfg.wwwroot + 'Reservation/getREventData',
                data: dataObj,
                dataType: 'json',
                type: "GET",
                error: function(xhr) {
                    alert('Ajax request error');
                },
                success: function(response) {
                    //this.rEvents = jQuery.parseJSON(response);
                    //debugger;
                    var datas = response;
                    /*datas.forEach(function(part, index) {
                        this[index]['start'] = new Date(this[index]['start']);
                        if( this[index]['end']) {
                            this[index]['end'] = new Date(this[index]['end']);
                        }
                    }, datas);
                    self.rEvents = datas; /** */
                    self.parseData(datas);
                    self.loadCalendar(roomId);
                }
            });
        },
        // 分析booking 資訊
        parseData: function(datas) {
            self= this;
            var _eventDateObj, title;
            for( let i = 0 ; i < datas.length ; i++ ) {
                bookObj = datas[i];
                roomId = bookObj.room_id;
                roomName = bookObj.room_name;
                if (! self.roomMeta.has(roomId)) {
                    self.roomMeta.set(roomId, { name: roomName ,color: self.rRoomColors.shift() } );
                }
                for (const [key, value] of Object.entries(bookObj)) {
                    if (Array.isArray(value) && value.length > 0) {
                        value.forEach(function(part, index) {
                            //log.debug(`roomId: ` + roomId);
                            title= this[index]['CLASS_NAME'];
                            //title= this[index]['CNAME'];
                            title= title.trim();
                            //log.debug(`title: ` + title);
                            _eventDateObj = self.dcsdRoomTimeObj(key, this[index]['FROM_TIME'].trim(), this[index]['TO_TIME'].trim());
                            console.log(_eventDateObj);
                            _eventDateObj['title'] = this[index]['CLASS_NAME'];
                            _eventDateObj['borderColor'] = self.roomMeta.get(roomId).color;
                            _eventDateObj['backgroundColor'] = self.roomMeta.get(roomId).color;
                            self.rEvents.push(_eventDateObj);
                        }, value);
                    }
                }
            }
        },
        /** 從 DCSD-Phy planning/classroom 取得的格式(不一致), 轉換後直接回傳 event object */
        dcsdRoomTimeObj: function (eventDate, fromTimeStr, toTimeStr) {
            var _time, startDate, endDate, allDay = false
            fromTimeStr= fromTimeStr.trim();
            toTimeStr= toTimeStr.trim();
            if (fromTimeStr.indexOf('00:00') == 0 && toTimeStr.indexOf('23:59') == 0 ) {
                // All day 整的booking.
                return {
                    start: new Date(eventDate),
                    allDay: true
                };
            } else {
                // Start
                if (fromTimeStr.indexOf(':') >= 0) {
                    startDate= new Date(eventDate + ' ' + fromTimeStr);
                } else {
                    _time = fromTimeStr.substring(0,2) + ':' + fromTimeStr.substring(2,4);
                    startDate= new Date(eventDate + ' ' + _time);
                }
                // End
                if (toTimeStr.indexOf(':') >= 0) {
                    endDate= new Date(eventDate + ' ' + toTimeStr);
                } else {
                    _time = toTimeStr.substring(0,2) + ':' + toTimeStr.substring(2,4);
                    endDate= new Date(eventDate + ' ' + _time);
                }
                return {
                    start: startDate,
                    end: endDate,
                    allDay: allDay
                };
            }
        },
        sampleData: [{"room_id":"B102","room_name":"B\u5340\u6559\u5b78\u5927\u6a131\u6a13B102\u6559\u5ba4","2023-05-22":[{"BTYPE":"3","ROOM_ID":"B102","BOOKING_DATE":"2023-05-22 00:00:00","CLASS_NAME":"\u81ea\u6bba\u9632\u6cbb\u5b88\u9580\u4eba\u66a8\u7cbe\u795e\u885b\u751f\u7814\u7fd2\u521d\u968e\u8a13\u7df4","FROM_TIME":"0900","TO_TIME":"0920","Year":"112","TERM":"1","CNAME":"\u85cd\u5a49\u798e"},{"BTYPE":"3","ROOM_ID":"B102","BOOKING_DATE":"2023-05-22 00:00:00","CLASS_NAME":"\u81ea\u6bba\u9632\u6cbb\u5b88\u9580\u4eba\u66a8\u7cbe\u795e\u885b\u751f\u7814\u7fd2\u521d\u968e\u8a13\u7df4","FROM_TIME":"0920","TO_TIME":"1210","Year":"112","TERM":"1","CNAME":"\u85cd\u5a49\u798e"},{"BTYPE":"3","ROOM_ID":"B102","BOOKING_DATE":"2023-05-22 00:00:00","CLASS_NAME":"\u81ea\u6bba\u9632\u6cbb\u5b88\u9580\u4eba\u66a8\u7cbe\u795e\u885b\u751f\u7814\u7fd2\u521d\u968e\u8a13\u7df4","FROM_TIME":"1330","TO_TIME":"1630","Year":"112","TERM":"1","CNAME":"\u85cd\u5a49\u798e"}],"2023-05-23":[],"2023-05-24":[{"BTYPE":"1","ROOM_ID":"B102","BOOKING_DATE":"2023-05-24 00:00:00","CLASS_NAME":"\u53c3\u8207\u5f0f\u9810\u7b97\u521d\u968e\u6559\u80b2\u63a8\u5ee3\u7814\u7fd2\u73ed","FROM_TIME":"00:00:00","TO_TIME":"23:59:00","Year":"112","TERM":"1","CNAME":"\u912d\u82b3\u5b9c"}],"2023-05-25":[{"BTYPE":"1","ROOM_ID":"B102","BOOKING_DATE":"2023-05-25 00:00:00","CLASS_NAME":"\u53c3\u8207\u5f0f\u9810\u7b97\u521d\u968e\u6559\u80b2\u63a8\u5ee3\u7814\u7fd2\u73ed","FROM_TIME":"00:00:00","TO_TIME":"23:59:00","Year":"112","TERM":"1","CNAME":"\u912d\u82b3\u5b9c"}],"2023-05-26":[{"BTYPE":"1","ROOM_ID":"B102","BOOKING_DATE":"2023-05-26 00:00:00","CLASS_NAME":"\u53c3\u8207\u5f0f\u9810\u7b97\u521d\u968e\u6559\u80b2\u63a8\u5ee3\u7814\u7fd2\u73ed","FROM_TIME":"00:00:00","TO_TIME":"23:59:00","Year":"112","TERM":"1","CNAME":"\u912d\u82b3\u5b9c"}],"2023-05-27":[],"2023-05-28":[]}],
        getSampleData : function () {
            self= this;
            datas = this.sampleData;
            debugger;
            //datas.forEach(function(part, index) {
            for( let i = 0 ; i < datas.length ; i++ ) {
                bookObj = datas[i];
                roomId = bookObj.room_id;
                roomName = bookObj.room_name;
                for (const [key, value] of Object.entries(bookObj)) {
                    //log.debug(`${key}: ${value}`);
                    if (Array.isArray(value) && value.length > 0) {
                        //log.debug(`${key}: ${value}`);
                        value.forEach(function(part, index) {
                            //log.debug(`value: ` + this[index]);
                            
                            log.debug(`roomId: ` + roomId);
                            log.debug(`title: ` + this[index]['CLASS_NAME']);
                            _startDate= self.dcsdRoomTime(key, this[index]['FROM_TIME'].trim());
                            log.debug(`startDate: ` + _startDate + '(' + this[index]['FROM_TIME'] + ')');
                            _endDate= self.dcsdRoomTime(key, this[index]['TO_TIME'].trim());                           
                            log.debug(`endDate: ` + _endDate + '(' + this[index]['TO_TIME'] + ')');
                            self.rEvents.push({
                                title: this[index]['CLASS_NAME'],
                                start: _startDate,
                                end: _endDate,
                                backgroundColor: '#f56954',
                                borderColor: '#f56954'
                            });
                        }, value);
                    }
                }
            }
        }, 
        getEventsData: function () {
            //Date for the calendar events (dummy data)
            var date = new Date();
            var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear();
            //log.debug('y/m/d:' + y + m + d);
            this.rEvents = [{
                    title: 'All Day Event',
                    start: new Date(y, m, 1),
                    backgroundColor: '#f56954', //red
                    borderColor: '#f56954' //red
                },
                {
                    title: 'Long Event',
                    start: new Date(y, m, d - 5),
                    end: new Date(y, m, d - 2),
                    backgroundColor: '#f39c12', //yellow
                    borderColor: '#f39c12' //yellow
                },
                {
                    title: 'Meeting',
                    start: new Date(y, m, d, 10, 30),
                    allDay: false,
                    backgroundColor: '#0073b7', //Blue
                    borderColor: '#0073b7' //Blue
                },
                {
                    title: 'Lunch',
                    start: new Date(y, m, d, 12, 0),
                    end: new Date(y, m, d, 14, 0),
                    allDay: false,
                    backgroundColor: '#00c0ef', //Info (aqua)
                    borderColor: '#00c0ef' //Info (aqua)
                },
                {
                    title: 'Birthday Party',
                    start: new Date(y, m, d + 1, 19, 0),
                    end: new Date(y, m, d + 1, 22, 30),
                    allDay: false,
                    backgroundColor: '#00a65a', //Success (green)
                    borderColor: '#00a65a' //Success (green)
                },
                {
                    title: 'Click for Google',
                    start: new Date(y, m, 28),
                    end: new Date(y, m, 29),
                    url: 'http://google.com/',
                    backgroundColor: '#3c8dbc', //Primary (light-blue)
                    borderColor: '#3c8dbc' //Primary (light-blue)
                }
            ];
            log.debug(this.rEvents);
        },
        init: function () {
            log.debug($("#query_reservation"));
            $("#query_reservation").submit(function(e) {
                e.preventDefault();
                console.log('submit!!');
            });
        },
        init_events: function (ele) {
            //self= this;
            ele.each(function () {
                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                    title: $.trim($(this).text()) // use the element's text as the event title
                }

                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject)

                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex        : 1070,
                    revert        : true, // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                })

            })
        },
        dropEvent: function(date, allDay) { // this function is called when something is dropped
            self= this;
            //today = new Date();
            //debugger;
            // retrieve the dropped element's stored Event Object
            var originalEventObject = $(this).data('eventObject')

            // we need to copy it, so that multiple events don't have a reference to the same object
            var copiedEventObject = $.extend({}, originalEventObject)

            // assign it the date that was reported
            copiedEventObject.start = date._d;
            copiedEventObject.allDay = allDay;
            copiedEventObject.backgroundColor = $(this).css('background-color');
            copiedEventObject.borderColor = $(this).css('border-color');

            // render the event on the calendar
            // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
            $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)
            //self.rCalendar.on('renderEvent', copiedEventObject, true);

            // is the "remove after drop" checkbox checked?
            if ($('#drop-remove').is(':checked')) {
                // if so, remove the element from the "Draggable Events" list
                $(this).remove()
            }
        },
        loadCalendar: function (roomId) {
            self= this;
            log.debug('mod_fullcalender-query parse data');
            //this.parseData();

            log.debug($("#query_reservation load calendar"));
            $('#show_reservation_data').load(M.cfg.wwwroot + 'fullcalendar/smallCalendar', function(a, b, c) {
                //self.init_events($('#external-events div.external-event'));
                self.rCalendar = $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month', //,agendaWeek' //,agendaDay'
                    },
                    buttonText: {
                        today: '回到今天',
                        month: '本月',
                        week: '本週',
                        day: '本日'
                    },
                    //events: self.getEventsData(),
                    events: self.rEvents,
                    editable: false,
                    droppable: false, // this allows things to be dropped onto the calendar !!!
                    //renderEvent: 
                    drop: self.dropEvent
                });
                self.prependRoomColor(roomId, self.roomMeta);
            });
        },
        prependRoomColor: function(roomId, rooms) {
            $('#external-events').empty();
            if (roomId) {
                val = rooms.get(roomId);
                var event = $('<div />'), currColor = val.color;
                event.css({
                    'background-color': currColor,
                    'border-color': currColor,
                    'color': '#fff'
                }).addClass('external-event');
                event.html(val.name);
                $('#external-events').append(event);
            } else {
                rooms.forEach(function(val, key){
                    log.debug(key, val);
                    //Create events
                    var event = $('<div />'), currColor = val.color;
                    event.css({
                        'background-color': currColor,
                        'border-color': currColor,
                        'color': '#fff'
                    }).addClass('external-event');
                    event.html(val.name);
                    //$('#external-events').prepend(event);
                    $('#external-events').append(event);
                });
            }
        }
    }
    
    return Example;
});