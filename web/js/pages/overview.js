import View from "../view.js"
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
let items = [];

export default {
    setData(newItem) {
        items = newItem;
    },

    async render() {
    },


    async machineTable(gild_id){
        try{
            var gilds = await Model.getGilds();
        }
        catch(e){
            console.log(e);
        }

        console.log(gilds);

        // Подготовим шаблон, вставим кнопки цехов
        resultsNode.innerHTML = View.render('overview', gilds);

        const tableNode = document.getElementById('owerviewMachineTable');

        try {
            var data = await Model.getGilds(gild_id);
            var machines = await Model.getGildsM(gild_id);

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

                html += `
                <th>
                <div class='card' style="width: 8rem; height: 10rem" gild_id='${data.gild_id}' dimx='${x}' dimy='${y}'>
                    <div  machine_id=${machine.machine_id}  class="card-body eventsMachineSet">

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
    }

}