<?php

require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

abstract class Modules
{

    protected $db;
    protected $log;
    protected $config;

    function __construct()
    {
        $this->db = new SafeMySQL();
        $this->log = Logi::getInstance();
        $this->config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    }


    /**
     * Обрезаем ошибку SafeMySQL 
     * @return array
     * 
     * Убираем упоминание о библиотеке и полный запрос
     * 
     */
    protected function eraseMySQLError($string)
    {
        return substr($string, strlen("SafeMySQL: "), strpos($string, " Full query") - strlen("SafeMySQL: "));

    }


    protected function logadd($value){
        $this->log->add($value);
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
