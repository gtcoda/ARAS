import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

// Обработчик для оборачивания фото в тег
Handlebars.registerHelper('setImg', function (obj) {

  if (obj != "undefined") {
    if (obj.split('/')[0] == "img") {
      return new Handlebars.SafeString(`
    <img class="card-img-top" src="${obj}">
    `);
    }
  }

  return new Handlebars.SafeString(obj);
});



// Обработчик для описание user
Handlebars.registerHelper('setUser', function (user_id) {

  if (user_id != "undefined") {


    return new Handlebars.SafeString(`
    <p>${users.get(user_id)}</p>
    `);


  }
  
  return new Handlebars.SafeString(user_id);
});

const resultsNode = document.getElementById(settings.getApp());
let items = [];
let users = new Map();

export default {

  setUsers(listUsers){

    listUsers.forEach(function(item, i, arr) {
      users.set(item.user_id,item.user_name);
    });

  },



  setData(newItem, machine_id) {

    /* Поля получаемого через API обьекта хоть и могут быть по разному отсортированы на сервере
    но js имеет право обращаться к ним в порялдке своего усмотрения.
    {
      "24": [
        {
        }
      ],
      "20": [
        {
        }
      ]
    }

    Преобразуем обьект в массив и развернем, что бы более новые ремонты оказались первыми
*/

    newItem = Object.values(newItem).reverse();
    items = {
      events: newItem,
      machine_id: machine_id
    }
  },

  render() {
    resultsNode.innerHTML = View.render('events', items);
  },


}