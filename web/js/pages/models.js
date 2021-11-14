import View from "../view.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById(settings.getApp());
let items =[];

export default{
    setData(newItem){
        items = newItem;
    },

    render(){
        resultsNode.innerHTML = View.render('models',items);
    }

}