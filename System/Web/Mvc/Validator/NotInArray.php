<?php

namespace System\Web\Mvc\Validator;

class NotInArray extends Validator {
    protected $array;
    
    public function __construct(array $array, $errMessage){
        $this->array = $array;
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if(in_array($this->value, $this->array)){
            return true;
        }
        return false;
    }
}