<?php

namespace System\Web\Mvc\Validators;

class Range extends Validator {
    
    protected $min;
    protected $max;
    
    public function __construct($min, $max, $errMessage){
        $this->min = $min;
        $this->max = $max;
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if(($this->value >= $this->min) && ($this->value <= $this->max)){
            return true;
        }
        return false;
    }
}