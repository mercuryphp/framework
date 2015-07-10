<?php

namespace System\Configuration;

class EnvironmentSection extends \System\Collections\Dictionary {

    public function __construct($section){

        $default = array(
            'locale' => 'en-GB',
            'dateTimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'timezone' => \System\Std\Date::now()->getTimezone()->getName(),
            'executionTime' => 30
        );
        
        $this->merge($default)->merge($section);
        $this->isReadOnly = true;
    }
    
    public function getLocale(){
        return $this->collection['locale'];
    }
    
    public function getDateTimeFormat(){
        return $this->collection['dateTimeFormat'];
    }
    
    public function getTimezone(){
        return $this->collection['timezone'];
    }
    
    public function getExecutionTime(){
        return $this->collection['executionTime'];
    }
}

?>