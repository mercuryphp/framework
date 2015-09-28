<?php

namespace System\Data\Entity\Attributes;

class DefaultValue {
    
    protected $defaultValue;
    
    /**
     * Initializes an instance of DefaultValue with a value.
     * 
     * @param   string $defaultValue
     */
    public function __construct($defaultValue){
        $this->defaultValue = $defaultValue;
    }
    
    /**
     * Gets the default value.
     * 
     * @return  mixed
     */
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