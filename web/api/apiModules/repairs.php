<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirModule'] . 'Repairs.php');


class repairsApi extends Api
{
    public $apiName = 'repairs';



    private $repair;

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();

        $this->repair = Repairs::getInstance();
    }

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
     */


    public function createAction()
    {
        try {
            $res = $this->repair->Open();
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Repair is open',
                'repair_id'      => $res,
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
