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




      var str = obj.replace(/\[b\](.*?)\[\/b\]/ig, '<b>$1</b>');
      str = str.replace(/\[i\](.*?)\[\/i\]/ig, '<i>$1</i>');
      str = str.replace(/\[u\](.*?)\[\/u\]/ig, '<u>$1</u>');
      console.log(str);
      str = str.replace(/\n/g, "<br />");
      str = str.replace(/\[list\](.*?)\[\/list\]/ig, '<ul>$1</ul>');
      str = str.replace(/\[\*\](.*?)<br \/>/ig, '<li>$1</li>');









      return new Handlebars.SafeString(str);
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



var toolbar = [{
  "type": "format",
  "title": "Полужирный [B]",
  "icon": "bold.png",
  "key": "b",
  "open": "[b]",
  "close": "[/b]",
  "block": false
}, {
  "type": "format",
  "title": "Курсив [I]",
  "icon": "italic.png",
  "key": "i",
  "open": "[i]",
  "close": "[/i]",
  "block": false
}, {
  "type": "format",
  "title": "Подчеркнутый [U]",
  "icon": "underline.png",
  "key": "u",
  "open": "[u]",
  "close": "[/u]",
  "block": false
}, {
  "type": "format",
  "title": "Зачеркнутый [D]",
  "icon": "strike.png",
  "key": "d",
  "open": "<del>",
  "close": "</del>",
  "block": false
},
{
  "type": "formatln",
  "title": "Маркированый список",
  "icon": "ul.png",
  "open": "[list]",
  "close": "[/list]",
  "key": "[*]",
  "block": true
},

];


/**
* Creates a toolbar button through the DOM
* Called for each entry of toolbar definition array (built by inc/toolbar.php and extended via js)
*
* Style the buttons through the toolbutton class
*
* @param {string} icon      image filename, relative to folder lib/images/toolbar/
* @param {string} label     title of button, show on mouseover
* @param {string} key       hint in title of button for access key
* @param {string} id        id of button, and '<id>_ico' of icon
* @param {string} classname for styling buttons
*
* @author Andreas Gohr <andi@splitbrain.org>
* @author Michal Rezler <m.rezler@centrum.cz>
*/
function createToolButton(icon, label, key, id, classname) {
  var $btn = jQuery(document.createElement('button')),
    $ico = jQuery(document.createElement('img'));

  // prepare the basic button stuff
  $btn.addClass('toolbutton');

  if (classname) {
    $btn.addClass(classname);
  }

  $btn.attr('title', label).attr('aria-controls', 'wiki__text');

  if (key) {
    $btn.attr('title', label + ' [' + key.toUpperCase() + ']')
      .attr('accessKey', key);
  }

  // set IDs if given
  if (id) {
    $btn.attr('id', id);
    $ico.attr('id', id + '_ico');
  }

  $btn.attr('type', "button");
  // create the icon and add it to the button
  if (icon.substr(0, 1) !== '/') {
    icon = 'css/images/toolbar/' + icon;
  }
  $ico.attr('src', icon);
  $ico.attr('alt', '');
  $ico.attr('width', 16);
  $ico.attr('height', 16);
  $btn.append($ico);

  // we have to return a DOM object (for compatibility reasons)
  return $btn[0];
}


function bind(fnc) {
  var Aps = Array.prototype.slice,
    static_args = Aps.call(arguments, 1);
  return function () {
    return fnc.apply(this, static_args.concat(Aps.call(arguments, 0)));
  };
}


/**
 * Create a toolbar
 *
 * @param  string tbid       ID of the element where to insert the toolbar
 * @param  string edid       ID of the editor textarea
 * @param  array  tb         Associative array defining the buttons
 * @param  bool   allowblock Allow buttons creating multiline content
 * @author Andreas Gohr <andi@splitbrain.org>
 */

function initToolbar(tbid, edid, tb, allowblock) {
  var $toolbar, $edit;
  if (typeof tbid == 'string') {
    $toolbar = jQuery('#' + tbid);
  } else {
    $toolbar = jQuery(tbid);
  }

  $edit = jQuery('#' + edid);

  if ($toolbar.length == 0 || $edit.length == 0 || $edit.attr('readOnly')) {
    return;
  }

  if (typeof allowblock === 'undefined') {
    allowblock = true;
  }

  //empty the toolbar area:
  $toolbar.html('');


  // Bp=за ограничения зоны видимости все обработчики создаются внутри init

  var format = function format(btn, props, edid) {
    console.log(btn);

    var start = props.open;
    var end = props.close;

    var element = document.getElementById(edid);
    if (document.selection) {
      element.focus();
      sel = document.selection.createRange();
      sel.text = start + sel.text + end;
    } else if (element.selectionStart || element.selectionStart == '0') {
      element.focus();
      var startPos = element.selectionStart;
      var endPos = element.selectionEnd;
      element.value = element.value.substring(0, startPos) + start + element.value.substring(startPos, endPos) + end + element.value.substring(endPos, element.value.length);
    } else {
      element.value += start + end;
    }


  }


  var formatln = function formatln(btn, props, edid) {
    console.log(props);

    var start = props.open;
    var end = props.close;
    var key = props.key;

    var element = document.getElementById(edid);
    if (document.selection) {
      element.focus();
      sel = document.selection.createRange();
      sel.text = sel.text.replace(/\n/g, "\n" + key);
      sel.text = start + sel.text + end;
    } else if (element.selectionStart || element.selectionStart == '0') {
      element.focus();
      var startPos = element.selectionStart;
      var endPos = element.selectionEnd;
      console.log(element.value.substring(startPos, endPos));
      element.value = element.value.substring(0, startPos) +
        start +
        "\n" + key +
        element.value.substring(startPos, endPos).replace(/\n/g, "\n" + key) +
        "\n" +
        end +
        element.value.substring(endPos, element.value.length);
    } else {
      element.value += start + "\n" + key + "\n" + end;
    }


  }

  jQuery.each(tb, function (k, val) {
    //   if (!tb.hasOwnProperty(k) || (!allowblock && val.block === true)) {
    //       return;
    //   }
    var actionFunc, $btn;

    var m = createToolButton(val.icon, val.title, val.key, val.id, val['class']);

    // create new button (jQuery object)
    $btn = jQuery(m);




    // type is a tb function -> assign it as onclick
    actionFunc = val.type;
    if (actionFunc == "format") {
      $btn.on('click', bind(format, $btn, val, edid));
      $toolbar.append($btn);
      return;
    }

    if (actionFunc == "formatln") {
      $btn.on('click', bind(formatln, $btn, val, edid));
      $toolbar.append($btn);
      return;
    }


  });
}





































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

    var machine_id = document.getElementById("QRCode").getAttribute("machine_id");

    var qrcode = new QRCode(document.getElementById("QRCode"), {
      width: 100,
      height: 100
    });

    function makeCode() {
      qrcode.makeCode("https://aras.gtcoda.ru/#Events/" + machine_id);
    }

    makeCode();


    let name = items.model.model_name;
    View.wiki('wiki', `https://aras.gtcoda.ru/dokuwiki/doku.php?id=станки:` + String(name).toLowerCase());




    initToolbar("toolbar", "event_message", toolbar, true);




  },

  /*
    format(e) {
      console.log(e);
      console.log(e.currentTarget.getAttribute("aria-controls"));
      console.log(e.currentTarget.getAttribute("open"));
      console.log(e.currentTarget.getAttribute("close"));
  
  
      var start = e.currentTarget.getAttribute("open");
      var end = e.currentTarget.getAttribute("close");
  
  
      var element = document.getElementById(e.currentTarget.getAttribute("aria-controls"));
      if (document.selection) {
        element.focus();
        sel = document.selection.createRange();
        sel.text = start + sel.text + end;
      } else if (element.selectionStart || element.selectionStart == '0') {
        element.focus();
        var startPos = element.selectionStart;
        var endPos = element.selectionEnd;
        element.value = element.value.substring(0, startPos) + start + element.value.substring(startPos, endPos) + end + element.value.substring(endPos, element.value.length);
      } else {
        element.value += start + end;
      }
  
  
  
  
  
    }
  
  */
}