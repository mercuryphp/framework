<?php

namespace System\Web\Mvc\Validators;

class Compare extends Validator {

    protected $compareValue;
    
    public function __construct($compareValue, $errMessage){
        $this->conpareValue = $compareValue;
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if($this->value == $this->conpareValue){
            return true;
        }
        return false;
    }
}