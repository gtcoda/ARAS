<?php
require_once($config['dirModule'] . 'ModulesClass.php');
require_once($config['dirModule'] . 'Repairs.php');

/**
 * 
 * Класс отвечает за:
 *      создание
 *      изменение
 *      получение
 *      *удаление
 * событий.
 * 
 */

class Events extends Modules
{
    private static $instance;
    private $table = "events";

    private $repair;


    protected function __construct()
    {
        parent::__construct();
        $this->repair = Repairs::getInstance();
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
     * Получить список всех последних событий
     * 
     * @return array
     */
    public function Gets()
    {
        $all = $this->db->getAll("SELECT * FROM ?n ORDER BY `event_id` Desc LIMIT 10 ", $this->table);
        return $all;
    }
    /**
     * Получить событие по event_id
     * 
     * @return array
     */
    public function Get($id)
    {

        try {
            $row = $this->db->getRow("SELECT * FROM ?n WHERE event_id=?i", $this->table, $id);
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
        }

        return $row;
    }


   /**
     * Получить последние события по machine_id
     * 
     * @return array
     */
    public function GetM($machine_id){
        $all = $this->db->getAll("SELECT * FROM ?n WHERE machine_id=?i ORDER BY `event_id` Desc LIMIT 10 ", $this->table, $machine_id);
        return $all;
    }

    /**
     * Получить последние события по machine_id обьеденив по ремонтам
     * 
     * @return array
     */
    public function GetMUnionRepair($machine_id){
        $all = $this->db->getAll("SELECT DISTINCT repair_id FROM ?n WHERE machine_id=?i ORDER BY `repair_id` Desc LIMIT 10 ", $this->table, $machine_id);
        //$all = array_reverse($all);
        $this->log->add($all);
        
        foreach ($all as &$value) {
            $out[$value["repair_id"]] = $this->db->getAll("SELECT * FROM ?n WHERE repair_id=?i ORDER BY `repair_id`,`event_id` LIMIT 10 ", $this->table, $value["repair_id"]);
        }
        
        return $out;
    }

    /**
     * Добавить машину
     * 
     * @return array
     */

    public function Add($arr)
    {
        $config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        $fields = ['machine_id', 'user_id', 'repair_id', 'event_data', 'event_message', 'event_modif_1', 'event_modif_2', 'event_modif_3'];

        // Проверим заполнены ли поля
        if (
            empty($arr['machine_id']) ||
            empty($arr['user_id']) ||
            empty($arr['event_message'])
        ) {
            throw new RuntimeException($config['messages']['NoCompl']);
        }

        $data = $this->db->filterArray($arr, $fields);

        $data['event_data'] = date('Y-m-d H:i:s');

        // Если нет id ремонта или введенный некоректный
        if (empty($arr['repair_id']) || !$this->repair->IssetRepair($arr['repair_id'])) {
            // Если неверный repair_id. Отдадим ошибку.
            if (!$this->repair->IssetRepair($arr['repair_id'])) {
                throw new RuntimeException($this->config['messages']['BadIdRepair']);
            }

            // Если же repair_id пустой, попробуем создать
            if (
                !empty($arr['event_modif_1']) &&
                ($arr['event_modif_1'] == 'Open' ||
                    $arr['event_modif_1'] == 'Info')
            ) { // В сообщении есть модификатор Open или Info
                // Откроем ремонт и добавим в запись
                $data['repair_id'] = $this->repair->Open();
            } else { // нет ни id ремонта, ни модификатора Open или Info. Выдадим ошибку.
                throw new RuntimeException($this->config['messages']['NoRepair']);
            }
        }



        try {
            $this->db->query("INSERT INTO ?n SET ?u", $this->table, $data);
            return $this->Get($this->db->insertId());
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException('Request is bad');
        }
    }
    /**
     * Изменить содержание записи по id
     * 
     * @return array
     */
    public function Update($arr, $id)
    {
        $fields = ['event_message'];

        // Проверим есть ли id
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если записи не существует
        if (empty($this->Get($id))) {
            throw new RuntimeException('No event!');
        }

        $data = $this->db->filterArray($arr, $fields);

        try {

            $this->db->query("UPDATE ?n SET ?u WHERE event_id = ?i", $this->table, $data, $id);
            return true;
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }
        return false;
    }


    // Удалить сообщение
    public function Delite($id)
    {
        // Проверим есть ли id
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если записи не существует
        if (empty($this->Get($id))) {
            throw new RuntimeException('No event!');
        }

        try {
            $this->db->query("DELETE FROM ?n WHERE event_id=?i", $this->table, $id);
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
