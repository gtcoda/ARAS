<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

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
                    <h1>Шапка</h1>
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
                        <li class="nav-item">
                            <a class="nav-link" data-role="nav-set" href="#Settings">Настройки</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="signIn" data-role="nav-log" href="#Sing"></a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>



        <!--  
        <menu>
            <div class="row">
                <div class="col">
                    <div class="btn-group btn-group-lg">
                        <a class="btn btn-default" data-role="nav-over" href="#Overview">Обзор</a>
                        <a class="btn btn-default" data-role="nav-calen" href="#Calendar">Календарь</a>
                        <a class="btn btn-default" data-role="nav-rep" href="#Report">Отчет</a>
                        <a class="btn btn-default" data-role="nav-set" href="#Settings">Настройки</a>
                        <a class="btn btn-default" data-role="nav-task" href="#Task">Задания</a>
                    </div>
                </div>
            </div>
        </menu>   -->



        <!--  Основная часть   -->

        <div id="app">

        </div>

    </div>



    <!--- Шаблон для входа -->
    <script id="signInTemplate" type="text/template">

        <div data-dialog-title="Вход">
            <form id="signInForm" class="form-horizontal" role="form" method="POST">
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
                            <button id="signInBotton" type="submit" class="btn btn-default btn-sm">Войти</button>
                            <a id="signUpBotton" class="btn btn-default" href="">Регистрация</a>
                        </div>
                    </div>
            </form>

            <div id="ErrorMessage">

            </div>
        </div>

    </script>

    <!--- Шаблон для регистрации -->
    <script id="signUpTemplate" type="text/template">

        <div data-dialog-title="Регистрация">
        <form id="signUpForm">
  <div class="form-group">
    <label for="exampleInputEmail1">Login</label>
    <input type="login" class="form-control" name="login" placeholder="Login">
    </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" name="password" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1" placeholder="Name">Name</label>
    <input class="form-control" name="name">
  </div>

  <button  type="submit" class="btn btn-primary">Регистрация</button>
</form>
<div id="ErrorMessage">

            </div>
        </div>
    </script>









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


    <!--  MVC   -->
    <script src="js/entry.js" type="module"></script>

    <script src="js/main.js" type="module"></script>
</body>

</html>