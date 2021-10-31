<?php
// Получим настройки
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirJWT'] . 'BeforeValidException.php');
require_once($config['dirJWT'] . 'ExpiredException.php');
require_once($config['dirJWT'] . 'SignatureInvalidException.php');
require_once($config['dirJWT'] . 'JWT.php');


abstract class Api
{
    public $apiName = ''; //users

    protected $method = ''; //GET|POST|PUT|DELETE

    public $requestUri = [];
    public $requestParams = [];
    public $requestGET = []; // параметры переданные через ?

    protected $action = ''; //Название метод для выполнения


    public function __construct()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");


        $this->requestGET = $_GET;

        //Массив GET параметров разделенных слешем(без параметров  после '?')
        $this->requestUri = explode('/', trim(strtok($_SERVER['REQUEST_URI'], '?'), '/'));


        #$this->requestParams = $_REQUEST;
        $this->requestParams = json_decode(file_get_contents("php://input"), true);

        //Определение метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    public function run()
    {

        //Первые 2 элемента массива URI должны быть "api" и название таблицы
        if (array_shift($this->requestUri) !== 'api' || array_shift($this->requestUri) !== $this->apiName) {
            throw new RuntimeException('API Not Found', 404);
        }

        // Проверим аутентификацию
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        $aut = $this->checkAut();
        // Но на API login проверку, конечно, не делаем
        // Если после проверки результат не true, значит есть аварийное сообщение. Его и отправляем.
        if ($config['authentication'] && $this->apiName != 'login' && $aut !== true) {
            return $aut;
        }

        //Определение действия для обработки
        $this->action = $this->getAction();

        //Если метод(действие) определен в дочернем классе API
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function response($data, $status = 500)
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($this->debagInfo($data));
    }

    private function requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }

    protected function getAction()
    {
        $method = $this->method;
        switch ($method) {
            case 'GET':
                if ($this->requestUri) {
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }

    abstract protected function indexAction();
    abstract protected function viewAction();
    abstract protected function createAction();
    abstract protected function updateAction();
    abstract protected function deleteAction();





    /**
     * Добавить отладочную информацию 
     * @return array
     */
    protected function debagInfo($answer)
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        if ($config['conf'] == 'debug') {
            $answer += ['debug' => [
                'responseJSON'  => $this->requestParams,
                'responseURL'   => $this->requestUri,
                'responseGET'   => $this->requestGET,
            ]];
        }

        return $answer;
    }

    /**
     * Проверить аутентификацию запроса 
     * @return array
     */
    protected function checkAut()
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

        // Какой из массивов параметрое будет заполнен неизвестно, 
        // поэтому проверяем сначала на наличие, а потом на присутствие ключа.
        if ((!empty($this->requestParams) && array_key_exists("jwt", $this->requestParams)) ||
            (!empty($this->requestGET) && array_key_exists("jwt", $this->requestGET))
        ) {

            try {
                if ((!empty($this->requestParams) && array_key_exists("jwt", $this->requestParams))) {
                    $jwt = $this->requestParams['jwt'];
                } else {
                    $jwt = $this->requestGET['jwt'];
                }

                Firebase\JWT\JWT::decode($jwt, $config['JWT']['key'], array('HS256'));
                return true;
            } catch (Exception $e) {
                $answer = array(
                    'status' => 'error',
                    'messages' => $e->getMessage()
                );
                return $this->response($answer, 404);
            }
        } else {
            $answer = array(
                'status' => 'error',
                'messages' => 'Нет токена!'
            );
            return $this->response($answer, 404);
        }
    }
}
