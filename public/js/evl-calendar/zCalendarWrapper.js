/**
 * jQuery Fullcalendar wrapper class
 * 
 * @author Tomasz Kuter <evolic_at_interia_dot_pl>
 * @param config
 */

function zCalendarWrapper(config) {
	this.constructor.population++;

	// ************************************************************************ 
	// PRIVATE VARIABLES AND FUNCTIONS 
	// ONLY PRIVELEGED METHODS MAY VIEW/EDIT/INVOKE 
	// *********************************************************************** 

	/**
	 * jQuery FullCalendar container e.g. '#calendar'
	 */
	var container = config.container;
	delete config.container;

	/**
	 * List of urls used to get/update/delete event(s)
	 * For example:
	 * {
	 *   get: '/events/get',
	 *   add: '/events/add',
	 *   update: '/events/update',
	 *   delete: '/events/delete'
	 * }
	 */
	var api = config.api;
	delete config.api;

	var locales = config.locales;
	delete config.locales;

	var defaults = {
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		selectable: true,
		selectHelper: true,
		firstDay: 1, // start week from Monday
		root: 'events',
		success: 'success',
		events: api.get,
		timeFormat: 'H:mm', // uppercase H for 24-hour clock
		axisFormat: 'H:mm',
		slotMinutes: 15,
		snapMinutes: 15,
		defaultEventMinutes: 45,
		select: function( startDate, endDate, allDay, jsEvent, view ) {
			createEvent( startDate, endDate, allDay, jsEvent, view );
		},
		eventDrop: function( event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view ) {
			updateEvent( event, revertFunc );
		},
		eventResize: function( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) {
			updateEvent( event, revertFunc );
		},
		eventClick: function( event, jsEvent, view ) {
			clickEvent( event );
		},
		loading: function(bool) {
			if (bool) $('#loading').show();
			else $('#loading').hide();
		}
	};

	var cfg = defaults;
	$.extend(true, cfg, config);

	var format = "yyyy-MM-dd HH:mm:ss";
	var calendar = $(container).fullCalendar(cfg);

	function createEvent( startDate, endDate, allDay, jsEvent, view ) {
		var ts = new Date().getTime();
		
		bootbox.prompt(translate('Event Title:'), function(title) {
			if (title) {
				startDate = $.fullCalendar.formatDate(startDate, format);
				endDate = $.fullCalendar.formatDate(endDate, format);
	
				$.ajax({
					url: api.add,
					data: {
						title: title,
						start: startDate,
						end: endDate,
						all_day: allDay,
						ts: ts
					},
					type: "POST",
					success: function( response ) {
						if (response.success) {
							bootbox.alert(response.message, function() {});
							var events = calendar.fullCalendar('clientEvents');
	
							for (var i in events) {
								if (typeof(events[i].ts) !== 'undefined' && events[i].ts == response.ts) {
									events[i].id = parseInt(response.id);
									delete events[i].ts;
								}
							}
						} else {
							bootbox.alert(response.message, function() {});
						}
					},
					error: function( jqXHR, textStatus, errorThrown ) {
						bootbox.alert('Error occured during saving event in the database', function() {});
					}
				});
				calendar.fullCalendar('renderEvent', {
					title: title,
					start: startDate,
					end: endDate,
					allDay: allDay,
					ts: ts
				}, true); // make the event "stick"
			}
		});
		calendar.fullCalendar('unselect');
	}

	function updateEvent( event, revertFunc ) {
		var ts = new Date().getTime();

		if (!confirm(translate("Is this okay?"))) {
			revertFunc();
		} else {
			$.ajax({
				url: api.update,
				data: {
					id: event.id,
					title: event.title,
					start: event.start.getTimestamp(),
					end: event.end.getTimestamp(),
					all_day: event.allDay,
					ts: ts
				},
				type: "POST",
				success: function( response ) {
					if (response.success) {
						bootbox.alert(response.message, function() {});
						var events = calendar.fullCalendar('clientEvents');

						for (var i in events) {
							if (typeof(events[i].ts) !== 'undefined' && events[i].ts == response.ts) {
								delete events[i].ts;
							}
						}
					} else {
						bootbox.alert(response.message, function() {});
					}
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					bootbox.alert('Error occured during saving event in the database', function() {});
				}
			});
		}
	}

	function deleteEvent ( event ) {
		
	}

	function editEvent ( event ) {
		
	}

	function clickEvent ( event ) {
		
	}

	function translate(text) {
		if (typeof(locales[text]) !== 'undefined') {
			return locales[text];
		} else {
			return text;
		}
	}
	
	// ************************************************************************ 
	// PRIVILEGED METHODS 
	// MAY BE INVOKED PUBLICLY AND MAY ACCESS PRIVATE ITEMS 
	// MAY NOT BE CHANGED; MAY BE REPLACED WITH PUBLIC FLAVORS 
	// ************************************************************************ 

	this.getCalendar = function () {
		return calendar;
	}

	// ************************************************************************ 
	// PUBLIC PROPERTIES -- ANYONE MAY READ/WRITE 
	// ************************************************************************ 

} 