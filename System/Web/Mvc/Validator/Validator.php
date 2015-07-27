<?php

namespace System\Web\Mvc\Validator;

abstract class Validator {
    
    protected $value;
    protected $errMessage;
    
    public function setValue($value){
        $this->value = $value;
    }
    
    public abstract function isValid();
    
    public function getMessage(){
        return $this->errMessage;
    }
}