<?php 
$config = include $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

require_once($config['dirApiModules'].'apiClass.php');
require_once($config['dirConfig'].'safeMySQL.php');
require_once($config['dirConfig'].'log.php');

require_once($config['dirModule'].'Users.php');


require_once ($config['dirJWT'].'BeforeValidException.php');
require_once ($config['dirJWT'].'ExpiredException.php');
require_once ($config['dirJWT'].'SignatureInvalidException.php');
require_once ($config['dirJWT'].'JWT.php');



class loginApi extends Api{
    public $apiName = 'login';


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
     * 
     * 
     * 
     * 
     */
    public function createAction()
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Зашли в обработчик!");

        $user = Users::getInstance();



        $log->add($this->requestParams);

        if ($user->CheckUser($this->requestParams['user_login'], $this->requestParams['user_password'])) {

            $row = $user->GetUserLogin($this->requestParams['user_login']);

            $token = array(
                "iss" => $config['JWT']['iss'],
                "aud" => $config['JWT']['aud'],
                "iat" => $config['JWT']['iat'],
                "nbf" => $config['JWT']['nbf'],
            );


            $token += ['data'=>array(
                'user_id' => $row['user_id'],
                'user_login' => $row['user_login'],
                'role_name' => $user->GetUserRole($row['user_id']),
            )];
            
            // Cоздадим токен JWT
            $jwt = Firebase\JWT\JWT::encode($token, $config['JWT']['key']);
            
            $answer = array(
                'status' => 'success',
                'messages' => 'User confirmed',
                'jwt' => $jwt,
            );
            return $this->response($answer, 200);
        }
        else{
            $answer = array(
                'status' => 'error',
                'messages' => 'User NOT confirmed'
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


        $log = Logi::getInstance();
        $log->add("Проверяем JWT ");
        
  
        if(array_key_exists("jwt",$this->requestParams)){

            try {

                $jwt = $this->requestParams['jwt'];
                $data = Firebase\JWT\JWT::decode($jwt, $config['JWT']['key'], array('HS256'));
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Пользователь подтвержден',
                    'data' => $data,
                );
                return $this->response($answer, 200);

            }

            catch(Exception $e){

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


?>