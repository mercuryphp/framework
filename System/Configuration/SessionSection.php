<?php

namespace System\Configuration;

class SessionSection {
    
    protected $section;
    
    public function __construct($section){
        if($section instanceof \System\Collections\Dictionary){
            $this->section = $section;
   
            if($this->section->secure && !in_array(strtolower($this->section->secure), array('true', 'false'))){
                throw new ConfigurationException("Invalid value specified for Session:Secure. Boolean required");
            }else{
                $this->section->secure = strtolower($this->section->secure) =='true' ? true : false;
            }
            
            if($this->section->httpOnly && !in_array(strtolower($this->section->httpOnly), array('true', 'false'))){
                throw new ConfigurationException("Invalid value specified for Session:HttpOnly. Boolean required");
            }else{
                $this->section->httpOnly = strtolower($this->section->httpOnly) =='true' ? true : false;
            }
        }
    }

    public function getName(){
        return $this->section->name;
    }
    
    public function getExpires(){
        return $this->section->expires;
    }
    
    public function getPath(){
        return $this->section->path;
    }
    
    public function getDomain(){
        return $this->section->domain;
    }
    
    public function getSecure(){
        return $this->section->secure;
    }
    
    public function getHttpOnly(){
        return $this->section->httpOnly;
    }

    public function getHandler(){
        return $this->section->handler;
    }
}

?>
