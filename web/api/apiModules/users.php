<?php
$config = include $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

require_once($config['dirApiModules'].'apiClass.php');
require_once($config['dirConfig'].'safeMySQL.php');
require_once($config['dirConfig'].'log.php');


require_once($config['dirModule'].'Users.php');


class usersApi extends Api
{
    public $apiName = 'users';

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/users
     * @return string
     */
    public function indexAction()
    {
        


        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Зашли в обработчик!");

        $user = Users::getInstance();
        $data = [];

        $data = $user->GetUsers();
        $answer = array(
            'status' => 'success',
            'messages' => 'All users',
            'data' => $data,
        );
        return $this->response($answer, 200);

    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/users/1
     * @return string
     * 
     * 
     * 
     */
    public function viewAction()
    {
        $user = Users::getInstance();
        $data = [];

        try {

            if (ctype_digit($this->requestUri[0])){
                $data = $user->GetUserId($this->requestUri[0]);
            }
            else{
                $data = $user->GetUserLogin($this->requestUri[0]);
            }


            $answer = array(
                'status' => 'success',
                'messages' => 'User',
                'data' => $data,
            );
            return $this->response($answer, 200);
        } catch (Exception $e) {
            $answer = array(
                'status' => 'error',
                'messages' => $e->getMessage(),
            );
            return $this->response($answer, 400);
        }
    }

    /**
     * Метод POST
     * Создание нового пользователя
     * http://ДОМЕН/users + параметры запроса name, login, password
     * @return string
     * 
     * Пример тела запроса
     * {
     * "user_login": "freddy",
     * "user_password": "5453123",
     * "user_name": "fred"
     * }
     * 
     * 
     * 
     * 
     */
    public function createAction()
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
 

        $user = Users::getInstance();

        try {
            if ($user->AddUser($this->requestParams)) {
                $answer = array(
                    'status' => 'success',
                    'messages' => 'User creation completed'
                );
                return $this->response($answer, 200);
            }
        } catch (Exception $e) {
            
            
            $log->add(print_r($e, true));

            $answer = array(
                'status' => 'error',
                'messages' => $e->getMessage(),
            );

            return $this->response($answer, 400);
        }
    }

    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id или логину)
     * http://ДОМЕН/users/1 + параметры запроса name, email
     * @return string
     */
    public function updateAction()
    {

        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Зашли в обработчик!");

        $user = Users::getInstance();

        try {
            if ($user->UpdateUser($this->requestParams, $this->requestUri[0])) {
                $answer = array(
                    'status' => 'success',
                    'messages' => 'User update'
                );
                return $this->response($answer, 200);
            }
        } catch (Exception $e) {


            $log->add(print_r($e, true));

            $answer = array(
                'status' => 'error',
                'messages' => $e->getMessage(),
            );

            return $this->response($answer, 400);
        }
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/users/1
     * @return string
     */
    public function deleteAction()
    {

        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Зашли в обработчик удаления!");

        $user = Users::getInstance();

        try {
            if ($user->DeleteUser($this->requestUri[0])) {
                $answer = array(
                    'status' => 'success',
                    'messages' => 'User delete'
                );
                return $this->response($answer, 200);
            }
        } catch (Exception $e) {


            $log->add(print_r($e, true));

            $answer = array(
                'status' => 'error',
                'messages' => $e->getMessage(),
            );

            return $this->response($answer, 400);
        }
    }
}
