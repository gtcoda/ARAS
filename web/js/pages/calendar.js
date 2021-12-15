import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
var items = [];


export default{
    setData(newItem) {
        items = newItem;
    },

    render() {
        resultsNode.innerHTML = "<p>Календарь</p>";
    },
}