<?php

namespace System\Web;

final class HttpCookie{

    private $name;
    private $value;
    private $expires;
    private $domain;
    private $path;
    private $secure;
    private $httpOnly;
    
    public function __construct($name, $value = ''){
        $this->name = $name;
        $this->value = $value;
        $this->expires = new \DateTime();
    }

    public function setName($name){
        $this->name = $name;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function setValue($value){
        $this->value = $value;
    }
    
    public function getValue(){
        return $this->value;
    }

    public function setExpires(\DateTime $dateTime){
        $this->expires = $dateTime;
    }
    
    public function getExpires(){
        return $this->expires;
    }
    
    public function setDomain($domain){
        $this->domain = $domain;
    }
    
    public function getDomain(){
        return $this->domain;
    }
    
    public function setPath($path){
        $this->path = $path;
    }
    
    public function getPath(){
        return $this->path;
    }
    
    public function setSecure($bool){
        $this->secure = $bool;
    }
    
    public function getSecure(){
        return $this->secure;
    }
    
    public function setHttpOnly($bool){
        $this->httpOnly = $bool;
    }
    
    public function getHttpOnly(){
        return $this->httpOnly;
    }
}

?>
