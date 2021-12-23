<?php
require_once($config['dirApiModules'] . 'apiClass.php');
require_once($config['dirModule'] . 'Gilds.php');


class gildsApi extends Api
{
    public $apiName = 'gilds';

    private $gild;

    function __construct()
    {
        // Вызовем конструктор родителя
        parent::__construct();

        $this->gild = Gilds::getInstance();
    }

    /**
     * Метод GET
     * 
     * Вернуть все цеха
     * http://ДОМЕН/gilds
     * @return string
     */
    public function indexAction()
    {
        $data = $this->gild->Gets();
        $answer = array(
            'status' => 'success',
            'messages' => 'All gilds',
            'data' => $data,
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

    /**
     * Метод GET
     * 
     * @api {get} /gilds/:id/:parameters?view=:view&format=:format Вернуть все модели оборудования
     * @apiVersion 0.1.0
     * @apiName GetGilds
     * @apiGroup Gilds
     *
     * @apiSuccess {Number}   id            id цеха  
     * @apiSuccess {String}   parameters    Параметр запроса. machines - запрос всех машин входящих в определенный цех.
     * 
     * @apiSuccess {Object[]} view      Выбор полей в виде [поле1,поле2,поле3]
     * @apiSuccess {String}   view.machine_id      id оборудования
     * @apiSuccess {String}   view.model_id        id модели
     * @apiSuccess {String}   view.machine_number  номер машины
     * @apiSuccess {String}   view.gild_id         Номер цеха
     * @apiSuccess {String}   view.machine_desc    Описание машины
     * @apiSuccess {String}   view.machine_posX    Позиция по X
     * @apiSuccess {String}   view.machine_posY    Позиция по Y
     * 
     * @apiSuccess {String}   format    Формат ответа all - все запрошеные поля в виде обьектов, index - индексирование по поле1
     * 
     * @apiSuccessExample {json} Success-Response(format=index&view=[model_id,machine_number]):
     *{
     *	"status": "success",
     *	"messages": "Machine Gild",
     *	"data": {
     *		"7": "5650",
     *		"11": "41815",
     *		"12": "5745"
     *	}
     *}
     *
     * @apiSuccessExample {json} Success-Response(format=index&view=[model_id,machine_number,gild_id]):
     * {
     *    "status": "success",
     *    "messages": "Machine Gild",
     *    "data": {
     *        "7": {
     *            "machine_number": "5650",
     *            "gild_id": "1"
     *        },
     *        "11": {
     *            "machine_number": "41815",
     *            "gild_id": "1"
     *        }
     *   }
     *}
     * 
     * @apiSuccessExample {json} Success-Response(view=[model_id,model_name]):
     * {
     *   "status": "success",
     *   "messages": "Machine Gild",
     *   "data": [
     *       {
     *          "model_id": "7",
     *          "machine_number": "41812",
     *          "gild_id": "1"
     *      },
     *      {
     *          "model_id": "12",
     *          "machine_number": "5745",
     *          "gild_id": "1"
     *      }
     *  ]
     *}
     * 
     */

    public function viewAction()
    {
        if ($this->requestUri[1] == "machines") {
            
            $data = $this->gild->GetsM(
                $this->requestUri[0],
                $this->requestGETParam["view"],
                $this->requestGETParam["format"]
            );

            $answer = array(
                'status' => 'success',
                'messages' => 'Machine Gild',
                'data' => $data,
            );
            return $this->response($answer, 200);
        } else {
            $data = $this->gild->Get($this->requestUri[0]);
            $answer = array(
                'status' => 'success',
                'messages' => 'Gild',
                'data' => $data,
            );
            return $this->response($answer, 200);
        }
    }

    /**
     * Метод POST
     * Создание нового цеха
     * http://ДОМЕН/gilds + параметры запроса 
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
        $res = $this->gild->Add($this->requestParams);
        $answer = array(
            'status'    => 'success',
            'messages'  => 'Gild creation completed',
            'data'      => $res,
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
        if ($this->gild->Update($this->requestParams, $this->requestUri[0])) {
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Gild update',
                'data'      => $this->gild->Get($this->requestUri[0]),
            );
            return $this->response($answer, 200);
        }
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/gilds/1
     * @return string
     */
    public function deleteAction()
    {
        if ($this->gild->Delite($this->requestUri[0])) {
            $answer = array(
                'status' => 'success',
                'messages' => 'Gild delete'
            );
            return $this->response($answer, 200);
        }
    }
}
