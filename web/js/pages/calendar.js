import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
var items = [];


export default{
    setData(newItem) {
        items = newItem;
    },

    async render() {
        console.log("grantt");
        resultsNode.innerHTML ="<div id='gantt_here' style='height:500px;'></div>";
        

        gantt.i18n.setLocale("ru");
		gantt.config.date_format = "%Y-%m-%d %H:%i:%S";
        gantt.config.readonly = true;

        gantt.init("gantt_here");

		var Repair_data = await Model.repairGranttData();



		console.log(Repair_data);

		gantt.parse(Repair_data);


		


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
    },
}