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

        /*
        
        
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
        */





        var html = "<table class='table-borderless' style='border-collapse:separate; border-spacing: 10px; '> <tbody> ";

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

                    html += `
            <th>
                <div class='card border-secondary gildMachineSet text-center align-items-center' style='cursor:pointer; width: 6rem; height: 6rem; border-radius: 1rem;' gild_id='${data.gild_id}' dimx='${x}' dimy='${y}'>
                    <div class="pos"><div>
                </div>
    
            </th>
            `;

                    continue;
                }

                html += `
        <th>
            <div class='card  bg-light border-secondary ' style=' width: 6rem; height: 6rem; border-radius: 1rem;' gild_id='${data.gild_id}' dimx='${x}' dimy='${y}'>
                <div  machine_id=${machine.machine_id}  class="card-body text-center">
                        
                    <a href="#Events/${machine.machine_id}" class="btn btn-light">${machine.machine_number}</a>
                    <p class="card-text">${machine.machine_desc}</p>
                </div>

                </div>
            </div>

        </th>
        `;
            }
            html += "</tr>";
        }


        html += "</tbody> </table>";




        resultsNode.innerHTML = html;



        $(".gildMachineSet").on("mouseover",function(e){
            

            let parrent = e.target.closest(".gildMachineSet");

            parrent.classList.add("bg-light");
          
        });

        $(".gildMachineSet").on("mouseout",function(e){
            
            let parrent = e.target.closest(".gildMachineSet");


            parrent.classList.remove("bg-light");
        });

    },



}