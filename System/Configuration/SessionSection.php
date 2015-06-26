<?php

namespace System\Configuration;

class SessionSection {
    
    protected $name;
    protected $handler;
    
    public function __construct($section){
        if($section instanceof \System\Collections\Dictionary){
            $this->name = $section->name;
            $this->handler = $section->handler;
        }
    }

    public function getName(){
        return $this->name;
    }

    public function getHandler(){
        return $this->handler;
    }
}

?>
