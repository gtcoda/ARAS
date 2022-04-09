import Model from "./model.js";
import * as settings from './settings.js';


import overviewPage from "./pages/overview.js"
import eventsPage from "./pages/events.js"
import modelsPage from "./pages/models.js"
import gildsPage from "./pages/gilds.js"
import calendarPage from "./pages/calendar.js"
import wikiPage from "./pages/wiki.js"
import prevMain from "./pages/prevMain.js"
import view from "./view.js";
import model from "./model.js";

const overwiewNavNode = document.querySelector('[data-role=nav-over]');
const settingsNavNode = document.querySelector('[data-role=nav-set]');

let activeNavNode;

function setActiveNavNode(node) {
    if (activeNavNode) {
        activeNavNode.classList.remove('active');
    }
    activeNavNode = node;
    activeNavNode.classList.add('active');
}


// Добавим в хеш параметр
function hashAddParam(param) {
    let hash = document.location.hash;
    document.location.hash = hash + "/" + param;
}


export default {

    async OverviewRoute(param) {
        if (param.id) {

            if (settings.getOverviewMT() == "plan") {
                overviewPage.machinePlan(param.id);
            }
            else if (settings.getOverviewMT() == "table") {
                overviewPage.machineTable(param.id);
            }


        }
        else {
            const gilds = await Model.getGilds();
            hashAddParam(gilds[0].gild_id);
        }

    },


    async EventsRoute(param) {
        /*
                События просто в линию
                const eventsM = await Model.getEvents(param.id);
                eventsPage.setData(eventsM, param.id);
                eventsPage.render();
        
        */
        
        
        const users = await Model.getUsers();
        eventsPage.setUsers(users);
        
        const machine = await Model.getMachines(param.id);
        

        const models = await Model.getModels();

        models.getModel = function(model_id){
            for (const key in this) {
                if(this[key].model_id == model_id){
                    return this[key];
                } 
            }
        };

        // События обьедененные по repair_id
        const eventsM = await Model.getEventsUnionRepair(param.id);


        const eventData = {
            eventsM: eventsM,
            machine: machine,
            model: models.getModel(machine.model_id),
        }

        

        eventsPage.setData(eventData);
        eventsPage.render();
    },

    async ModelsRoute() {
        const models = await Model.getModels();

        modelsPage.setData(models);
        modelsPage.render();

    },

    async CalendarRoute() {
        calendarPage.setData();
        calendarPage.render();

    },

    async WikiRoute(){
        wikiPage.render();
    },
 
    async GildsRoute(param) {

        console.log(param);
        if (param.id) {
            const gilds = await Model.getGilds(param.id);
            gildsPage.setDataId(gilds);
            //gildsPage.render();
        }
        else {
            const gilds = await Model.getGilds();
            gildsPage.setData(gilds);
            gildsPage.render();
        }

    },

    async PrevMainRoute(){
        prevMain.render();
    }


};
