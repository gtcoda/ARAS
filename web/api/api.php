<?php


/*
require_once('config/safemysql.class.php');
require_once('config/log.php');

// Подготовимся к логированию
$log = Logi::getInstance();

// Получим настройки
$config = include 'config/config.php';

// необходимые HTTP-заголовки 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// получаем отправленные данные 
$data = json_decode(file_get_contents("php://input"));

# Author - Fedor Vlasenko, vlasenkofedor@gmail.com
define('METHOD', $_SERVER['REQUEST_METHOD']);
define('URI', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));


// Отрежем из URL часть /api/
$url = mb_substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),5);

// Разобьем параметры URL по "/"  
$request = explode('/',trim($url, "/"));
$module = array_shift($request);
array_slice($request,0,1);

// Структура содержащая параметры запроса
$apiRequest = [
    'method' => $_SERVER['REQUEST_METHOD'], // Метод запроса
    'module' => $module,                    // Запрашиваемый модуль    
    'parametersUrl' => $request,            // Параметры переданные через URL
    'parameterJSON' => $data,               // Параметры переданые через тело запроса
];



$db = new SafeMysql(array('user' => $config['db']->login, 'pass' => $config['db']->password,'db' => $config['db']->db));







switch ($apiRequest['module']) {
    case 'users': {


    } break;

    case 'model': {


    } break;
    
    
    default:{
        $json = [
            'status'    => 0,
            'messages'  => 'ERROR: not modules.',
            'data' => $apiRequest,
        ];
    } break;
}

header('Location:' . $apiRequest['module'], true, 404);
echo json_encode($json);



*/


// Получим настройки
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once($config['dirConfig'] . 'log.php');


// Подготовимся к логированию
$log = Logi::getInstance();

################### Разбор стройки URL

// Отрежем из URL часть /api/
$url = mb_substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 5);
// Разобьем параметры URL по "/"  
$request = explode('/', trim($url, "/"));


################### Запустим на исполнение

// Если имя запрашиваемого модуля есть в настройках
if (array_key_exists($request[0], $config['api'])) {
    // Подключим файл
    require_once($config['dirApiModules'] . $config['api'][$request[0]]['file']);
    // Создадим API
    $Api = new $config['api'][$request[0]]['class']();
    // Запусти API
    echo $Api->run();
} else {
// Если модуля нет, ответим    
    header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");
    header("HTTP/1.1 405 Method Not Allowed ");

    $answer = array(
        'status'    => 'false',
        'message'   => 'Api not found.'
    );

    if ($config['conf'] == 'debug') {
        $answer += ['debug' => [
            'responseJSON' => json_decode(file_get_contents("php://input")),
            'responseURL' => $request,
        ]];
    }

    echo json_encode($answer);
}








?>