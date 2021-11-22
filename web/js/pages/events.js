import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

// Обработчик для оборачивания фото в тег
Handlebars.registerHelper('setImg', function (obj) {
  console.log(obj);
  if(obj.split('/')[0] == "img"){
    return new Handlebars.SafeString(`
    <img class="card-img-top" src="${obj}">
    `);
  }
  return new Handlebars.SafeString(obj);
});

const resultsNode = document.getElementById(settings.getApp());
let items = [];

export default {
  setData(newItem, machine_id) {
    items = {
      events: newItem,
      machine_id: machine_id
    }
  },

  render() {
    console.log(items);
    resultsNode.innerHTML = View.render('events', items);
  },


}