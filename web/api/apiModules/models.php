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
     * @apiSuccess {Object[]} view      Выбор полей в виде {поле1,поле2,поле3}
     * @apiSuccess {String}   view.model_id        id модели 
     * @apiSuccess {String}   view.model_name      Название модели 
     * @apiSuccess {String}   view.model_desc      Описание модели
     * @apiSuccess {String}   format    Формат ответа all - все запрошеные поля в виде обьектов, index - индексирование по поле1
     * 
     * @apiSuccessExample {json} Success-Response(view={model_id,model_name}&format=index):
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
     * @apiSuccessExample {json} Success-Response(view={model_id,model_name}):
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
     */
    public function indexAction()
    {
        $fields_arr = [];
        if (!empty($this->requestGET['view'])) {
            $fields = $this->get_string_between($this->requestGET['view'], "{", "}");
            $fields_arr = explode(',', (string)$fields);
        }

        try {
            if (!empty($this->requestGET['format']) && $this->requestGET['format'] == "index") {
                $data = $this->model->Gets($fields_arr, "index");
            } else {
                $data = $this->model->Gets($fields_arr, "all");
            }
        } catch (Exception $e) {
            $answer = array(
                'status' => 'error',
                'messages' => $e->getMessage(),
            );
            return $this->response($answer, 400);
        }


        $answer = array(
            'status' => 'success',
            'messages' => 'All models',
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
     */
    public function viewAction()
    {
        try {
            $data = $this->model->Get($this->requestUri[0]);
            $answer = array(
                'status' => 'success',
                'messages' => 'Model',
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
        try {
            $res = $this->model->Add($this->requestParams);
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Model creation completed',
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
     * "model_name": "ГДВ400", // Название
     * "model_desc": "Обрабатывающий центр", // Описание  
     * }
     * @return string
     */
    public function updateAction()
    {
        try {
            if ($this->model->Update($this->requestParams, $this->requestUri[0])) {
                $answer = array(
                    'status'    => 'success',
                    'messages'  => 'Model update',
                    'data'      => $this->model->Get($this->requestUri[0]),
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
     * http://ДОМЕН/models/1
     * @return string
     */
    public function deleteAction()
    {

        try {
            if ($this->model->Delite($this->requestUri[0])) {
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
