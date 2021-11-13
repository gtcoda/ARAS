import View from "../view.js"

const resultsNode = document.getElementById('app');
let items =[];

export default{
    setData(newItem){
        items = newItem;
    },

    render(){
        resultsNode.innerHTML = View.render('user',items);
    }

}