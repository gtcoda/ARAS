<?php
require_once($config['dirModule'] . 'ModulesClass.php');



class Repairs extends Modules  {
    private static $instance;
    private $table = "repairs";


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

    // Создадим новый id ремонта
    public function Open(){
        
        try {
            $this->db->query("INSERT INTO ?n SET `repair_status` = ?s", $this->table,"open");

            return $this->db->insertId(); 
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }
        return false;
    }

    public function IssetRepair($id){
        $res = $this->db->getOne("SELECT repair_status FROM ?n WHERE repair_id = ?s",$this->table,$id);
        if(!empty($res)){
            return true;
        }
        return false;
        
    }

    // Вернуть id последнего ремонта на машине с {id}
    public function Сurrent($machine_id){
        try {

            $this->log->add("Cvjnhbv id машины для получения помледнего ремонта");

            $this->log->add($machine_id);
            $res = $this->db->getOne("SELECT `repair_id` FROM ?n WHERE (machine_id = ?s) AND (repair_id IS NOT NULL) ORDER BY `repair_id` DESC LIMIT 1","events",$machine_id);

            $this->log->add($res);

            return $res; 
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }
        return false;
    }



}















?>