import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

// Обработчик для оборачивания фото в тег
Handlebars.registerHelper('setImg', function (obj) {

  if (obj != "undefined") {
    if (obj.split('/')[0] == "img") {
      return new Handlebars.SafeString(`
    <img class="card-img-top img-fluid img-thumbnail" src="${obj}">
    `);
    }
  }

  return new Handlebars.SafeString(obj);
});



// Обработчик для описание user
Handlebars.registerHelper('setUser', function (user_id) {

  if (user_id != "undefined") {
    return new Handlebars.SafeString(`
    ${users.get(user_id)}
    `);
  }
  
  return new Handlebars.SafeString(user_id);
});

// Обработчик для формирования даты
Handlebars.registerHelper('setDateRepair', function (event_data) {


  if (event_data != "undefined") {
    var options = {
      month: 'long',
      day: 'numeric',
      timezone: 'UTC'
    };
    var d = new Date(event_data).toLocaleString("ru",options);
    
    return new Handlebars.SafeString(`
      ${d}
    `);
  }
  
  return new Handlebars.SafeString(event_data);
});


// Обработчик для формирования даты каждогоо сообщения
Handlebars.registerHelper('setDate', function (event_data) {
  var options = {
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    timezone: 'UTC'
  };

  if (event_data != "undefined") {
    var d = new Date(event_data).toLocaleString("ru",options);
    return new Handlebars.SafeString(`${d}`);
  }
  
  return new Handlebars.SafeString(event_data);
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



  setData(eventData) {

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


console.log(eventData.eventsM);

var options = {
  year: 'numeric',
  month: 'long',
  day: 'numeric',
  timezone: 'UTC'
};

console.log(new Date().toLocaleString("ru", options));


    var d = new Date(eventData.eventsM[11][0].event_data).toLocaleString("ru",options);
    console.log(d);
    if(eventData==null){
      eventData = {}
    }
    else {
      eventData.eventsM = Object.values(eventData.eventsM).reverse();
    }
   
    
    items = eventData;
  },

  render() {
    resultsNode.innerHTML = View.render('events', items);
  },


}