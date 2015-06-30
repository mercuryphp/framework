<?php

namespace System\Configuration;

class SessionSection {
    
    protected $section;
    
    public function __construct($section){
        $defaults = new \System\Collections\Dictionary();
        $defaults->add('name', 'PHPSESSID')
            ->add('expires', 0)
            ->add('secure', false)
            ->add('httpOnly', true)
            ->add('handler', 'System.Web.Session.FileSystem');

        $defaults->merge($section);
        $this->section = $defaults;
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
