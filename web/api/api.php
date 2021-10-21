<?php
require_once('config/safemysql.class.php');
require_once('config/log.php');
require_once('config/config.php');

$log = Logi::getInstance();





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

$config = Config::getInstance();

$log->add("Проверка");

$log->add($config->get('login'));


//$db = new SafeMysql(array('user' => $config->get('login'), 'pass' => $config->get('password'),'db' => $config->get('db'), 'charset' => $config->get('charset')));





echo json_encode($apiRequest);











die();



function router($url, ...$args)
{
    (empty($args[1]) || false !== strpos(METHOD, $args[0]))
    && (URI === $url || preg_match('#^' . $url . '$#iu', URI, $match))
    && die(call_user_func_array(end($args), $match ?? []));
}

router('/api/games', 'GET', function () {
    echo 'список игрушек';
});

router('/api/game/(\d+)', 'GET', function (...$args) {
    echo 'информация о игрушке: ', $args[1];
});

router('/api/games', 'POST', function () {
    echo 'добавить новую игрушку';
});

router('/api/games/(\d+)', 'PUT', function (...$args) {
    echo 'обновить существующую игрушку: ', $args[1];
});

router('/api/games/(\d+)', 'DELETE', function (...$args) {
    echo ' удалить игрушку: ', $args[1];
});

?>