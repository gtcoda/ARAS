<?php 

return array(
    // Конфигурация. Различные ответы сервера
    'conf' => 'debug',
    'authentication' => false, // true - запретить анонимные запросы, false - разрешить 
    // Данные для подключения к БД
    'db' => array (
        'login' => 'aras',
        'password' => 'jj3ey7QiLUGvaO40',
        'db' => 'ARAS',
        'charset' => 'utf-8',
    ),
    // Настройки для JWT
    'JWT'=>array(
        'key'   => 'secret_key_1223486',    // Секретный ключ
        'iss'   => 'aras.gtcoda.ru',        // Адрес удостоверяющего центра
        'aud'   => 'aras.gtcoda.ru',        // Имя клиента для которого выпущен токен
        'iat'   => time(),                  // Время выпуска токена
        'nbf'   => time(),                  // Време начала действия токена
        'exp'   => (time() + (60*60*24*7)), // Время окончания действия токена (1 месяц с даты выпуска)

    ),
    
    // Данные api
    'api' => array(
        // Каждая конечная точка API описывается тремя свойствами.
        // Название конечной точки в URL /api/{модуль}
        'users' => array(
            // Имя класса обрабатывающего модуль
            'class'  => 'usersApi',
            // Назнавния файла где данный модуль хранится
            'file'   => 'users.php',
        ),
        'login' => array(
            'class'  => 'loginApi',
            'file'   => 'login.php',
        ),
        'gilds' => array(
            'class' => 'gildsApi',
            'file'  => 'gilds.php',
        ),

    ),

    // Пути 
    'dirApiModules' => $_SERVER['DOCUMENT_ROOT'].'/api/apiModules/',
    'dirJWT'        => $_SERVER['DOCUMENT_ROOT'].'/api/jwt/',
    'dirModule'     => $_SERVER['DOCUMENT_ROOT'].'/modules/',
    'dirObject'     => $_SERVER['DOCUMENT_ROOT'].'/object/',
    'dirConfig'     => $_SERVER['DOCUMENT_ROOT'].'/config/',
    'dirLog'        => $_SERVER['DOCUMENT_ROOT'].'/log/',
    

);
