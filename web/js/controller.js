import Model from "./model.js";

export default{
    async OverviewRoute(){
        const users = await Model.getUsers();
        console.log(users);
    },

    async SettingsRoute(){
        const events = await Model.getEvents();
        console.log(events);
    },

};