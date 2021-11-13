import View from "./view.js";
import Model from "./model.js";
import * as settings from './settings.js';


/*
Функции для работы обслуживания входа и регистрации
*/
$(function () {
    $.ajaxSetup({
        cache: false
    });

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

    // Создать модальное окно
    function modal(html){
        $(html) // Создание элемента div
        .addClass("dialog") // Назначение элементу класса
        .appendTo("body") // Вставляет в элемент, после содержимого
        .dialog({ // Создание из элемента диалогового окна
            title: $(html).attr("data-dialog-title"), // Добавим в заголовок атрибут.
            close: function () { // Действие по крестику
                $(this).remove()
            },
            modal: true, // Можно взаимойдействовать с страницей
            draggable: true
        })
    }


    /**
     * 
     * Обработчик вызовов модального окна входа и регистрации
     * 
     * Вход -> Модальное окно входа -> Обработчик кнопки регистрации -> модальное окно регистрации -> Обработчик регистрации
     *                              -> Обработчик входа
     * 
     */

    function singIn() {
        // Навешиваем событие на click на элемент #signIn
        $("#signIn").on("click", function (e) {
            // Отменить действие браузера по умолчанию
            e.preventDefault();
            // Остановка всплытия события
            e.stopPropagation();

            // Если токена нет выдавим форму и войдем
            if ($.isEmptyObject(settings.getJWT())) {
                // Получим шаблон
                let html = View.render('signIn', {});


                modal(html);

                $('#signInForm').submit(async function (e) {
                    // Отменить действие браузера по умолчанию
                    e.preventDefault();
                    // Остановка всплытия события
                    e.stopPropagation();

                    let res = Model.login(this.login.value, this.password.value);

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


                });


                // Закрыть окно по клику за пределами модального окна
                $(document).on("click", function (e) {
                    $(e.target).closest(".ui-dialog").length || $(".dialog").dialog("close");
                })



                // Навешиваем событие на click на элемент #signUp
                $("#signUpBotton").on("click", function (e) {
                    // Отменить действие браузера по умолчанию
                    e.preventDefault();
                    // Остановка всплытия события
                    e.stopPropagation();

                    // Получим шаблон
                    let html = View.render('signUp', {});

                    console.log(html);
                    // Очистим существующее модальное окно и заполним новым шаблоном
                    $(".dialog").empty();
                    $(".dialog").append(html);

                    $(".ui-dialog-title").empty();
                    $(".ui-dialog-title").append($(html).attr("data-dialog-title"));


                    // Навешиваем событие на отправку формы
                    $('#signUpForm').submit(async function (e) {
                        // Отменить действие браузера по умолчанию
                        e.preventDefault();
                        // Остановка всплытия события
                        e.stopPropagation();

                        let res = Model.setUser(this.login.value, this.password.value, this.name.value);

                        res.then(result => {
                            // Закроем окно
                            $(".dialog").dialog("close");


                        },
                            error => {
                                $('#ErrorMessage').empty();
                                $('#ErrorMessage').append(JSON.parse(error.response).messages);
                            }
                        );


                    });






                });





            }
            else {
                // Если токен есть, удалим данные входа
                settings.setJWT('');
                checkSingInMenu();
            }

        });
    }

    

    checkSingInMenu();

    singIn();

});


/*
$(function(){
$(".nav-link").on("click",function(){
    let hash = $(this).attr("href");
    document.location.hash = '';
    document.location.hash = hash;
});

});

*/