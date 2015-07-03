<?php

namespace System\Web\Security;

use System\Std\Environment;
use System\Std\String;
use System\Data\Connection;

class Membership {
    
    protected $con;
    protected $params = array();
    
    public static function initialize(array $params = array(), \System\Data\Connection $con = null){
        $membership = new Membership();
        $membership->params['tablePrefix'] = 'merc_';
        $membership->params = array_merge($membership->params,$params);
        
        if(!$con){
            $membership->con = new Connection(Environment::getDefaultConnectionString());
            
            if(!$membership->con){
                throw new \RuntimeException("no default connection");
            }
        }
        
        $membership->createMembershipSchema();
        $membership->createUserSchema();
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