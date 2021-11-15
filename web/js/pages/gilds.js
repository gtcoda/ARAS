import View from "../view.js";
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
    setDataId(data) {
        var html = `
        <table class='table-bordered' style='border-collapse:separate; border-spacing: 10px;'>
            <tbody> `;


        for (var y = data.gild_dimY; y > 0; y--) {
            html += "<tr>";
            for (var x = 1; x <= data.gild_dimX; x++) {
 
                html += `
                <th>
                <div class='card gildMachineSet' dimx='${x}' dimy='${y}'>
                    <div class="card-body">
                        {${x}:${y}}
                    </div>
                </div>
                </th>
                `;
            }
            html += "</tr>";
        }

        html += `</tbody>
        </table>`;

        resultsNode.innerHTML = html;

    },


    getTable() {

    }




}