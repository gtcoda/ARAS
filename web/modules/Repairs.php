<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');



class Repairs {
    private static $instance;
    private $table = "repairs";
    private $db;
    private $log;

    protected function __construct()
    {
        $this->db = new SafeMySQL();
        $this->log = Logi::getInstance();
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



}















?>