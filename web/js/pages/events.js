import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

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