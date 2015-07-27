<?php

namespace System\Web\Security;

final class AuthenticationTicket {
    
    private $name;
    private $expire;
    private $userData;
    
    public function __construct($name, $expire, $userData){
        $this->name = $name;
        $this->expire = $expire;
        $this->userData = $userData;
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