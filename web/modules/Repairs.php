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


    public function GetGrantt()
    {

        $repair_data = array();
        $machine_data = array();
        $model_data = array();

        $repairs =  $this->db->getAll("SELECT * FROM ?n ORDER BY `repair_id` DESC LIMIT 10", "repairs");

        foreach ($repairs as $repair) {
            $events =  $this->db->getAll("SELECT *,DATE_FORMAT(event_data,'%Y-%m-%d') AS date FROM ?n WHERE `repair_id` = ?i", "events", $repair["repair_id"]);

            if (empty($events)) {
                continue;
            }
            $repair_data[$repair["repair_id"]] = array(
                "machine_id" => $events[0]["machine_id"],
                "event_message" => $events[0]["event_message"],
                "start_date" => $events[0]["date"],
                "end_date" => end($events)["date"],
            );
        }

        foreach ($repair_data as $r_data) {
            $machine = $this->db->getAll("SELECT * FROM ?n WHERE `machine_id` = ?i", "machines", $r_data["machine_id"]);


            if (array_key_exists($machine[0]["machine_number"], $machine_data)) {

                $temp = $machine_data[$machine[0]["machine_number"]];
                array_push($temp, $r_data);
                $machine_data[$machine[0]["machine_number"]] = $temp;
            } else {
                $machine_data[$machine[0]["machine_number"]][0] = $r_data;
            }
        }

        foreach ($machine_data as $machine_number => $m_data) {
            $model_id = $this->db->getOne("SELECT model_id FROM machines WHERE machine_number = ?i", $machine_number);
            $model = $this->db->getAll("SELECT * FROM ?n WHERE `model_id` = ?i", "models", $model_id);

            if (array_key_exists($model[0]["model_name"], $model_data)) {

                $temp = $model_data[$model[0]["model_name"]];
                array_push($temp, $m_data);
                $model_data[$model[0]["model_name"]] = $temp;
            } else {
                $v = array($machine_number => $m_data);
                $model_data[$model[0]["model_name"]] = $v;
            }
        }

        $this->logadd($model_data);


        //return $model_data;


        $data = array();


        $i_model = 1;
        $i_machine = 100;
        $i_repair = 1000;

        foreach($model_data as $md_key => $md){

            
            $data[] = array(
                "id" => $i_model,
                "text" => $md_key,
                "start_date" => "2022-04-01",
                "end_date" => "2022-04-28",
                "duration" => 30,
                "open" => true
            );


            foreach($md as $mac_key => $mach_data){
                $data[] = array(
                    "id" => $i_machine,
                    "text" => $mac_key,
                    "start_date" => "2022-04-01",
                    "end_date" => "2022-04-28",
                    "duration" => 30,
                    "open" => true,
                    "parent" => $i_model
                );

                foreach($mach_data as $rep){

                    $data[] = array(
                        "id" => $i_repair,
                        "text" => $rep["event_message"],
                        "start_date" => $rep["start_date"],
                        "end_date" => $rep["end_date"],
                        "duration" => 1,
                        "open" => false,
                        "parent" => $i_machine
                    );
                    $i_repair++;
                }
                    

                $i_machine++;
            }



            $i_model++;
        }


        return $data;

    }
}
