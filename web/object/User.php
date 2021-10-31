<?php 


class User {
    private $user_id;
    private $user_login;
    private $user_password;
    private $user_name;

    public function __construct($login,$password,$name,$id = null){
        $this->user_login = $login;
        $this->user_password = password_hash($password, PASSWORD_DEFAULT);
        $this->user_name = $name;
        
        if ($id != null){
            $this->user_id = $id;
        }
        
    }


    public function setId($id){
        $this->user_id = $id;
    }


    public function setLogin($login){
        $this->user_login = $login;
    }

    public function setPassword($password){
        $this->user_password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setName($name){
        $this->user_name = $name;
    }


    // Вернуть информацию о пользователе для вставки в базу
    public function getSQL(){
        return $user = array(
            'user_login' => $this->user_login,
            'user_password' => $this->user_password,
            'user_name' => $this->user_name
        );
    }




}



?>