<?php 
require_once('log.php');

class Config {

    private static $instance;
    
    private static $config = [
        'login' => 'aras',
        'password' => 'jj3ey7QiLUGvaO40',
        'db' => 'aras',
        'charset' => 'utf-8'
    ];

    

    protected function __construct() { }
    protected function __clone() { }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }




    public static function getInstance(){
       
       
        if(empty(self::$instance)) self::$instance = new self();
        return self::$instance;

    }

    

    public function get($key){
        $log = Logi::getInstance();
        
        $log->add(" Внутри config.php");
        $log->add( print_r($config,true));
        return $config[$key];
    }
    


}


















?>