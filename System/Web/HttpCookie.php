<?php

namespace System\Web;

final class HttpCookie {

    private $name;
    private $value;
    private $expires;
    private $path;
    private $domain;
    private $isSecure;
    private $isHttpOnly;
    
    public function __construct($name, $value = '', $expires = 0, $path = '/', $domain = '', $isSecure = false, $isHttpOnly = true ){
        $this->name = $name;
        $this->value = $value;
        $this->expires = $expires;
        $this->path = $path;
        $this->domain = $domain;
        $this->isSecure = $isSecure;
        $this->isHttpOnly = $isHttpOnly;
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

    public function setExpires($expires){
        $this->expires = $expires;
    }
    
    public function getExpires(){
        return $this->expires;
    }
    
    public function setPath($path){
        $this->path = $path;
    }
    
    public function getPath(){
        return $this->path;
    }
    
    public function setDomain($domain){
        $this->domain = $domain;
    }
    
    public function getDomain(){
        return $this->domain;
    }

    public function isSecure($bool = null){
        if(!is_null($bool)){
            $this->isSecure = $bool;
        }else{
            return $this->isSecure;
        }
    }

    public function isHttpOnly($bool = null){
        if(!is_null($bool)){
            $this->isHttpOnly = $bool;
        }else{
            return $this->isHttpOnly;
        }
    }
}

?>
