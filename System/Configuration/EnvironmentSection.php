<?php

namespace System\Configuration;

class EnvironmentSection {

    protected $section;
    
    public function __construct($section){
        $defaults = new \System\Collections\Dictionary();
        $defaults->add('locale', 'en-GB')
            ->add('dateTimeFormat', 'yyyy-MM-dd HH:mm:ss')
            ->add('timezone', \System\Std\Date::now()->getTimezone()->getName())
            ->add('executionTime', 30);
        
        $defaults->merge($section);
        $this->section = $defaults;
    }
    
    public function getLocale(){
        return $this->section->locale;
    }
    
    public function getDateTimeFormat(){
        return $this->section->dateTimeFormat;
    }
    
    public function getTimezone(){
        return $this->section->timezone;
    }
    
    public function getExecutionTime(){
        return $this->section->executionTime;
    }
}

?>