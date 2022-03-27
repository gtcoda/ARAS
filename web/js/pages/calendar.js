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

        let myIframe = document.createElement('iframe');

        myIframe.src = `https://aras.gtcoda.ru/dokuwiki/`;
        myIframe.width = `100%`;
        myIframe.id = `frame`;
        myIframe.setAttribute(`scrolling`,`no`);
        

        resultsNode.append(myIframe);

        // Вешаем обработчик события onload на наш элемент iframe, который лежит в myIframe

        myIframe.onload = () => {
            let interval = setInterval(function(){
                myIframe.height = myIframe.contentWindow.document.body.scrollHeight;
            },1000);


        }

    },
}