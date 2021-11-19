<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirModule'] . 'Events.php');
require_once($config['dirModule'] . 'Users.php');

require_once($config['dirJWT'] . 'BeforeValidException.php');
require_once($config['dirJWT'] . 'ExpiredException.php');
require_once($config['dirJWT'] . 'SignatureInvalidException.php');
require_once($config['dirJWT'] . 'JWT.php');

class eventsApi extends Api
{
    public $apiName = 'events';
    private $event;

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();

        $this->event = Events::getInstance();
    }

    /**
     * Метод GET
     * 
     * Вернуть все события. С ограничениями
     * http://ДОМЕН/events
     * @return string
     */
    public function indexAction()
    {
        $data = [];

        $data = $this->event->Gets();
        $answer = array(
            'status' => 'success',
            'messages' => 'All events',
            'data' => $data,
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод GET
     * 
     * Получить информацию о сообщении, оборудовании с {id}
     * http://ДОМЕН/events/1
     * 
     * Получить все сооющения относящиеся к машине с {id}
     * http://ДОМЕН/events/machine/{id}
     * @return string
     */
    public function viewAction()
    {
        try {
           
           
           
            if($this->requestUri[0]=="machine"){
                

            }
            else{
                $data = $this->event->Get($this->requestUri[0]);
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Event',
                    'data' => $data,
                );
                return $this->response($answer, 200);
            }

           
           






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
     * Создание нового сообщения
     * http://ДОМЕН/events + параметры запроса 
     * @return string
     * 
     * Пример тела запроса
     * {
     * "machine_id"     : "4",     // id станка
     * "event_message"  : "1",     // сообщение
     * "repair_id"      : "2"      // id ремонта к которому относится запись
     * "event_modif_1"  : "1"      // Модификатор события
     * "event_modif_2"  : "2"      // Модификатор события
     * "event_modif_3"  : ""       // Модификатор события
     * "jwt"            : "token"
     * }
     */


    public function createAction()
    {

        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Добавляем сообщение");


        $log->add($this->requestParams);

        try {
            $login_data = $this->login();
        } catch (Exception $e) {
            $answer = array(
                'status'    => 'error',
                'messages'  => $e->getMessage()
            );
            $log->add($answer);
            return $this->response($answer, 400);
        }

        $param["user_id"]       = ((array)$login_data['data'])['user_id'];
        $param["machine_id"]    = $this->requestParams['machine_id'];
        $param["repair_id"]     = $this->requestParams['repair_id'];
        $param["event_message"] = $this->requestParams['event_message'];

        if (!empty($this->requestParams['event_modif_1'])) {
            $param["event_modif_1"] = $this->requestParams['event_modif_1'];
        }
        if (!empty($this->requestParams['event_modif_2'])) {
            $param["event_modif_2"] = $this->requestParams['event_modif_2'];
        }
        if (!empty($this->requestParams['event_modif_3'])) {
            $param["event_modif_3"] = $this->requestParams['event_modif_3'];
        }

        

        try {
            $res = $this->event->Add($param);
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Event Add',
                'data'      => $res,
            );

            return $this->response($answer, 200);
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
     * Изменить сообщение(только текст)
     * Пример тела запроса
     * {
     * "event_message"  : "Измененное собщение",     // сообщение
     * "jwt"            : "token"
     * }
     * @return string
     */
    public function updateAction()
    {

        try {
            $this->login();
        } catch (Exception $e) {
            $answer = array(
                'status'    => 'error',
                'messages'  => $e->getMessage()
            );
            $this->log->add($answer);
            return $this->response($answer, 400);
        }

        try {
            if ($this->event->Update($this->requestParams, $this->requestUri[0])) {
                $answer = array(
                    'status'    => 'success',
                    'messages'  => 'Event update',
                    'data'      => $this->event->Get($this->requestUri[0]),
                );
                return $this->response($answer, 200);
            }
        } catch (Exception $e) {
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
     * http://ДОМЕН/events/1
     * @return string
     */
    public function deleteAction()
    {
        
        try {
            $this->login();
        } catch (Exception $e) {
            $answer = array(
                'status'    => 'error',
                'messages'  => $e->getMessage()
            );
            $this->log->add($answer);
            return $this->response($answer, 400);
        }





        try {
            if ($this->event->Delite($this->requestUri[0])) {
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Event delete'
                );
                return $this->response($answer, 200);
            }
        } catch (Exception $e) {
            $answer = array(
                'status' => 'error',
                'messages' => $e->getMessage(),
            );

            return $this->response($answer, 400);
        }
    }
}
