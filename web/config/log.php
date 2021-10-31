<?php 


class Logi {
    private static $instance;

    protected function __construct() { }
    protected function __clone() { }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }




    public static function getInstance(){
        if(empty(self::$instance)) {

            // Получим настройки
            $config = include $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

            file_put_contents($config['dirLog'].'log.txt',"");
            self::$instance = new self();
        }

        return self::$instance;
    }

    

    public function add($value){

        // Получим настройки
        $config = include $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

        file_put_contents($config['dirLog'].'log.txt',print_r($value,true), FILE_APPEND );
        file_put_contents($config['dirLog'].'log.txt',"\n\r", FILE_APPEND );
        
    }

    
    


}


?>