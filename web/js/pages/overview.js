import View from "../view.js"
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
let items = [];
let body;   // Данные с планом размещения или таблицей станков

let overwiew_gilds; // Данные для строки с цехами

// Обработчик для переключателя
Handlebars.registerHelper('MTChecked', function (obj) {
    if (settings.getOverviewMT() == "table") {
        return new Handlebars.SafeString("checked");
    }

});


// Обработчик для статуса ремонта
Handlebars.registerHelper('setStatusLastRepair', function (status) {
    if(status != "undefined" && status != null){
        return new Handlebars.SafeString(status);
    }
    return new Handlebars.SafeString("");
});

// Обработчик для даты последнего ремонта
Handlebars.registerHelper('setDateLastRepair', function (lastdate) {
    // Склонение существительных
    let GetNoun = function(number, one, two, five) {
        number = Math.abs(number);
        number %= 100;
        if (number >= 5 && number <= 20) {
            return five;
        }
        number %= 10;
        if (number == 1) {
            return one;
        }
        if (number >= 2 && number <= 4) {
            return two;
        }
        return five;
    } 

    if (lastdate != "undefined" && lastdate != null) {

        let currentDate = Date.parse(new Date());
        let days = (currentDate - Date.parse(lastdate))/86400000;       //86400000 - ms в дне
        let divdays = Math.round(days);


        var options = {
          month: 'long',
          day: 'numeric',
          timezone: 'UTC'
        };
        var d = new Date(lastdate).toLocaleString("ru",options);
        /** GetNoun(divdays,'день','дня','дней') */
        return new Handlebars.SafeString(`
          ${d} (${divdays}д назад...)
        `);
      }
      
      return new Handlebars.SafeString("");

});



export default {
    async setData(o_gilds, o_body) {
        overwiew_gilds = o_gilds;
        body = o_body;
    },

    render() {
    }, 



    async machinePlan(gild_id) {
        try {
            var gilds = await Model.getGilds();
        }
        catch (e) {
            console.log(e);
        }

        // Подготовим шаблон, вставим кнопки цехов
        resultsNode.innerHTML = View.render('overview', gilds);

        const tableNode = document.getElementById('owerviewMachineTable');

        try {
            var data = await Model.getGilds(gild_id);
            var machines = await Model.getGildsM(gild_id);
            var models = await Model.getModelsIndex();

        }
        catch (e) {
            console.log(e);
        }

        var html = `
        <table class='table-borderless' style='border-collapse:separate; border-spacing: 10px;'>
            <tbody> `;


        for (var y = data.gild_dimY; y > 0; y--) {
            html += "<tr>";
            for (var x = 1; x <= data.gild_dimX; x++) {


                var m = machines.filter(function (item) {
                    if ((item.machine_posX == x) & (item.machine_posY == y)) {
                        return true;
                    }
                    return false;
                });



                if (typeof m[0] != "undefined") {
                    var machine = m[0];
                }
                else {
                    var machine = {
                        machine_number: "",
                        machine_desc: ""
                    };
                }

                if (models[machine.model_id] != undefined) {
                    var model = models[machine.model_id];
                }
                else {
                    var model = "";
                }



                html += `
                <th>
                <div class='card' style="width: 8rem; height: 10rem" gild_id='${data.gild_id}' dimx='${x}' dimy='${y}'>
                    <div  machine_id=${machine.machine_id}  class="card-body eventsMachineSet">
                        <p class="card-text">${model}</p>
                        <a href="#Events/${machine.machine_id}"><h5 class="card-title">${machine.machine_number}</h5></a>
                        <p class="card-text">${machine.machine_desc}</p>

                    </div>
                </div>
                </th>
                `;
            }
            html += "</tr>";
        }

        html += `</tbody>
        </table>`;
        tableNode.innerHTML = html;
    },

    async machineTable(gild_id) {
        try {
            var gilds = await Model.getGilds();
        }
        catch (e) {
            console.log(e);
        }

        // Подготовим шаблон, вставим кнопки цехов
        resultsNode.innerHTML = View.render('overview', gilds);

        const tableNode = document.getElementById('owerviewMachineTable');

        try {
            var data = await Model.getGilds(gild_id);
            var machines = await Model.getGildsMIndex(gild_id);
            var models = await Model.getModelsIndex();

        }
        catch (e) {
            console.log(e);
        }
        

        var m = {};

        for(var key in machines){
            m[models[key]] = machines[key];
        }

        console.log(m);


        tableNode.innerHTML =  View.render('overviewTable', m);

        
        //tableNode.innerHTML = "<p>Table</p>";
    }

}