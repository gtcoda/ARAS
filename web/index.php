<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>



    <script src="rest-client.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>


    <!--  SPA   
    <script src="js/route.js"></script>
    <script src="js/router.js"></script>
    <script src="js/app.js"></script>
-->

    <!--  MVC   -->
    <script src="js/entry.js" type="module"></script>





    <script>
      /*  var api = new RestClient('https://aras.gtcoda.ru/api');
        api.res('users');
        api.users('gtcoda').get().then(function(users) {
            console.log(users.data.user_id);
        })

        api.res('events');
        api.events.get().then(function(events) {
            console.log(events);
        })

*/

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

                $("<div> <label>Логин</label> </div>") // Создание элемента div
                    .addClass("dialog") // Назначение элементу класса
                    .appendTo("body") // Вставляет в элемент, после содержимого
                    .dialog({ // Создание из элемента диалогового окна
                        title: $(this).attr("data-dialog-title"), // Добавим в заголовок атрибут.
                        close: function() { // Действие по крестику
                            $(this).remove()
                        },
                        modal: true, // Можно взаимойдействовать с страницей
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

    <!--  Шапка с меню   -->
    <footer>
        <div class="btn-group btn-group-lg">
            <a class="btn btn-default" data-role="nav-over" href="#Overview">Все пользователи</a>
            <a class="btn btn-default" data-role="nav-set" href="#Settings">Все события</a>
        </div>
    </footer>

    <!--  Основная часть   -->

    <div id="app">

    </div>


    <p>Проверка </p>
    <button class='login' data-dialog-title="Войти">Войти</button>


    <script id="userTemplate" type="text/template">

        <div>
        <p class=".font-weight-bold">Id : {{user_id}}</p>
        <p>Login: {{user_login}}</p>
        <p>Name: {{user_name}}</p>
    </div>

    </script>






    <script id="singInTemplate" type="text/template">

        <form class="form-horizontal" role="form" method="POST">
            <div class="form-group">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Логин</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" placeholder="Логин" name="login">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Пароль</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" placeholder="Пароль" name="password">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default btn-sm">Войти</button>
                    </div>
                </div>
        </form>

    </script>





    


</body>

</html>