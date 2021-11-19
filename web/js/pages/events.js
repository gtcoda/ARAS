import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
let items = [];

export default {
    setData(newItem) {
        items = newItem;
    },

    render(id) {
      console.log(id);
      resultsNode.innerHTML = View.render('events');
    },



    // Установка поля цеха
    async setDataId(data) {

    },



}