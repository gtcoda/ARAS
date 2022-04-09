import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
var items = [];


export default {
	setData(newItem) {
		items = newItem;
	},

	async render() {
		// Очистим элемент для приложения
		resultsNode.innerHTML = "";
		var initialLocaleCode = 'ru';
		var localeSelectorEl = document.getElementById('locale-selector');
		//var calendarEl = document.getElementById('calendar');
		let div = document.createElement('div');
		div.id = "divCalendar";
		document.getElementById(settings.getApp()).append(div);


		var calendarEl = document.getElementById("divCalendar");


		var Repair_data = await Model.repairGranttData();



		console.log(Repair_data);

		var calendar = new FullCalendar.Calendar(calendarEl, {
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,listMonth'
			},
			themeSystem: 'bootstrap5',
			initialDate: '2022-04-01',
			locale: initialLocaleCode,
			buttonIcons: false, // show the prev/next text
			weekNumbers: false, // Номера недель
			navLinks: true, // can click day/week names to navigate views
			editable: false,
			dayMaxEvents: true, // allow "more" link when too many events
			//events: Repair_data,
			eventClick: function (info) {
				console.log(info);
				console.log(info.event.id);

				document.location.hash = "Events/" + info.event.id;

			},
			events: function (fetchInfo, successCallback, failureCallback) {



				console.log(fetchInfo);

				var options = {
					year : 'numeric',
					month: 'numeric',
					day: 'numeric',
					timezone: 'UTC'
				  };
				var start = new Date(fetchInfo.start).toLocaleString("fr-CA", options);
				var end = new Date(fetchInfo.end).toLocaleString("fr-CA", options);

				console.log(start);
				console.log(end);


				$.ajax({
					url: '/api/repairs?start='+start+'&end='+end,
					dataType: 'json',
					type: 'GET',
					success: function (response) {
						console.log(response);
						var user_events = response.data;
						successCallback(user_events);
					}
				})

			}


		});

		calendar.render();

	},
}