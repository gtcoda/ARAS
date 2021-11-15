<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="css/bootstrap.css" rel="stylesheet">


    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>



    <script src="js/rest-client.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>


    <!--  SPA   
    <script src="js/route.js"></script>
    <script src="js/router.js"></script>
    <script src="js/app.js"></script>
-->


    <script>
        $(function() {
            function getTemplate(templateName) {
                var templateUrl = "templates/" + templateName + "Template.hbs";

                $.ajax({
                    url: templateUrl,
                    success: function(data) {
                        $("#templates").append(data);
                    }
                });
            }
            getTemplate('sing');
            getTemplate('models');
            getTemplate('gilds');

        });


        /*  var api = new RestClient('https://aras.gtcoda.ru/api');
        api.res('users');
        api.users('gtcoda').get().then(function(users) {
            console.log(users.data.user_id);
        })

        api.res('events');
        api.events.get().then(function(events) {
            console.log(events);
        })



                <p>Message: {{event_message}}</p>
                <p>MachineId: {{machine_id}}</p>
                <p>UserId: {{user_id}}</p>


           API.res('login');
            API.login.post({
                user_login: "login",
                user_password: "password"
            }).then(function(login) {
                console.log(" Ошибки нет ");
            },function(xnr){
                console.log(" Ошибка ");
                console.log(xnr);
            });





            */
    </script>
    <title>ARAS</title>
</head>

<body>

    <div class="container">
        <!--  Шапка с меню   -->
        <footer>
            <div class="row">
                <div class="col">
                    <h2>Шапка</h2>
                </div>
            </div>
        </footer>




        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" data-role="nav-over" href="#Overview">Обзор</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" data-role="nav-calen" href="#Calendar">Календарь</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-role="nav-rep" href="#Report">Отчет</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-role="nav-task" href="#Task">Задания</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#Setting" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Настройки
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#Gilds">Цех</a></li>
                                <li><a class="dropdown-item" href="#Model">Модель</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="signIn" data-role="nav-log" href="#Sing"></a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <!--  Основная часть   -->

        <div class="mt-3"  id="app">

        </div>

    </div>


    <script id="userTemplate" type="text/template">

        <div>
        <p class=".font-weight-bold">Id : {{user_id}}</p>
        <p>Login: {{user_login}}</p>
        <p>Name: {{user_name}}</p>
    </div>

    </script>

    <script id="usersTemplate" type="text/template">
        <div class="flex-container">
        {{#each this}}
        <div>
            <p>Id : {{user_id}}</p>
            <p>Login: {{user_login}}</p>
            <p>Name: {{user_name}}</p>
        </div>
        {{/each}}
    </div>
    </script>


    <script id="eventsTemplate" type="text/template">
        <div>
            <select data-role="sort">
                <option value="event_id"> По event_id </option>
                <option value="user_id"> По user_id </option>
            </select>    
            <div class="flex-container">
            {{#each this}}
                <div>
                    <p>Id : {{event_id}}</p>
                    <p>Message: {{event_message}}</p>
                    <p>MachineId: {{machine_id}}</p>
                    <p>UserId: {{user_id}}</p>
                </div>
            {{/each}}   
            </div>
        </div>
    </script>



    <div id="templates">


    </div>

    <!--  MVC   -->
    <script src="js/entry.js" type="module"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/main.js" type="module"></script>







</body>

</html>