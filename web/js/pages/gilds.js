import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
let items = [];

export default {
    setData(newItem) {
        items = newItem;
    },

    render() {
        resultsNode.innerHTML = View.render('gilds', items);
    },



    // Установка поля цеха
    async setDataId(data) {

        console.log(data);

        try {
            var machines = await Model.getGildsM(data.gild_id);

        }
        catch (e) {
            console.log(e);
        }

        /**
         * 
         * Создадим поле цеха
         * 
         * Используется таблица на основе DIV
         */
        var html = "<div class='container-fluid'>";

        for (var y = data.gild_dimY; y > 0; y--) {
            html += "<div class='row row-eq-height'>";
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

                html += `
                <div class="col">
                    <div class='card gildMachineSet' gild_id='${data.gild_id}' dimx='${x}' dimy='${y}'>
                        <div class="card-body">

                        <h5 class="card-title">${machine.machine_number}</h5>
                        <p class="card-text">${machine.machine_desc}</p>
                        <p class="card-text"> {${x}:${y}}</p>

                        </div>
                    </div>
                </div>
                `;
            }
            html += "</div>";
        }


        html += "</div>";

        resultsNode.innerHTML = html;

    },



}