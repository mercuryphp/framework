<?php

namespace System\Globalization;

class CultureInfo {
    
    protected $xml = null;
    
    public function __construct($name){
        $dataFile = \System\Std\String::set(dirname(__FILE__).'/Data/'.$name.'.xml')->replace('\\', '/');
        
        if(is_file($dataFile)){
            $this->xml = simplexml_load_file($dataFile);
        }
    }
    
    public function getDisplayName(){
        return (string)$this->xml->displayName;
    }
    
    public function getShortDateString(){
        return (string)$this->xml->datetime->shortDatePattern;
    }
    
    public function getLongDatePattern(){
        return (string)$this->xml->datetime->longDatePattern;
    }
    
    public function getDayNames(){
        return new DayNames($this->xml->datetime->dayNames);
    }
    
    public function getMonthNames(){
        return new MonthNames($this->xml->datetime->monthNames);
    }
}

?>