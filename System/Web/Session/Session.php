<?php

namespace System\Web\Session;

abstract class Session {
    
    protected $collection = array();
    protected $sessionName;
    protected $sessionStarted = false;
    protected $sessionId;
    protected $expire = 0;
    protected $path = '/';
    protected $domain = '';
    protected $secure = false;
    protected $httpOnly = true;
    protected $onSaveFunction = null;
    
    public function expire(){
        return \System\Std\Date::now();
    }
    
    public function setPath($path){
        $this->path = $path;
    }
    
    public function setDomain($domain){
        $this->domain = $domain;
    }
    
    public function setSecure($secure){
        $this->secure = $secure;
    }
    
    public function setHttpOnly($httpOnly){
        $this->httpOnly = $httpOnly;
    }

    public function hasKey($key){
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            return true;
        }
        return false;
    }

    public function remove($key) {
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            unset($this->collection[$key]);
        }
        return false;
    }
    
    public function abandon(){
        setcookie($this->sessionName, '', time()-86400, '/', '', false, true);
        $this->sessionStarted = false;
    }
    
    public function onSave(callable $function){
        $this->onSaveFunction = $function;
    }
    
    public function set($key, $value){
        $this->sessionStarted = true;
        $this->collection[$key] = $value;
    }
    
    public function get($key) {
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            return $this->collection[$key];
        }
        return false;
    }
    
    public function __set($key, $value){
        $this->set($key, $value); 
    }
    
    public function __get($key) {
        return $this->get($key);
    }
    
    public abstract function writeSession();
}

?>