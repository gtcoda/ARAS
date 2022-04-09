import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
var items = [];

const NoTo = 0;
const TO1 = 1;
const TO2 = 2;

// Обработчик вставки таблицы
Handlebars.registerHelper('insertTable', function (obj) {
	return new Handlebars.SafeString(View.render("prevMainTable", obj));
});


// Обработчик вставки названия ТО
Handlebars.registerHelper('insertTO', function (obj) {
	console.log(obj);
	let m;
	switch (obj){
		case TO2: m = "<td class ='bg-success'>TO2</td>"; break;
		case TO1:m = "<td class ='bg-primary'>TO1</td>"; break;
		case NoTo: m = "<td>-</td>"; break;
	}

	return new Handlebars.SafeString(m);
});




export default {
	setData(newItem) {
		items = newItem;
	},

	async render() {

		items = await Model.getMachinesIndexModel();

		// Установим каждой машине пустой ппр
		for (var item in items) {
			items[item].forEach(element => {
				element.ppr = [
					NoTo,NoTo,NoTo,NoTo,NoTo,NoTo,NoTo,NoTo,NoTo,NoTo,NoTo,NoTo
				];
			});
		}

		console.log(items);


		resultsNode.innerHTML = View.render("prevMain", items);

	},

	async generate(e) {
		Array.prototype.rotateRight = function (n) {
			this.unshift.apply(this, this.splice(n, this.length));
			return this;
		}


		console.log(e);
		let model = e.getAttribute("model");
		let model_div = document.getElementById("table_" + model);
		let mod = items[model];

		const number_to1 = 3;
		const number_to2 = 12;
		let ar = [];

		for (let n = 0; n < 12; n++) {
			if (n == 0) {
				ar[n] = TO2;
				continue;
			}
			if (n % number_to2 == 0) {
				ar[n] = TO2;
				continue;
			}
			if(n % number_to1 == 0){
				ar[n] = TO1;
				continue;
			}

			ar[n] = NoTo;

		}

		console.log(ar);

		for (var i = 0; i < mod.length; i++) {
			Object.assign(mod[i].ppr, ar);
			ar.rotateRight(-1);

		}

		console.log(mod);

		model_div.innerHTML = View.render("prevMainTable", mod);

	}
}

