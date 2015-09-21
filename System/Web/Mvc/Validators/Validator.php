<?php

namespace System\Web\Mvc\Validators;

abstract class Validator implements Validator {
    
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