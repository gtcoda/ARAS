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
 * цехов.
 * 
 */

class Gilds
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


    public function GetGilds()
    {
        $db = new SafeMySQL();
        $all = $db->getAll("SELECT * FROM ?n", "gilds");
        return $all;
    }

    public function GetGild($id)
    {
        $db = new SafeMySQL();

        try {
            $row = $db->getRow("SELECT * FROM ?n WHERE gild_id=?i", "gilds", $id);
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
        }

        return $row;
    }

    public function AddGild($arr)
    {
        $fields = ['gild_number', 'gild_name', 'gild_desc', 'gild_dimX', 'gild_dimY'];

        // Проверим заполнены ли поля
        if (
            empty($arr['gild_number']) ||
            empty($arr['gild_name']) ||
            empty($arr['gild_desc']) ||
            empty($arr['gild_dimX']) ||
            empty($arr['gild_dimY'])
        ) {
            throw new RuntimeException('Request is empty');
        }

        $db = new SafeMySQL();
        $data = $db->filterArray($arr, $fields);

        try {
            $db->query("INSERT INTO ?n SET ?u", "gilds", $data);
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
    public function UpdateGild($arr, $id)
    {
        $fields = ['gild_number', 'gild_name', 'gild_desc', 'gild_dimX', 'gild_dimY'];

        // Проверим заполнены ли поля
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если users не существует
        if (empty($this->GetGild($id))) {
            throw new RuntimeException('Gild not exists!');
            return false;
        }


        $db = new SafeMySQL();
        $data = $db->filterArray($arr, $fields);

        try {

            $db->query("UPDATE ?n SET ?u WHERE gild_id = ?i", 'gilds', $data, $id);
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

    public function DeliteGild($id)
    {
        // Проверим заполнены ли полу
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если users не существует
        if (empty($this->GetGild($id))) {
            throw new RuntimeException('Gild not exists!');
            return false;
        }

        $db = new SafeMySQL();
        try {
            $db->query("DELETE FROM ?n WHERE gild_id=?i", 'gilds', $id);
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
