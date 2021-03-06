<?php

/**
 * 
 * Класс отвечает за обработку ППР
 * 
 */

use PMA\libraries\Console;

require_once($config['dirModule'] . 'ModulesClass.php');
class Maintenance extends Modules
{
    private static $instance;



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

    // Список всех видов ППР
    function GetTypes()
    {
        try {
            $raw = $this->db->getAll("SELECT * FROM m_types");
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }


        return $raw;
    }

    // Вернуть все типы ремонтов модели
    function GetType($model_id)
    {



        try {

            if (empty($model_id)) {
                $raw = $this->db->getAll("SELECT model_id, mtype_id, mtype_name, mtype_period FROM `m_models` INNER JOIN m_types ON m_models.mtype_id = m_types.mtype_id");
                return $this->arrayInd($raw, "model_id");
            }




            $raw = $this->db->getAll("SELECT model_id, m_types.mtype_id, mtype_name, mtype_period FROM `m_models` INNER JOIN m_types ON m_models.mtype_id = m_types.mtype_id WHERE model_id = ?i", $model_id);
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }


        return $raw;
    }


    // Формируем список активированых ППР по моделям
    function GetTypeTable()
    {
        $models =  $this->db->getInd("model_id", "SELECT model_id,model_name FROM models");
        $mtypes = $this->db->getInd("mtype_id", "SELECT mtype_id,mtype_name FROM m_types");


        $res = [];

        foreach ($models as $model) {

            $s = [];
            foreach ($mtypes as $mtype) {
                if ($this->db->getOne("SELECT mtype_id FROM m_models WHERE mtype_id = ?i AND model_id = ?i", $mtype["mtype_id"], $model["model_id"])) {
                    $s += array($mtype["mtype_id"] => true);
                } else {
                    $s += array($mtype["mtype_id"] => false);
                }
            }


            $res[] = array(
                "model_id" => $model["model_id"],
                "model_name" => $model["model_name"],
                "mtype" => $s
            );
        }



        return $res;
    }

    // Добавить или удалить ППР к модели
    function SetModelType($model_id, $mtype_id, $value)
    {

        try {

            if ($value) { // добавить ППР к модели
                if (!($this->db->getOne("SELECT * FROM m_models WHERE model_id = ?i AND mtype_id = ?i", $model_id, $mtype_id))) { // Проверим что записи нет
                    // добавим
                    $this->db->query("INSERT INTO m_models (model_id, mtype_id) VALUES (?i,?i)", $model_id, $mtype_id);
                }
            } else { // Убрать ППР из модели
                $this->db->query("DELETE FROM m_models WHERE model_id = ?i AND mtype_id = ?i", $model_id, $mtype_id);
            }
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
    }



    function SetMaintenceSheduler($data)
    {
        try {
            foreach ($data as $machine) {
                $this->log->add($machine);

                // Удалим все записи для машины
                $this->db->query("DELETE FROM m_schedule WHERE machine_id = ?i ", $machine["machine_id"]);
                // Добавим новые в календарь
                foreach ($machine["maintence"] as $mount => $mtype_id) {
                    if ($mtype_id) {

                        $date = "2022-" . sprintf("%02d", $mount + 1) . "-01";
                        $this->db->query(
                            "INSERT INTO m_schedule (machine_id, m_date, mtype_id) VALUES (?i,?s,?i)",
                            $machine["machine_id"],
                            $date,
                            $mtype_id

                        );
                    }
                }
            }
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
    }



    function SetMaintenceShedulerEvent($data)
    {
        $this->log->add($data);
        try {
            $this->db->query("UPDATE m_schedule SET m_date = ?s WHERE schedule_id = ?i", $data["m_date"], (int)$data["schedule_id"]);
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
    }

    // Форминует список назначеных ППР по машинам или машине
    function GetMaintenceScheduler($machine_id)
    {


        try {

            if (!empty($machine_id)) {
                $ret = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

                $raw = $this->db->getAll("SELECT * FROM m_schedule INNER JOIN m_types ON m_types.mtype_id = m_schedule.mtype_id WHERE machine_id = ?i ", $machine_id);
                $this->log->add($raw);

                foreach ($raw as $schedul) {
                    $input = $schedul["m_date"];


                    list($day, $month, $year) = explode("-", $input);

                    $ret[$month - 1] = $schedul["mtype_name"];
                }

                return $ret;
            } else {


                $raw = $this->db->getAll("SELECT * FROM m_schedule INNER JOIN m_types ON m_types.mtype_id = m_schedule.mtype_id ");


                $data = array();
                $machines = $this->arrayInd($raw, "machine_id");

                foreach ($machines as $machine) {

                    $this->log->add($machine);

                    $ret = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($machine as $schedul) {

                        $this->log->add($schedul);

                        $input = $schedul["m_date"];


                        list($day, $month, $year) = explode("-", $input);

                        $ret[$month - 1] = $schedul["mtype_name"];
                    }

                    $this->log->add($ret);
                    $this->log->add($machine);
                    $data += [$machine[0]["machine_id"] => $ret];

                    $this->log->add($data);
                    $this->log->add("+++++++++++++++++++++++++++++++++++++++++++++++++++++++");
                }

                return $data;
            }
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
    }



    // Сформируем список ППР на запрошеный месяц
    function GetMaintenceSchedulerDate($start, $end)
    {
        try {
            $maintences = $this->db->getAll(

                "   SELECT
                        machine_number,
                        m_date,
                        mtype_name,
                        model_name,
                        schedule_id
                    FROM
                        `m_schedule`
                    INNER JOIN
                        m_types
                    ON
                        m_types.mtype_id = m_schedule.mtype_id
                    INNER JOIN
                        machines
                    ON
                        m_schedule.machine_id = machines.machine_id
                    INNER JOIN
                        models
                    ON
                        machines.model_id = models.model_id
                    WHERE
                        m_date BETWEEN ?s AND ?s    
                        
                        
                        ",
                $start,
                $end
            );



            $this->log->add($maintences);

            foreach ($maintences as $maintence) {
                $data[] = array(
                    "id" => $maintence["schedule_id"],
                    "title" => $maintence["machine_number"] . " (" . $maintence["model_name"] . ") " . $maintence["mtype_name"],
                    "start" => $this->format_data($maintence["m_date"]),
                    "end"   => $this->format_data($maintence["m_date"])
                );
            }

            return $data;
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
    }

    // Вернем все назначеные машине ремонты на этот месяц или на запрошеный день.
    // 
    function GetMaintenceScheduleRepair($machine_id = null, $date = null)
    {

        try {


            if (empty($machine_id)) {
                if (empty($date) || $date = null) {
                    $maintences =   $this->db->getAll(
                        "SELECT * FROM `m_schedule` INNER JOIN m_types ON m_schedule.mtype_id = m_types.mtype_id WHERE YEAR(`m_date`) = YEAR(NOW()) AND MONTH(`m_date`) = MONTH(NOW())"
                    );
                } else {
                    $maintences =   $this->db->getAll(
                        "SELECT * FROM `m_schedule` INNER JOIN m_types ON m_schedule.mtype_id = m_types.mtype_id WHERE m_date = ?s",
                        $date
                    );
                    if (empty($maintences)) {
                        return null;
                    }
                }
            } else {
                if (empty($date)) {
                    $maintences =   $this->db->getAll(
                        "SELECT * FROM `m_schedule` INNER JOIN m_types ON m_schedule.mtype_id = m_types.mtype_id WHERE YEAR(`m_date`) = YEAR(NOW()) AND MONTH(`m_date`) = MONTH(NOW()) AND machine_id = ?i",
                        $machine_id
                    );
                } else {
                    $maintences =   $this->db->getAll(
                        "SELECT * FROM `m_schedule` INNER JOIN m_types ON m_schedule.mtype_id = m_types.mtype_id WHERE machine_id = ?i AND m_date = ?s",
                        $machine_id,
                        $date
                    );

                    if (empty($maintences)) {
                        return null;
                    }

                    return $maintences[0];
                }
            }

            return $maintences;
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
    }



    // Сформируем список произведенных ППР. Все или за период.
    function GetMaintenceScheduleComplited($machine_id = null, $start = null, $end = null)
    {

        try {

            if (!empty($machine_id)) {

                $data =   $this->db->getAll(
                    "SELECT
                    users.user_id,
                    users.user_name,
                    mevent_messages,
                    machine_id,
                    m_date,
                    mtype_name,
                    m_types.mtype_id
                FROM
                    `m_events`
                INNER JOIN
                    m_schedule
                ON
                    m_events.schedule_id = m_schedule.schedule_id
                INNER JOIN
                    m_types
                ON
                    m_types.mtype_id = m_schedule.mtype_id
                INNER JOIN 
                    users
                ON
                m_events.user_id = users.user_id
                WHERE machine_id = ?i",
                    $machine_id
                );


                return $data;

            }
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
    }


    function AddShedulerRepairEvent($data)
    {
        try {

            // Удалим все записи для машины
            $this->db->query("DELETE FROM m_events WHERE schedule_id = ?i ", $data["schedule_id"]);

            $this->db->query(
                "INSERT INTO m_events (user_id, schedule_id, mevent_messages) VALUES (?i,?i,?s)",
                $data["user_id"],
                $data["schedule_id"],
                $data["mevent_messages"]
            );
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
            throw new RuntimeException((string)$this->eraseMySQLError($e->getMessage()));
        }
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
