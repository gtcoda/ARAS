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

		/*	console.log("grantt");
			resultsNode.innerHTML ="<div id='gantt_here' style='height:500px;'></div>";
		    
	
			gantt.i18n.setLocale("ru");
			gantt.config.date_format = "%Y-%m-%d %H:%i:%S";
			gantt.config.readonly = true;
	
			gantt.init("gantt_here");
	
			var Repair_data = await Model.repairGranttData();
	
	
	
			console.log(Repair_data);
	
			gantt.parse({data : [  
				{
					"id": 100,
					"text": "42800(ГДВ400)",
					"start_date": "2022-04-01",
					"duration": 30,
					"open": true
				},
				{
					"id": 1000,
					"text": "Авария привода S. Перезапустили.",
					"start_date": "2022-04-04",
					"duration": 1,
					"open": false,
					"parent": 100
				},
				{
					"id": 101,
					"text": "5184(6Т13)",
					"start_date": "2022-04-01",
					"end_date": "2022-04-28",
					"duration": 30,
					"open": true
				},
				{
					"id": 1001,
					"text": "Отпустили муфту, двигалось по двум координатам.",
					"start_date": "2022-04-04",
					"end_date": "2022-04-04",
					"duration": 1,
					"open": false,
					"parent": 101
				},
				{
					"id": 102,
					"text": "41505(ИР500)",
					"start_date": "2022-04-01",
					"end_date": "2022-04-28",
					"duration": 30,
					"open": true
				},
				{
					"id": 1002,
					"text": "Вышел в диалог и сдвинул наладку.",
					"start_date": "2022-03-31",
					"end_date": "2022-03-31",
					"duration": 1,
					"open": false,
					"parent": 102
				},
				{
					"id": 103,
					"text": "42803(1П426)",
					"start_date": "2022-04-01",
					"end_date": "2022-04-28",
					"duration": 30,
					"open": true
				},
				{
					"id": 1003,
					"text": "По разному выходит в исходное",
					"start_date": "2022-03-31",
					"end_date": "2022-03-31",
					"duration": 1,
					"open": false,
					"parent": 103
				},
				{
					"id": 104,
					"text": "41579(HMC560)",
					"start_date": "2022-04-01",
					"end_date": "2022-04-28",
					"duration": 30,
					"open": true
				},
				{
					"id": 1004,
					"text": "Не опускается палета.",
					"start_date": "2022-03-28",
					"end_date": "2022-03-28",
					"duration": 1,
					"open": false,
					"parent": 104
				}
				
				]});
	
	
			
	
	
	/*		
	
	{id:1,text:"HMC560",start_date:"01-04-2020",duration:30,open:true},
					{id:2,text:"RS-25",start_date:"01-04-2020",duration:30,open:true}
	
	
	{
		data:[
	{"id":0,"text":"HMC560","start_date":"01-04-2020","duration":30,"open":true},
	{"id":1,"text":"RS-25","start_date":"01-04-2020","duration":30,"open":true},
	{"id":2,"text":"SL-10","start_date":"01-04-2020","duration":30,"open":true},
	{"id":3,"text":"AMSONIC4100","start_date":"01-04-2020","duration":30,"open":true},
	{"id":4,"text":"SR-32J","start_date":"01-04-2020","duration":30,"open":true}
	]
	}
	
	[
	{"id":0,"text":"HMC560","start_date":"01-04-2020","duration":30,"open":true},
	{"id":1,"text":"RS-25","start_date":"01-04-2020","duration":30,"open":true},
	{"id":2,"text":"SL-10","start_date":"01-04-2020","duration":30,"open":true},
	{"id":3,"text":"AMSONIC4100","start_date":"01-04-2020","duration":30,"open":true},
	{"id":4,"text":"SR-32J","start_date":"01-04-2020","duration":30,"open":true}
	]
	
	
	gantt.parse({
				data: [
					{ id: 1, text: "Project #2", start_date: "01-04-2020", duration: 18, progress: 0.4, open: true },
					{ id: 2, text: "Task #1", start_date: "02-04-2020", duration: 8, progress: 0.6, parent: 1 },
					{ id: 3, text: "Task #2", start_date: "11-04-2020", duration: 8, progress: 0.6, parent: 1 }
				],
				links: [
					{id: 1, source: 1, target: 2, type: "1"},
					{id: 2, source: 2, target: 3, type: "0"}
				]
			});
	
	*/

		document.addEventListener('DOMContentLoaded', async function () {
			var initialLocaleCode = 'ru';
			var localeSelectorEl = document.getElementById('locale-selector');
			//var calendarEl = document.getElementById('calendar');
			var calendarEl = document.getElementById(settings.getApp());


			var Repair_data = await Model.repairGranttData();
	
	
	
			console.log(Repair_data);

			var calendar = new FullCalendar.Calendar(calendarEl, {
				headerToolbar: {
					left: 'prev,next today',
					center: 'title',
					right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
				},
				initialDate: '2022-04-01',
				locale: initialLocaleCode,
				buttonIcons: false, // show the prev/next text
				weekNumbers: true,
				navLinks: true, // can click day/week names to navigate views
				editable: true,
				dayMaxEvents: true, // allow "more" link when too many events
				events: Repair_data,
				eventClick: function(info) {
					console.log(info);	
				}
			});

			
/*
[
					{
						title: 'All Day Event',
						start: '2020-09-01'
					},
					{
						title: 'Long Event',
						start: '2020-09-07',
						end: '2020-09-10'
					},
					{
						groupId: 999,
						title: 'Repeating Event',
						start: '2020-09-09T16:00:00'
					},
					{
						groupId: 999,
						title: 'Repeating Event',
						start: '2020-09-16T16:00:00'
					},
					{
						title: 'Conference',
						start: '2020-09-11',
						end: '2020-09-13'
					},
					{
						title: 'Meeting',
						start: '2020-09-12T10:30:00',
						end: '2020-09-12T12:30:00'
					},
					{
						title: 'Lunch',
						start: '2020-09-12T12:00:00'
					},
					{
						title: 'Meeting',
						start: '2020-09-12T14:30:00'
					},
					{
						title: 'Happy Hour',
						start: '2020-09-12T17:30:00'
					},
					{
						title: 'Dinner',
						start: '2020-09-12T20:00:00'
					},
					{
						title: 'Birthday Party',
						start: '2020-09-13T07:00:00'
					},
					{
						title: 'Click for Google',
						url: 'http://google.com/',
						start: '2020-09-28'
					}
				]

*/

			calendar.render();

		});


	},
}