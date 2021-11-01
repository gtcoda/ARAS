<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');


/**
 * 
 * Класс отвечает за:
 *      создание
 *      изменение
 *      получение
 *      удаление
 * моделей оборудования.
 * 
 */

class Models
{
    private static $instance;
    protected function __construct()
    {
    }
    protected function __clone()
    {
    }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function Gets()
    {
        $db = new SafeMySQL();
        $all = $db->getAll("SELECT * FROM ?n", "models");
        return $all;
    }

    public function Get($id)
    {
        $db = new SafeMySQL();

        try {
            $row = $db->getRow("SELECT * FROM ?n WHERE model_id=?i", "models", $id);
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
        }

        return $row;
    }

    public function AddModel($arr)
    {
        $fields = ['model_name', 'model_desc'];

        // Проверим заполнены ли поля
        if (
            empty($arr['model_name']) ||
            empty($arr['model_desc'])
        ) {
            throw new RuntimeException('Request is empty');
        }

        $db = new SafeMySQL();
        $data = $db->filterArray($arr, $fields);

        try {
            $db->query("INSERT INTO ?n SET ?u", "model", $data);
            return true;
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }

        return false;
    }
    /**
     * Изменить цех по $id
     * 
     * @return array
     */
    public function UpdateModel($arr, $id)
    {
        $fields = ['model_name', 'model_desc'];

        // Проверим заполнены ли поля
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если model не существует
        if (empty($this->GetModel($id))) {
            throw new RuntimeException('Model not exists!');
            return false;
        }


        $db = new SafeMySQL();
        $data = $db->filterArray($arr, $fields);

        try {

            $db->query("UPDATE ?n SET ?u WHERE model_id = ?i", 'models', $data, $id);
            return true;
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
            return false;
        }


        return false;
    }

    public function DeliteModel($id)
    {
        // Проверим заполнены ли полу
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если users не существует
        if (empty($this->GetModel($id))) {
            throw new RuntimeException('Model not exists!');
            return false;
        }

        $db = new SafeMySQL();
        try {
            $db->query("DELETE FROM ?n WHERE model_id=?i", 'models', $id);
            return true;
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
            return false;
        }


        return false;
    }
}
