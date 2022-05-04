import View from "../view.js"
import Model from "../model.js";

const resultsNode = document.getElementById('app');
let items = [];

export default{
    async setData(newItem){
        items = newItem;
        console.log(" await Model.getS()");
        console.log( await Model.getS());
        console.log( "await Model.getS()");
    },

    render(){
        resultsNode.innerHTML = View.render('user',items);
    }

}