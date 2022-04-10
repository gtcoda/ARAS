import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
var items = [];

const NoTo = 0;
const TO1 = 1;
const TO2 = 2;


var MaintenanceTypes = [];
var MaintenanceModelOn;

// Обработчик вставки таблицы
Handlebars.registerHelper('insertTypesMaintence', function (obj) {
	let data = {
		Mtypes: MaintenanceTypes,
		ModelOn: MaintenanceModelOn
	}
	return new Handlebars.SafeString(View.render("prevMainMaintenanceTypes", data));
});


// Обработчик вставки годового плана
Handlebars.registerHelper('insertYears', function (obj) {
	return new Handlebars.SafeString(View.render("prevMainYears", obj));
});


// Обработчик вставки таблицы
Handlebars.registerHelper('insertTable', function (obj) {
	return new Handlebars.SafeString(View.render("prevMainTable", obj));
});


// Обработчик вставки календаря
Handlebars.registerHelper('insertCalendar', function (obj) {
	return new Handlebars.SafeString(View.render("prevMainCalendar", obj));
});

// Обработчик вставки названия ТО
Handlebars.registerHelper('insertTO', function (obj) {
	let m;
	switch (obj) {
		case "ТО2": m = "<td class ='bg-success'>TO2</td>"; break;
		case "ТО1": m = "<td class ='bg-primary'>TO1</td>"; break;
		case NoTo: m = "<td>-</td>"; break;
	}

	return new Handlebars.SafeString(m);
});



// Создать модальное окно
// html - код модального окна 
function modal(html, id) {
	$(html) // Создание элемента div
		.addClass("dialog") // Назначение элементу класса
		.attr('id', id)     // Назначить окну id
		.appendTo("body")   // Вставляет в элемент, после содержимого
		.dialog({ // Создание из элемента диалогового окна
			title: $(html).attr("data-dialog-title"), // Добавим в заголовок атрибут.
			close: function () { // Действие по крестику
				$(this).remove()
			},
			modal: true, // Нельзя взаимойдействовать с страницей
			draggable: true
		})
}



export default {

	async navigate(e){
		var Mapp = document.getElementById("MaintenenceApps");


		console.log(e.currentTarget.getAttribute("role"));


		switch (e.currentTarget.getAttribute("role")){
			case "Set":  break;
			case "Years": break;
			case "Calendar": Mapp.innerHTML = View.render("prevMainCalendar"); break;
			

		}



		
	},


	async setData() {
		MaintenanceTypes = await Model.getMaintenceTypes();
		MaintenanceModelOn = await Model.getMaintenceModelOn();
	},

	async render() {

		items = await Model.getMachinesIndexModel();
		let PPR = await Model.getMaintenceSchedule();

		// Установим каждой машине пустой ппр
		for (var item in items) {
			items[item].forEach(element => {



				element.ppr = PPR[element.machine_id];

				//	element.ppr = [
				//		NoTo, NoTo, NoTo, NoTo, NoTo, NoTo, NoTo, NoTo, NoTo, NoTo, NoTo, NoTo
				//	];



			});
		}


		resultsNode.innerHTML = View.render("prevMain", items);









		var Calendar = FullCalendar.Calendar;
		var Draggable = FullCalendar.Draggable;

		
		var calendarEl = document.getElementById('MaintenenceCalendar');
		



		// initialize the calendar
		// -----------------------------------------------------------------

		var calendar = new Calendar(calendarEl, {
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth'
			},
			editable: true,
			droppable: true, // this allows things to be dropped onto the calendar
			drop: function (info) {
				// is the "remove after drop" checkbox checked?
				//if (checkbox.checked) {
					// if so, remove the element from the "Draggable Events" list
					info.draggedEl.parentNode.removeChild(info.draggedEl);
				//}

				console.log(info);

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
					url: '/api/maintenance/scheduler/date?start='+start+'&end='+end,
					dataType: 'json',
					type: 'GET',
					success: function (response) {
						console.log(response);
						var user_events = response.data;
						successCallback(user_events);
					}
				})

			},
			eventDrop: function(info) {
				console.log(info.event.startStr);
				console.log(info.event.id);		

				Model.setScheduleDate(info.event.id,info.event.startStr);


			}
		});

		calendar.render();















	},

	async generate(e) {
		Array.prototype.rotateRight = function (n) {
			this.unshift.apply(this, this.splice(n, this.length));
			return this;
		}


		let model = e.getAttribute("model");
		let model_id = e.getAttribute("model_id");
		let model_div = document.getElementById("table_" + model);
		let mod = items[model];

		let modelTypeTo = await Model.getMaintenceType(model_id);

		let ar = [];
		let shablon = [];

		for (let n = 0; n < 12; n++) {

			let z = false;


			for (let i = 0; i < modelTypeTo.length; i++) {

				if (n % modelTypeTo[i].mtype_period == 0) {
					ar[n] = modelTypeTo[i].mtype_name;
					shablon[n] = modelTypeTo[i].mtype_id;
					z = true;
					continue;
				}
			}

			if (z) continue;

			ar[n] = NoTo;
			shablon[n] = 0;

		}

		console.log(modelTypeTo);
		console.log(mod);
		console.log(shablon);


		let raw = [];
		for (var i = 0; i < mod.length; i++) {


			let temp = [];
			Object.assign(temp, shablon);
			raw.push({
				machine_id: mod[i].machine_id,
				maintence: temp
			});

			shablon.rotateRight(-1);

			Object.assign(mod[i].ppr, ar);
			ar.rotateRight(-1);
		}

		console.log(raw);
		Model.setMaintence(raw);

		model_div.innerHTML = View.render("prevMainTable", mod);

	},

	// Настройка ППР
	async settings(e) {
		console.log(e);

		let model = await Model.getModelsIndex();
		console.log(model);

		let maintence = await Model.getMaintenceModelOn();
		console.log(maintence);

		let m = {
			model: model,
			mainten: maintence
		}

		// Получим шаблон
		let html = View.render("prevMainAddMaintence", m);
		modal(html);
	},

	async addMaintence(form) {

		let val = {
			model_id: form.model_id.value,
			mtype_name: form.mtype_name.value,
			mtype_period: form.mtype_period.value
		}

		console.log(val);
	},

	// Обработчик чекбоксов выбора ППР
	async MaintenanceCheckbox(e) {
		Model.setMaintenceTypes(
			e.getAttribute("model_id"),
			e.getAttribute("mtype_id"),
			e.checked
		);
	}
}

