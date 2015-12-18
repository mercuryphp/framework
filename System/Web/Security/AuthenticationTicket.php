<?php

namespace System\Web\Security;

final class AuthenticationTicket {
    
    private $name;
    private $expire;
    private $userData;
    
    public function __construct($name, $userData = '', $expire = 0){
        $this->name = $name;
        $this->userData = $userData;
        $this->expire = $expire;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getExpire(){
        return $this->expire;
    }
    
    public function getUserData(){
        return $this->userData;
    }
}