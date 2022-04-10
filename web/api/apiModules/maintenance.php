<?php
require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirModule'] . 'Maintenance.php');


class maintenanceApi extends Api
{
    public $apiName = 'maintenance';
    private $mt;

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();
        $this->mt = Maintenance::getInstance();
    }

    /**
     * Метод GET
     * 
     * 
     * http://ДОМЕН/maintenance
     * @return string
     */
    public function indexAction()
    {
        

        $answer = array(
            'status' => 'success',
            'messages' => 'All gilds',
            'data' => ""
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод GET
     * 
     * Получить информацию о цехе с {id}
     * http://ДОМЕН/gilds/1
     * Получить все машины цеха {id}
     * http://ДОМЕН/gilds/1/machines
     * @return string
     */

    public function viewAction()
    {


        if ($this->requestUri[0] == "types") {
            $data = $this->mt->GetTypes();
        }
        else if ($this->requestUri[0] == "type") {
            $data = $this->mt->GetType($this->requestUri[1]);
        }
        else if ($this->requestUri[0] == "table") {
            $data = $this->mt->GetTypeTable();
        }
        else if($this->requestUri[0] == "scheduler"){
            if($this->requestUri[1] == "date"){
                $data = $this->mt->GetMaintenceSchedulerDate($this->requestGETParam["start"],$this->requestGETParam["end"]);
            }
            else{
                $data = $this->mt->GetMaintenceScheduler($this->requestUri[1]);
            }
        }
        else{

        }



        $answer = array(
            'status' => 'success',
            'messages' => '',
            'data' => $data
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод POST
     * Загрузка файла
     * http://ДОМЕН/
     * @return string
     * 
     * }
     */


    public function createAction()
    {
        
        

        if ($this->requestUri[0] == "setModelMain") {
            $this->mt->SetModelType(
                $this->requestParams["model_id"],
                $this->requestParams["mtype_id"],
                $this->requestParams["value"]
            );
            $messages = "Model ".$this->requestParams["model_id"]." maintenance update.";
        }

        elseif($this->requestUri[0] == "setShedulerMain"){
            $this->mt->SetMaintenceSheduler( $this->requestParams["data"]);
        }
        elseif($this->requestUri[0] == "scheduler"){
            $this->mt->SetMaintenceShedulerEvent( $this->requestParams["data"]);
        }


        $answer = array(
            'status'    => 'success',
            'messages'  => $messages,
            'data'      => ""
        );

        return $this->response($answer, 200);
    }


    /**
     * Метод PUT
     * 
     * @return string
     */
    public function updateAction()
    {

        $answer = array(
            'status'    => 'success',
            'messages'  => 'Gild update',
            'data'      => ""
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/files/1
     * @return string
     */
    public function deleteAction()
    {

        $answer = array(
            'status' => 'success',
            'messages' => 'Gild delete'
        );
        return $this->response($answer, 200);
    }
}
