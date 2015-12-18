<?php

namespace System\Web\Mvc\Validators;

class Required extends Validator {

    public function __construct($errMessage){
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if(is_scalar($this->value) && strlen($this->value) > 0){
            return true;
        }
        if(is_object($this->value)){
            return true;
        }
        return false;
    }
}