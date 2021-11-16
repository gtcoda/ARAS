import View from "./view.js";
import Model from "./model.js";
import * as settings from './settings.js';


checkSingInMenu();




// Обработчик на все кнопки
$('body').on('click', 'button', function (e) {

    // Обределим то за кнопка, и запустим обработчик
    switch (this.id) {
        case 'setModelButton': setModelButton(e);
            break;
        case 'setGildButton': setModalButton(e,'setGild');
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
    }


});



// Обработчик на все отправки форм
$('body').on('submit', 'form', function (e) {
    // Отменить действие браузера по умолчанию
    e.preventDefault();
    // Остановка всплытия события
    e.stopPropagation();

    //  за кнопка, и запустим обработчик
    switch (this.id) {
        case 'addModelForm': addModel(this);
            break;
        case 'signInForm': singIn(this);
            break;
        case 'signUpForm': singUp(this);
            break;
        case 'addMachineGildsForm': addMachineGilds(this);;
            break;
        case 'updateModelForm': UpdateModel(this);
            break;
        case 'addGildForm': addGild(this);
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
    }


});


// Обработчик на все кнопки по типу <a>
$('body').on('click', 'a', function (e) {
    // Определим то за ссылка, и запустим обработчик
    switch (this.id) {
        case 'signIn': singInBotton(e);
            break;
        case 'signUpBotton': signUpBotton(e);
            break;
        case 'updateModelButton': updateModelBotton(e);
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        case '': ;
            break;
        default: {
        }
    }

});

/**
 *
 * Функции страницы добавления машины на странице настроек
 *
 */
// Обработчик настройки gilds
$('body').on('click', '.gildMachineSet', async function (e) {

    const models = await Model.getModels();

    var data = {
        dimX: this.getAttribute('dimx'),
        dimY: this.getAttribute('dimy'),
        gild_id: this.getAttribute('gild_id'),
        models: models
    };

    var html = View.render('addMachine', data);

    modal(html);
});





// Обработчик модального окна добавления
function setModalButton(e,template) {
    // Отменить действие браузера по умолчанию
    e.preventDefault();
    // Остановка всплытия события
    e.stopPropagation();
    // Получим шаблон
    let html = View.render(template, {});
    modal(html);
}

// Дернем хеш, для перезагрузки содержимого
function hashRefresh() {
    let hash = document.location.hash;
    document.location.hash = '';
    document.location.hash = hash;
}

// Создать модальное окно
// html - код модального окна 
function modal(html, id) {
    $(html) // Создание элемента div
        .addClass("dialog") // Назначение элементу класса
        .attr('id', id)     // Назначить окну id
        .appendTo("body")   // Вставляет в элемент, после содержимого
        .dialog({ // Создание из элемента диалогового окна
            title: $(html).attr("data-dialog-title"), // Добавим в заголовок атрибут.
            close: function () { // Действие по крестику
                $(this).remove()
            },
            modal: true, // Нельзя взаимойдействовать с страницей
            draggable: true
        })
}

// Закрыть окно по клику за пределами модального окна
$(document).on("click", function (e) {
    $(e.target).closest(".ui-dialog").length || $(".dialog").dialog("close");
})

/**
 *
 * Функции регистрация и вход
 *
 */

// Проконтролируем надпись в меню
function checkSingInMenu() {
    // Нет токена
    if ($.isEmptyObject(settings.getJWT())) {
        $("#signIn").empty();
        $("#signIn").append(' Войти ');
    }
    else {
        $("#signIn").empty();
        $("#signIn").append(' Выйти ');
    }
}

// Обработчик добавления модального окна входа
function singInBotton(e) {
    // Отменить действие браузера по умолчанию
    e.preventDefault();
    // Остановка всплытия события
    e.stopPropagation();
    // Если токена нет выдавим форму и войдем
    if ($.isEmptyObject(settings.getJWT())) {
        // Получим шаблон
        let html = View.render('signIn');
        modal(html);
    }

    else {
        // Если токен есть, удалим данные входа
        settings.setJWT('');
        checkSingInMenu();
    }

}


// Обработчик входа
async function singIn(e) {

    let res = Model.login(e.login.value, e.password.value);

    res.then(result => {
        settings.setJWT(result);

        checkSingInMenu();
        // Закроем окно
        $(".dialog").dialog("close");
    },
        error => {
            $('#ErrorMessage').empty();
            $('#ErrorMessage').append(JSON.parse(error.response).messages);
        }
    );


}

// Обработчик добавления модального окна регистрации
function signUpBotton(e) {
    // Отменить действие браузера по умолчанию
    e.preventDefault();
    // Остановка всплытия события
    e.stopPropagation();

    // Получим шаблон
    let html = View.render('signUp');

    console.log(html);
    // Очистим существующее модальное окно и заполним новым шаблоном
    $(".dialog").empty();
    $(".dialog").append(html);

    $(".ui-dialog-title").empty();
    $(".ui-dialog-title").append($(html).attr("data-dialog-title"));
}

// Обработчик регистрации
async function singUp(e) {

    let res = Model.setUser(e.login.value, e.password.value, e.name.value);

    res.then(result => {
        // Закроем окно
        $(".dialog").dialog("close");


    },
        error => {
            $('#ErrorMessage').empty();
            $('#ErrorMessage').append(JSON.parse(error.response).messages);
        }
    );


}


/**
 *
 * Функции страницы добавления модели
 *
 */

// Обработчик модального окна добавления модели
function setModelButton(e) {
    // Отменить действие браузера по умолчанию
    e.preventDefault();
    // Остановка всплытия события
    e.stopPropagation();
    // Получим шаблон
    let html = View.render('addModels', {});
    modal(html);
}

// Обработка добавления
async function addModel(form) {

    let val = {
        model_name: form.model_name.value,
        model_desc: form.model_desc.value
    }

    let res = Model.setModel(val);

    res.then(result => {
        // Закроем окно
        $(".dialog").dialog("close");
        // Обновим содержимое
        hashRefresh();
    },
        error => {
            $('#ErrorMessage').empty();
            $('#ErrorMessage').append(JSON.parse(error.response).messages);
        }
    );

}

// 
async function updateModelBotton(e) {
    // Отменить действие браузера по умолчанию
    e.preventDefault();
    // Остановка всплытия события
    e.stopPropagation();

    var model = await Model.getModels(e.target.getAttribute('model_id'));

    console.log(model);


    // Получим шаблон
    let html = View.render('updateModel', model);
    modal(html);
}

function UpdateModel(form) {
    
    var val = {
        model_id: form.model_id.value,
        model_namr: form.model_name.value,
        model_desc: form.model_desc.value
    }


    console.log(val);


    let res = Model.updateModel(val);

    res.then(result => {
        // Закроем окно
        $(".dialog").dialog("close");
        hashRefresh();
    },
        error => {
            $('#ErrorMessage').empty();
            $('#ErrorMessage').append(JSON.parse(error.response).messages);
        }
    );
}


/**
 * 
 * 
 * Функции страницы настройки цеха
 * 
 */


// Обработчик формы добавления цеха
function addGild(form){
    var val = {
            gild_number:    form.gild_number.value,
            gild_name:      form.gild_name.value,
            gild_desc:      form.gild_desc.value,
            gild_dimX:      form.gild_dimX.value,
            gild_dimY:      form. gild_dimY.value
        }
    
        let res = Model.setGild(val);

        res.then(result => {
            // Закроем окно
            $(".dialog").dialog("close");
            hashRefresh();
    
        },
            error => {
                $('#ErrorMessage').empty();
                $('#ErrorMessage').append(JSON.parse(error.response).messages);
            }
        );


}

// Получение настройки машины и размещение в DOM
// Обработчик формы
function addMachineGilds(form) {

    // Получим координаты изменяемой ячейки
    var x = form.dimX.value;
    var y = form.dimY.value;

    var val = {
        model_id: form.model_id.value,
        machine_number: form.machine_number.value,
        gild_id: form.gild_id.value,
        machine_desc: form.machine_desc.value,
        machine_posX: form.dimX.value,
        machine_posY: form.dimY.value
    }


    console.log(val);


    let res = Model.setMachines(val);

    res.then(result => {
        // Закроем окно
        $(".dialog").dialog("close");
        hashRefresh();

    },
        error => {
            $('#ErrorMessage').empty();
            $('#ErrorMessage').append(JSON.parse(error.response).messages);
        }
    );

}
