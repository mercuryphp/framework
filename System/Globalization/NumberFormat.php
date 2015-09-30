<?php

namespace System\Globalization;

class NumberFormat {
    
    protected $formats;
    
    public function __construct($formats){
        $this->formats = $formats;
    }
    
    public function getCurrencySymbol(){ 
        return (string)$this->formats->currencySymbol;
    }
    
    public function getNumberDecimalDigits(){
        return (string)$this->formats->numberDecimalDigits;
    }
    
    public function getCurrencyGroupSeparator(){
        return (string)$this->formats->currencyGroupSeparator;
    }
    
    public function getCurrencyDecimalSeparator(){
        return (string)$this->formats->currencyDecimalSeparator;
    }
    
    public function getCurrencyPositivePattern(){
        return (string)$this->formats->currencyPositivePattern;
    }
    
    public function formatCurrency($value){
        $pattern = $this->getCurrencyPositivePattern();

        $left = '';
        $right = '';
        
        switch ($pattern) {
            case 0:
                $left = $this->getCurrencySymbol();
                break;
            case 2:
                $right = $this->getCurrencySymbol();
                break;
        }
      
        return trim($left.$this->formatNumber($value).' '.$right);
    }
    
    public function formatNumber($value){
        return number_format(
            round($value, (int)$this->getNumberDecimalDigits()), 
            (int)$this->getNumberDecimalDigits(), 
            $this->getCurrencyDecimalSeparator(), 
            $this->getCurrencyGroupSeparator()
        );
    }
}