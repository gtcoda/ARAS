<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>

    <script src="js/lib/rest-client.js"></script>
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/bootstrap.js"></script>
    <script src="js/lib/jquery-ui.min.js"></script>

    <script src="js/lib/dhtmlx/dhtmlxgantt.js?v=7.1.10"></script>
	<link rel="stylesheet" href="js/lib/dhtmlx/dhtmlxgantt.css?v=7.1.10">

    <title>ARAS</title>
</head>

<body>

    <div class="container">
        <!--  Шапка с меню   -->
        <footer>
            <div class="row">
                <div class="col">
                    <h2>Автоматизированная система учета ремонтов</h2>
                </div>
            </div>
        </footer>




        <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
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
                            <a class="nav-link" data-role="nav-task" href="#Wiki">Документация</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#Setting" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Настройки
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#Gilds">Цех</a></li>
                                <li><a class="dropdown-item" href="#Models">Модель</a></li>
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

        <div class="mt-3" id="app">

        </div>

    </div>

    <div id="templates">
    <?php
    $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    require_once($config['dirConfig'] . 'log.php');

    $log = Logi::getInstance();

    $file = scandir($config['dirTemplate']);

 
    for($i=2;$i<count($file);$i++){
        echo file_get_contents($config['dirTemplate'].$file[$i],true);
    }
    

    
    ?>

    </div>


    <!--  MVC   -->
    <script src="js/entry.js" type="module"></script>
    <script src="js/main.js" type="module"></script>



</body>

</html>