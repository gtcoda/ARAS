import View from "../view.js";
import * as settings from '../settings.js';

const resultsNode = document.getElementById('app');
let items =[];
let sorting = settings.getEventsSort();

export default{
    setData(newItem){
        items = newItem;
    },

    setSorting(newSorting){
        sorting = newSorting;
        settings.setEventsSort(newSorting);
    },

    render(){
        if(sorting === 'event_id'){

            items.sort(function (a, b) {
                if (a.event_id > b.event_id) {
                  return 1;
                }
                if (a.event_id < b.event_id) {
                  return -1;
                }
                // a должно быть равным b
                return 0;
              });



        } else if(sorting === 'user_id'){
            items.sort(function (a, b) {
                if (a.event_id < b.event_id) {
                  return 1;
                }
                if (a.event_id > b.event_id) {
                  return -1;
                }
                // a должно быть равным b
                return 0;
              });
        }

        resultsNode.innerHTML = View.render('events',items);;
    
        const sort = resultsNode.querySelector('[data-role=sort]');

        sort.value = sorting;
        sort.addEventListener('change',e=>{
            this.setSorting(e.target.value);
            this.render();
        });

    
    }

}