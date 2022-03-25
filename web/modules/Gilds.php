<?php
require_once($config['dirModule'] . 'ModulesClass.php');
require_once($config['dirModule'] . 'Repairs.php');


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

class Gilds extends Modules
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


    public function Gets()
    {
        $all = $this->db->getAll("SELECT * FROM ?n", "gilds");
        return $all;
    }

    public function Get($id)
    {
        try {
            $row = $this->db->getRow("SELECT * FROM ?n WHERE gild_id=?i", "gilds", $id);
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
        }

        return $row;
    }

    // Получение всех машин цеха с id

    public function GetsM($gild_id, $fields = [], $format = 'all')
    {



        if(!empty($fields)){
            if($format == "all"){
                $all = $this->db->getAll("SELECT ?l FROM ?n WHERE gild_id=?i", $fields, "machines", $gild_id);
                return $all;
            }
            if($format == "index"){
               
               
                $all = $this->db->getAll("SELECT ?l FROM ?n WHERE gild_id=?i", $fields, "machines", $gild_id);
                
                $repairs = Repairs::getInstance();
                // Добавим к выводу еще и информацию о последнем ремонте
                foreach($all as &$value){
                    $value += ['repair'=>$repairs->CurrentData($value['machine_id'])];
                }

                $res = [];
                foreach ($all as $value) {
                    $key = $value[$fields[0]];
                    $key_count = count($res[$key]);
                    $res[$key][$key_count] = $value;
                }




                $this->log->add($res);
                
                return $res;
            }
        }



        $all = $this->db->getAll("SELECT * FROM ?n WHERE gild_id=?i", "machines", $gild_id);
        return $all;
    }

    public function Add($arr)
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


        $data = $this->db->filterArray($arr, $fields);

        try {
            $this->db->query("INSERT INTO ?n SET ?u", "gilds", $data);
            return $this->Get($this->db->insertId());
        } catch (Exception $e) {
            $this->log->add($e->getMessage());
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
    public function Update($arr, $id)
    {
        $fields = ['gild_number', 'gild_name', 'gild_desc', 'gild_dimX', 'gild_dimY'];

        // Проверим заполнены ли поля
        if (empty($id)) {
            throw new RuntimeException('Request is empty');
        }

        //Если users не существует
        if (empty($this->Get($id))) {
            throw new RuntimeException('Gild not exists!');
        }



        $data = $this->db->filterArray($arr, $fields);

        try {
            $this->db->query("UPDATE ?n SET ?u WHERE gild_id = ?i", 'gilds', $data, $id);
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
            throw new RuntimeException('Gild not exists!');
        }


        try {
            $this->db->query("DELETE FROM ?n WHERE gild_id=?i", 'gilds', $id);
            return true;
        } catch (Exception $e) {

            $this->log->add($e->getMessage());
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }


        return false;
    }
}
