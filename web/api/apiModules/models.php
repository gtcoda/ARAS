<?php
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
     * Вернуть все модели оборудования
     * http://ДОМЕН/gilds
     * @return string
     */
    public function indexAction()
    {
        $fields_arr = [];
        if (!empty($this->requestGET['format'])) {
            $fields = $this->get_string_between($this->requestGET['format'], "{", "}");
            $fields_arr = explode(',', (string)$fields);
        }

        try {
            if(!empty($this->requestGET['view']) && $this->requestGET['format'] == "index"){
                $data = $this->model->Gets($fields_arr);
            }
            $data = $this->model->Gets($fields_arr);
        
        
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
     * @return string
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
