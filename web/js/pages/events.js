import View from "../view.js";
import Model from "../model.js";
import * as settings from '../settings.js';

// Обработчик для сообщения events
Handlebars.registerHelper('setImg', function (obj) {

  if (obj != "undefined") {
    // Фото. Обернем в тег.
    if (obj.split('/')[0] == "img") {
      return new Handlebars.SafeString(`
    <img class="card-img-top img-fluid img-thumbnail" src="${obj}">
    `);
    }
    else {// Перевод строки в <br>
      return new Handlebars.SafeString(`${obj.replace(/\n/g, "<br />")}`);
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
    var d = new Date(event_data).toLocaleString("ru", options);

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
    var d = new Date(event_data).toLocaleString("ru", options);
    return new Handlebars.SafeString(`${d}`);
  }

  return new Handlebars.SafeString(event_data);
});

// Обработчик для сравнения в IF/ELSE
Handlebars.registerHelper('ifEq', function (a, b, obj) {
  if (a == b) {
    return obj.fn(this);
  } else {
    return obj.inverse(this);
  }

});



const resultsNode = document.getElementById(settings.getApp());
let items = [];
let users = new Map();

export default {

  setUsers(listUsers) {
    listUsers.forEach(function (item, i, arr) {
      users.set(item.user_id, item.user_name);
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

    if (eventData == null) { eventData = {} }

    if (eventData.eventsM == null) { eventData.eventsM = {}; }
    else { eventData.eventsM = Object.values(eventData.eventsM).reverse(); }

    console.log(eventData);

    items = eventData;
  },

  render() {
    resultsNode.innerHTML = View.render('events', items);

    let myIframe = document.createElement('iframe');

    let name = items.model.model_name;
    console.log();

    myIframe.src = `https://aras.gtcoda.ru/dokuwiki/doku.php?id=станки:` + String(name).toLowerCase();
    myIframe.width = `100%`;
    myIframe.id = `frame`;
    myIframe.setAttribute(`scrolling`, `no`);
    document.getElementById('wiki').append(myIframe);

    // Вешаем обработчик события onload на наш элемент iframe, который лежит в myIframe
    myIframe.onload = () => {

      let interval = setInterval(function () {

        if (myIframe.contentWindow != null) {
          myIframe.height = myIframe.contentWindow.document.body.scrollHeight;
        }

      }, 1000);


    }

  },


}