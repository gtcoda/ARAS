<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirModule'] . 'Users.php');


require_once($config['dirJWT'] . 'BeforeValidException.php');
require_once($config['dirJWT'] . 'ExpiredException.php');
require_once($config['dirJWT'] . 'SignatureInvalidException.php');
require_once($config['dirJWT'] . 'JWT.php');



class loginApi extends Api
{
    public $apiName = 'login';

    private $user;

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();

        $this->user = Users::getInstance();
    }

    /**
     * Метод GET
     * 
     * http://ДОМЕН/login
     * @return string
     */
    public function indexAction()
    {
        $answer = array(
            'status' => 'error',
            'messages' => 'Метод запрещен GET',
        );
        return $this->response($answer, 400);
    }

    /**
     * Метод GET
     * 
     * http://ДОМЕН/login/1
     * @return string
     * 
     * 
     * 
     */
    public function viewAction()
    {
        $answer = array(
            'status' => 'error',
            'messages' => 'Метод запрещен GET2',
        );
        return $this->response($answer, 400);
    }

    /**
     * Метод POST
     * Проверка аутентификационных данным и возврат токена
     * http://ДОМЕН/login + параметры запроса 
     * @return string
     * 
     * Пример тела запроса
     * {
     * "user_login": "freddy",
     * "user_password": "5453123",
     * }

     */
    public function createAction()
    {
        if ($this->user->CheckUser($this->requestParams['user_login'], $this->requestParams['user_password'])) {

            $row = $this->user->GetUserLogin($this->requestParams['user_login']);

            $token = array(
                "iss" => $this->config['JWT']['iss'],
                "aud" => $this->config['JWT']['aud'],
                "iat" => $this->config['JWT']['iat'],
                "nbf" => $this->config['JWT']['nbf'],
            );


            $token += ['data' => array(
                'user_id' => $row['user_id'],
                'user_login' => $row['user_login'],
                'role_name' => $this->user->GetUserRole($row['user_id']),
            )];

            // Cоздадим токен JWT
            $jwt = Firebase\JWT\JWT::encode($token, $this->config['JWT']['key']);

            $answer = array(
                'status' => 'success',
                'messages' => 'User confirmed',
                'jwt' => $jwt,
            );
            return $this->response($answer, 200);
        } else {
            $answer = array(
                'status' => 'error',
                'messages' => 'Неверный логин\пароль'
            );
            return $this->response($answer, 400);
        }
    }


    /**
     * Метод PUT
     * 
     * @return string
     */
    public function updateAction()
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';


        if (array_key_exists("jwt", (array)$this->requestParams)) {

            try {

                $jwt = $this->requestParams['jwt'];
                $data = Firebase\JWT\JWT::decode($jwt, $config['JWT']['key'], array('HS256'));
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Пользователь подтвержден',
                    'data' => $data,
                );
                return $this->response($answer, 200);
            } catch (Exception $e) {

                $answer = array(
                    'status' => 'error',
                    'messages' => $e->getMessage()
                );
                return $this->response($answer, 404);
            }
        }

        $answer = array(
            'status' => 'error',
            'messages' => 'Нет токена!'
        );
        return $this->response($answer, 404);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/users/1
     * @return string
     */
    public function deleteAction()
    {
        $answer = array(
            'status' => 'error',
            'messages' => 'Метод запрещен2',
        );
        return $this->response($answer, 400);
    }
}
