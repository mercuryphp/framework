<?php

namespace System\Web\Session;

abstract class Session {
    
    protected $collection = array();
    protected $sessionName;
    protected $sessionStarted = false;
    protected $sessionId;
    protected $onSaveFunction = null;
    
    public function hasKey($key){
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            return true;
        }
        return false;
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
    
    public function remove($key) {
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            unset($this->collection[$key]);
        }
        return false;
    }
    
    public function __set($key, $value){
        $this->set($key, $value); 
    }
    
    public function __get($key) {
        return $this->get($key);
    }
    
    public function onSave(callable $function){
        $this->onSaveFunction = $function;
    }
    
    public abstract function writeSession();
}

?>