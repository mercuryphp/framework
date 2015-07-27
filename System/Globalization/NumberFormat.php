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
}