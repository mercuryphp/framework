<?php

namespace System\Web\Mvc\Validators;

class IsMatch extends Validator {
    
    protected $pattern;
    
    public function __construct($pattern, $errMessage){
        $this->pattern = $pattern;
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        return preg_match('/'.$this->pattern.'/', $this->value);    
    }
}