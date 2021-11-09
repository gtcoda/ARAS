<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="rest-client.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>


    <!--  SPA   -->
    <script src="js/route.js"></script>
    <script src="js/router.js"></script>
    <script src="js/app.js"></script>





    <script>
        var api = new RestClient('https://aras.gtcoda.ru/api');
        api.res('users');
        api.users('gtcoda').get().then(function(users) {
            console.log(users.data.user_id);
        })



        $(function() {
            $.ajaxSetup({
                cache: false
            });

            // Навешиваем событие на click на элемент
            $(".login").on("click", function(e) {
                // Отменить действие браузера по умолчанию
                e.preventDefault();
                // Остановка всплытия события
                e.stopPropagation();

                $("<div> <label>Логин</label> </div>")        // Создание элемента div
                    .addClass("dialog") // Назначение элементу класса
                    .appendTo("body")   // Вставляет в элемент, после содержимого
                    .dialog({           // Создание из элемента диалогового окна
                        title: $(this).attr("data-dialog-title"),   // Добавим в заголовок атрибут.
                        close: function() {                         // Действие по крестику
                            $(this).remove()
                        },
                        modal: true,    // Можно взаимойдействовать с страницей
                        draggable: true                               
                    })
                // .load(this.href);
            });

            // Закрыть окно по клику за пределами модального окна
            $(document).on("click", function(e) {
                $(e.target).closest(".ui-dialog").length || $(".dialog").dialog("close");
            })
        });
    </script>
    <title>ARAS</title>
</head>

<body>
    <p>Проверка </p>
    <button class='login' data-dialog-title="Войти">Войти</button>
    



    <ul>
      <li><a href="#home">Home</a></li>
      <li><a href="#about">About</a></li>
    </ul>
    <div id="app">

    </div>


    
</body>

</html>