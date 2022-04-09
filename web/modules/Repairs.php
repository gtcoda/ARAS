<?php
require_once($config['dirModule'] . 'ModulesClass.php');



class Repairs extends Modules
{
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
    public function Open()
    {

        try {
            $this->db->query("INSERT INTO ?n SET `repair_status` = ?s", $this->table, "open");

            return $this->db->insertId();
        } catch (Exception $e) {
            $this->logadd($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }
        return false;
    }

    // Проверка на существование открытого ремонта с id
    public function IssetRepair($id)
    {
        $res = $this->db->getOne("SELECT repair_status FROM ?n WHERE repair_id = ?s", $this->table, $id);
        if (!empty($res)) {
            return true;
        }
        return false;
    }


    // Вернуть id последнего ремонта на машине с {id}
    public function Сurrent($machine_id)
    {
        try {

            $this->logadd("id машины для получения помледнего ремонта");
            $this->logadd($machine_id);

            $res = $this->db->getOne("SELECT `repair_id` FROM ?n WHERE (machine_id = ?s) AND (repair_id IS NOT NULL) ORDER BY `repair_id` DESC LIMIT 1", "events", $machine_id);

            $this->logadd($res);

            return $res;
        } catch (Exception $e) {
            $this->logadd($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }
        return false;
    }

    // Вернуть данные относящиеся к последнему открытому ремонту
    public function CurrentData($machine_id)
    {
        $repair_id = $this->Сurrent($machine_id);


        $events_repair = $this->db->getAll(
            "SELECT * FROM ?n WHERE (machine_id = ?s) AND (repair_id = ?s)",
            "events",
            $machine_id,
            $repair_id
        );

        $status = null;
        $lastdate = null;
        foreach ($events_repair as $value) {
            if ($value['event_modif_1'] != null) {
                $status = $value['event_modif_1'];
            }
            if ($value['event_data'] != null) {
                $lastdate = $value['event_data'];
            }
        }


        $repairData = array(
            'repair_id' => $repair_id,
            'status'    =>   $status,
            'lastdate'  =>   $lastdate,

        );

        return $repairData;
    }


    // Сформировать данные для отображение на колендаре
    function getCalendar($start = null, $end = null)
    {

        if (empty($end)) {
            $end = date("Y-m-d");
        }
        if (empty($start)) {
            $start = date("Y-m-d", strtotime("-1 month"));
        }





        $data = array();
        $end_date = array();

        // Получиим сообщения всех интересующих ремонтов
        $events = $this->db->getAll(
            "SELECT machine_id, repair_id, event_data, event_message, DATE_FORMAT(event_data,'%Y-%m-%d') 
                                    FROM `events` 
                                    WHERE 
                                            repair_id IN (SELECT DISTINCT repair_id FROM `events` WHERE event_data BETWEEN ?s AND ?s ORDER BY repair_id) 
                                        AND 
                                            event_modif_1 = 'Open'",
            $start,
            $end
        );

        // Сформируем даты окончания ремонтов
        $end_date = $this->db->getInd(
            "repair_id",
            "  SELECT repair_id, event_data AS end_data 
                                        FROM `events` 
                                        WHERE 
                                            repair_id IN (SELECT DISTINCT repair_id FROM `events` WHERE event_data BETWEEN ?s AND ?s ORDER BY repair_id)  
                                        ORDER BY `events`.`repair_id` ASC",
            $start,
            $end
        );


        $this->logadd($end_date);

        // Вытащим номера и название моделей для оборудования
        $models = $this->db->getInd("machine_id", "SELECT machine_id, model_name, machine_number FROM `machines` INNER JOIN models ON machines.model_id = models.model_id");


        foreach ($events as $event) {
            $data[] = array(
                "id" => $event["machine_id"],
                "title" =>  $models[$event["machine_id"]]["machine_number"] .
                    " (" .  $models[$event["machine_id"]]["model_name"] . ") " .
                    $event["event_message"],
                "start" => $this->format_data($event["event_data"]),
                "end"   => $this->format_data($end_date[$event["repair_id"]]["end_data"])
            );
        }

        return $data;
    }


    function format_data($data)
    {
        if (!empty($data)) {
            return date("Y-m-d", strtotime($data));
        } else {
            return date("Y-m-d");
        }
    }
}
