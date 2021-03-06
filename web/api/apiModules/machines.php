<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirModule'] . 'Machines.php');



class machinesApi extends Api
{
    public $apiName = 'machines';



    private $machine;

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();

        $this->machine = Machines::getInstance();
    }



    /**
     * Метод GET
     * 
     * Вернуть все еденицы оборудования
     * http://ДОМЕН/machines
     * @return string
     */
    public function indexAction()
    {

        $data = [];

        $data = $this->machine->Gets();
        $answer = array(
            'status' => 'success',
            'messages' => 'All machines',
            'data' => $data,
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод GET
     * 
     * Получить информацию о, оборудовании с {id}
     * http://ДОМЕН/machines/1
     * @return string
     */
    public function viewAction()
    {

        try {
            $data = $this->machine->Get($this->requestUri[0]);
            $answer = array(
                'status' => 'success',
                'messages' => 'Machine',
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
     * Создание новой еденицы оборудования
     * http://ДОМЕН/machines + параметры запроса 
     * @return string
     * 
     * Пример тела запроса
     * {
     * "model_id"       : "5",      // id модели оборудования
     * "machine_number" : "42097",  // табельный номер
     * "gild_id"        : "1",      // id цеха
     * "machine_desc"   : "Стоящий вдоль" // Комментарий к машине
     * "machine_posX"   : "10"      // Позиция машины в цеху по Х
     * "machine_posY"   : "12"      // Позиция машины в цеху по Y
     * }
     */


    public function createAction()
    {

        try {
            $res = $this->machine->Add($this->requestParams);
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Machine creation completed',
                'data'      => $res,
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
     * Метод PUT
     * Изменить модель 
     * Пример тела запроса
     * {
     * "model_id"       : "5",      // id модели оборудования
     * "machine_number" : "42097",  // табельный номер
     * "gild_id"        : "1",      // id цеха
     * "machine_desc"   : "Стоящий вдоль" // Комментарий к машине
     * "machine_posX"   : "10"      // Позиция машины в цеху по Х
     * "machine_psxY"   : "12"      // Позиция машины в цеху по Y
     * }
     * @return string
     */
    public function updateAction()
    {


        try {
            if ($this->machine->Update($this->requestParams, $this->requestUri[0])) {
                $answer = array(
                    'status'    => 'success',
                    'messages'  => 'Model update',
                    'data'      => $this->machine->Get($this->requestUri[0]),
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
     * http://ДОМЕН/machines/1
     * @return string
     */
    public function deleteAction()
    {

        try {
            if ($this->machine->Delite($this->requestUri[0])) {
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Model delete'
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
