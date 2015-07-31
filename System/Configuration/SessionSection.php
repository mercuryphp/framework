<?php

namespace System\Configuration;

class SessionSection extends \System\Collections\Dictionary {

    public function __construct($section){

        $defaults = array(
            'name' => 'PHPSESSID',
            'expires' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => true,
            'handler' => 'System.Web.Session.FileSystem'
        );

        $this->merge($defaults)->merge($section);
    }
    
    public function setName($name){
        $this->collection['name'] = $name;
    }

    public function getName(){
        return $this->collection['name'];
    }
    
    public function setExpires($expires){
        $this->collection['expires'] = $expires;
    }
    
    public function getExpires(){
        return $this->collection['expires'];
    }
    
    public function setPath($path){
        $this->collection['path'] = $path;
    }
    
    public function getPath(){
        return $this->collection['path'];
    }
    
    public function setDomain($domain){
        $this->collection['domain'] = $domain;
    }
    
    public function getDomain(){
        return $this->collection['domain'];
    }
    
    public function isSecure($bool = null){ 
        if(is_null($bool)){
            return $this->collection['secure'];
        }
        $this->collection['secure'] = $bool;
    }
    
    public function isHttpOnly($bool = null){
        if(is_null($bool)){
            return (bool)$this->collection['httpOnly'];
        }
        $this->collection['httpOnly'] = (bool)$bool;
    }

    public function getHandler(){
        return $this->collection['handler'];
    }
}