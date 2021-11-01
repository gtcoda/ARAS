<?php 
$config = include $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

require_once($config['dirApiModules'].'apiClass.php');
require_once($config['dirConfig'].'safeMySQL.php');
require_once($config['dirConfig'].'log.php');

require_once($config['dirModule'].'Gilds.php');


class gildsApi extends Api{
    public $apiName = 'gilds';

     /**
     * Метод GET
     * 
     * Вернуть все цеха
     * http://ДОМЕН/gilds
     * @return string
     */
    public function indexAction()
    {
        $gild = Gilds::getInstance();
        $data = [];

        $data = $gild->GetGilds();
        $answer = array(
            'status' => 'success',
            'messages' => 'All users',
            'data' => $data,
        );
        return $this->response($answer, 200);

    }

    /**
     * Метод GET
     * 
     * Получить информацию о цехе с {id}
     * http://ДОМЕН/login/1
     * @return string
     */
    public function viewAction()
    {
        $gild = Gilds::getInstance();
        $data = [];

        try {
            $data = $gild->GetGild($this->requestUri[0]);
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
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Добавляем цех");

        $gild = Gilds::getInstance();

        $log->add($this->requestParams);

        try {
            if ($gild->AddGild($this->requestParams)) {
                $answer = array(
                    'status' => 'success',
                    'messages' => 'Gild creation completed'
                );
                return $this->response($answer, 200);
            }
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
     * 
     * @return string
     */
    public function updateAction()
    {
        $gild = Gilds::getInstance();

        try {
            if ($gild->UpdateGild($this->requestParams, $this->requestUri[0])) {
                $answer = array(
                    'status'    => 'success',
                    'messages'  => 'Gild update',
                    'data'      => $gild->GetGild($this->requestUri[0]),
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
        $gild = Gilds::getInstance();
        try {
            if ($gild->DeliteGild($this->requestUri[0])) {
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


?>