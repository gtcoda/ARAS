<?php

require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');
/**
 * 
 * Класс отвечает за обработку переодических событий
 * 
 */

class Cron extends Modules
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
    /**
     * 
     * Закрыть все ремонты подходящие под условия.
     * Изменить время сообщения на время предпоследнего сообщения, если сообщение закрытия пустое. 
     */
    function updateTimeCloseRepair()
    {
        $repairs = $this->db->getAll("SELECT * FROM events WHERE repair_id IN (SELECT repair_id FROM `events` WHERE event_modif_1 = 'Close')");
        $data = $this->arrayInd($repairs, "repair_id");

        foreach ($data as $rep) {
            $end = end($rep);
            $prev = prev($rep);

            if (empty($end["event_message"])) {
                $this->db->parse("UPDATE events SET event_data = '" . $prev["event_data"] . "' WHERE event_id = " . $end["event_id"]);
            }
        }
    }

    // Закрыть все открытые ремонты.
    function closeRepair()
    {
        $repairs = $this->db->getAll("SELECT event_id, repair_id, event_data, event_modif_1 FROM events");
        $data = $this->arrayInd($repairs, "repair_id");

        foreach ($data as $rep) {
            $end = end($rep);
            $prev = prev($rep);

            if ($end["event_modif_1"] == "") {
                $this->db->parse("UPDATE events SET event_modif_1 = 'Close' WHERE event_id = " . $end["event_id"]);
            }
        }
    }



    // Закрыть все открытые ремонты.
    function checkCloseRepair()
    {
        $repairs = $this->db->getAll("SELECT DISTINCT machine_id, repair_id FROM `events`");
        $data = $this->arrayInd($repairs, "machine_id");
        $this->logadd($data);

        foreach ($data as $rep) {
            $end = end($rep);
            $prev = prev($rep);

            if ($end["event_modif_1"] == "") {
                $this->db->parse("UPDATE events SET event_modif_1 = 'Close' WHERE event_id = " . $end["event_id"]);
            }
        }
    }

    // Закрыть все ненужные ремонты.
    function checkCloseRepairNot()
    {
        $repairs = $this->db->getAll("SELECT DISTINCT machine_id, repair_id FROM `events`");
        $data = $this->arrayInd($repairs, "machine_id");

        foreach ($data as $rep) {
            // Если только один ремонт, он нами не нужен.
            if (count($rep) > 2) {

                // Достаем предпоследний. 
                end($rep);
                $prev = prev($rep);
                $this->logadd($prev);

                $event = $this->db->getAll("SELECT DISTINCT * FROM `events` WHERE repair_id = " . $prev["repair_id"] . " ORDER BY event_id DESC LIMIT 1");
                $this->logadd($event);
            }
        }
    }


    // Преобразовать массив $arrs в ассоциативный массив по ключу $value
    function arrayInd($arrs, $value)
    {
        $data = array();
        foreach ($arrs as $arr) {
            if (array_key_exists($arr[$value], $data)) {
                $data[$arr[$value]][] = $arr;
            } else {
                $data[$arr[$value]] = array($arr);
            }
        }

        return $data;
    }
}
