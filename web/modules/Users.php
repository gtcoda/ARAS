<?php
$config = include $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once($config['dirConfig'].'safeMySQL.php');
require_once($config['dirConfig'].'log.php');

class Users {


    private static $instance;
    protected function __construct() { }
    protected function __clone() { }
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
    private $users = [];

    public static function getInstance()
    {
        if (empty(self::$instance)) {  self::$instance = new self(); }
        return self::$instance;
    }


/**
 * 
 * Вернуть информацию одного пользователя с {id}
 * 
 * @return array
 * 
 * 
 * 
 */
    public function GetUserId($id)
    {
        $db = new SafeMySQL();

        try {
            $row = $db->getRow("SELECT user_id, user_login, user_name FROM ?n WHERE user_id=?i", "users", $id);
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
        }


        if (empty($row)) {
            throw new RuntimeException('User not valid');
        }

        return $row;
    }

/**
 * 
 * Вернуть информацию одного пользователя с определенным логином
 * 
 * @return array
 * 
 * 
 * 
 */
    public function GetUserLogin($login)
    {
        $db = new SafeMySQL();

        try {
            $row = $db->getRow("SELECT user_id, user_login, user_name FROM ?n WHERE user_login=?s", "users", $login);
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
        }

        return $row;
    }


/**
 * 
 * Вернуть информацию одного пользователя с определенным логином
 * 
 * @return array
 * 
 * 
 * 
 */
public function GetUser($value)
{
    $db = new SafeMySQL();

    try {

        if (ctype_digit($value)){
            $row = $db->getRow("SELECT user_id, user_login, user_name FROM ?n WHERE user_id=?i", "users", $value);
        }
        else{
            $row = $db->getRow("SELECT user_id, user_login, user_name FROM ?n WHERE user_login=?s", "users", $value);
        }

    } catch (Exception $e) {
        $log = Logi::getInstance();
        $log->add(print_r($e->getMessage(), true));
    }

    return $row;
}





/**
 * Вернуть информацию обо всех пользователях
 * 
 * @return array
 */
    public function GetUsers()
    {
        $db = new SafeMySQL();
        $all = $db->getAll("SELECT user_id, user_login, user_name FROM ?n", "users");
        return $all;
    }


/**
 * Вернуть роль пользователя с {id}
 * @return string
 */

    public function GetUserRole($id){
        // Подготовимся к логированию
        $log = Logi::getInstance();
        $log->add("Зашли в GetUserRole!");

        $log->add($id);
        $db = new SafeMySQL();
        $row = $this->GetUserId($id);
        $log ->add($row);

        try {
            $row = $db->getOne("SELECT role_id FROM ?n WHERE user_id=?i", "users", $id);
            return $db->getOne("SELECT role_name FROM roles WHERE role_id = ?i",$row);
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
        }
        return false;
    }

/**
 * Добавить нового пользователя
 * 
 * @return array
 */
    public function AddUser($arr)
    {
        $fields = ['user_password', 'user_login', 'user_name','role_id'];

        // Проверим заполнены ли поля
        if (
            empty($arr['user_login']) ||
            empty($arr['user_password']) ||
            empty($arr['user_name'])
        ) {
            throw new RuntimeException('Request is empty');
        }

        
        //Если такой пользователь существует
        if(!empty($this->GetUserLogin($arr['user_login']))){
            throw new RuntimeException('User is exists!');
            return false;
        }

        // Захешируем пароль
        $arr['user_password'] = password_hash($arr['user_password'], PASSWORD_DEFAULT);


        $db = new SafeMySQL();

        // Получим id роли serviceman

        $roleServiceMan = $db->getOne("SELECT role_id FROM roles WHERE role_name = 'serviceman'");

        $data = $db->filterArray($arr, $fields);

        $data += ['role_id' => $roleServiceMan];


        $log = Logi::getInstance();
        $log->add("Создаем пользователя с ролью.");
        $log->add($data);


        try {
            $db->query("INSERT INTO ?n SET ?u", "users", $data);
            return true;
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
        }

        return false;
    }





/**
 * Изменить запись пользователя по $id или логину
 * 
 * @return array
 */
    public function UpdateUser($arr,$id){
        $fields = ['user_password', 'user_name'];

        // Проверим заполнены ли поля
        if (
            empty($id)||
            empty($arr['user_password']) ||
            empty($arr['user_name'])
        ) {
            throw new RuntimeException('Request is empty');
        }

        //Если users не существует
        if(empty($this->GetUser($id))){
            throw new RuntimeException('User not exists!');
            return false;
        }

        // Захешируем пароль
        $arr['user_password'] = password_hash($arr['user_password'], PASSWORD_DEFAULT);

        $db = new SafeMySQL();
        $data = $db->filterArray($arr, $fields);


        try {

            if (ctype_digit($id)){
                $db->query("UPDATE ?n SET ?u WHERE user_id = ?i", 'users', $data,$id);
            }
            else{
                $db->query("UPDATE ?n SET ?u WHERE user_login = ?s", 'users', $data,$id);
            }


            
            return true;
        } catch (Exception $e) {
            $log = Logi::getInstance();
            $log->add(print_r($e->getMessage(), true));
            // Выдадим выше ошибку, но без подробностей
            throw new RuntimeException('Request is bad');
            return false;
        }

        
        return false;
    }

    


/**
 * Удалить запись пользователя
 * 
 * @return array
 */
public function DeleteUser($id){

    // Проверим заполнены ли полу
        if (empty($id)) {
        throw new RuntimeException('Request is empty');
    }

    //Если users не существует
    if(empty($this->GetUser($id))){
        throw new RuntimeException('User not exists!');
        return false;
    }

    $db = new SafeMySQL();
    try {

        if (ctype_digit($id)){
            $db->query("DELETE FROM ?n WHERE user_id=?i", 'users', $id);
        }
        else{
            $db->query("DELETE FROM ?n WHERE user_login=?s", 'users', $id);
        }

        return true;
    } catch (Exception $e) {
        $log = Logi::getInstance();
        $log->add(print_r($e->getMessage(), true));
        // Выдадим выше ошибку, но без подробностей
        throw new RuntimeException('Request is bad');
        return false;
    }

    
    return false;
}







/**
 * Проверить правильность введенной пары логин/пароль
 * @return bool
 */
public function CheckUser($login,$password)
{
    $db = new SafeMySQL();

    try {
        $row = $db->getRow("SELECT * FROM ?n WHERE user_login=?s", "users", $login);
        $log = Logi::getInstance();
        $log->add($row);
    } catch (Exception $e) {
        $log = Logi::getInstance();
        $log->add(print_r($e->getMessage(), true));
    }

    
    if(password_verify($password,$row['user_password'])){
        return true;
    }
    return false;
}



}
