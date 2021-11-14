import Model from "./model.js";
import View from "./view.js";
import eventsPage from "./pages/events.js"
import usersPage from "./pages/users.js"
import modelsPage from "./pages/models.js"

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

export default {
    async OverviewRoute() {
        const users = await Model.getUsers();

        usersPage.setData(users);
        usersPage.render();

        setActiveNavNode(overwiewNavNode);
    },

    async SettingsRoute() {
        const events = await Model.getEvents();

        eventsPage.setData(events);
        eventsPage.render();


        setActiveNavNode(settingsNavNode);
    },



    async ModelRoute(){
        const models = await Model.getModels();

        modelsPage.setData(models);
        modelsPage.render();

    },


};