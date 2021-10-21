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
        if(empty(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    

    public function add($value){
        file_put_contents('log.txt',$value, FILE_APPEND );
        file_put_contents('log.txt',"\n\r", FILE_APPEND );
        
    }
    


}


?>