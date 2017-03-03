<?php

namespace System\Web\Mvc\Validators;

class Length extends Validator {
    
    protected $length;
    
    public function __construct($length, $errMessage){
        $this->length = $length;
        $this->errMessage = $errMessage;
    }
    
    public function getLength(){
        return $this->length;
    }
    
    public function isValid(){
        if(strlen($this->value) != $this->length){
            return false;
        }
        return true;
    }
}