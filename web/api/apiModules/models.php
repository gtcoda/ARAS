<?php 
$config = include $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

require_once($config['dirApiModules'].'apiClass.php');
require_once($config['dirConfig'].'safeMySQL.php');
require_once($config['dirConfig'].'log.php');

require_once($config['dirModule'].'Models.php');



class modelsApi extends Api{
    public $apiName = 'models';

     /**
     * Метод GET
     * 
     * Вернуть все модели оборудования
     * http://ДОМЕН/gilds
     * @return string
     */
    public function indexAction()
    {
        $model = Models::getInstance();
        $data = [];

        $data = $model->Gets();
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
     * Получить информацию ою обюорудовании с {id}
     * http://ДОМЕН/login/1
     * @return string
     */
    public function viewAction()
    {
        $model = Models::getInstance();
        $data = [];

        try {
            $data = $model->Get($this->requestUri[0]);
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
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Добавляем модель");

        $model = Models::getInstance();

        $log->add($this->requestParams);

        try {
            $res = $model->Add($this->requestParams);
                $answer = array(
                    'status'    => 'success',
                    'messages'  => 'Model creation completed',
                    'data'      => $res,
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
        $model = Models::getInstance();

        try {
            if ($model->Update($this->requestParams, $this->requestUri[0])) {
                $answer = array(
                    'status'    => 'success',
                    'messages'  => 'Model update',
                    'data'      => $model->Get($this->requestUri[0]),
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
        $model = Models::getInstance();
        try {
            if ($model->Delite($this->requestUri[0])) {
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