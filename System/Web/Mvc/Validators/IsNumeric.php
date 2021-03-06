<?php

namespace System\Web\Mvc\Validators;

class IsNumeric extends Validator {

    public function __construct($errMessage){
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if(is_numeric($this->value)){
            return true;
        }
        return false;
    }
}