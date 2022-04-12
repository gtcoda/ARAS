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
     * @api {get} /maintenance/:parameter1/:parameter2 Модуль ППР
     * @apiVersion 0.1.0
     * @apiName Maintenance
     * @apiGroup Maintenance
     *
     * 
     * @apiSuccess {String}   parameters1    Параметр запроса. types - возвращвет все типы ТО;
     * type - все типы ТО конкретной модели задаваемый :parameter2
     * table - возвращвет список активированых ППР по моделям
     * 
     */


    /**
     * Метод GET
     * 
     * @api {get} /maintenance/scheduler/:mod/:id[?date=:date] Назначеные ППР
     * @apiVersion 0.1.0
     * @apiName MaintenanceScheduler
     * @apiGroup Maintenance
     *
     * @apiDescription
     * Часть maintenance управляющая колендарем и событиями.
     * :mod = repair - управление выполняния назначеными ППР 
     * 
     * 
     * @apiSuccess {Number}   id        machine_id машины  
     * @apiSuccess {String}   [date]    Необязательный парамет. Дата на которую запрошивается ППР.    
     * 
     * 
     * * @apiSuccessExample {json} Success-Response(api/maintenance/scheduler/repair/44):
     *  {
     *     "status": "success",
     *     "messages": "Maintense machines planed",
     *     "data": [
     *        {
     *           "schedule_id": "134",
     *           "machine_id": "44",
     *           "m_date": "2022-04-11",
     *           "mtype_id": "1"
     *        }
     *     ]
     *   }
     * 
     * 
     * 
     * 
     */


    /**
     * Метод GET
     * 
     * @api {get} /maintenance/scheduler/repair/[:id][?date=:date] Назначеные  ППР
     * @apiVersion 0.1.0
     * @apiName MaintenanceSchedulerRepair
     * @apiGroup Maintenance
     *
     * @apiDescription
     * Возврашает все назначеные машине :id ППР  на текущий месяц.
     * При указании даты, на дату.
     * При отсутствии :id, все ППР на дату.
     * При отсутствии :id и :date, все ППР на текущий
     * 
     * @apiSuccess {Number}   id        machine_id машины  
     * @apiSuccess {String}   [date]    Необязательный парамет. Дата на которую запрошивается ППР.    
     * 
     * 
     * @apiSuccessExample {json} Success-Response(api/maintenance/scheduler/repair/44):
     *  {
     *     "status": "success",
     *     "messages": "Maintense machines planed",
     *     "data": [
     *        {
     *           "schedule_id": "134",
     *           "machine_id": "44",
     *           "m_date": "2022-04-11",
     *           "mtype_id": "1"
     *        }
     *     ]
     *   }
     * 
     *  @apiSuccessExample {json} Success-Response(api/maintenance/scheduler/repair/44?date=2022-04-12):
     *  {
     *     "status": "success",
     *     "messages": "Maintense machines planed",
     *     "data": {
     *           "schedule_id": "134",
     *           "machine_id": "44",
     *           "m_date": "2022-04-11",
     *           "mtype_id": "1"
     *        }
     *   }
     * 
     * 
     * @apiSuccessExample {json} Success-Response(api/maintenance/scheduler/repair/44?date=2022-04-13):
     *  {
     *     "status": "success",
     *     "messages": "Maintense machines planed",
     *     "data": null
     *   }
     * 
     * 
     */

    public function viewAction()
    {

        $messages = "";

        if ($this->requestUri[0] == "types") {
            $data = $this->mt->GetTypes();
        } else if ($this->requestUri[0] == "type") {
            $data = $this->mt->GetType($this->requestUri[1]);
        } else if ($this->requestUri[0] == "table") {
            $data = $this->mt->GetTypeTable();
        } else if ($this->requestUri[0] == "scheduler") {
            if ($this->requestUri[1] == "date") {
                $data = $this->mt->GetMaintenceSchedulerDate($this->requestGETParam["start"], $this->requestGETParam["end"]);
            } else if ($this->requestUri[1] == "repair") {
                $messages = "Maintense machines planed";
                $data = $this->mt->GetMaintenceScheduleRepair($this->requestUri[2], $this->requestGETParam["date"]);
            } else {
                $data = $this->mt->GetMaintenceScheduler($this->requestUri[1]);
            }
        } else {
        }



        $answer = array(
            'status' => 'success',
            'messages' => $messages,
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
            $messages = "Model " . $this->requestParams["model_id"] . " maintenance update.";
        } elseif ($this->requestUri[0] == "setShedulerMain") {
            $this->mt->SetMaintenceSheduler($this->requestParams["data"]);
        } elseif ($this->requestUri[0] == "scheduler") {
            $this->mt->SetMaintenceShedulerEvent($this->requestParams["data"]);
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
