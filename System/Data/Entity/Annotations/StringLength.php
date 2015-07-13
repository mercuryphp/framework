<?php

namespace System\Data\Entity\Annotations;

class StringLength extends ValidationAttribute {
    
    protected $length;
    
    public function __construct($length, $errorMessage = ''){
        $this->length = $length;
        $this->errorMessage = $errorMessage;
    }
    
    public function getLength(){
        return $this->length;
    }
    
    public function isValid(){
        if(is_string($this->value) && strlen($this->value) > $this->length){
            return false;
        }
        return true;
    }
    
    public function getErrorMessage(){
        if($this->errorMessage){
            return $this->errorMessage;
        }
        return sprintf('%s must be less than %s characters', $this->columnName, $this->length);
    }

}

?>