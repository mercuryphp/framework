<?php

namespace System\Web\Session;

abstract class Session {
    
    protected $httpRequest;
    protected $httpResponse;
    protected $collection = array();
    protected $sessionName;
    protected $sessionStarted = false;
    protected $sessionId;
    protected $expires = 0;
    protected $path = '/';
    protected $domain = '';
    protected $isSecure = false;
    protected $isHttpOnly = true;
    
    public function __construct($request, $response){
        $this->httpRequest = $request;
        $this->httpResponse = $response;
    }
    
    public function setName($name){
        $this->sessionName = $name;
    }
    
    public function getName(){
        return $this->sessionName;
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
        if(is_null($bool)){
            return $this->isSecure;
        }
        $this->isSecure = (bool)$bool;
    }

    public function isHttpOnly($bool = null){
        if(is_null($bool)){
            return $this->isHttpOnly;
        }
        $this->isHttpOnly = (bool)$bool;
    }

    public function hasKey($key){
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            return true;
        }
        return false;
    }

    public function remove($key){
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            unset($this->collection[$key]);
        }
        return false;
    }
    
    public function abandon(){
        $this->httpResponse->getCookies()->add(new \System\Web\HttpCookie($this->sessionName,'',time()-86400, '', '/', false, true));
        $this->sessionStarted = false;
    }
    
    public function set($key, $value){
        $this->sessionStarted = true;
        $this->collection[$key] = $value;
    }
    
    public function get($key, $default = null){
        $this->sessionStarted = true;
        if(array_key_exists($key, $this->collection)){
            return $this->collection[$key];
        }
        if($default != null){
            return $default;
        }
        return false;
    }
    
    public function __set($key, $value){
        $this->set($key, $value); 
    }
    
    public function __get($key, $default = null) {
        return $this->get($key, $default);
    }
    
    public abstract function write();
}