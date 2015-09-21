<?php

namespace System\Web\Mvc\Validators;

class IsAlpha extends Validator {

    public function __construct($errMessage){
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if(ctype_alpha($this->value)){
            return true;
        }
        return false;
    }
}