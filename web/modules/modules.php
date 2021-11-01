<?php
$config = include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once($config['dirConfig'] . 'safeMySQL.php');
require_once($config['dirConfig'] . 'log.php');

require_once($config['dirModule'].'Gilds.php');
require_once($config['dirModule'].'Users.php');
require_once($config['dirModule'].'Models.php');

// Фабрика модулей

class modules
{
    private static $instance;
    protected function __construct()
    {
    }
    protected function __clone()
    {
    }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance($modul)
    {

        /*
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
        */

        return $modul::getInstance();
    }
    
}
?>