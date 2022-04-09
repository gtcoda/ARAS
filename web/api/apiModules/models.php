<?php

/**
 * @apiDefine admin:computer User access only
 * This optional description belong to to the group admin.
 */


$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirModule'] . 'Models.php');



class modelsApi extends Api
{
    public $apiName = 'models';

    private $model;

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();
        $this->model = Models::getInstance();
    }

    /**
     * Метод GET
     * 
     * @api {get} /models?view=:view&format=:format Вернуть все модели оборудования
     * @apiVersion 0.1.0
     * @apiName GetModels
     * @apiGroup Models
     *
     * @apiSuccess {Object[]} view      Выбор полей в виде [поле1,поле2,поле3]
     * @apiSuccess {String}   view.model_id        id модели 
     * @apiSuccess {String}   view.model_name      Название модели 
     * @apiSuccess {String}   view.model_desc      Описание модели
     * @apiSuccess {String}   format    Формат ответа all - все запрошеные поля в виде обьектов, index - индексирование по поле1, machineIndex - все станки проиндексированные по модели
     * 
     * @apiSuccessExample {json} Success-Response(view=[model_id,model_name]&format=index):
     * {
     * "status": "success",
     * "messages": "All models",
     * "data": {
     *         "1": "16А20",
     *        "2": "1П426",
     *        "13": "СС2В"
     *        }
     * }
     * 
     * @apiSuccessExample {json} Success-Response(view=[model_id,model_name]):
     * {
     * "status": "success",
     * "messages": "All models",
     * "data": [
     *     {
     *         "model_id": "1",
     *         "model_name": "16А20"
     *     },
     *     {
     *         "model_id": "2",
     *         "model_name": "1П426"
     *     },
     *     {
     *         "model_id": "13",
     *         "model_name": "СС2В"
     *     }
     *  ]
     * }
     * 
     * 
     * @apiSuccessExample {json} Success-Response(format=machineIndex):
     * {
     * "status": "success",
     * "messages": "Machine index for model",
     *   "data": {
     *      "RS-25": [
     *         {
     *         "machine_id": "65",
     *         "machine_number": "41693",
     *         "model_desc": "Periton",
     *         "model_name": "RS-25"
     *          }
     *        ],
     *       "HMC560": [
     *          {
     *           "machine_id": "66",
     *           "machine_number": "41579",
     *           "model_desc": "HURON",
     *           "model_name": "HMC560"
     *           }
     *        ]
     *    }
     *  }
     * 
     * 
     * 
     */
    public function indexAction()
    {

        $messages = "";
        if ($this->requestGETParam["format"] == "machineIndex") {
            $messages = "Machine index for model";
            $data =  $this->model->GetMachinesIndexModel();
        } else {
            $data = $this->model->Gets($this->requestGETParam["view"], $this->requestGETParam["format"]);
        }




        $answer = array(
            'status' => 'success',
            'messages' => $messages,
            'data' => $data,
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод GET
     * 
     * Получить информацию ою оборудовании с {id}
     * http://ДОМЕН/gilds/1
     * Метод GET
     * 
     * @api {get} /models/:id Получить информацию об оборудовании с {id}
     * @apiVersion 0.1.0
     * @apiName GetModelsId
     * @apiGroup Models
     *
     * @apiSuccess {Number} id  id модели
     * 
     * @api {get} /model/machine/:machine_id
     * @apiVersion 0.1.0
     * @apiName GetModelForMachineId
     * @apiGroup Models
     *
     * @apiSuccess {Number} machine_id  id станка
     * 
     */
    public function viewAction()
    {
        $messages = 'Model';
        // Запрос модели по machine_id
        if ($this->requestUri[0] == "machine") {
            $data = $this->model->GetModelForMachine($this->requestUri[1]);
            $messages = "Info for machine id";
        }
        // Запрос всех машин по модели
        elseif ($this->requestUri[0] == "machines") {
            $data = $this->model->GetMachinesIndexModel();
            $messages = "Machine index for model";
        } else {
            $data = $this->model->Get($this->requestUri[0]);
        }

        $answer = array(
            'status' => 'success',
            'messages' => $messages,
            'data' => $data,
        );
        return $this->response($answer, 200);
    }

    /**
     * Метод POST
     * Создание новой модели оборудования
     * http://ДОМЕН/gilds + параметры запроса 
     * @return string
     * 
     * Пример тела запроса
     * {
     * "model_name": "ГДВ400", // Название
     * "model_desc": "Обрабатывающий центр", // Описание  
     * }
     */


    public function createAction()
    {

        $res = $this->model->Add($this->requestParams);
        $answer = array(
            'status'    => 'success',
            'messages'  => 'Model creation completed',
            'data'      => $res,
        );
        return $this->response($answer, 200);
    }


    /**
     * Метод PUT
     * Изменить модель 
     * Пример тела запроса
     * {
     * "model_name": "ГДВ400", // Название
     * "model_desc": "Обрабатывающий центр", // Описание  
     * }
     * @return string
     */
    public function updateAction()
    {

        if ($this->model->Update($this->requestParams, $this->requestUri[0])) {
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Model update',
                'data'      => $this->model->Get($this->requestUri[0]),
            );
            return $this->response($answer, 200);
        }
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/models/1
     * @return string
     */
    public function deleteAction()
    {
        if ($this->model->Delite($this->requestUri[0])) {
            $answer = array(
                'status' => 'success',
                'messages' => 'Model delete'
            );
            return $this->response($answer, 200);
        }
    }
}
