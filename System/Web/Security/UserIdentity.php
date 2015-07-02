<?php

namespace System\Web\Security;

class UserIdentity {

    protected $name;
    protected $userData;
    protected $isAuthenticated;
    
    public function __construct($name, $userData = '', $isAuthenticated = false){
        $this->name = $name;
        $this->userData = $userData;
        $this->isAuthenticated = $isAuthenticated;
    }

    public function getName(){
        return $this->name;
    }
    
    public function getUserData(){
        return $this->userData;
    }
    
    public function isAuthenticated(){
        return $this->isAuthenticated;
    }
}