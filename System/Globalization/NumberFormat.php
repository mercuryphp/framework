<?php

namespace System\Globalization;

class NumberFormat {
    
    protected $formats;
    
    /**
     * Initializes an instance of NumberFormat.
     * 
     * @param   object $formats
     */
    public function __construct($formats){
        $this->formats = $formats;
    }
    
    /**
     * Gets the currency symbol for the culture.
     * 
     * @return  string
     */
    public function getCurrencySymbol(){ 
        return (string)$this->formats->currencySymbol;
    }
    
    /**
     * Gets the number of decimal places to use in numeric values.
     * 
     * @return  string
     */
    public function getNumberDecimalDigits(){
        return (string)$this->formats->numberDecimalDigits;
    }
    
    /**
     * Gets the number of decimal places to use in numeric values.
     * 
     * @return  string
     */
    public function getCurrencyGroupSeparator(){
        return (string)$this->formats->currencyGroupSeparator;
    }
    
    public function getCurrencyDecimalSeparator(){
        return (string)$this->formats->currencyDecimalSeparator;
    }
    
    public function getCurrencyNegativePattern(){
        return (string)$this->formats->currencyNegativePattern;
    }
    
    public function getCurrencyPositivePattern(){
        return (string)$this->formats->currencyPositivePattern;
    }
    
    public function formatCurrency($value){
        $positivePattern = $this->getCurrencyPositivePattern();
        $negativePattern = $this->getCurrencyNegativePattern();
        
        $left = '';
        $right = '';
        $oBracket = '';
        $cBracket = '';
        $ns = array_fill(0, 15, '');
        
        if($value < 0){
            if(in_array($negativePattern, array(0,4,14,15))){
                $oBracket = '(';
                $cBracket = ')';
            }else{
                $ns[$negativePattern] = '-';
            }
        }

        switch ($positivePattern){
            case 0:
                $left = $this->getCurrencySymbol().$ns[2];
                break;
            case 1:
                $right = $this->getCurrencySymbol();
                break;
            case 2:
                $left = $this->getCurrencySymbol().' ';
                
                if($ns[2]){
                    $left = trim($left).$ns[2];
                }
                break;
            case 3:
                $right = ' '.$this->getCurrencySymbol();
                break;
        }
      
        return $oBracket.$ns[1].$ns[5].$ns[9].$left.$ns[8].$ns[12].$this->formatNumber(abs($value)).$ns[6].$ns[13].$right.$ns[3].$ns[7].$ns[10].$ns[11].$cBracket;
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