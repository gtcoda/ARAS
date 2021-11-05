<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirModule'] . 'Repairs.php');


class repairsApi extends Api
{
    public $apiName = 'repairs';

    /**
     * Метод GET
     * @return string
     */
    public function indexAction()
    {
        return $this->response("", 404);
    }

    /**
     * Метод GET
     * @return string
     */
    public function viewAction()
    {
        return $this->response("", 404);
    }

    /**
     * Метод POST
     * Создание нового идентификатора ремонта
     * http://ДОМЕН/repairs + без параметров 
     * @return string
     * 
     * Пример тела запроса
     * {
     * "gild_number": "4", // Номер цеха()
     * "gild_name": "Сборочный", // Название
     * "gild_desc": "Расположен там то, начальник тот то", // Описание  
     * "gild_dimX":  "10", // Размер в ячейках по Х
     * "gild_dimY":  "20" // Размер в ячейках по Y
     * }
     */


    public function createAction()
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Добавляем id ремонт");

        $repair = Repairs::getInstance();

        try {
            $res = $repair->Open();
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Repair is open',
                'repair_id'      => $res,
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
     * @return string
     */
    public function updateAction()
    {
        return $this->response("", 404);
    }

    /**
     * Метод DELETE
     * @return string
     */
    public function deleteAction()
    {
        return $this->response("", 404);
    }
}
