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
    public function viewAction()
    {
        if($this->requestUri[1]=="machines"){
            try {
                $data = $this->gild->GetsM($this->requestUri[0]);
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Machine Gild',
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
        else{
            try {
                $data = $this->gild->Get($this->requestUri[0]);
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Gild',
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

        try {
            $res = $this->gild->Add($this->requestParams);
            $answer = array(
                'status'    => 'success',
                'messages'  => 'Gild creation completed',
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
     * 
     * @return string
     */
    public function updateAction()
    {


        try {
            if ($this->gild->Update($this->requestParams, $this->requestUri[0])) {
                $answer = array(
                    'status'    => 'success',
                    'messages'  => 'Gild update',
                    'data'      => $this->gild->Get($this->requestUri[0]),
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
     * http://ДОМЕН/gilds/1
     * @return string
     */
    public function deleteAction()
    {

        try {
            if ($this->gild->Delite($this->requestUri[0])) {
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Gild delete'
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
