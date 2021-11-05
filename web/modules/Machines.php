<?php
require_once($config['dirModule'] . 'ModulesClass.php');

/**
 * 
 * Класс отвечает за:
 *      создание
 *      изменение
 *      получение
 *      удаление
 * станков.
 * 
 */

class Machines extends Modules
{
    private static $instance;
    private $table = "machines";

    protected function __construct()
    {
        parent::__construct();
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

    /**
     * Получить список всех машин
     * 
     * @return array
     */
    public function Gets()
    {
        $all = $this->db->getAll("SELECT * FROM ?n", $this->table);
        return $all;
    }
    /**
     * Получить машину по $id
     * 
     * @return array
     */
    public function Get($id)
    {
      

        try {
            $row = $this->db->getRow("SELECT * FROM ?n WHERE machine_id=?i", $this->table, $id);
        } catch (Exception $e) {
            
            $this->log->add($e->getMessage());
        }

        return $row;
    }
    /**
     * Добавить машину
     * 
     * @return array
     */

    public function Add($arr)
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        $fields = ['model_id', 'machine_number', 'gild_id', 'machine_desc', 'machine_posX', 'machine_posY'];

        // Проверим заполнены ли поля
        if (
            empty($arr['model_id']) ||
            empty($arr['machine_number']) ||
            empty($arr['gild_id']) ||
            empty($arr['machine_posX']) ||
            empty($arr['machine_posY'])
        ) {
            throw new RuntimeException($config['messages']['NoCompl']);
        }

       
        $data = $this->db->filterArray($arr, $fields);

        try {
            $this->db->query("INSERT INTO ?n SET ?u", $this->table, $data);
            return $this->Get($this->db->insertId());
        } catch (Exception $e) {
            
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }

        return false;
    }
    /**
     * Изменить машину по $id
     * 
     * @return array
     */
    public function Update($arr, $id)
    {
        $fields = ['model_id', 'machine_number', 'gild_id', 'machine_desc', 'machine_posX', 'machine_posY'];

        // Проверим заполнены ли поля
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если model не существует
        if (empty($this->Get($id))) {
            throw new RuntimeException('Model not exists!');
            return false;
        }


         $data = $this->db->filterArray($arr, $fields);

        try {

            $this->db->query("UPDATE ?n SET ?u WHERE machine_id = ?i", $this->table, $data, $id);
            return true;
        } catch (Exception $e) {
            
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
            return false;
        }


        return false;
    }

    public function Delite($id)
    {
        // Проверим заполнены ли полу
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если users не существует
        if (empty($this->Get($id))) {
            throw new RuntimeException('Machine not exists!');
            return false;
        }


        try {
            $this->db->query("DELETE FROM ?n WHERE machine_id=?i", $this->table, $id);
            return true;
        } catch (Exception $e) {
            
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
            return false;
        }


        return false;
    }
}
