<?php
require_once($config['dirModule'] . 'ModulesClass.php');
require_once($config['dirModule'] . 'Machines.php');


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


class Models extends Modules
{
    private static $instance;
    private $table = 'models';

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


    public function Gets($fields = [], $view = 'all')
    {



        try {

            if (empty($fields)) {
                $all = $this->db->getAll("SELECT * FROM ?n", $this->table);
            } else {
                if ($view == "all") {
                    $all = $this->db->getAll("SELECT ?l FROM ?n", $fields, $this->table);
                }
                if ($view == "index") {
                    $all = $this->db->getIndCol($fields[0], "SELECT ?l FROM ?n", $fields, $this->table);
                }
            }
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }


        return $all;
    }

    public function Get($id)
    {
        try {
            $row = $this->db->getRow("SELECT * FROM ?n WHERE model_id=?i", $this->table, $id);
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
        }

        return $row;
    }

    public function GetModelForMachine($machine_id)
    {
        $M = Machines::getInstance();
        $machine = $M->Get($machine_id);



        return $this->Get($machine['model_id']);
    }

    // Вернуть станки интексированные по модели.
    public function GetMachinesIndexModel()
    {
        try {
            $raw = $this->db->getAll("SELECT machine_id, machine_number, model_desc, model_name FROM `machines` INNER JOIN models ON machines.model_id = models.model_id");
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }

        $machineIndexModel = $this->arrayInd($raw, "model_name");
        return $machineIndexModel;
    }

    public function Add($arr)
    {

        $fields = ['model_name', 'model_desc'];

        // Проверим заполнены ли поля
        if (
            empty($arr['model_name']) ||
            empty($arr['model_desc'])
        ) {
            throw new RuntimeException($this->config['messages']['NoCompl']);
        }


        $data = $this->db->filterArray($arr, $fields);

        try {
            $this->db->query("INSERT INTO ?n SET ?u", $this->table, $data);

            return $this->Get($this->db->insertId());
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException($this->config['messages']['BadReq']);
        }

        return false;
    }
    /**
     * Изменить модель по $id
     * 
     * @return array
     */
    public function Update($arr, $id)
    {

        $fields = ['model_name', 'model_desc'];

        // Проверим заполнены ли поля
        if (empty($id)) {
            throw new RuntimeException($this->config['messages']['NoCompl']);
        }

        //Если model не существует
        if (empty($this->Get($id))) {
            throw new RuntimeException('Model not exists!');
            return false;
        }

        $data = $this->db->filterArray($arr, $fields);

        try {

            $this->db->query("UPDATE ?n SET ?u WHERE model_id = ?i", $this->table, $data, $id);
            return true;
        } catch (Exception $e) {

            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
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
            throw new RuntimeException('Model not exists!');
        }


        try {
            $this->db->query("DELETE FROM ?n WHERE model_id=?i", $this->table, $id);
            return true;
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }


        return false;
    }
}
