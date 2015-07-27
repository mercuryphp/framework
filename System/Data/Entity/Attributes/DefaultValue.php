<?php

namespace System\Data\Entity\Attributes;

class DefaultValue {
    
    protected $defaultValue;
    
    public function __construct($defaultValue){
        $this->defaultValue = $defaultValue;
    }
    
    public function getDefaultValue(){
        
        if(strtolower($this->defaultValue) == '@now'){
            $this->defaultValue = \System\Std\Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        }
        else if(strtolower($this->defaultValue) == '@time'){
            $this->defaultValue = \System\Std\Date::now()->toString('HH:mm:ss');
        }
        else if(strtolower($this->defaultValue) == '@timestamp'){
            $this->defaultValue = \System\Std\Date::now()->getTimestamp();
        }
        
        return $this->defaultValue;
    }
}