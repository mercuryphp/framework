<?php

namespace System\Globalization;

class CultureInfo {
    
    protected $xml = null;
    protected $numberFormat;


    public function __construct($name = ''){
        $dataFile = \System\Std\Str::set(dirname(__FILE__).'/Data/'.$name.'.xml')->replace('\\', '/');
        
        if(is_file($dataFile)){
            $this->xml = simplexml_load_file($dataFile);
        }else{
            throw new \Exception(sprintf("Culture '%s' is not supported", $name));
        }
        
        $this->numberFormat = new NumberFormat($this->xml->numberFormat);
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
    
    public function getNumberFormat(){
        return $this->numberFormat;
    }
    
    public function formatCurrency($value, $currencySymbol = true){
        $pattern = $this->numberFormat->getCurrencyPositivePattern();

        $left = '';
        $right = '';
        
        switch ($pattern) {
            case 0:
                $left = $this->numberFormat->getCurrencySymbol();
                break;
            case 2:
                $right = $this->numberFormat->getCurrencySymbol();
                break;
        }
        
        if(!$currencySymbol){
            $left=''; $right ='';
        }

        $value = $left.number_format(
            round($value, (int)$this->numberFormat->getNumberDecimalDigits()), 
            (int)$this->numberFormat->getNumberDecimalDigits(), 
            $this->numberFormat->getCurrencyDecimalSeparator(), 
            $this->numberFormat->getCurrencyGroupSeparator()
        ).' '.$right;
        
        return trim($value);
    }
}