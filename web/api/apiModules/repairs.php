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
        //$data = $this->repair->GetGrantt();
        $data = $this->repair->getCalendar();
        $answer = array(
            'status'    => 'success',
            'messages'  => 'GranttData',
            'data'      => $data
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод GET
     * @return string
     * 
     * site/repairs/{id} - получить номер последнего ремента для машины с id
     */
    public function viewAction()
    {

        $res = $this->repair->Сurrent($this->requestUri[0]);
        $data = $this->repair->CurrentData($this->requestUri[0]);
        $answer = array(
            'status'    => 'success',
            'messages'  => 'Current repair_id',
            'repair_id' => $res,
            'data'      => $data,
        );
        return $this->response($answer, 200);
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
