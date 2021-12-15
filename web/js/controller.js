import Model from "./model.js";
import * as settings from './settings.js';


import overviewPage from "./pages/overview.js"
import eventsPage from "./pages/events.js"
import modelsPage from "./pages/models.js"
import gildsPage from "./pages/gilds.js"
import calendarPage from "./pages/calendar.js"
import view from "./view.js";

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



        // События обьедененные по repair_id
        const eventsM = await Model.getEventsUnionRepair(param.id);

        eventsPage.setData(eventsM, param.id);
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

    }


};