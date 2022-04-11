import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
var items = [];

const NoTo = 0;
const TO1 = 1;
const TO2 = 2;


var MaintenanceTypesData = {};

// Обработчик вставки таблицы
Handlebars.registerHelper('insertTypesMaintence', function (obj) {
	return new Handlebars.SafeString(View.render("prevMainMaintenanceTypes", MaintenanceTypesData));
});


// Обработчик вставки годового плана
Handlebars.registerHelper('insertYears', function () {
	return new Handlebars.SafeString(View.render("prevMainYears", items));
});


// Обработчик вставки таблицы генерации годичного плана ТО
Handlebars.registerHelper('insertTable', function (obj) {
	console.log(obj);
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
		case 0: m = "<td>-</td>"; break;
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


function AddCalendar() {

	var Calendar = FullCalendar.Calendar;
	var Draggable = FullCalendar.Draggable;

	var Mapp = document.getElementById("MaintenenceApps");

	Mapp.innerHTML = View.render("prevMainCalendar");
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


		},
		events: function (fetchInfo, successCallback, failureCallback) {

			var options = {
				year: 'numeric',
				month: 'numeric',
				day: 'numeric',
				timezone: 'UTC'
			};
			var start = new Date(fetchInfo.start).toLocaleString("fr-CA", options);
			var end = new Date(fetchInfo.end).toLocaleString("fr-CA", options);

			$.ajax({
				url: '/api/maintenance/scheduler/date?start=' + start + '&end=' + end,
				dataType: 'json',
				type: 'GET',
				success: function (response) {
					var user_events = response.data;
					successCallback(user_events);
				}
			})

		},
		eventDrop: function (info) {
			Model.setScheduleDate(info.event.id, info.event.startStr);
		}
	});

	calendar.render();
}



export default {

	async navigate(e) {
		var Mapp = document.getElementById("MaintenenceApps");

		document.querySelectorAll('#MaintenenseNav').forEach(function (elem) {
			elem.classList.remove("active");
		});
		e.currentTarget.classList.add("active");


		switch (e.currentTarget.getAttribute("role")) {
			case "Set": Mapp.innerHTML = View.render("prevMainMaintenanceTypes", MaintenanceTypesData); break;
			case "Years": Mapp.innerHTML = View.render("prevMainYears", items); break;
			case "Calendar": AddCalendar(); break;
		}

	},


	async setData() {
		let MaintenanceTypes = await Model.getMaintenceTypes();
		let MaintenanceModelOn = await Model.getMaintenceModelOn();

		MaintenanceTypesData = {
			Mtypes: MaintenanceTypes,
			ModelOn: MaintenanceModelOn
		}

		items = await Model.getMachinesIndexModel();
		let PPR = await Model.getMaintenceSchedule();

		// Установим каждой машине ппр 
		for (var item in items) {
			items[item].forEach(element => {
				element.maintence = PPR[element.machine_id];
			});
		}

		console.log(items);
	},

	async render() {
		resultsNode.innerHTML = View.render("prevMain", items);
		// Вкладка при загрузке календарь на месяц
		AddCalendar();
	},

	// генерирование годового плана исходя из выбраных ТО
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

		let shablon = [];


		if (modelTypeTo.length == 0) {
			for (let n = 0; n < 12; n++) {
				shablon[n] = 0;
			}
		}
		else {
			for (let n = 0; n < 12; n++) {
				let z = false;
				for (let i = 0; i < modelTypeTo.length; i++) {
					if (n % modelTypeTo[i].mtype_period == 0) {
						shablon[n] = modelTypeTo[i].mtype_id;
						z = true;
						continue;
					}
				}
				if (z) continue;
				shablon[n] = 0;
			}
		}


		let raw = [];
		for (var i = 0; i < mod.length; i++) {
			let temp = [];
			Object.assign(temp, shablon);
			raw.push({
				machine_id: mod[i].machine_id,
				maintence: temp
			});


			mod[i].maintence = temp;

			shablon.rotateRight(-1);
		}


		//Обновим данные по модели с учетом вновь сгенерированных данных и выведем
		items = await Model.getMachinesIndexModel();
		let PPR = await Model.getMaintenceSchedule();
		// Установим каждой машине ппр 
		for (var item in items) {
			items[item].forEach(element => { element.maintence = PPR[element.machine_id]; });
		}
		mod = items[model];

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

	// Обработчик чекбоксов выбора ППР для модели
	async MaintenanceCheckbox(e) {
		Model.setMaintenceTypes(
			e.getAttribute("model_id"),
			e.getAttribute("mtype_id"),
			e.checked
		);
	}
}

