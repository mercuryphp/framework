<?php

namespace System\Web\Security;

use System\Std\Environment;
use System\Std\Object;
use System\Std\String;
use System\Std\Date;
use System\Data\Connection;

class Membership {
    
    protected $con;
    protected $params = array();
    
    const SHA256 = 'sha256';
    
    public function __construct(\System\Data\Connection $con = null){
        if(!$con){
            $connectionString = Environment::getDefaultConnectionString();
            
            if(!$connectionString){
                throw new \RuntimeException("no default connection");
            }
            
            $this->con = new Connection($connectionString);
        }
    }
    
    public function setup(array $params = array(), \System\Data\Connection $con = null){
        $this->params['tablePrefix'] = '';
        $this->params = array_merge($this->params,$params);
        
        $this->createMembershipSchema();
        $this->createUserSchema();
    }
    
    public function createUser($username, $password, $email, $firstName = '', $middleName = '', $lastName = ''){
        $membership = array(
            'password' => $password,
            'password_salt' => $password,
            'email' => $email,
            'is_active' => 1,
            'is_locked' => 0,
            'created_date' => Date::now()->toString(),
            'failed_password_count' => 0
        );
        
        $this->con->insert('membership', $membership);
        
        $user = array(
            'user_id' => $this->con->getInsertId(),
            'username' => $username,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName
        );

        $this->con->insert('users', $user);
        
        $obj = Object::toObject('System.Web.Security.MembershipUser', $membership, $user );
        
        print_R($obj); exit;
    }
    
    public static function createHmac($type, $string, $key){
        return hash_hmac($type, $string, $key);
    }
    
    public static function createIV($size, $source){
         return base64_encode(mcrypt_create_iv ($size, $source));
    }
    
    protected function createMembershipSchema(){

        $driver = $this->con->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $autoNumber = 'UNSIGNED NOT NULL AUTO_INCREMENT';
        
        switch ($driver) {
            case 'mysql':
                break;
        }
        
        $sql = String::set('CREATE TABLE ')
            ->append($this->params['tablePrefix'])
            ->append('membership ('.PHP_EOL)
            ->append('user_id INT '.$autoNumber.','.PHP_EOL)
            ->append('password VARCHAR(128) NOT NULL,'.PHP_EOL)
            ->append('password_salt VARCHAR(128) NOT NULL,'.PHP_EOL)  
            ->append('email VARCHAR(128),'.PHP_EOL)   
            ->append('password_question VARCHAR(256),'.PHP_EOL) 
            ->append('password_answer VARCHAR(128),'.PHP_EOL)
            ->append('is_active TINYINT,'.PHP_EOL)
            ->append('is_locked TINYINT,'.PHP_EOL) 
            ->append('created_date DATETIME,'.PHP_EOL)    
            ->append('last_login_date DATETIME,'.PHP_EOL)
            ->append('last_password_changed_date DATETIME,'.PHP_EOL)  
            ->append('last_locked_date DATETIME,'.PHP_EOL)
            ->append('failed_password_count int,'.PHP_EOL)
            ->append('PRIMARY KEY (`user_id`))');    
        
        $this->con->query($sql);
    }
    
    protected function createUserSchema(){

        $sql = String::set('CREATE TABLE ')
            ->append($this->params['tablePrefix'])
            ->append('users ('.PHP_EOL)
            ->append('user_id INT,'.PHP_EOL)
            ->append('username VARCHAR(128) NOT NULL,'.PHP_EOL)
            ->append('first_name VARCHAR(50),'.PHP_EOL)  
            ->append('middle_name VARCHAR(50),'.PHP_EOL)  
            ->append('last_name VARCHAR(50),'.PHP_EOL)
            ->append('last_active_date DATETIME NOT NULL,'.PHP_EOL)      
            ->append('PRIMARY KEY (`user_id`))');    
        
        $this->con->query($sql);
    }
}

?>