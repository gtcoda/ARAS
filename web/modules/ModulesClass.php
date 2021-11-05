<?php

require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

abstract class Modules{

    protected $db;
    protected $log;
    protected $config;

    function __construct()
    {
        $this->db = new SafeMySQL();
        $this->log = Logi::getInstance();
        $this->config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    } 
}

?>