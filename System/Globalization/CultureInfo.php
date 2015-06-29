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
    
    public function numberFormat(){
        return new NumberFormat($this->xml->numberFormat);
    }
    
    public function formatCurrency($value){
        $pattern = $this->numberFormat()->getCurrencyPositivePattern();

        $left = '';
        $right = '';
        
        switch ($pattern) {
            case 0:
                $left = $this->numberFormat()->getCurrencySymbol();
                break;
            case 2:
                $right = $this->numberFormat()->getCurrencySymbol();
                break;
        }

        $value = $left.number_format(
            round($value, (int)$this->numberFormat()->getNumberDecimalDigits()), 
            (int)$this->numberFormat()->getNumberDecimalDigits(), 
            $this->numberFormat()->getCurrencyDecimalSeparator(), 
            $this->numberFormat()->getCurrencyGroupSeparator()
        ).' '.$right;
        
        return $value;
    }
}

?>