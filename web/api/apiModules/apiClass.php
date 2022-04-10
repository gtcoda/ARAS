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
    public $requestGETParam = []; // обработаные параметры Get переданные через ?

    protected $action = ''; //Название метод для выполнения

    protected $config = [];
    protected $log;


    public function __construct()
    {
        $this->log = Logi::getInstance();
        $this->config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");


        $this->requestGET = $_GET;
        $this->requestGETParam = $this->GETParamDecode($this->requestGET);
        $this->statusCode = array(
            'OK' => 200,
        );

        //Массив GET параметров разделенных слешем(без параметров  после '?')
        $this->requestUri = explode('/', trim(strtok($_SERVER['REQUEST_URI'], '?'), '/'));


        #$this->requestParams = $_REQUEST;
        $this->requestParams = json_decode(file_get_contents("php://input"), true);

        


        $this->log->add("##################### Полученые данные в запросе ##############");
        $this->log->add("requestGET:");
        $this->log->add($this->requestGET);
        $this->log->add("requestUri:");
        $this->log->add($this->requestUri);
        $this->log->add("requestParams:");
        $this->log->add($this->requestParams);
        $this->log->add("###############################################################");

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
            //######################################################################################################
            /**
             * Поймаем все необработаные исключения и выведем ошибку
             */
            try {
                return $this->{$this->action}();
            } catch (Exception $e) {
                $answer = array(
                    'status' => 'error',
                    'messages' => $e->getMessage(),
                );
                return $this->response($answer, 400);
            }            
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
            400 => 'Bad Request',
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
                'responseGETParam'   => $this->requestGETParam,
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



    /**
     * Проверить аутентификацию запроса 
     * @return array
     * 
     * Коды Ошибок:
     * 1 - No token,
     * 2 - Invalid token
     */


    protected function login()
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

        // Какой из массивов параметрое будет заполнен неизвестно, 
        // поэтому проверяем сначала на наличие, а потом на присутствие ключа.
        if ((!empty($this->requestParams) && array_key_exists("jwt", $this->requestParams)) ||
            (!empty($this->requestGET) && array_key_exists("jwt", $this->requestGET))
        ) {

            try {
                // Получим jwt
                if ((!empty($this->requestParams) && array_key_exists("jwt", $this->requestParams))) {
                    $jwt = $this->requestParams['jwt'];
                } else {
                    $jwt = $this->requestGET['jwt'];
                }

                $login_data = Firebase\JWT\JWT::decode($jwt, $config['JWT']['key'], array('HS256'));

                return (array)$login_data;
            } catch (Exception $e) {
                throw new RuntimeException('Invalid token', 2);
            }
        } else {
            throw new RuntimeException('No token', 1);
        }
    }

    /**
     * Получает подстроку между двух строк 
     * @return array
     * 
     * 
     * $fullstring = 'this is my [tag]dog[/tag]';
     * $parsed = get_string_between($fullstring, '[tag]', '[/tag]');
     * echo $parsed; // (result = dog)
     * 
     */

    protected function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }



    /**
     * 
     * Преобразует параметр view
     * {model_id,model_name}
     * в массив аргументов
     */
    protected function viewGetArr($value)
    {
        if (empty($value)) return;
        $fields = $this->get_string_between($value, "[", "]");
        return explode(',', (string)$fields);
    }

    /**
     * 
     * Преобразование известных параметров GET
     * 
     */
    protected function GETParamDecode($get){
        $this->log->add($get);
        
        $GETParam["format"] = (empty($this->requestGET['format'])) ? 'all' : $this->requestGET['format'];

        foreach($get as $key => $value){
            if($key == "view"){
                $GETParam[$key] = $this->viewGetArr($value);
            }
            else{
                $GETParam[$key] = $value;
            }
        }


        return $GETParam;
    }

    /**
     * 
     * Преобразование в файл
     * 
    */
    function base64_to_file($base64_string, $output_file) {

        $this->log->add($base64_string);


        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }

}
