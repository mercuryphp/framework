<?php

namespace System\Web\Mvc\Validator;

class IsEmpty extends Validator {

    public function __construct($errMessage){
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if($this->value==''){
            return false;
        }
        return true;
    }
}